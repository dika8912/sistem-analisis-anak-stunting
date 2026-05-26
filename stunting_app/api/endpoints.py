from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select, and_, func, distinct, or_
from sqlalchemy.orm import selectinload
from typing import List, Optional
import math

from stunting_app.core.database import get_db_session
from stunting_app.config.settings import settings
from stunting_app.schemas.schemas import (
    DetectionRequest, DetectionResponse, WHOCalculationResponse, MLPredictionResponse,
    MeasurementCreateRequest, MeasurementResponse, GuardianResponse, ChildResponse,
    ChildCreateRequest, ChildUpdateRequest, EducationResponse, EducationCreateRequest, EducationUpdateRequest,
    AdminStatsResponse
)
from stunting_app.services.ml_prediction_service import MLPredictionService
from stunting_app.services.zscore_service import ZScoreService
from stunting_app.services.recommendation_service import RecommendationService
from stunting_app.repositories.who_repository import WHORepository
from stunting_app.repositories.child_repository import ChildRepository
from stunting_app.repositories.measurement_repository import MeasurementRepository
from stunting_app.repositories.education_repository import EducationRepository
from stunting_app.models.user import User, RoleEnum
from stunting_app.models.guardian import Guardian
from stunting_app.models.child import Child
from stunting_app.models.measurement import Measurement
from stunting_app.models.stunting_result import StuntingResult
from stunting_app.models.education import Education
from stunting_app.api.deps import require_user, require_admin, get_current_active_user

router = APIRouter()

# Instantiate Singletons
ml_service = MLPredictionService(models_dir=settings.MODELS_DIR)
who_repo = WHORepository()
child_repo = ChildRepository()
measurement_repo = MeasurementRepository()
education_repo = EducationRepository()

async def get_who_calculation(
    db: AsyncSession, gender: str, age_in_months: int, height_cm: float, weight_kg: float
) -> WHOCalculationResponse:
    # Fetch L, M, S for Height-for-Age (lhfa)
    lhfa_std = await who_repo.get_standard(db, gender, age_in_months, 'lhfa')
    haz = ZScoreService.calculate_zscore(height_cm, lhfa_std.l, lhfa_std.m, lhfa_std.s) if lhfa_std else 0.0
        
    # Fetch L, M, S for Weight-for-Age (wfa)
    wfa_std = await who_repo.get_standard(db, gender, age_in_months, 'wfa')
    waz = ZScoreService.calculate_zscore(weight_kg, wfa_std.l, wfa_std.m, wfa_std.s) if wfa_std else 0.0

    # Fetch L, M, S for Weight-for-Length/Height (wflh)
    wflh_std = await who_repo.get_standard(db, gender, age_in_months, 'wflh')
    whz = ZScoreService.calculate_zscore(weight_kg, wflh_std.l, wflh_std.m, wflh_std.s) if wflh_std else 0.0

    return WHOCalculationResponse(
        haz_zscore=round(haz, 2),
        waz_zscore=round(waz, 2),
        whz_zscore=round(whz, 2),
        stunting_status_who=ZScoreService.classify_stunting(haz),
        wasting_status_who=ZScoreService.classify_wasting(whz),
        underweight_status_who=ZScoreService.classify_underweight(waz)
    )

@router.post("/api/detect", response_model=DetectionResponse)
async def detect_on_the_fly(request: DetectionRequest, db: AsyncSession = Depends(get_db_session)):
    who_res = await get_who_calculation(db, request.gender, request.age_in_months, request.height_cm, request.weight_kg)
    ml_res_dict = ml_service.predict(request.gender, request.age_in_months, request.height_cm, request.weight_kg)
    ml_res = MLPredictionResponse(**ml_res_dict)
    recommendations = await RecommendationService.get_recommendations(db, stunting_status=who_res.stunting_status_who, age_months=request.age_in_months)
    return DetectionResponse(input=request, who_calculation=who_res, ml_prediction=ml_res, recommendations=recommendations)

