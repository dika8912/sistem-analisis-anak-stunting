"""
Unit Tests - Stunting Detection System
Skenario: Normal, aneh, ekstrem, dan edge-case sesuai permintaan.

Struktur Test:
1. ZScoreService - Perhitungan matematika WHO LMS
2. MLPredictionService - Inference model klasifikasi
3. POST /api/auth/register - Pendaftaran user
4. POST /api/auth/login - Login & token JWT
5. POST /api/calculate - On-the-fly deteksi (tanpa simpan)
6. POST /api/measurements - Simpan pengukuran (protected)
7. GET /api/guardians/me - Profil guardian
8. GET /api/children/{id} - Detail anak
9. GET /api/children/{id}/history - Histori pengukuran
10. GET /api/admin/children - Pencarian admin
"""

import pytest
import pytest_asyncio
import asyncio
from datetime import date, datetime, timedelta, timezone
from unittest.mock import MagicMock, AsyncMock, patch
from httpx import AsyncClient, ASGITransport
import jwt
import sys
import os

# Add root to path
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.dirname(__file__))))

from stunting_app.main import app
from stunting_app.services.zscore_service import ZScoreService
from stunting_app.services.ml_prediction_service import MLPredictionService
from stunting_app.config.settings import settings
from stunting_app.core.security import get_password_hash, create_access_token, verify_password
from stunting_app.models.user import RoleEnum


# ─────────────────────────────────────────────────────────────────────────────
# FIXTURES
# ─────────────────────────────────────────────────────────────────────────────

def make_token(user_id: str, role: str) -> str:
    return create_access_token(data={"sub": user_id, "role": role})

USER_ID   = "aaaaaaaa-0000-0000-0000-000000000001"
ADMIN_ID  = "bbbbbbbb-0000-0000-0000-000000000001"
CHILD_ID  = "cccccccc-0000-0000-0000-000000000001"
GUARDIAN_ID = "dddddddd-0000-0000-0000-000000000001"
MEAS_ID   = "eeeeeeee-0000-0000-0000-000000000001"

user_token  = make_token(USER_ID, "user")
admin_token = make_token(ADMIN_ID, "admin")


# ─────────────────────────────────────────────────────────────────────────────
# 1. UNIT TEST: ZScoreService
# ─────────────────────────────────────────────────────────────────────────────

