from sqlalchemy import String, ForeignKey, Date
from sqlalchemy.orm import Mapped, mapped_column, relationship
from datetime import date
from typing import List
from stunting_app.core.base import Base, TimestampMixin, generate_uuid

class Child(Base, TimestampMixin):
    __tablename__ = "children"

    id: Mapped[str] = mapped_column(String(36), primary_key=True, default=generate_uuid)
    guardian_id: Mapped[str] = mapped_column(String(36), ForeignKey("guardians.id", ondelete="CASCADE"), nullable=False)
    name: Mapped[str] = mapped_column(String(100), nullable=False)
    gender: Mapped[str] = mapped_column(String(1), nullable=False)  # 'M' or 'F'
    date_of_birth: Mapped[date] = mapped_column(Date, nullable=False)
    nik: Mapped[str | None] = mapped_column(String(16), nullable=True, unique=True)

    guardian = relationship("Guardian", back_populates="children")
    measurements = relationship("Measurement", back_populates="child", cascade="all, delete-orphan")
