import pytest
import pytest_asyncio
from httpx import AsyncClient, ASGITransport
from unittest.mock import AsyncMock, MagicMock, patch

from stunting_app.main import app
from stunting_app.models.user import RoleEnum

admin_token = "fake-admin-token"
user_token = "fake-user-token"

class TestEducationFeatures:
    @pytest.mark.anyio
    async def test_get_all_educations(self):
        """Public/User can get all educations"""
        async def fake_get_db():
            db = AsyncMock()
            yield db

        mock_edu = MagicMock()
        mock_edu.id = "edu-1"
        mock_edu.title = "Pentingnya Gizi"
        mock_edu.content = "Gizi sangat penting untuk anak..."
        mock_edu.image_url = "http://example.com/img.jpg"

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        with patch("stunting_app.api.endpoints.education_repo.get_all", new_callable=AsyncMock, return_value=[mock_edu]):
            async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
                resp = await ac.get("/api/educations")
                
        app.dependency_overrides.clear()
        assert resp.status_code == 200
        data = resp.json()
        assert len(data) == 1
        assert data[0]["title"] == "Pentingnya Gizi"

    @pytest.mark.anyio
    async def test_admin_create_education(self):
        """Admin can create new education content"""
        mock_admin = MagicMock()
        mock_admin.id = "admin-id"
        mock_admin.role = RoleEnum.admin

        from stunting_app.api.deps import require_admin
        app.dependency_overrides[require_admin] = lambda: mock_admin

        async def fake_get_db():
            db = AsyncMock()
            yield db

        mock_edu = MagicMock()
        mock_edu.id = "edu-new"
        mock_edu.title = "Tips Menyusui"
        mock_edu.content = "Menyusui eksklusif 6 bulan..."
        mock_edu.image_url = None

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        with patch("stunting_app.api.endpoints.education_repo.create", new_callable=AsyncMock, return_value=mock_edu):
            async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
                resp = await ac.post("/api/admin/educations", json={
                    "title": "Tips Menyusui",
                    "content": "Menyusui eksklusif 6 bulan..."
                }, headers={"Authorization": f"Bearer {admin_token}"})
                
        app.dependency_overrides.clear()
        assert resp.status_code == 201
        assert resp.json()["title"] == "Tips Menyusui"

    @pytest.mark.anyio
    async def test_user_cannot_create_education(self):
        """User cannot create education (403)"""
        mock_user = MagicMock()
        mock_user.id = "user-id"
        mock_user.role = RoleEnum.user

        from stunting_app.api.deps import require_admin
        # We simulate require_admin failing because user is not admin
        from fastapi import HTTPException
        def fake_require_admin():
            raise HTTPException(status_code=403, detail="Not enough permissions")
            
        app.dependency_overrides[require_admin] = fake_require_admin

        async def fake_get_db():
            db = AsyncMock()
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/admin/educations", json={
                "title": "Should fail",
                "content": "Not allowed"
            }, headers={"Authorization": f"Bearer {user_token}"})
                
        app.dependency_overrides.clear()
        assert resp.status_code == 403