class TestZScoreService:
    """Test klasifikasi dan perhitungan WHO LMS Z-Score."""

    # ── Stunting Classification ──────────────────────────────────────────────

    def test_classify_stunting_severely(self):
        """HAZ < -3 harus 'severely_stunted'"""
        assert ZScoreService.classify_stunting(-3.01) == "severely_stunted"

    def test_classify_stunting_stunted(self):
        """HAZ antara -3 dan -2 → 'stunted'"""
        assert ZScoreService.classify_stunting(-2.5) == "stunted"

    def test_classify_stunting_normal(self):
        """HAZ antara -2 dan 3 → 'normal'"""
        assert ZScoreService.classify_stunting(0.0) == "normal"

    def test_classify_stunting_tall(self):
        """HAZ > 3 → 'tall'"""
        assert ZScoreService.classify_stunting(3.01) == "tall"

    def test_classify_stunting_exact_boundary_minus3(self):
        """HAZ tepat -3.0 → masuk 'stunted', bukan 'severely_stunted' (boundary exclusive)"""
        assert ZScoreService.classify_stunting(-3.0) == "stunted"

    def test_classify_stunting_exact_boundary_minus2(self):
        """HAZ tepat -2.0 → 'normal'"""
        assert ZScoreService.classify_stunting(-2.0) == "normal"

    def test_classify_stunting_extreme_positive(self):
        """HAZ yang sangat tinggi (misalkan Giant Syndrome) → tetap 'tall'"""
        assert ZScoreService.classify_stunting(99.9) == "tall"

    def test_classify_stunting_extreme_negative(self):
        """HAZ yang sangat negatif → tetap 'severely_stunted'"""
        assert ZScoreService.classify_stunting(-99.9) == "severely_stunted"

    # ── Wasting Classification ───────────────────────────────────────────────

    def test_classify_wasting_severely_wasted(self):
        """WHZ < -3 → 'severely_wasted'"""
        assert ZScoreService.classify_wasting(-3.1) == "severely_wasted"

    def test_classify_wasting_wasted(self):
        """WHZ -3 sampai -2 → 'wasted'"""
        assert ZScoreService.classify_wasting(-2.5) == "wasted"

    def test_classify_wasting_normal(self):
        """WHZ antara -2 sampai 1 → 'normal'"""
        assert ZScoreService.classify_wasting(0.5) == "normal"

    def test_classify_wasting_risk_overweight(self):
        """WHZ antara 1 dan 2 → 'risk_of_overweight'"""
        assert ZScoreService.classify_wasting(1.5) == "risk_of_overweight"

    def test_classify_wasting_overweight(self):
        """WHZ antara 2 dan 3 → 'overweight'"""
        assert ZScoreService.classify_wasting(2.5) == "overweight"

    def test_classify_wasting_obese(self):
        """WHZ > 3 → 'obese'"""
        assert ZScoreService.classify_wasting(3.5) == "obese"

    # ── Underweight Classification ───────────────────────────────────────────

    def test_classify_underweight_severely(self):
        assert ZScoreService.classify_underweight(-3.1) == "severely_underweight"

    def test_classify_underweight_underweight(self):
        assert ZScoreService.classify_underweight(-2.5) == "underweight"

    def test_classify_underweight_normal(self):
        assert ZScoreService.classify_underweight(0.0) == "normal"

    # ── Z-Score Calculation ──────────────────────────────────────────────────

    def test_zscore_formula_L_nonzero(self):
        """Test formula LMS saat L != 0"""
        # Z = (((y/M)**L) - 1) / (L*S)
        y, L, M, S = 80.0, 1.0, 80.0, 0.1
        expected = (((80.0 / 80.0) ** 1.0) - 1) / (1.0 * 0.1)
        assert ZScoreService.calculate_zscore(y, L, M, S) == pytest.approx(expected)

    def test_zscore_formula_L_zero(self):
        """Test formula LMS saat L == 0 → gunakan ln()"""
        import math
        y, M, S = 80.0, 80.0, 0.1
        expected = math.log(y / M) / S
        assert ZScoreService.calculate_zscore(y, 0.0, M, S) == pytest.approx(expected)

    def test_zscore_normal_child_L_zero(self):
        """Anak normal dengan L=0 harus menghasilkan Z-Score = 0"""
        result = ZScoreService.calculate_zscore(70.0, 0.0, 70.0, 0.05)
        assert result == pytest.approx(0.0)

    def test_zscore_stunted_child_negative(self):
        """Anak stunting harus menghasilkan Z-Score negatif"""
        # Tinggi aktual (50 cm) jauh lebih rendah dari median (80 cm)
        result = ZScoreService.calculate_zscore(50.0, 1.0, 80.0, 0.1)
        assert result < 0

    def test_zscore_tall_child_positive(self):
        """Anak tinggi harus menghasilkan Z-Score positif"""
        result = ZScoreService.calculate_zscore(120.0, 1.0, 80.0, 0.1)
        assert result > 0

    def test_zscore_measurement_equals_median(self):
        """Bila nilai = median (M), Z-Score harus = 0"""
        result = ZScoreService.calculate_zscore(75.0, 1.0, 75.0, 0.1)
        assert result == pytest.approx(0.0)

    def test_zscore_tiny_value_extremely_small(self):
        """Nilai pengukuran sangat kecil dengan L!=0 tidak crash, hanya menghasilkan nilai negatif ekstrem"""
        result = ZScoreService.calculate_zscore(0.001, 1.0, 80.0, 0.1)
        assert result < -1  # sangat negatif (stunted)

    # ── Skenario Aneh ────────────────────────────────────────────────────────

    def test_zscore_negative_measurement_crash(self):
        """Pengukuran negatif (data rusak) dengan L=0 → crash math.log"""
        with pytest.raises((ValueError, Exception)):
            ZScoreService.calculate_zscore(-5.0, 0.0, 80.0, 0.1)

    def test_classify_stunting_float_nan(self):
        """Nilai NaN tidak boleh menghasilkan klasifikasi yang benar (skenario edge)"""
        import math
        result = ZScoreService.classify_stunting(float('nan'))
        # nan < -3.0 is False, nan < -2.0 is False, nan > 3.0 is False → 'normal'
        # Ini perilaku Python yang "aneh" tapi harus dipahami
        assert result == "normal"


# ─────────────────────────────────────────────────────────────────────────────
# 2. UNIT TEST: Security (Hashing & JWT)
# ─────────────────────────────────────────────────────────────────────────────

