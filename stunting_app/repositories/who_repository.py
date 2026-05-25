from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select
from typing import Optional
from stunting_app.models.who_standard import WHOStandard
from stunting_app.repositories.base_repository import BaseRepository

class WHORepository(BaseRepository[WHOStandard]):
    def __init__(self):
        super().__init__(WHOStandard)

    async def get_standard(
        self, db: AsyncSession, gender: str, age_in_months: int, measure_type: str
    ) -> Optional[WHOStandard]:
        query = select(self.model).where(
            self.model.gender == gender.upper(),
            self.model.age_in_months == age_in_months,
            self.model.measure_type == measure_type
        )
        result = await db.execute(query)
        return result.scalar_one_or_none()
