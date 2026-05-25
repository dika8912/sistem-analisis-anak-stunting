import sys
import os

# Ensure the root project directory is in the sys path
sys.path.insert(0, os.path.dirname(os.path.dirname(__file__)))

from fastapi import FastAPI
from stunting_app.api.endpoints import router as api_router
from stunting_app.api.auth import router as auth_router

app = FastAPI(
    title="Stunting Detection System API",
    description="Backend API for WHO and ML based Stunting and Wasting Classification with RBAC",
    version="1.0.0"
)

app.include_router(auth_router, prefix="/api/auth", tags=["Authentication"])
app.include_router(api_router, tags=["Detection & Measurement"])

@app.get("/")
def health_check():
    return {"status": "ok", "message": "Stunting Detection System API is running."}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("stunting_app.main:app", host="0.0.0.0", port=5601, reload=True)
