from sqlalchemy import String, ForeignKey, Float
from sqlalchemy.orm import Mapped, mapped_column, relationship
from typing import Optional
from stunting_app.core.base import Base, TimestampMixin, generate_uuid

class StuntingResult(Base, TimestampMixin):
    __tablename__ = "stunting_results"

    id: Mapped[str] = mapped_column(String(36), primary_key=True, default=generate_uuid)
    measurement_id: Mapped[str] = mapped_column(String(36), ForeignKey("measurements.id", ondelete="CASCADE"), unique=True, nullable=False)
    haz_zscore: Mapped[float] = mapped_column(Float, nullable=False)
    waz_zscore: Mapped[float] = mapped_column(Float, nullable=False)
    whz_zscore: Mapped[float] = mapped_column(Float, nullable=False)
    stunting_status_who: Mapped[str] = mapped_column(String(30), nullable=False)
    wasting_status_who: Mapped[str] = mapped_column(String(30), nullable=False)
    underweight_status_who: Mapped[str] = mapped_column(String(30), nullable=False)
    stunting_status_ml: Mapped[Optional[str]] = mapped_column(String(30), nullable=True)
    wasting_status_ml: Mapped[Optional[str]] = mapped_column(String(30), nullable=True)
    ml_stunting_confidence: Mapped[Optional[float]] = mapped_column(Float, nullable=True)
    ml_wasting_confidence: Mapped[Optional[float]] = mapped_column(Float, nullable=True)
    notes: Mapped[Optional[str]] = mapped_column(String(500), nullable=True)

    measurement = relationship("Measurement", back_populates="stunting_result")
