from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select, and_, func, distinct, or_
from sqlalchemy.orm import selectinload
from typing import List, Optional, Tuple
import math

from stunting_app.core.database import get_db_session
from stunting_app.config.settings import settings
from stunting_app.schemas.schemas import (
    CalculateRequest, CalculateResponse, PredictRequest, PredictResponse, MLPredictionResponse,
    MeasurementCreateRequest, MeasurementResponse, GuardianResponse, ChildResponse,
    ChildCreateRequest, ChildUpdateRequest, EducationResponse, EducationCreateRequest, EducationUpdateRequest,
    AdminStatsResponse
)
from stunting_app.services.ml_prediction_service import MLPredictionService
from stunting_app.services.recommendation_service import RecommendationService
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
child_repo = ChildRepository()
measurement_repo = MeasurementRepository()
education_repo = EducationRepository()

@router.post("/api/calculate", response_model=CalculateResponse)
async def calculate_zscore_on_the_fly(request: CalculateRequest, db: AsyncSession = Depends(get_db_session)):
    try:
        ml_res_dict = ml_service.predict(request.gender, request.age_in_months, request.height_cm, request.weight_kg)
        ml_res = MLPredictionResponse(**ml_res_dict)
        
        # We can use ML stunting status to get recommendations
        recommendations = await RecommendationService.get_recommendations(db, stunting_status=ml_res.stunting_status_ml, age_months=request.age_in_months)
        meal_plan = RecommendationService.get_meal_plan(stunting_status=ml_res.stunting_status_ml, age_months=request.age_in_months)
        
        import datetime
        if ml_res.stunting_status_ml in ["zona_bahaya", "zona_sedang"] or ml_res.wasting_status_ml in ["zona_bahaya", "zona_sedang"]:
            next_visit = datetime.date.today() + datetime.timedelta(days=14)
        else:
            next_visit = datetime.date.today() + datetime.timedelta(days=30)

        return CalculateResponse(
            input=request, 
            ml_prediction=ml_res, 
            recommendations=recommendations,
            meal_plan=meal_plan,
            next_visit_date=next_visit
        )
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Calculation error: {str(e)}")

@router.post("/api/predict", response_model=PredictResponse)
async def predict_ml_on_the_fly(request: PredictRequest):
    try:
        ml_res_dict = ml_service.predict(request.gender, request.age_in_months, request.height_cm, request.weight_kg)
        ml_res = MLPredictionResponse(**ml_res_dict)
        return PredictResponse(input=request, ml_prediction=ml_res)
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Prediction error: {str(e)}")

@router.post("/api/measurements", response_model=MeasurementResponse)
async def create_measurement(
    request: MeasurementCreateRequest, 
    db: AsyncSession = Depends(get_db_session),
    current_user: User = Depends(require_user)
):
    try:
        child = await child_repo.get_by_id(db, request.child_id)
        if not child:
            raise HTTPException(status_code=404, detail="Child not found")
            
        # Authorization check
        if current_user.role != RoleEnum.admin:
            query = select(Guardian).where(Guardian.user_id == current_user.id)
            guardian = (await db.execute(query)).scalars().first()
            if not guardian or child.guardian_id != guardian.id:
                raise HTTPException(status_code=403, detail="Not authorized to add measurement for this child")
            
        age_td = request.measured_at - child.date_of_birth
        age_months = max(0, math.floor(age_td.days / 30.44))
        
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
        result_data = {
            "stunting_status_ml": ml_res_dict.get("stunting_status_ml"),
            "wasting_status_ml": ml_res_dict.get("wasting_status_ml"),
            "ml_stunting_confidence": ml_res_dict.get("stunting_confidence", 0.0),
            "ml_wasting_confidence": ml_res_dict.get("wasting_confidence", 0.0)
        }
        
        db_measurement = await measurement_repo.create_with_result(db, measurement_data, result_data)
        recommendations = await RecommendationService.get_recommendations(db, stunting_status=ml_res_dict["stunting_status_ml"], age_months=age_months)
        meal_plan = RecommendationService.get_meal_plan(stunting_status=ml_res_dict["stunting_status_ml"], age_months=age_months)
        
        import datetime
        if ml_res_dict["stunting_status_ml"] in ["zona_bahaya", "zona_sedang"] or ml_res_dict["wasting_status_ml"] in ["zona_bahaya", "zona_sedang"]:
            next_visit = request.measured_at + datetime.timedelta(days=14)
        else:
            next_visit = request.measured_at + datetime.timedelta(days=30)
        
        return MeasurementResponse(
            id=db_measurement.id,
            child_id=db_measurement.child_id,
            measured_at=db_measurement.measured_at,
            age_in_months=db_measurement.age_in_months,
            weight=db_measurement.weight,
            height=db_measurement.height,
            head_circumference=db_measurement.head_circumference,
            measured_by=db_measurement.measured_by,
            stunting_result={
                "stunting_status_ml": ml_res_dict.get("stunting_status_ml"),
                "wasting_status_ml": ml_res_dict.get("wasting_status_ml"),
                "stunting_confidence": ml_res_dict.get("stunting_confidence", 0.0),
                "wasting_confidence": ml_res_dict.get("wasting_confidence", 0.0)
            },
            food_recommendations=recommendations,
            meal_plan=meal_plan,
            next_visit_date=next_visit
        )
    except HTTPException:
        raise
    except Exception as e:
        import traceback
        traceback.print_exc()
        raise HTTPException(status_code=500, detail=f"Gagal menyimpan pengukuran: {str(e)}")

