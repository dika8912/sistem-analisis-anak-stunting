from sqlalchemy import String, Integer
from sqlalchemy.orm import Mapped, mapped_column
from stunting_app.core.base import Base, TimestampMixin, generate_uuid

class FoodRecommendation(Base, TimestampMixin):
    __tablename__ = "food_recommendations"

    id: Mapped[str] = mapped_column(String(36), primary_key=True, default=generate_uuid)
    stunting_status: Mapped[str] = mapped_column(String(30), nullable=False)
    min_age_months: Mapped[int] = mapped_column(Integer, nullable=False)
    max_age_months: Mapped[int] = mapped_column(Integer, nullable=False)
    recommendation_text: Mapped[str] = mapped_column(String(1000), nullable=False)