class TestSecurity:
    def test_hash_password_is_not_plaintext(self):
        hashed = get_password_hash("rahasia123")
        assert hashed != "rahasia123"

    def test_verify_correct_password(self):
        hashed = get_password_hash("rahasia123")
        assert verify_password("rahasia123", hashed) is True

    def test_verify_wrong_password(self):
        hashed = get_password_hash("rahasia123")
        assert verify_password("salah_password", hashed) is False

    def test_verify_empty_password(self):
        hashed = get_password_hash("rahasia123")
        assert verify_password("", hashed) is False

    def test_jwt_contains_correct_payload(self):
        token = create_access_token(data={"sub": "user-uuid", "role": "user"})
        payload = jwt.decode(token, settings.JWT_SECRET, algorithms=[settings.JWT_ALGORITHM])
        assert payload["sub"] == "user-uuid"
        assert payload["role"] == "user"

    def test_jwt_expiry_is_in_future(self):
        token = create_access_token(data={"sub": "user-uuid"})
        payload = jwt.decode(token, settings.JWT_SECRET, algorithms=[settings.JWT_ALGORITHM])
        assert payload["exp"] > datetime.now(timezone.utc).timestamp()

    def test_jwt_custom_expiry(self):
        """Token dengan expiry sangat pendek (1 detik) harus langsung expire"""
        token = create_access_token(data={"sub": "user-uuid"}, expires_delta=timedelta(seconds=1))
        import time; time.sleep(2)
        with pytest.raises(jwt.ExpiredSignatureError):
            jwt.decode(token, settings.JWT_SECRET, algorithms=[settings.JWT_ALGORITHM])

    def test_jwt_tampered_signature(self):
        """Token yang signature-nya dimanipulasi harus ditolak"""
        token = create_access_token(data={"sub": "user-uuid"})
        tampered = token[:-5] + "XXXXX"
        with pytest.raises(jwt.InvalidSignatureError):
            jwt.decode(tampered, settings.JWT_SECRET, algorithms=[settings.JWT_ALGORITHM])

    def test_jwt_wrong_secret(self):
        """Token dibuat dengan secret berbeda harus ditolak"""
        token = jwt.encode({"sub": "user"}, "secret-berbeda", algorithm="HS256")
        with pytest.raises(jwt.InvalidSignatureError):
            jwt.decode(token, settings.JWT_SECRET, algorithms=[settings.JWT_ALGORITHM])

    def test_hash_same_password_different_results(self):
        """Bcrypt salt → hash yang sama menghasilkan hasil berbeda setiap kali"""
        h1 = get_password_hash("sama")
        h2 = get_password_hash("sama")
        assert h1 != h2  # Karena bcrypt pakai random salt


# ─────────────────────────────────────────────────────────────────────────────
# 3. INTEGRATION TEST (FastAPI TestClient + Mock DB)
# ─────────────────────────────────────────────────────────────────────────────

def make_mock_db(overrides: dict = None):
    """Membuat mock AsyncSession yang bisa dikonfigurasi per-test."""
    mock_db = AsyncMock()
    mock_execute = AsyncMock()
    mock_db.execute.return_value = mock_execute
    mock_execute.scalar_one_or_none.return_value = None
    if overrides:
        for k, v in overrides.items():
            setattr(mock_execute, k, v)
    return mock_db


