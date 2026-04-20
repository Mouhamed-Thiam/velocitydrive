import math
from typing import List, Dict, Tuple
from repositories.user_repository import UserRepository
from repositories.item_repository import ItemRepository
from repositories.rating_repository import RatingRepository
from models.item import Item

class RecommenderService:
    def __init__(self):
        self.user_repo = UserRepository()
        self.item_repo = ItemRepository()
        self.rating_repo = RatingRepository()

    def cosine_similarity(self, ratings_a: Dict[int, int], ratings_b: Dict[int, int]) -> float:
        """Calcule la similarité cosinus entre deux utilisateurs."""
        common_items = set(ratings_a.keys()) & set(ratings_b.keys())
        if not common_items:
            return 0.0

        dot_product = sum(ratings_a[item] * ratings_b[item] for item in common_items)
        norm_a = math.sqrt(sum(r**2 for r in ratings_a.values()))
        norm_b = math.sqrt(sum(r**2 for r in ratings_b.values()))
        if norm_a == 0 or norm_b == 0:
            return 0.0
        return dot_product / (norm_a * norm_b)

    def get_similar_users(self, user_id: int, min_common: int = 2) -> List[Tuple[int, float]]:
        """Retourne une liste de (user_id, similarité) pour tous les utilisateurs ayant au moins min_common films en commun."""
        target_ratings = self.rating_repo.get_ratings_by_user(user_id)
        if not target_ratings:
            return []

        all_users = self.user_repo.get_all()
        similarities = []
        for other in all_users:
            if other.id == user_id:
                continue
            other_ratings = self.rating_repo.get_ratings_by_user(other.id)
            common = set(target_ratings.keys()) & set(other_ratings.keys())
            if len(common) >= min_common:
                sim = self.cosine_similarity(target_ratings, other_ratings)
                if sim > 0:
                    similarities.append((other.id, sim))
        # Trier par similarité décroissante
        similarities.sort(key=lambda x: x[1], reverse=True)
        return similarities

    def recommend_items(self, user_id: int, top_n: int = 5, min_similarity: float = 0.3, min_common: int = 2) -> List[Tuple[Item, float]]:
        """Recommande top_n items pour l'utilisateur."""
        # 1. Obtenir les items déjà notés par l'utilisateur
        user_ratings = self.rating_repo.get_ratings_by_user(user_id)
        rated_item_ids = set(user_ratings.keys())

        # 2. Obtenir les utilisateurs similaires
        similar_users = self.get_similar_users(user_id, min_common)
        similar_users = [(uid, sim) for uid, sim in similar_users if sim >= min_similarity]
        if not similar_users:
            return []

        # 3. Récupérer les notes des utilisateurs similaires sur les items non notés par l'utilisateur
        # On va collecter pour chaque item non noté la somme pondérée des similarités
        predictions = {}
        sim_sum = {}
        for other_id, sim in similar_users:
            other_ratings = self.rating_repo.get_ratings_by_user(other_id)
            for item_id, rating in other_ratings.items():
                if item_id in rated_item_ids:
                    continue
                predictions[item_id] = predictions.get(item_id, 0) + sim * rating
                sim_sum[item_id] = sim_sum.get(item_id, 0) + sim

        # 4. Calculer la note prédite et trier
        candidates = []
        for item_id, total_weight in predictions.items():
            if sim_sum[item_id] > 0:
                predicted_rating = total_weight / sim_sum[item_id]
                candidates.append((item_id, predicted_rating))
        candidates.sort(key=lambda x: x[1], reverse=True)

        # 5. Récupérer les objets Item
        result = []
        for item_id, pred in candidates[:top_n]:
            item = self.item_repo.get_by_id(item_id)
            if item:
                result.append((item, pred))
        return result