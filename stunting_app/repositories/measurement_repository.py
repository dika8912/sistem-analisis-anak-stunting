from sqlalchemy.ext.asyncio import AsyncSession
from stunting_app.models.measurement import Measurement
from stunting_app.models.stunting_result import StuntingResult
from stunting_app.repositories.base_repository import BaseRepository

class MeasurementRepository(BaseRepository[Measurement]):
    def __init__(self):
        super().__init__(Measurement)
        
    async def create_with_result(
        self, db: AsyncSession, measurement_data: dict, result_data: dict
    ) -> Measurement:
        db_measurement = self.model(**measurement_data)
        db.add(db_measurement)
        await db.flush()  # To get measurement id before commit

        result_data["measurement_id"] = db_measurement.id
        db_result = StuntingResult(**result_data)
        db.add(db_result)

        await db.commit()
        await db.refresh(db_measurement)
        return db_measurement
