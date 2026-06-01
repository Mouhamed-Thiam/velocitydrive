<?php
/**
 * Classe Admin - Gestion de l'authentification
 * Login, logout, vérification de session
 */

class Admin {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Authentifie un administrateur
     * @param string $username - Nom d'utilisateur
     * @param string $password - Mot de passe
     * @return bool|int - ID de l'admin ou false
     */
    public function login($username, $password) {
        // Vérifier que les identifiants ne sont pas vides
        if (empty($username) || empty($password)) {
            return false;
        }
        
        // Récupérer l'admin de la BD
        $sql = "SELECT * FROM administrateurs WHERE username = :username AND statut = 'actif'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $admin = $stmt->fetch();
        
        // Vérifier le mot de passe
        if ($admin && verifyPassword($password, $admin['password'])) {
            // Mettre à jour la date de dernière connexion
            $sql = "UPDATE administrateurs SET last_login = NOW() WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $admin['id'], PDO::PARAM_INT);
            $stmt->execute();
            
            // Stocker les infos en session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['login_time'] = time();
            
            return $admin['id'];
        }
        
        return false;
    }
    
    /**
     * Déconnecte l'administrateur
     * @return void
     */
    public function logout() {
        session_destroy();
    }
    
    /**
     * Vérifie si l'admin est connecté
     * @return bool - true si connecté
     */
    public function isLoggedIn() {
        return isset($_SESSION['admin_id']) && 
               isset($_SESSION['login_time']) && 
               (time() - $_SESSION['login_time']) < SESSION_TIMEOUT;
    }
    
    /**
     * Récupère l'ID de l'admin connecté
     * @return int|false - ID ou false
     */
    public function getLoggedInId() {
        return $this->isLoggedIn() ? $_SESSION['admin_id'] : false;
    }
    
    /**
     * Récupère les infos de l'admin connecté
     * @return array|false - Données ou false
     */
    public function getLoggedInAdmin() {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $sql = "SELECT * FROM administrateurs WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['admin_id'], PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Change le mot de passe d'un admin
     * @param int $adminId - ID de l'admin
     * @param string $oldPassword - Ancien mot de passe
     * @param string $newPassword - Nouveau mot de passe
     * @return bool - true si succès
     */
    public function changePassword($adminId, $oldPassword, $newPassword) {
        // Récupérer l'admin
        $sql = "SELECT password FROM administrateurs WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $adminId, PDO::PARAM_INT);
        $stmt->execute();
        $admin = $stmt->fetch();
        
        // Vérifier l'ancien mot de passe
        if (!$admin || !verifyPassword($oldPassword, $admin['password'])) {
            return false;
        }
        
        // Mettre à jour le nouveau mot de passe
        $hashedPassword = hashPassword($newPassword);
        $sql = "UPDATE administrateurs SET password = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->bindValue(':id', $adminId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
?>