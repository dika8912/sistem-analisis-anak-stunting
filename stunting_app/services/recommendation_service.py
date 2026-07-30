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

    @staticmethod
    def get_meal_plan(stunting_status: str, age_months: int) -> dict:
        if age_months < 6:
            return {
                "pagi": "ASI Eksklusif (susui sesering mungkin)",
                "siang": "ASI Eksklusif",
                "malam": "ASI Eksklusif"
            }
        
        if 6 <= age_months < 12:
            if stunting_status in ["zona_bahaya", "zona_sedang"]:
                return {
                    "pagi": "Bubur saring dengan telur puyuh rebus dan kaldu ayam",
                    "siang": "Nasi tim lumat dengan hati ayam dan wortel cincang",
                    "malam": "Bubur lumat dengan ikan lele kukus dan tahu"
                }
            else:
                return {
                    "pagi": "Bubur susu atau pure pisang",
                    "siang": "Nasi tim saring dengan tahu dan sayur bayam",
                    "malam": "Bubur lumat dengan fillet ikan"
                }
        else:
            if stunting_status in ["zona_bahaya", "zona_sedang"]:
                return {
                    "pagi": "Nasi dengan 2 butir telur dadar/rebus dan tempe",
                    "siang": "Nasi padat dengan lele goreng garing dan sayur sop kaldu tulang",
                    "malam": "Nasi hangat dengan semur hati ayam dan tahu"
                }
            else:
                return {
                    "pagi": "Nasi dengan telur rebus dan sayur bening",
                    "siang": "Nasi dengan ikan laut/tawar goreng dan tumis kangkung",
                    "malam": "Nasi dengan perkedel tempe dan tumis buncis"
                }
