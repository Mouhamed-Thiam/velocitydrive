<?php
/**
 * Vérification de session - À inclure en début de chaque page admin
 * Redirige vers login si pas connecté
 */

session_start();

// Inclure les fichiers de configuration
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../config/security.php';
require_once '../classes/Admin.php';

// Créer une instance de la classe Admin
$admin = new Admin($pdo);

// Vérifier la connexion
if (!$admin->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Refresher la variable de temps pour le timeout
$_SESSION['login_time'] = time();
?>