@router.post("/api/measurements", response_model=MeasurementResponse)
async def create_measurement(
    request: MeasurementCreateRequest, 
    db: AsyncSession = Depends(get_db_session),
    current_user: User = Depends(require_user)
):
    child = await child_repo.get_by_id(db, request.child_id)
    if not child:
        raise HTTPException(status_code=404, detail="Child not found")
        
    # Authorization check
    if current_user.role != RoleEnum.admin:
        query = select(Guardian).where(Guardian.user_id == current_user.id)
        guardian = (await db.execute(query)).scalar_one_or_none()
        if not guardian or child.guardian_id != guardian.id:
            raise HTTPException(status_code=403, detail="Not authorized to add measurement for this child")
        
    age_td = request.measured_at - child.date_of_birth
    age_months = max(0, math.floor(age_td.days / 30.44))
    
    who_res = await get_who_calculation(db, child.gender, age_months, request.height, request.weight)
    ml_res_dict = ml_service.predict(child.gender, age_months, request.height, request.weight)
    
    measurement_data = {
        "child_id": request.child_id,
        "measured_at": request.measured_at,
        "age_in_months": age_months,
        "weight": request.weight,
        "height": request.height,
        "head_circumference": request.head_circumference,
        "measured_by": request.measured_by
    }
    result_data = {**who_res.model_dump(), **ml_res_dict}
    
    db_measurement = await measurement_repo.create_with_result(db, measurement_data, result_data)
    recommendations = await RecommendationService.get_recommendations(db, stunting_status=who_res.stunting_status_who, age_months=age_months)
    
    return MeasurementResponse(
        id=db_measurement.id,
        child_id=db_measurement.child_id,
        measured_at=db_measurement.measured_at,
        age_in_months=db_measurement.age_in_months,
        weight=db_measurement.weight,
        height=db_measurement.height,
        head_circumference=db_measurement.head_circumference,
        measured_by=db_measurement.measured_by,
        stunting_result=result_data,
        food_recommendations=recommendations
    )

@router.get("/api/guardians/me", response_model=GuardianResponse)
async def get_my_guardian_profile(db: AsyncSession = Depends(get_db_session), current_user: User = Depends(require_user)):
    query = select(Guardian).where(Guardian.user_id == current_user.id)
    guardian = (await db.execute(query)).scalar_one_or_none()
    if not guardian:
        raise HTTPException(status_code=404, detail="Guardian profile not found")
    return guardian

@router.get("/api/children/{id}", response_model=ChildResponse)
async def get_child(id: str, db: AsyncSession = Depends(get_db_session), current_user: User = Depends(get_current_active_user)):
    child = await child_repo.get_by_id_with_guardian(db, id)
    if not child:
        raise HTTPException(status_code=404, detail="Child not found")
        
    if current_user.role != RoleEnum.admin:
        if not child.guardian or child.guardian.user_id != current_user.id:
            raise HTTPException(status_code=403, detail="Not authorized")
            
    return child

@router.post("/api/children", response_model=ChildResponse, status_code=status.HTTP_201_CREATED)
async def create_child(
    request: ChildCreateRequest,
    db: AsyncSession = Depends(get_db_session),
    current_user: User = Depends(require_user)
):
    query = select(Guardian).where(Guardian.user_id == current_user.id)
    guardian = (await db.execute(query)).scalar_one_or_none()
    
    if not guardian:
        raise HTTPException(status_code=403, detail="Only users with a guardian profile can add a child.")
        
    child_data = {
        "guardian_id": guardian.id,
        "name": request.name,
        "gender": request.gender,
        "date_of_birth": request.date_of_birth,
        "nik": request.nik
    }
    
    new_child = await child_repo.create(db, child_data)
    return new_child

@router.get("/api/children/{id}/history")
async def get_child_history(id: str, db: AsyncSession = Depends(get_db_session), current_user: User = Depends(get_current_active_user)):
    child = await child_repo.get_by_id_with_guardian(db, id)
    if not child:
        raise HTTPException(status_code=404, detail="Child not found")
        
    if current_user.role != RoleEnum.admin:
        if not child.guardian or child.guardian.user_id != current_user.id:
            raise HTTPException(status_code=403, detail="Not authorized")
            
    query = select(Measurement).options(selectinload(Measurement.stunting_result)).where(Measurement.child_id == id).order_by(Measurement.measured_at.desc())
    result = await db.execute(query)
    measurements = result.scalars().all()
    
    return [{"measurement": m, "result": m.stunting_result} for m in measurements]