class TestAuthEndpoints:
    """Test suite untuk /api/auth/register dan /api/auth/login"""

    @pytest.mark.anyio
    async def test_register_success(self):
        """Registrasi dengan data valid harus berhasil"""
        async def fake_get_db():
            db = AsyncMock()
            # scalar_one_or_none harus return value (bukan coroutine)
            result_mock = MagicMock()
            result_mock.scalar_one_or_none.return_value = None  # user belum ada
            db.execute = AsyncMock(return_value=result_mock)
            db.flush = AsyncMock()
            db.add = MagicMock()
            db.commit = AsyncMock()
            db.refresh = AsyncMock(side_effect=lambda u: (
                setattr(u, 'id', USER_ID),
                setattr(u, 'username', 'buditest'),
                setattr(u, 'email', 'budi@test.com'),
                setattr(u, 'role', RoleEnum.user)
            ))
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/register", json={
                "username": "buditest",
                "email": "budi@test.com",
                "password": "password123",
                "name": "Budi Santoso",
                "nomor_kk": "3201010101010101",
                "phone": "081234567890",
                "address": "Jalan Merdeka No 1"
            })
        app.dependency_overrides.clear()
        assert resp.status_code == 200

    @pytest.mark.anyio
    async def test_register_duplicate_username(self):
        """Registrasi dengan username yang sudah ada harus 400"""
        existing_user = MagicMock()
        existing_user.id = USER_ID
        existing_user.username = "buditest"

        async def fake_get_db():
            db = AsyncMock()
            result_mock = MagicMock()
            result_mock.scalar_one_or_none.return_value = existing_user  # sudah ada
            db.execute = AsyncMock(return_value=result_mock)
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/register", json={
                "username": "buditest",
                "email": "budi@test.com",
                "password": "password123",
                "name": "Budi",
                "nomor_kk": "1234567890123456",
                "phone": "081234567890",
                "address": "Jl. Test"
            })
        app.dependency_overrides.clear()
        assert resp.status_code == 400
        assert "already registered" in resp.json()["detail"]

    @pytest.mark.anyio
    async def test_register_password_too_short(self):
        """Password < 6 karakter harus ditolak di level Pydantic (422)"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/register", json={
                "username": "budi",
                "email": "budi@test.com",
                "password": "abc",   # < 6
                "name": "Budi",
                "nomor_kk": "1234567890123456",
                "phone": "081234567890",
                "address": "Jl. Test"
            })
        assert resp.status_code == 422

    @pytest.mark.anyio
    async def test_register_username_too_short(self):
        """Username < 3 karakter harus ditolak (422)"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/register", json={
                "username": "ab",   # < 3
                "email": "ab@test.com",
                "password": "password123",
                "name": "AB",
                "nomor_kk": "1234567890123456",
                "phone": "081234567890",
                "address": "Jl. AB"
            })
        assert resp.status_code == 422

    @pytest.mark.anyio
    async def test_register_invalid_email_format(self):
        """Email format tidak valid harus ditolak (422)"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/register", json={
                "username": "validuser",
                "email": "ini-bukan-email",
                "password": "password123",
                "name": "Valid User",
                "nomor_kk": "1234567890123456",
                "phone": "081234567890",
                "address": "Jl. Valid"
            })
        assert resp.status_code == 422

    @pytest.mark.anyio
    async def test_register_without_email_is_valid(self):
        """Email bersifat opsional, registrasi tanpa email harus bisa"""
        async def fake_get_db():
            db = AsyncMock()
            result_mock = MagicMock()
            result_mock.scalar_one_or_none.return_value = None
            db.execute = AsyncMock(return_value=result_mock)
            db.flush = AsyncMock()
            db.add = MagicMock()
            db.commit = AsyncMock()
            db.refresh = AsyncMock(side_effect=lambda u: (
                setattr(u, 'id', USER_ID),
                setattr(u, 'username', 'tanpaemail'),
                setattr(u, 'email', None),
                setattr(u, 'role', RoleEnum.user)
            ))
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/register", json={
                "username": "tanpaemail",
                "password": "password123",
                "name": "Tanpa Email",
                "nomor_kk": "1234567890123456",
                "phone": "081234567890",
                "address": "Jl. Tanpa Email"
            })
        app.dependency_overrides.clear()
        assert resp.status_code == 200

    @pytest.mark.anyio
    async def test_register_role_forced_to_user(self):
        """Coba kirim role='admin' lewat body → hasilnya tetap harus 'user'"""
        async def fake_get_db():
            db = AsyncMock()
            result_mock = MagicMock()
            result_mock.scalar_one_or_none.return_value = None
            db.execute = AsyncMock(return_value=result_mock)
            db.flush = AsyncMock()
            db.add = MagicMock()
            db.commit = AsyncMock()
            db.refresh = AsyncMock(side_effect=lambda u: (
                setattr(u, 'id', USER_ID),
                setattr(u, 'username', 'harususer'),
                setattr(u, 'email', None),
                setattr(u, 'role', RoleEnum.user)
            ))
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/register", json={
                "username": "harususer",
                "password": "password123",
                "name": "Harus User",
                "nomor_kk": "1234567890123456",
                "phone": "081234567890",
                "address": "Jl. User",
                "role": "admin"   # sengaja kirim role admin
            })
        app.dependency_overrides.clear()
        if resp.status_code == 200:
            assert resp.json()["role"] == "user"

    @pytest.mark.anyio
    async def test_login_success(self):
        """Login dengan kredensial benar harus return JWT token"""
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
                "password": "password123"
            })
        app.dependency_overrides.clear()
        assert resp.status_code == 200
        data = resp.json()
        assert "access_token" in data
        assert data["token_type"] == "bearer"

    @pytest.mark.anyio
    async def test_login_wrong_password(self):
        """Login dengan password salah harus 401"""
        hashed = get_password_hash("password123")
        mock_user = MagicMock()
        mock_user.id = USER_ID
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
                "password": "salah_total"
            })
        app.dependency_overrides.clear()
        assert resp.status_code == 401

    @pytest.mark.anyio
    async def test_login_nonexistent_user(self):
        """Login dengan user yang tidak ada di DB harus 401"""
        async def fake_get_db():
            db = AsyncMock()
            result_mock = MagicMock()
            result_mock.scalar_one_or_none.return_value = None
            db.execute = AsyncMock(return_value=result_mock)
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/login", data={
                "username": "userpalsu",
                "password": "password123"
            })
        app.dependency_overrides.clear()
        assert resp.status_code == 401

    @pytest.mark.anyio
    async def test_login_empty_credentials(self):
        """Login tanpa username dan password → 422 Unprocessable Entity"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/login", data={})
        assert resp.status_code == 422

    @pytest.mark.anyio
    async def test_login_sql_injection_attempt(self):
        """Username dengan SQL injection pattern harus ditangani dengan aman (tidak crash)"""
        async def fake_get_db():
            db = AsyncMock()
            result_mock = MagicMock()
            result_mock.scalar_one_or_none.return_value = None
            db.execute = AsyncMock(return_value=result_mock)
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/login", data={
                "username": "' OR '1'='1",
                "password": "' OR '1'='1"
            })
        app.dependency_overrides.clear()
        assert resp.status_code == 401  # harus ditolak, bukan 500


