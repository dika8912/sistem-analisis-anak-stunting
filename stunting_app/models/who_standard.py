from sqlalchemy import String, Integer, Float
from sqlalchemy.orm import Mapped, mapped_column
from stunting_app.core.base import Base

class WHOStandard(Base):
    __tablename__ = "who_standards"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    gender: Mapped[str] = mapped_column(String(1), nullable=False) # 'M' or 'F'
    age_in_months: Mapped[int] = mapped_column(Integer, nullable=False)
    measure_type: Mapped[str] = mapped_column(String(10), nullable=False) # 'lhfa', 'wfa', 'wflh'
    l: Mapped[float] = mapped_column(Float, nullable=False)
    m: Mapped[float] = mapped_column(Float, nullable=False)
    s: Mapped[float] = mapped_column(Float, nullable=False)
