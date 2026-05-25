from sqlalchemy.orm import Mapped, mapped_column, relationship
from sqlalchemy import String, Enum
from stunting_app.core.base import Base, TimestampMixin, generate_uuid
import enum

class RoleEnum(str, enum.Enum):
    admin = "admin"
    user = "user"

class User(Base, TimestampMixin):
    __tablename__ = "users"

    id: Mapped[str] = mapped_column(String(36), primary_key=True, default=generate_uuid)
    username: Mapped[str] = mapped_column(String(100), unique=True, index=True, nullable=False)
    email: Mapped[str | None] = mapped_column(String(100), unique=True, index=True, nullable=True)
    hashed_password: Mapped[str] = mapped_column(String(255), nullable=False)
    role: Mapped[RoleEnum] = mapped_column(Enum(RoleEnum), default=RoleEnum.user, nullable=False)

    guardian = relationship("Guardian", back_populates="user", uselist=False, cascade="all, delete-orphan")