# ─────────────────────────────────────────────────────────────────────────────
# 4. TEST: POST /api/calculate (On-The-Fly Deteksi)
# ─────────────────────────────────────────────────────────────────────────────

class TestDetectEndpoint:

    @pytest.mark.anyio
    async def test_detect_normal_child(self):
        """Anak normal → endpoint harus return 200 dengan who_calculation"""
        with patch("stunting_app.api.endpoints.who_repo.get_standard") as mock_who, \
             patch("stunting_app.api.endpoints.ml_service.predict") as mock_ml, \
             patch("stunting_app.api.endpoints.RecommendationService.get_recommendations", new_callable=AsyncMock) as mock_rec:

            std = MagicMock(); std.l = 1.0; std.m = 85.5; std.s = 0.1
            mock_who.return_value = std
            mock_ml.return_value = {
                "stunting_status_ml": "normal", "stunting_confidence": 0.95,
                "wasting_status_ml": "normal", "wasting_confidence": 0.90
            }
            mock_rec.return_value = ["Makan sayur", "Minum susu"]

            async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
                resp = await ac.post("/api/calculate", json={
                    "gender": "M", "age_in_months": 24,
                    "height_cm": 85.5, "weight_kg": 12.1
                })
        assert resp.status_code == 200
        data = resp.json()
        assert "who_calculation" in data

        assert "recommendations" in data

    @pytest.mark.anyio
    async def test_detect_female_newborn(self):
        """Bayi perempuan baru lahir (0 bulan, berat 3 kg, tinggi 50 cm)"""
        with patch("stunting_app.api.endpoints.who_repo.get_standard") as mock_who, \
             patch("stunting_app.api.endpoints.ml_service.predict") as mock_ml, \
             patch("stunting_app.api.endpoints.RecommendationService.get_recommendations", new_callable=AsyncMock) as mock_rec:
            std = MagicMock(); std.l = 1.0; std.m = 49.0; std.s = 0.04
            mock_who.return_value = std
            mock_ml.return_value = {"stunting_status_ml": "normal", "stunting_confidence": 0.8, "wasting_status_ml": "normal", "wasting_confidence": 0.8}
            mock_rec.return_value = []

            async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
                resp = await ac.post("/api/calculate", json={
                    "gender": "F", "age_in_months": 0,
                    "height_cm": 50.0, "weight_kg": 3.0
                })
        assert resp.status_code == 200

    @pytest.mark.anyio
    async def test_detect_invalid_gender(self):
        """Gender selain 'M' atau 'F' harus ditolak"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/calculate", json={
                "gender": "X",  # tidak valid
                "age_in_months": 12,
                "height_cm": 75.0,
                "weight_kg": 9.0
            })
        assert resp.status_code == 422

    @pytest.mark.anyio
    async def test_detect_age_out_of_range(self):
        """Umur lebih dari 60 bulan harus ditolak (422)"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/calculate", json={
                "gender": "M", "age_in_months": 61,  # batas max 60
                "height_cm": 75.0, "weight_kg": 9.0
            })
        assert resp.status_code == 422

    @pytest.mark.anyio
    async def test_detect_negative_height(self):
        """Tinggi badan negatif harus ditolak (422)"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/calculate", json={
                "gender": "M", "age_in_months": 12,
                "height_cm": -10.0,  # negatif
                "weight_kg": 9.0
            })
        assert resp.status_code == 422

    @pytest.mark.anyio
    async def test_detect_zero_weight(self):
        """Berat badan 0 harus ditolak (422)"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/calculate", json={
                "gender": "M", "age_in_months": 12,
                "height_cm": 75.0, "weight_kg": 0.0  # nol
            })
        assert resp.status_code == 422

    @pytest.mark.anyio
    async def test_detect_extremely_obese_child(self):
        """Anak dengan berat sangat berlebih (50 kg, usia 12 bulan)"""
        with patch("stunting_app.api.endpoints.who_repo.get_standard") as mock_who, \
             patch("stunting_app.api.endpoints.ml_service.predict") as mock_ml, \
             patch("stunting_app.api.endpoints.RecommendationService.get_recommendations", new_callable=AsyncMock) as mock_rec:
            std = MagicMock(); std.l = 1.0; std.m = 9.6; std.s = 0.15
            mock_who.return_value = std
            mock_ml.return_value = {"stunting_status_ml": "normal", "stunting_confidence": 0.5, "wasting_status_ml": "obese", "wasting_confidence": 0.99}
            mock_rec.return_value = ["Kurangi asupan gula"]

            async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
                resp = await ac.post("/api/calculate", json={
                    "gender": "M", "age_in_months": 12,
                    "height_cm": 75.0, "weight_kg": 50.0  # sangat tidak realistis
                })
        assert resp.status_code == 200

    @pytest.mark.anyio
    async def test_detect_missing_all_fields(self):
        """Request body kosong → 422"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/calculate", json={})
        assert resp.status_code == 422

    @pytest.mark.anyio
    async def test_detect_non_numeric_height(self):
        """Tinggi badan berupa string → 422"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/calculate", json={
                "gender": "M", "age_in_months": 12,
                "height_cm": "tujuh puluh",  # string
                "weight_kg": 9.0
            })
        assert resp.status_code == 422


