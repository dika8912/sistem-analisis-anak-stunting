from pydantic import BaseModel, Field, EmailStr
from typing import List, Optional
from datetime import date
from enum import Enum

class RoleEnum(str, Enum):
    admin = "admin"
    user = "user"

class Token(BaseModel):
    access_token: str
    token_type: str

class RegisterRequest(BaseModel):
    username: str = Field(..., min_length=3)
    email: Optional[EmailStr] = None
    password: str = Field(..., min_length=6)
    name: str = Field(..., description="Nama Guardian")
    nomor_kk: str = Field(..., description="Nomor Kartu Keluarga")
    phone: str
    address: str

class UserResponse(BaseModel):
    id: str
    username: str
    email: Optional[str]
    role: RoleEnum

class GuardianResponse(BaseModel):
    id: str
    user_id: str
    name: str
    nomor_kk: str
    phone: str
    email: Optional[str]
    address: str

class ChildResponse(BaseModel):
    id: str
    guardian_id: str
    name: str
    gender: str
    date_of_birth: date

class ChildCreateRequest(BaseModel):
    name: str = Field(..., min_length=2)
    gender: str = Field(..., description="'M' for Male, 'F' for Female", pattern="^[MF]$")
    date_of_birth: date

class DetectionRequest(BaseModel):
    gender: str = Field(..., description="'M' for Male, 'F' for Female", pattern="^[MF]$")
    age_in_months: int = Field(..., ge=0, le=60)
    height_cm: float = Field(..., gt=0)
    weight_kg: float = Field(..., gt=0)

class WHOCalculationResponse(BaseModel):
    haz_zscore: float
    waz_zscore: float
    whz_zscore: float
    stunting_status_who: str
    wasting_status_who: str
    underweight_status_who: str

class MLPredictionResponse(BaseModel):
    stunting_status_ml: str
    stunting_confidence: float
    wasting_status_ml: str
    wasting_confidence: float

class DetectionResponse(BaseModel):
    input: DetectionRequest
    who_calculation: WHOCalculationResponse
    ml_prediction: MLPredictionResponse
    recommendations: List[str]

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
