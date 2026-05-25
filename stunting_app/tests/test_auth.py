import pytest
from httpx import AsyncClient, ASGITransport
from unittest.mock import MagicMock, AsyncMock
from datetime import datetime, timedelta, timezone
import jwt

from stunting_app.main import app
from stunting_app.models.user import RoleEnum
from stunting_app.core.security import get_password_hash, create_access_token
from stunting_app.config.settings import settings

USER_ID = "aaaaaaaa-0000-0000-0000-000000000001"

class TestNewAuthEndpoints:
    @pytest.mark.anyio
    async def test_login_remember_me(self):
        """Login dengan remember_me=True harus mengembalikan token berdurasi 30 hari."""
        hashed = get_password_hash("password123")
        mock_user = MagicMock()
        mock_user.id = USER_ID
        mock_user.username = "buditest"
        mock_user.hashed_password = hashed
        mock_user.role = RoleEnum.user

        async def fake_get_db():
            db = AsyncMock()
            result_mock = MagicMock()
            result_mock.scalar_one_or_none.return_value = mock_user
            db.execute = AsyncMock(return_value=result_mock)
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/login", data={
                "username": "buditest",
                "password": "password123",
                "remember_me": "true"
            })
        app.dependency_overrides.clear()
        
        assert resp.status_code == 200
        data = resp.json()
        assert "access_token" in data
        
        # Decode and check expiry
        token = data["access_token"]
        payload = jwt.decode(token, settings.JWT_SECRET, algorithms=[settings.JWT_ALGORITHM])
        
        expected_exp = datetime.now(timezone.utc) + timedelta(days=30)
        # Verify it's close to 30 days
        assert abs(payload["exp"] - expected_exp.timestamp()) < 10

    @pytest.mark.anyio
    async def test_forgot_password_success(self):
        """Forgot password dengan email yang valid harus mengembalikan token reset."""
        mock_user = MagicMock()
        mock_user.id = USER_ID
        mock_user.email = "budi@test.com"

        async def fake_get_db():
            db = AsyncMock()
            result_mock = MagicMock()
            result_mock.scalar_one_or_none.return_value = mock_user
            db.execute = AsyncMock(return_value=result_mock)
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/forgot-password", json={
                "email": "budi@test.com"
            })
        app.dependency_overrides.clear()
        
        assert resp.status_code == 200
        data = resp.json()
        assert "reset_token" in data
        assert "If your email is registered" in data["message"]
        
        # Verify token type
        payload = jwt.decode(data["reset_token"], settings.JWT_SECRET, algorithms=[settings.JWT_ALGORITHM])
        assert payload["type"] == "reset_password"

    @pytest.mark.anyio
    async def test_forgot_password_invalid_email(self):
        """Forgot password dengan email yang tidak terdaftar tetap mengembalikan pesan sukses tanpa token (keamanan)."""
        async def fake_get_db():
            db = AsyncMock()
            result_mock = MagicMock()
            result_mock.scalar_one_or_none.return_value = None
            db.execute = AsyncMock(return_value=result_mock)
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/forgot-password", json={
                "email": "tidakada@test.com"
            })
        app.dependency_overrides.clear()
        
        assert resp.status_code == 200
        data = resp.json()
        assert "reset_token" not in data
        assert "If your email is registered" in data["message"]

    @pytest.mark.anyio
    async def test_reset_password_success(self):
        """Reset password dengan token valid mengubah password."""
        mock_user = MagicMock()
        mock_user.id = USER_ID

        async def fake_get_db():
            db = AsyncMock()
            result_mock = MagicMock()
            result_mock.scalar_one_or_none.return_value = mock_user
            db.execute = AsyncMock(return_value=result_mock)
            db.add = MagicMock()
            db.commit = AsyncMock()
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db
        
        token = create_access_token(data={"sub": USER_ID, "type": "reset_password"}, expires_delta=timedelta(minutes=15))

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/reset-password", json={
                "token": token,
                "new_password": "newpassword123"
            })
        app.dependency_overrides.clear()
        
        assert resp.status_code == 200
        assert resp.json()["message"] == "Password successfully reset"

    @pytest.mark.anyio
    async def test_reset_password_invalid_token(self):
        """Reset password dengan token invalid atau tipe salah gagal."""
        # Type is not 'reset_password'
        token = create_access_token(data={"sub": USER_ID, "type": "access_token"}, expires_delta=timedelta(minutes=15))

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/reset-password", json={
                "token": token,
                "new_password": "newpassword123"
            })
            
        assert resp.status_code == 400
        assert resp.json()["detail"] == "Invalid token"

    @pytest.mark.anyio
    async def test_reset_password_expired_token(self):
        """Reset password dengan token yang expired gagal."""
        token = create_access_token(data={"sub": USER_ID, "type": "reset_password"}, expires_delta=timedelta(seconds=-1))

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/reset-password", json={
                "token": token,
                "new_password": "newpassword123"
            })
            
        assert resp.status_code == 400
        assert resp.json()["detail"] == "Token has expired"