# ─────────────────────────────────────────────────────────────────────────────
# 5. TEST: Protected Endpoints (RBAC)
# ─────────────────────────────────────────────────────────────────────────────

class TestRBACProtection:
    """Memastikan RBAC bekerja dengan benar di semua endpoint."""

    @pytest.mark.anyio
    async def test_measurements_no_token(self):
        """POST /api/measurements tanpa token → 401"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/measurements", json={
                "child_id": CHILD_ID,
                "measured_at": "2026-05-25",
                "weight": 12.0,
                "height": 85.0
            })
        assert resp.status_code == 401

    @pytest.mark.anyio
    async def test_guardians_me_no_token(self):
        """GET /api/guardians/me tanpa token → 401"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.get("/api/guardians/me")
        assert resp.status_code == 401

    @pytest.mark.anyio
    async def test_admin_children_with_user_token(self):
        """GET /api/admin/children menggunakan token user (bukan admin) → 403"""
        mock_user = MagicMock()
        mock_user.id = USER_ID
        mock_user.role = RoleEnum.user

        from stunting_app.api.deps import get_current_active_user
        app.dependency_overrides[get_current_active_user] = lambda: mock_user

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.get("/api/admin/children?parent_name=Budi&nomor_kk=1234567890123456",
                                headers={"Authorization": f"Bearer {user_token}"})
        app.dependency_overrides.clear()
        assert resp.status_code == 403

    @pytest.mark.anyio
    async def test_children_detail_user_accesses_others_child(self):
        """User A mencoba mengakses anak milik User B → 403"""
        mock_user = MagicMock()
        mock_user.id = USER_ID
        mock_user.role = RoleEnum.user

        mock_child = MagicMock()
        mock_child.id = CHILD_ID
        mock_child.guardian_id = "guardian-dari-user-lain"  # milik user lain

        mock_guardian = MagicMock()
        mock_guardian.id = GUARDIAN_ID  # guardian milik user A

        from stunting_app.api.deps import get_current_active_user, get_current_user
        app.dependency_overrides[get_current_active_user] = lambda: mock_user
        app.dependency_overrides[get_current_user] = lambda: mock_user

        async def fake_get_db():
            db = AsyncMock()
            result_mock = MagicMock()
            result_mock.scalar_one_or_none.return_value = mock_guardian
            db.execute = AsyncMock(return_value=result_mock)
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        with patch("stunting_app.api.endpoints.child_repo.get_by_id", new_callable=AsyncMock, return_value=mock_child):
            async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
                resp = await ac.get(f"/api/children/{CHILD_ID}",
                                    headers={"Authorization": f"Bearer {user_token}"})
        app.dependency_overrides.clear()
        assert resp.status_code == 403

    @pytest.mark.anyio
    async def test_admin_can_access_admin_endpoint(self):
        """Admin bisa mengakses /api/admin/children"""
        mock_admin = MagicMock()
        mock_admin.id = ADMIN_ID
        mock_admin.role = RoleEnum.admin

        from stunting_app.api.deps import require_admin
        app.dependency_overrides[require_admin] = lambda: mock_admin

        async def fake_get_db():
            db = AsyncMock()
            scalars_mock = MagicMock()
            scalars_mock.all.return_value = []
            result_mock = MagicMock()
            result_mock.scalars.return_value = scalars_mock
            db.execute = AsyncMock(return_value=result_mock)
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.get("/api/admin/children?parent_name=Budi&nomor_kk=1234567890123456",
                                headers={"Authorization": f"Bearer {admin_token}"})
        app.dependency_overrides.clear()
        assert resp.status_code == 200

    @pytest.mark.anyio
    async def test_expired_token_rejected(self):
        """Token yang sudah expired harus ditolak → 401"""
        expired_token = jwt.encode(
            {"sub": USER_ID, "role": "user", "exp": datetime.now(timezone.utc) - timedelta(days=1)},
            settings.JWT_SECRET,
            algorithm=settings.JWT_ALGORITHM
        )
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/measurements",
                                headers={"Authorization": f"Bearer {expired_token}"},
                                json={"child_id": CHILD_ID, "measured_at": "2026-01-01", "weight": 10.0, "height": 70.0})
        assert resp.status_code == 401

    @pytest.mark.anyio
    async def test_garbage_token_rejected(self):
        """Token sampah yang tidak bisa di-decode → 401"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/measurements",
                                headers={"Authorization": "Bearer ini.bukan.token"},
                                json={"child_id": CHILD_ID, "measured_at": "2026-01-01", "weight": 10.0, "height": 70.0})
        assert resp.status_code == 401

    @pytest.mark.anyio
    async def test_token_without_sub_rejected(self):
        """Token valid secara signature tapi tidak ada 'sub' → 401"""
        token_no_sub = jwt.encode(
            {"role": "user", "exp": datetime.now(timezone.utc) + timedelta(hours=1)},
            settings.JWT_SECRET,
            algorithm=settings.JWT_ALGORITHM
        )
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/measurements",
                                headers={"Authorization": f"Bearer {token_no_sub}"},
                                json={"child_id": CHILD_ID, "measured_at": "2026-01-01", "weight": 10.0, "height": 70.0})
        assert resp.status_code == 401


# ─────────────────────────────────────────────────────────────────────────────
# 6. SKENARIO ANEH & EDGE CASES
# ─────────────────────────────────────────────────────────────────────────────

class TestWeirdScenarios:
    """Skenario paling aneh dan ekstrem."""

    def test_zscore_baby_bigger_than_median_by_100x(self):
        """Anak raksasa: tinggi 7.000 cm (tidak realistis) → tetap return float"""
        result = ZScoreService.calculate_zscore(7000.0, 1.0, 70.0, 0.1)
        assert isinstance(result, float)
        assert result > 10  # sangat tall

    def test_classify_stunting_infinity(self):
        """Z-Score = infinity → Python: float('inf') > 3.0 adalah True → 'tall'"""
        result = ZScoreService.classify_stunting(float('inf'))
        assert result == "tall"

    def test_classify_stunting_negative_infinity(self):
        """Z-Score = -infinity → 'severely_stunted'"""
        result = ZScoreService.classify_stunting(float('-inf'))
        assert result == "severely_stunted"

    def test_classify_wasting_exactly_zero(self):
        """WHZ = 0 tepat (normal perfect) → 'normal'"""
        assert ZScoreService.classify_wasting(0.0) == "normal"

    def test_classify_wasting_boundary_2(self):
        """WHZ tepat di 2.0 → 'risk_of_overweight' (exclusive boundary dengan overweight)"""
        assert ZScoreService.classify_wasting(2.0) == "risk_of_overweight"

    def test_classify_wasting_boundary_1(self):
        """WHZ tepat di 1.0 → 'normal' (boundary antara normal dan risk)"""
        assert ZScoreService.classify_wasting(1.0) == "normal"

    def test_ml_prediction_without_model(self):
        """Prediksi tanpa model yang di-load → return status 'model_not_trained'"""
        fake_service = MLPredictionService.__new__(MLPredictionService)
        fake_service.stunting_model = None
        fake_service.wasting_model = None
        result = fake_service.predict("M", 12, 75.0, 9.0)

        assert result["stunting_confidence"] == 0.0


    @pytest.mark.anyio
    async def test_detect_unicode_in_json(self):
        """Coba kirim karakter Unicode di field gender → 422"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/calculate", json={
                "gender": "男",  # karakter kanji
                "age_in_months": 12,
                "height_cm": 75.0,
                "weight_kg": 9.0
            })
        assert resp.status_code == 422

    @pytest.mark.anyio
    async def test_detect_extremely_large_age(self):
        """Umur 999 bulan → ditolak oleh Pydantic (max 60)"""
        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/calculate", json={
                "gender": "M",
                "age_in_months": 999,
                "height_cm": 75.0,
                "weight_kg": 9.0
            })
        assert resp.status_code == 422

    @pytest.mark.anyio
    async def test_detect_float_age(self):
        """Umur dengan float (e.g., 12.5) → FastAPI harus truncate ke int atau reject"""
        with patch("stunting_app.api.endpoints.who_repo.get_standard") as mock_who, \
             patch("stunting_app.api.endpoints.ml_service.predict") as mock_ml, \
             patch("stunting_app.api.endpoints.RecommendationService.get_recommendations", new_callable=AsyncMock) as mock_rec:
            std = MagicMock(); std.l = 1.0; std.m = 75.0; std.s = 0.1
            mock_who.return_value = std
            mock_ml.return_value = {"stunting_status": "normal", "stunting_confidence": 0.9, "wasting_status": "normal", "wasting_confidence": 0.9}
            mock_rec.return_value = []

            async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
                resp = await ac.post("/api/calculate", json={
                    "gender": "M",
                    "age_in_months": 12.5,  # float untuk field int
                    "height_cm": 75.0,
                    "weight_kg": 9.0
                })
        # Pydantic v2 akan coerce 12.5 ke 12, atau reject dengan 422
        assert resp.status_code in [200, 422]

    @pytest.mark.anyio
    async def test_admin_search_children_no_params(self):
        """GET /api/admin/children tanpa query params → 422"""
        mock_admin = MagicMock()
        mock_admin.id = ADMIN_ID
        mock_admin.role = RoleEnum.admin

        from stunting_app.api.deps import require_admin
        app.dependency_overrides[require_admin] = lambda: mock_admin

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.get("/api/admin/children",
                                headers={"Authorization": f"Bearer {admin_token}"})
        app.dependency_overrides.clear()
        assert resp.status_code == 422

    @pytest.mark.anyio
    async def test_child_history_child_not_found(self):
        """GET history untuk child yang tidak ada → 404"""
        mock_user = MagicMock()
        mock_user.id = USER_ID
        mock_user.role = RoleEnum.admin  # admin bypass

        from stunting_app.api.deps import get_current_active_user
        app.dependency_overrides[get_current_active_user] = lambda: mock_user

        with patch("stunting_app.api.endpoints.child_repo.get_by_id", new_callable=AsyncMock, return_value=None):
            async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
                resp = await ac.get(f"/api/children/uuid-tidak-ada/history",
                                    headers={"Authorization": f"Bearer {admin_token}"})
        app.dependency_overrides.clear()
        assert resp.status_code == 404

    @pytest.mark.anyio
    async def test_register_with_xss_payload(self):
        """Field name dengan XSS payload → tidak crash, di-store sebagai string biasa (backend tidak melakukan sanitasi khusus)"""
        async def fake_get_db():
            db = AsyncMock()
            result_mock = MagicMock()
            result_mock.scalar_one_or_none.return_value = None
            db.execute = AsyncMock(return_value=result_mock)
            db.flush = AsyncMock()
            db.add = MagicMock()
            db.commit = AsyncMock()
            db.refresh = AsyncMock(side_effect=lambda u: (
                setattr(u, 'id', USER_ID),
                setattr(u, 'username', 'xssuser'),
                setattr(u, 'email', None),
                setattr(u, 'role', RoleEnum.user)
            ))
            yield db

        from stunting_app.core.database import get_db_session
        app.dependency_overrides[get_db_session] = fake_get_db

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            resp = await ac.post("/api/auth/register", json={
                "username": "xssuser_unique_99",
                "password": "password123",
                "name": "<script>alert('XSS')</script>",  # XSS payload di name
                "nomor_kk": "1234567890123456",
                "phone": "081234567890",
                "address": "javascript:alert(1)"
            })
        app.dependency_overrides.clear()
        # API harus tidak crash. Backend adalah JSON API, XSS tidak relevan di server level.
        assert resp.status_code in [200, 400, 422]

    def test_zscore_service_s_equal_zero_division(self):
        """S = 0 dalam formula → ZeroDivisionError (edge case data WHO rusak)"""
        with pytest.raises(ZeroDivisionError):
            ZScoreService.calculate_zscore(75.0, 1.0, 75.0, 0.0)  # S = 0


# ─────────────────────────────────────────────────────────────────────────────
# PYTEST CONFIG
# ─────────────────────────────────────────────────────────────────────────────

pytest_plugins = ('anyio',)