@router.get("/api/guardians/me", response_model=GuardianResponse)
async def get_my_guardian_profile(db: AsyncSession = Depends(get_db_session), current_user: User = Depends(require_user)):
    query = select(Guardian).options(selectinload(Guardian.children)).where(Guardian.user_id == current_user.id)
    guardian = (await db.execute(query)).scalars().first()
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
    try:
        target_guardian_id = None
        if current_user.role == "admin" and request.guardian_id:
            target_guardian_id = request.guardian_id
        else:
            query = select(Guardian).where(Guardian.user_id == current_user.id)
            guardian = (await db.execute(query)).scalars().first()
            if not guardian:
                new_guardian = Guardian(
                    user_id=current_user.id,
                    name=current_user.username,
                    phone="-",
                    address="-"
                )
                db.add(new_guardian)
                await db.commit()
                await db.refresh(new_guardian)
                guardian = new_guardian
            target_guardian_id = guardian.id
            
        raw_gender = str(request.gender).upper().strip()
        if raw_gender in ["L", "LAKI-LAKI", "MALE", "BOY"]:
            gender_val = "M"
        elif raw_gender in ["P", "PEREMPUAN", "FEMALE", "GIRL"]:
            gender_val = "F"
        else:
            gender_val = raw_gender[:1] if raw_gender else "M"

        clean_nik = request.nik.strip() if (request.nik and request.nik.strip()) else None
            
        child_data = {
            "guardian_id": target_guardian_id,
            "name": request.name,
            "gender": gender_val,
            "date_of_birth": request.date_of_birth,
            "nik": clean_nik
        }
        
        new_child = await child_repo.create(db, child_data)
        try:
            loaded_child = await child_repo.get_by_id_with_guardian(db, new_child.id)
            if isinstance(loaded_child, Child):
                return loaded_child
        except Exception:
            pass
        return new_child
    except HTTPException:
        raise
    except Exception as e:
        import traceback
        traceback.print_exc()
        raise HTTPException(status_code=400, detail=f"Gagal menambahkan data anak: {str(e)}")

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
    
    response = []
    for m in measurements:
        if m.stunting_result:
            status = m.stunting_result.stunting_status_ml or m.stunting_result.stunting_status_who
            stunting_dict = {
                "stunting_status_who": m.stunting_result.stunting_status_who,
                "wasting_status_who": m.stunting_result.wasting_status_who,
                "stunting_status_ml": m.stunting_result.stunting_status_ml,
                "wasting_status_ml": m.stunting_result.wasting_status_ml,
                "ml_stunting_confidence": m.stunting_result.ml_stunting_confidence or 0.0,
                "ml_wasting_confidence": m.stunting_result.ml_wasting_confidence or 0.0,
                "haz_zscore": m.stunting_result.haz_zscore,
                "waz_zscore": m.stunting_result.waz_zscore,
                "whz_zscore": m.stunting_result.whz_zscore,
            }
        else:
            status = "normal"
            stunting_dict = {
                "stunting_status_who": "normal",
                "wasting_status_who": "normal",
                "stunting_status_ml": "normal",
                "wasting_status_ml": "normal",
                "ml_stunting_confidence": 0.0,
                "ml_wasting_confidence": 0.0,
                "haz_zscore": 0.0,
                "waz_zscore": 0.0,
                "whz_zscore": 0.0,
            }
        meal_plan = RecommendationService.get_meal_plan(status, m.age_in_months)
        response.append({
            "id": m.id,
            "child_id": m.child_id,
            "measured_at": m.measured_at,
            "age_in_months": m.age_in_months,
            "weight": m.weight,
            "height": m.height,
            "head_circumference": m.head_circumference,
            "measured_by": m.measured_by,
            "stunting_result": stunting_dict,
            "food_recommendations": [],
            "meal_plan": meal_plan,
            "next_visit_date": None
        })
        
    return response

@router.get("/api/admin/children", response_model=List[ChildResponse])
async def search_children(
    parent_name: str, 
    nomor_kk: str, 
    db: AsyncSession = Depends(get_db_session), 
    current_user: User = Depends(require_admin)
):
    query = select(Child).options(selectinload(Child.guardian)).join(Guardian).where(
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
        query = select(Child).options(selectinload(Child.guardian)).where(Child.nik == query_val)
    elif category.lower() == 'nama':
        query = select(Child).options(selectinload(Child.guardian)).where(Child.name.ilike(f"%{query_val}%"))
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
    try:
        loaded_child = await child_repo.get_by_id_with_guardian(db, id)
        if isinstance(loaded_child, Child):
            return loaded_child
    except Exception:
        pass
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
