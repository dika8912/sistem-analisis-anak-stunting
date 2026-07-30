import sys
import os
import logging
from contextlib import asynccontextmanager

# Ensure the root project directory is in the sys path
sys.path.insert(0, os.path.dirname(os.path.dirname(__file__)))

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from stunting_app.api.endpoints import router as api_router
from stunting_app.api.auth import router as auth_router
from stunting_app.config.settings import settings

logger = logging.getLogger(__name__)


def _ensure_models_trained():
    """Auto-train ML models on startup if model files are missing."""
    stunting_path = os.path.join(settings.MODELS_DIR, 'stunting_model.joblib')
    wasting_path  = os.path.join(settings.MODELS_DIR, 'wasting_model.joblib')

    if os.path.exists(stunting_path) and os.path.exists(wasting_path):
        logger.info("ML models already exist — skipping auto-training.")
        return

    logger.warning("ML model files not found. Attempting auto-training...")

    # Cari dataset CSV
    ml_dir = os.path.join(os.path.dirname(__file__), 'ml')
    dataset_path = os.path.join(ml_dir, 'dataset', 'stunting_wasting_dataset.csv')

    if not os.path.exists(dataset_path):
        logger.error(
            f"Dataset tidak ditemukan di {dataset_path}. "
            "Silakan latih model secara manual dengan: "
            "python -m stunting_app.ml.train"
        )
        return

    try:
        from stunting_app.ml.train import train_models
        train_models(csv_path=dataset_path, output_dir=settings.MODELS_DIR)
        logger.info("Auto-training selesai. Model siap digunakan.")
    except Exception as e:
        logger.error(f"Auto-training gagal: {e}. Prediksi ML tidak akan tersedia.")


@asynccontextmanager
async def lifespan(app: FastAPI):
    # Startup
    _ensure_models_trained()
    yield
    # Shutdown (jika diperlukan)

app = FastAPI(
    title="Stunting Detection System API",
    description="Backend API for WHO and ML based Stunting and Wasting Classification with RBAC",
    version="1.0.0",
    lifespan=lifespan
)

# ─────────────────────────────────────────────────────────────────────────────
# CORS Middleware
# Konfigurasi origins di .env: CORS_ORIGINS=https://domain1.com,https://domain2.com
# ─────────────────────────────────────────────────────────────────────────────
_cors_origins = [o.strip() for o in settings.CORS_ORIGINS.split(",") if o.strip() and o.strip() != "*"]
_allow_all = "*" in [o.strip() for o in settings.CORS_ORIGINS.split(",")]

app.add_middleware(
    CORSMiddleware,
    allow_origins=_cors_origins if not _allow_all else ["*"],
    allow_origin_regex=".*" if _allow_all else None,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(auth_router, prefix="/api/auth", tags=["Authentication"])
app.include_router(api_router, tags=["Detection & Measurement"])

@app.get("/")
def health_check():
    return {"status": "ok", "message": "Stunting Detection System API is running."}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("stunting_app.main:app", host="0.0.0.0", port=5601, reload=True)
