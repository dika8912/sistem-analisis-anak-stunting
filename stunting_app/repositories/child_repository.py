from stunting_app.models.child import Child
from stunting_app.repositories.base_repository import BaseRepository

class ChildRepository(BaseRepository[Child]):
    def __init__(self):
        super().__init__(Child)
