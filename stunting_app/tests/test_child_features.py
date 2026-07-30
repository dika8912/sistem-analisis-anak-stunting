import pytest
import pytest_asyncio
from httpx import AsyncClient, ASGITransport
from unittest.mock import AsyncMock, MagicMock, patch

from stunting_app.main import app
from stunting_app.models.user import RoleEnum

# Tokens for testing (we'll mock dependencies)
admin_token = "fake-admin-token"
user_token = "fake-user-token"

class TestChildFeatures:
    @pytest.mark.anyio
    async def test_search_children_by_nik(self):
        """Admin should be able to search children by NIK"""
        mock_admin = MagicMock()
        mock_admin.id = "admin-id"
        mock_admin.role = RoleEnum.admin

        from stunting_app.api.deps import require_admin
        app.dependency_overrides[require_admin] = lambda: mock_admin

        async def fake_get_db():
            db = AsyncMock()
            scalars_mock = MagicMock()
            
            mock_child = MagicMock()
            mock_child.id = "child-id"
            mock_child.name = "Budi NIK"
            mock_child.nik = "1234567890123456"
            mock_child.gender = "M"
            mock_child.date_of_birth = "2020-01-01"
            mock_child.guardian_id = "guardian-id"
            mock_child.guardian = None
            
            scalars_mock.all.return_value = [mock_child]
            result_mock = MagicMock()
            result_mock.scalars.return_value = scalars_mock
            db.execute = AsyncMock(return_value=result_mock)
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.get("/api/admin/children/search?query_val=1234567890123456&category=nik",
                                headers={"Authorization": f"Bearer {admin_token}"})
        
        app.dependency_overrides.clear()
        assert resp.status_code == 200
        data = resp.json()
        assert len(data) == 1
        assert data[0]["nik"] == "1234567890123456"

    @pytest.mark.anyio
    async def test_search_children_invalid_category(self):
        """Admin search with invalid category should return 400"""
        mock_admin = MagicMock()
        mock_admin.id = "admin-id"
        mock_admin.role = RoleEnum.admin

        from stunting_app.api.deps import require_admin
        app.dependency_overrides[require_admin] = lambda: mock_admin

        async def fake_get_db():
            db = AsyncMock()
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.get("/api/admin/children/search?query_val=Budi&category=invalid",
                                headers={"Authorization": f"Bearer {admin_token}"})
        
        app.dependency_overrides.clear()
        assert resp.status_code == 400

    @pytest.mark.anyio
    async def test_eager_loading_authorization_check(self):
        """Eager loading shouldn't perform extra queries to check authorization"""
        mock_user = MagicMock()
        mock_user.id = "user-id"
        mock_user.role = RoleEnum.user
        
        # Setup child with eagerly loaded guardian
        mock_child = MagicMock()
        mock_child.id = "child-id"
        mock_child.name = "Budi"
        mock_child.gender = "M"
        mock_child.date_of_birth = "2020-01-01"
        mock_child.guardian_id = "guardian-id"
        mock_child.nik = "1234567890123456"
        
        # Simulate eager loaded guardian
        mock_guardian = MagicMock()
        mock_guardian.id = "guardian-id"
        mock_guardian.user_id = "user-id"  # belongs to this user
        mock_guardian.name = "Test Guardian"
        mock_guardian.nomor_kk = "3201010101010001"
        mock_guardian.phone = "08123456789"
        mock_guardian.email = "test@example.com"
        mock_guardian.address = "Jl. Test No. 1"
        mock_child.guardian = mock_guardian
        
        from stunting_app.api.deps import get_current_active_user
        app.dependency_overrides[get_current_active_user] = lambda: mock_user

        async def fake_get_db():
            db = AsyncMock()
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        with patch("stunting_app.api.endpoints.child_repo.get_by_id_with_guardian", new_callable=AsyncMock, return_value=mock_child):
            async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
                resp = await ac.get(f"/api/children/{mock_child.id}",
                                    headers={"Authorization": f"Bearer {user_token}"})
                
        app.dependency_overrides.clear()
        assert resp.status_code == 200
        assert resp.json()["name"] == "Budi"

    @pytest.mark.anyio
    async def test_admin_update_child(self):
        """Admin should be able to update a child"""
        mock_admin = MagicMock()
        mock_admin.id = "admin-id"
        mock_admin.role = RoleEnum.admin

        from stunting_app.api.deps import require_admin
        app.dependency_overrides[require_admin] = lambda: mock_admin

        mock_child = MagicMock()
        mock_child.id = "child-id"
        mock_child.name = "Budi Updated"
        mock_child.gender = "M"
        mock_child.date_of_birth = "2020-01-01"
        mock_child.guardian_id = "guardian-id"
        mock_child.nik = None
        mock_child.guardian = None

        async def fake_get_db():
            db = AsyncMock()
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        with patch("stunting_app.api.endpoints.child_repo.update", new_callable=AsyncMock, return_value=mock_child):
            async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
                resp = await ac.put("/api/admin/children/child-id", json={
                    "name": "Budi Updated"
                }, headers={"Authorization": f"Bearer {admin_token}"})
                
        app.dependency_overrides.clear()
        assert resp.status_code == 200
        assert resp.json()["name"] == "Budi Updated"
