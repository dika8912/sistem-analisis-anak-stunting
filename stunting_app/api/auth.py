from fastapi import APIRouter, Depends, HTTPException, status, Form
from fastapi.security import OAuth2PasswordRequestForm
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select, or_
from stunting_app.core.database import get_db_session
from stunting_app.core.security import verify_password, get_password_hash, create_access_token
from stunting_app.models.user import User, RoleEnum
from stunting_app.models.guardian import Guardian
from stunting_app.schemas.schemas import RegisterRequest, Token, UserResponse, ForgotPasswordRequest, ResetPasswordRequest
from stunting_app.config.settings import settings
import jwt
from datetime import timedelta

router = APIRouter()

@router.post("/register", response_model=UserResponse)
async def register(request: RegisterRequest, db: AsyncSession = Depends(get_db_session)):
    # Check if username exists
    query = select(User).where((User.username == request.username))
    result = await db.execute(query)
    if result.scalar_one_or_none():
        raise HTTPException(status_code=400, detail="Username sudah terdaftar. Silakan gunakan username lain.")
        
    # Check if email exists
    email_val = request.email.strip() if request.email and request.email.strip() else None
    if email_val:
        query_email = select(User).where((User.email == email_val))
        result_email = await db.execute(query_email)
        if result_email.scalar_one_or_none():
            raise HTTPException(status_code=400, detail="Email sudah terdaftar. Silakan gunakan email lain atau langsung Login.")
        
    # Create user (force role to user)
    new_user = User(
        username=request.username,
        email=email_val,
        hashed_password=get_password_hash(request.password),
        role=RoleEnum.user
    )
    db.add(new_user)
    await db.flush() # get new_user.id
    
    # Create Guardian profile
    new_guardian = Guardian(
        user_id=new_user.id,
        name=request.name,
        nomor_kk=request.nomor_kk,
        phone=request.phone,
        email=email_val,
        address=request.address
    )
    db.add(new_guardian)
    await db.commit()
    await db.refresh(new_user)
    
    return UserResponse(
        id=new_user.id,
        username=new_user.username,
        email=new_user.email,
        role=new_user.role
    )

@router.post("/login", response_model=Token)
async def login(
    form_data: OAuth2PasswordRequestForm = Depends(),
    remember_me: bool = Form(False),
    db: AsyncSession = Depends(get_db_session)
):
    query = select(User).where(
        or_(
            User.username == form_data.username,
            User.email == form_data.username
        )
    )
    result = await db.execute(query)
    user = result.scalar_one_or_none()
    
    if not user or not verify_password(form_data.password, user.hashed_password):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Incorrect username or password",
            headers={"WWW-Authenticate": "Bearer"},
        )
        
    if remember_me:
        expires_delta = timedelta(days=30)
    else:
        expires_delta = timedelta(minutes=settings.ACCESS_TOKEN_EXPIRE_MINUTES)

    access_token = create_access_token(
        data={"sub": user.id, "role": user.role.value},
        expires_delta=expires_delta
    )
    return {"access_token": access_token, "token_type": "bearer"}

@router.post("/forgot-password")
async def forgot_password(request: ForgotPasswordRequest, db: AsyncSession = Depends(get_db_session)):
    query = select(User).where(User.email == request.email)
    result = await db.execute(query)
    user = result.scalar_one_or_none()
    
    if not user:
        return {"message": "If your email is registered, you will receive a password reset link."}
        
    expires_delta = timedelta(minutes=15)
    reset_token = create_access_token(
        data={"sub": user.id, "type": "reset_password"},
        expires_delta=expires_delta
    )
    
    # In a real application, send this token via email.
    # We return it here for testing and demonstration purposes.
    return {
        "message": "If your email is registered, you will receive a password reset link.",
        "reset_token": reset_token
    }

@router.post("/reset-password")
async def reset_password(request: ResetPasswordRequest, db: AsyncSession = Depends(get_db_session)):
    try:
        payload = jwt.decode(request.token, settings.JWT_SECRET, algorithms=[settings.JWT_ALGORITHM])
        user_id: str = payload.get("sub")
        token_type: str = payload.get("type")
        if token_type != "reset_password" or not user_id:
            raise HTTPException(status_code=400, detail="Invalid token")
    except jwt.ExpiredSignatureError:
        raise HTTPException(status_code=400, detail="Token has expired")
    except jwt.PyJWTError:
        raise HTTPException(status_code=400, detail="Invalid token")
        
    query = select(User).where(User.id == user_id)
    result = await db.execute(query)
    user = result.scalar_one_or_none()
    
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
        
    user.hashed_password = get_password_hash(request.new_password)
    db.add(user)
    await db.commit()
    
    return {"message": "Password successfully reset"}
