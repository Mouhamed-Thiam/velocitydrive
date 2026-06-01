<?php
/**
 * Fonctions de sécurité
 * Hachage de mots de passe, génération de tokens, etc.
 */

/**
 * Hache un mot de passe avec bcrypt
 * @param string $password - Mot de passe en clair
 * @return string - Mot de passe haché
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Vérifie un mot de passe contre son hash
 * @param string $password - Mot de passe en clair
 * @param string $hash - Hash stocké en BD
 * @return bool - true si le mot de passe est correct
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Génère un token CSRF pour éviter les attaques
 * @return string - Token aléatoire
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie la validité d'un token CSRF
 * @param string $token - Token à vérifier
 * @return bool - true si valide
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Échappe les données pour éviter XSS
 * @param string $data - Données à échapper
 * @return string - Données échappées
 */
function sanitize($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Valide une adresse email
 * @param string $email - Email à valider
 * @return bool - true si valide
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide un entier
 * @param mixed $value - Valeur à valider
 * @return bool - true si c'est un entier valide
 */
function isValidInt($value) {
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

/**
 * Génère un nom de fichier sécurisé
 * @param string $original - Nom original du fichier
 * @return string - Nom sécurisé et unique
 */
function generateSafeFilename($original) {
    $ext = pathinfo($original, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $ext;
}
?>