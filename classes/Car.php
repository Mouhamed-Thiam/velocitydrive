<?php
/**
 * Classe Car - Gestion des voitures (CRUD)
 * Utilise PDO avec requêtes préparées pour la sécurité
 */

class Car {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Récupère toutes les voitures
     * @param int $limit - Nombre de résultats
     * @param int $offset - Décalage (pour pagination)
     * @return array - Liste des voitures
     */
    public function getAll($limit = 12, $offset = 0) {
        $sql = "SELECT * FROM voitures WHERE statut = 'disponible' 
                ORDER BY date_creation DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère une voiture par ID
     * @param int $id - ID de la voiture
     * @return array|false - Données de la voiture ou false
     */
    public function getById($id) {
        $sql = "SELECT * FROM voitures WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Recherche les voitures par critères
     * @param string $brand - Marque (optionnel)
     * @param float $minPrice - Prix minimum (optionnel)
     * @param float $maxPrice - Prix maximum (optionnel)
     * @param string $fuelType - Type de carburant (optionnel)
     * @return array - Voitures trouvées
     */
    public function search($brand = '', $minPrice = 0, $maxPrice = 999999, $fuelType = '') {
        $sql = "SELECT * FROM voitures WHERE statut = 'disponible'";
        $params = [];
        
        // Filtre par marque
        if (!empty($brand)) {
            $sql .= " AND marque = :brand";
            $params[':brand'] = $brand;
        }
        
        // Filtre par prix
        $sql .= " AND prix >= :minPrice AND prix <= :maxPrice";
        $params[':minPrice'] = $minPrice;
        $params[':maxPrice'] = $maxPrice;
        
        // Filtre par carburant
        if (!empty($fuelType)) {
            $sql .= " AND carburant = :fuelType";
            $params[':fuelType'] = $fuelType;
        }
        
        $sql .= " ORDER BY date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Ajoute une nouvelle voiture
     * @param array $data - Données de la voiture
     * @return int|false - ID de la voiture créée ou false
     */
    public function create($data) {
        $sql = "INSERT INTO voitures 
                (marque, modele, annee, prix, kilometrage, carburant, 
                 transmission, description, image, couleur, type_vehicule, 
                 portes, cylindree, puissance) 
                VALUES 
                (:marque, :modele, :annee, :prix, :kilometrage, :carburant, 
                 :transmission, :description, :image, :couleur, :type_vehicule, 
                 :portes, :cylindree, :puissance)";
        
        $stmt = $this->pdo->prepare($sql);
        
        // Liaison des paramètres
        $stmt->bindValue(':marque', $data['marque']);
        $stmt->bindValue(':modele', $data['modele']);
        $stmt->bindValue(':annee', $data['annee'], PDO::PARAM_INT);
        $stmt->bindValue(':prix', $data['prix']);
        $stmt->bindValue(':kilometrage', $data['kilometrage'], PDO::PARAM_INT);
        $stmt->bindValue(':carburant', $data['carburant']);
        $stmt->bindValue(':transmission', $data['transmission']);
        $stmt->bindValue(':description', $data['description']);
        $stmt->bindValue(':image', $data['image']);
        $stmt->bindValue(':couleur', $data['couleur']);
        $stmt->bindValue(':type_vehicule', $data['type_vehicule']);
        $stmt->bindValue(':portes', $data['portes'], PDO::PARAM_INT);
        $stmt->bindValue(':cylindree', $data['cylindree']);
        $stmt->bindValue(':puissance', $data['puissance']);
        
        if ($stmt->execute()) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }
    
    /**
     * Met à jour une voiture
     * @param int $id - ID de la voiture
     * @param array $data - Données à mettre à jour
     * @return bool - true si succès
     */
    public function update($id, $data) {
        $sql = "UPDATE voitures SET 
                marque = :marque, modele = :modele, annee = :annee, 
                prix = :prix, kilometrage = :kilometrage, carburant = :carburant, 
                transmission = :transmission, description = :description, 
                couleur = :couleur, type_vehicule = :type_vehicule, 
                portes = :portes, cylindree = :cylindree, puissance = :puissance
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':marque', $data['marque']);
        $stmt->bindValue(':modele', $data['modele']);
        $stmt->bindValue(':annee', $data['annee'], PDO::PARAM_INT);
        $stmt->bindValue(':prix', $data['prix']);
        $stmt->bindValue(':kilometrage', $data['kilometrage'], PDO::PARAM_INT);
        $stmt->bindValue(':carburant', $data['carburant']);
        $stmt->bindValue(':transmission', $data['transmission']);
        $stmt->bindValue(':description', $data['description']);
        $stmt->bindValue(':couleur', $data['couleur']);
        $stmt->bindValue(':type_vehicule', $data['type_vehicule']);
        $stmt->bindValue(':portes', $data['portes'], PDO::PARAM_INT);
        $stmt->bindValue(':cylindree', $data['cylindree']);
        $stmt->bindValue(':puissance', $data['puissance']);
        
        return $stmt->execute();
    }
    
    /**
     * Supprime une voiture
     * @param int $id - ID de la voiture
     * @return bool - true si succès
     */
    public function delete($id) {
        // Supprimer d'abord les images associées (cascade)
        $sql = "DELETE FROM voitures WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Compte le nombre total de voitures
     * @return int - Nombre de voitures
     */
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM voitures WHERE statut = 'disponible'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Récupère les marques distinctes
     * @return array - Liste des marques
     */
    public function getBrands() {
        $sql = "SELECT DISTINCT marque FROM voitures ORDER BY marque";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Récupère les prix min/max
     * @return array - ['min' => x, 'max' => y]
     */
    public function getPriceRange() {
        $sql = "SELECT MIN(prix) as min_price, MAX(prix) as max_price FROM voitures";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>