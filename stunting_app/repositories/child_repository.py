from stunting_app.models.child import Child
from stunting_app.repositories.base_repository import BaseRepository
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select
from sqlalchemy.orm import selectinload
from typing import Optional

class ChildRepository(BaseRepository[Child]):
    def __init__(self):
        super().__init__(Child)
        
    async def get_by_id_with_guardian(self, db: AsyncSession, id: str) -> Optional[Child]:
        query = select(Child).options(selectinload(Child.guardian)).where(Child.id == id)
        result = await db.execute(query)
        return result.scalar_one_or_none()

