from sqlalchemy import String, ForeignKey, Date, Float, Integer
from sqlalchemy.orm import Mapped, mapped_column, relationship
from datetime import date
from typing import Optional
from stunting_app.core.base import Base, TimestampMixin, generate_uuid

class Measurement(Base, TimestampMixin):
    __tablename__ = "measurements"

    id: Mapped[str] = mapped_column(String(36), primary_key=True, default=generate_uuid)
    child_id: Mapped[str] = mapped_column(String(36), ForeignKey("children.id", ondelete="CASCADE"), nullable=False)
    measured_at: Mapped[date] = mapped_column(Date, nullable=False)
    age_in_months: Mapped[int] = mapped_column(Integer, nullable=False)
    weight: Mapped[float] = mapped_column(Float, nullable=False) # in kg
    height: Mapped[float] = mapped_column(Float, nullable=False) # in cm
    head_circumference: Mapped[Optional[float]] = mapped_column(Float, nullable=True) # in cm
    measured_by: Mapped[Optional[str]] = mapped_column(String(50), nullable=True)

    child = relationship("Child", back_populates="measurements")
    stunting_result = relationship("StuntingResult", back_populates="measurement", uselist=False, cascade="all, delete-orphan")
