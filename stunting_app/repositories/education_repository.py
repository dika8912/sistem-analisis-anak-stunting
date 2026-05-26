from stunting_app.models.education import Education
from stunting_app.repositories.base_repository import BaseRepository

class EducationRepository(BaseRepository[Education]):
    def __init__(self):
        super().__init__(Education)