@router.get("/api/admin/children", response_model=List[ChildResponse])
async def search_children(
    parent_name: str, 
    nomor_kk: str, 
    db: AsyncSession = Depends(get_db_session), 
    current_user: User = Depends(require_admin)
):
    query = select(Child).join(Guardian).where(
        and_(
            Guardian.name.ilike(f"%{parent_name}%"),
            Guardian.nomor_kk == nomor_kk
        )
    )
    result = await db.execute(query)
    children = result.scalars().all()
    return children

@router.get("/api/admin/children/search", response_model=List[ChildResponse])
async def search_children_by_category(
    query_val: str,
    category: str,
    db: AsyncSession = Depends(get_db_session),
    current_user: User = Depends(require_admin)
):
    if category.lower() == 'nik':
        query = select(Child).where(Child.nik == query_val)
    elif category.lower() == 'nama':
        query = select(Child).where(Child.name.ilike(f"%{query_val}%"))
    else:
        raise HTTPException(status_code=400, detail="Category must be 'nik' or 'nama'")
    result = await db.execute(query)
    return result.scalars().all()

@router.put("/api/admin/children/{id}", response_model=ChildResponse)
async def update_child(id: str, request: ChildUpdateRequest, db: AsyncSession = Depends(get_db_session), current_user: User = Depends(require_admin)):
    update_data = request.model_dump(exclude_unset=True)
    child = await child_repo.update(db, id=id, obj_in=update_data)
    if not child:
        raise HTTPException(status_code=404, detail="Child not found")
    return child

@router.delete("/api/admin/children/{id}", status_code=status.HTTP_204_NO_CONTENT)
async def delete_child(id: str, db: AsyncSession = Depends(get_db_session), current_user: User = Depends(require_admin)):
    success = await child_repo.delete(db, id=id)
    if not success:
        raise HTTPException(status_code=404, detail="Child not found")
    return None

@router.get("/api/educations", response_model=List[EducationResponse])
async def get_educations(db: AsyncSession = Depends(get_db_session)):
    return await education_repo.get_all(db)

@router.get("/api/educations/{id}", response_model=EducationResponse)
async def get_education(id: str, db: AsyncSession = Depends(get_db_session)):
    edu = await education_repo.get_by_id(db, id)
    if not edu:
        raise HTTPException(status_code=404, detail="Education not found")
    return edu

@router.post("/api/admin/educations", response_model=EducationResponse, status_code=status.HTTP_201_CREATED)
async def create_education(request: EducationCreateRequest, db: AsyncSession = Depends(get_db_session), current_user: User = Depends(require_admin)):
    return await education_repo.create(db, obj_in=request.model_dump())

@router.put("/api/admin/educations/{id}", response_model=EducationResponse)
async def update_education(id: str, request: EducationUpdateRequest, db: AsyncSession = Depends(get_db_session), current_user: User = Depends(require_admin)):
    edu = await education_repo.update(db, id=id, obj_in=request.model_dump(exclude_unset=True))
    if not edu:
        raise HTTPException(status_code=404, detail="Education not found")
    return edu

@router.delete("/api/admin/educations/{id}", status_code=status.HTTP_204_NO_CONTENT)
async def delete_education(id: str, db: AsyncSession = Depends(get_db_session), current_user: User = Depends(require_admin)):
    success = await education_repo.delete(db, id=id)
    if not success:
        raise HTTPException(status_code=404, detail="Education not found")
    return None

@router.get("/api/admin/stats", response_model=AdminStatsResponse)
async def get_admin_stats(db: AsyncSession = Depends(get_db_session), current_user: User = Depends(require_admin)):
    # Total children
    result = await db.execute(select(func.count(Child.id)))
    total_children = result.scalar() or 0

    # Total measurements
    result = await db.execute(select(func.count(Measurement.id)))
    total_measurements = result.scalar() or 0

    # Needs attention (unique children who have latest measurement as stunted or wasted)
    # Using distinct to only count each child once even if they have multiple bad measurements
    query = select(func.count(distinct(Measurement.child_id)))\
        .join(StuntingResult, Measurement.id == StuntingResult.measurement_id)\
        .where(
            or_(
                StuntingResult.stunting_status_who.in_(['stunted', 'severely_stunted']),
                StuntingResult.wasting_status_who.in_(['wasted', 'severely_wasted', 'severely_underweight', 'underweight'])
            )
        )
    result = await db.execute(query)
    needs_attention = result.scalar() or 0

    return AdminStatsResponse(
        total_children=total_children,
        total_measurements=total_measurements,
        needs_attention=needs_attention
    )
