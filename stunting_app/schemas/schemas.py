from pydantic import BaseModel, Field, EmailStr, field_validator
from typing import List, Optional
from datetime import date
from enum import Enum

class ForgotPasswordRequest(BaseModel):
    email: EmailStr

class ResetPasswordRequest(BaseModel):
    token: str
    new_password: str = Field(..., min_length=6)

class RoleEnum(str, Enum):
    admin = "admin"
    user = "user"

class Token(BaseModel):
    access_token: str
    token_type: str

class RegisterRequest(BaseModel):
    username: str = Field(..., min_length=3)
    email: Optional[str] = None
    password: str = Field(..., min_length=6)
    name: str = Field(..., description="Nama Guardian")
    nomor_kk: str = Field(..., description="Nomor Kartu Keluarga")
    phone: str
    address: str

    @field_validator("email")
    @classmethod
    def validate_email(cls, v: Optional[str]) -> Optional[str]:
        if v and v.strip():
            if "@" not in v or "." not in v:
                raise ValueError("Format email tidak valid")
        return v

class UserResponse(BaseModel):
    id: str
    username: str
    email: Optional[str]
    role: RoleEnum

class GuardianSimpleResponse(BaseModel):
    id: str
    user_id: str
    name: str
    nomor_kk: str
    phone: str
    email: Optional[str] = None
    address: str

class ChildResponse(BaseModel):
    id: str
    guardian_id: str
    name: str
    gender: str
    date_of_birth: date
    nik: Optional[str] = None
    guardian: Optional[GuardianSimpleResponse] = None

class GuardianResponse(BaseModel):
    id: str
    user_id: str
    name: str
    nomor_kk: str
    phone: str
    email: Optional[str]
    address: str
    children: Optional[List[ChildResponse]] = None

class ChildCreateRequest(BaseModel):
    name: str = Field(..., min_length=2)
    gender: str = Field(..., description="'M' for Male, 'F' for Female", pattern="^[MF]$")
    date_of_birth: date
    nik: Optional[str] = Field(None, min_length=16, max_length=16, description="16 digit NIK")
    guardian_id: Optional[str] = None

class ChildUpdateRequest(BaseModel):
    name: Optional[str] = Field(None, min_length=2)
    gender: Optional[str] = Field(None, description="'M' for Male, 'F' for Female", pattern="^[MF]$")
    date_of_birth: Optional[date] = None
    nik: Optional[str] = Field(None, min_length=16, max_length=16, description="16 digit NIK")

class EducationResponse(BaseModel):
    id: str
    title: str
    content: str
    image_url: Optional[str] = None

class EducationCreateRequest(BaseModel):
    title: str = Field(..., min_length=3)
    content: str = Field(..., min_length=10)
    image_url: Optional[str] = None

class EducationUpdateRequest(BaseModel):
    title: Optional[str] = Field(None, min_length=3)
    content: Optional[str] = Field(None, min_length=10)
    image_url: Optional[str] = None


class CalculateRequest(BaseModel):
    gender: str = Field(..., description="'M' for Male, 'F' for Female", pattern="^[MF]$")
    age_in_months: int = Field(..., ge=0, le=60)
    height_cm: float = Field(..., gt=0)
    weight_kg: float = Field(..., gt=0)

class PredictRequest(BaseModel):
    gender: str = Field(..., description="'M' for Male, 'F' for Female", pattern="^[MF]$")
    age_in_months: int = Field(..., ge=0, le=60)
    height_cm: float = Field(..., gt=0)
    weight_kg: float = Field(..., gt=0)

class MLPredictionResponse(BaseModel):
    stunting_status_ml: str
    stunting_confidence: float
    wasting_status_ml: str
    wasting_confidence: float

class MealPlan(BaseModel):
    pagi: str
    siang: str
    malam: str

class CalculateResponse(BaseModel):
    input: CalculateRequest
    ml_prediction: MLPredictionResponse
    recommendations: List[str]
    meal_plan: Optional[MealPlan] = None
    next_visit_date: Optional[date] = None

class PredictResponse(BaseModel):
    input: PredictRequest
    ml_prediction: MLPredictionResponse

class MeasurementCreateRequest(BaseModel):
    child_id: str
    measured_at: date
    weight: float
    height: float
    head_circumference: Optional[float] = None
    measured_by: Optional[str] = None

class MeasurementResponse(BaseModel):
    id: str
    child_id: str
    measured_at: date
    age_in_months: int
    weight: float
    height: float
    head_circumference: Optional[float]
    measured_by: Optional[str]
    stunting_result: dict
    food_recommendations: List[str]
    meal_plan: Optional[MealPlan] = None
    next_visit_date: Optional[date] = None

class AdminStatsResponse(BaseModel):
    total_children: int
    total_measurements: int
    needs_attention: int
