<?php
/**
 * Page de déconnexion
 * Détruit la session et redirige vers le login
 */

require_once '../config/database.php';
require_once '../config/security.php';
require_once '../classes/Admin.php';

session_start();

$admin = new Admin($pdo);
$admin->logout();

header('Location: login.php?logout=1');
exit();
?>