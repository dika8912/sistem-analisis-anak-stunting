from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select
from typing import List
from stunting_app.models.food_recommendation import FoodRecommendation

class RecommendationService:
    @staticmethod
    async def get_recommendations(
        db: AsyncSession, stunting_status: str, age_months: int
    ) -> List[str]:
        """
        Fetch food recommendations matching the stunting status and age range.
        If no specific recommendation for status, fallback to general advice.
        """
        query = select(FoodRecommendation.recommendation_text).where(
            FoodRecommendation.stunting_status == stunting_status,
            FoodRecommendation.min_age_months <= age_months,
            FoodRecommendation.max_age_months >= age_months
        )
        result = await db.execute(query)
        recommendations = result.scalars().all()
        
        if not recommendations:
            return ["Lanjutkan pemantauan tumbuh kembang bulanan di Posyandu."]
            
        return list(recommendations)
