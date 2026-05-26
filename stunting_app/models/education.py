from sqlalchemy import String, Text
from sqlalchemy.orm import Mapped, mapped_column
from stunting_app.core.base import Base, TimestampMixin, generate_uuid

class Education(Base, TimestampMixin):
    __tablename__ = "educations"

    id: Mapped[str] = mapped_column(String(36), primary_key=True, default=generate_uuid)
    title: Mapped[str] = mapped_column(String(200), nullable=False)
    content: Mapped[str] = mapped_column(Text, nullable=False)
    image_url: Mapped[str | None] = mapped_column(String(500), nullable=True)
