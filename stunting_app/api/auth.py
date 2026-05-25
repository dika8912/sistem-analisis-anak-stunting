from fastapi import APIRouter, Depends, HTTPException, status
from fastapi.security import OAuth2PasswordRequestForm
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select
from stunting_app.core.database import get_db_session
from stunting_app.core.security import verify_password, get_password_hash, create_access_token
from stunting_app.models.user import User, RoleEnum
from stunting_app.models.guardian import Guardian
from stunting_app.schemas.schemas import RegisterRequest, Token, UserResponse

router = APIRouter()

@router.post("/register", response_model=UserResponse)
async def register(request: RegisterRequest, db: AsyncSession = Depends(get_db_session)):
    # Check if username exists
    query = select(User).where((User.username == request.username))
    result = await db.execute(query)
    if result.scalar_one_or_none():
        raise HTTPException(status_code=400, detail="Username already registered")
        
    # Create user (force role to user)
    new_user = User(
        username=request.username,
        email=request.email,
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
        email=request.email,
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
async def login(form_data: OAuth2PasswordRequestForm = Depends(), db: AsyncSession = Depends(get_db_session)):
    query = select(User).where(User.username == form_data.username)
    result = await db.execute(query)
    user = result.scalar_one_or_none()
    
    if not user or not verify_password(form_data.password, user.hashed_password):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Incorrect username or password",
            headers={"WWW-Authenticate": "Bearer"},
        )
        
    access_token = create_access_token(data={"sub": user.id, "role": user.role.value})
    return {"access_token": access_token, "token_type": "bearer"}
