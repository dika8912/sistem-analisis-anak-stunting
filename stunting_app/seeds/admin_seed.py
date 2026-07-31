import asyncio
import sys
import os

# Add workspace to path (2 levels up from stunting_app/seeds/admin_seed.py)
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.dirname(__file__))))

from stunting_app.core.database import async_session_factory
from stunting_app.models.user import User, RoleEnum
from stunting_app.core.security import get_password_hash
from sqlalchemy import select

async def seed_admin():
    async with async_session_factory() as db:
        # Check if admin already exists
        query = select(User).where(User.username == "admin")
        result = await db.execute(query)
        if admin:
            admin.role = RoleEnum.admin
            admin.hashed_password = get_password_hash("admin123")
            await db.commit()
            print("Admin user already exists - ensured role is admin and password is admin123.")
            return

        new_admin = User(
            username="admin",
            email="admin@stuntingapp.local",
            hashed_password=get_password_hash("admin123"),
            role=RoleEnum.admin
        )
        db.add(new_admin)
        await db.commit()
        print("Admin user seeded successfully with username 'admin' and password 'admin123'")

if __name__ == "__main__":
    if sys.platform == 'win32':
        asyncio.set_event_loop_policy(asyncio.WindowsSelectorEventLoopPolicy())
    asyncio.run(seed_admin())
