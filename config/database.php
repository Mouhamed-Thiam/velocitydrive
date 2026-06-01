<?php
/**
 * Configuration de la Base de Données
 * Utilise PDO pour une connexion sécurisée
 * Gestion centralisée de la connexion à MySQL
 */

// Constantes de connexion
define('DB_HOST', 'localhost');      // Serveur MySQL
define('DB_USER', 'root');           // Utilisateur MySQL
define('DB_PASS', '');               // Mot de passe (vide par défaut dans XAMPP)
define('DB_NAME', 'car_showroom');   // Nom de la base de données

// Options PDO pour la sécurité
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lève des exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Retour en tableau associatif
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Utilise les vraies requêtes préparées
];

try {
    // Création de la connexion PDO
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        $options
    );
    
    // La connexion est réussie
} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Fonction utile pour éviter les injections XSS
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Fonction pour vérifier la connexion
function isConnected() {
    global $pdo;
    return $pdo !== null;
}
?>