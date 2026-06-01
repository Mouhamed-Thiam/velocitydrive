<?php
/**
 * Classe Image - Gestion des images des voitures
 * Upload, suppression, et gestion des galeries
 */

class Image {
    private $pdo;
    private $uploadDir;
    private $maxFileSize;
    private $allowedExtensions;
    
    public function __construct($pdo, $uploadDir = '', $maxFileSize = 5242880) {
        $this->pdo = $pdo;
        $this->uploadDir = $uploadDir ?: IMAGES_PATH;
        $this->maxFileSize = $maxFileSize;
        $this->allowedExtensions = ALLOWED_EXTENSIONS;
    }
    
    /**
     * Récupère toutes les images d'une voiture
     * @param int $carId - ID de la voiture
     * @return array - Liste des images
     */
    public function getCarImages($carId) {
        $sql = "SELECT * FROM images_voitures WHERE car_id = :car_id ORDER BY ordre ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':car_id', $carId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Upload une image
     * @param array $file - $_FILES['image']
     * @param int $carId - ID de la voiture
     * @return bool|string - Chemin du fichier ou false
     */
    public function upload($file, $carId) {
        // Vérifications de base
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        // Vérifier la taille
        if ($file['size'] > $this->maxFileSize) {
            return false;
        }
        
        // Vérifier l'extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions)) {
            return false;
        }
        
        // Vérifier le type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, ALLOWED_MIME_TYPES)) {
            return false;
        }
        
        // Générer un nom de fichier sécurisé
        $filename = generateSafeFilename($file['name']);
        $filepath = $this->uploadDir . $filename;
        
        // Créer le répertoire s'il n'existe pas
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
        
        // Déplacer le fichier uploadé
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Enregistrer en BD
            $sql = "INSERT INTO images_voitures (car_id, chemin_image, ordre) 
                    VALUES (:car_id, :chemin_image, 
                    (SELECT COALESCE(MAX(ordre), 0) + 1 FROM images_voitures WHERE car_id = :car_id))";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':car_id', $carId, PDO::PARAM_INT);
            $stmt->bindValue(':chemin_image', UPLOAD_DIR . $filename);
            
            if ($stmt->execute()) {
                return UPLOAD_DIR . $filename;
            }
        }
        
        return false;
    }
    
    /**
     * Supprime une image
     * @param int $imageId - ID de l'image
     * @return bool - true si succès
     */
    public function delete($imageId) {
        // Récupérer le chemin du fichier
        $sql = "SELECT chemin_image FROM images_voitures WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $imageId, PDO::PARAM_INT);
        $stmt->execute();
        $image = $stmt->fetch();
        
        if ($image) {
            // Supprimer le fichier du disque
            $filepath = ROOT_PATH . $image['chemin_image'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            
            // Supprimer de la BD
            $sql = "DELETE FROM images_voitures WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $imageId, PDO::PARAM_INT);
            return $stmt->execute();
        }
        
        return false;
    }
    
    /**
     * Met à jour l'ordre des images
     * @param array $imageIds - Tableau d'IDs dans l'ordre
     * @return bool - true si succès
     */
    public function reorder($imageIds) {
        $sql = "UPDATE images_voitures SET ordre = :ordre WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($imageIds as $ordre => $id) {
            $stmt->bindValue(':ordre', $ordre, PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
        
        return true;
    }
    
    /**
     * Récupère l'image principale d'une voiture
     * @param int $carId - ID de la voiture
     * @return string - Chemin de l'image
     */
    public function getMainImage($carId) {
        $sql = "SELECT chemin_image FROM images_voitures WHERE car_id = :car_id 
                ORDER BY ordre ASC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':car_id', $carId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['chemin_image'] : 'assets/images/placeholder.jpg';
    }
}
?>