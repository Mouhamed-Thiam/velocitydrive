<?php
/**
 * Page de suppression d'une voiture
 * Supprime la voiture et toutes ses images associées
 */

require_once 'session-check.php';
require_once '../classes/Car.php';
require_once '../classes/Image.php';

$car = new Car($pdo);
$image = new Image($pdo);

// Récupérer l'ID de la voiture
$carId = intval($_GET['id'] ?? 0);

if ($carId === 0) {
    header('Location: dashboard.php');
    exit();
}

// Récupérer les données de la voiture
$carData = $car->getById($carId);

if (!$carData) {
    header('Location: dashboard.php');
    exit();
}

// Récupérer toutes les images associées
$images = $image->getCarImages($carId);

// Supprimer les fichiers images du disque
foreach ($images as $img) {
    $filepath = ROOT_PATH . $img['chemin_image'];
    if (file_exists($filepath)) {
        unlink($filepath);
    }
}

// Supprimer la voiture (les images en BD sont supprimées automatiquement via CASCADE)
if ($car->delete($carId)) {
    header('Location: dashboard.php?deleted=1');
} else {
    header('Location: dashboard.php?error=1');
}
exit();
?>