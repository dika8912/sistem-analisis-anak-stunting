import sys
import os
import asyncio
import csv
from sqlalchemy import select
from sqlalchemy.ext.asyncio import create_async_engine, async_sessionmaker, AsyncSession

# Ensure we can import the app
sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), '../../')))

from stunting_app.config.settings import settings
from stunting_app.models.who_standard import WHOStandard

DATABASE_URL = (
    f"mysql+aiomysql://{settings.DB_USER}:{settings.DB_PASSWORD}"
    f"@{settings.DB_HOST}:{settings.DB_PORT}/{settings.DB_NAME}"
)

engine = create_async_engine(DATABASE_URL, echo=True)
async_session_factory = async_sessionmaker(bind=engine, expire_on_commit=False)

async def seed_who_standards(csv_path: str):
    async with async_session_factory() as session:
        # Check if already seeded
        result = await session.execute(select(WHOStandard).limit(1))
        if result.scalar_one_or_none():
            print("WHO Standards table is already seeded.")
            return

        standards = []
        with open(csv_path, mode='r') as file:
            reader = csv.DictReader(file)
            for row in reader:
                standards.append(
                    WHOStandard(
                        gender=row['gender'],
                        age_in_months=int(row['age_in_months']),
                        measure_type=row['measure_type'],
                        l=float(row['l']),
                        m=float(row['m']),
                        s=float(row['s'])
                    )
                )
        
        session.add_all(standards)
        await session.commit()
        print(f"Successfully seeded {len(standards)} WHO standard records.")

if __name__ == "__main__":
    csv_file = os.path.join(os.path.dirname(__file__), "who_standards.csv")
    asyncio.run(seed_who_standards(csv_file))
