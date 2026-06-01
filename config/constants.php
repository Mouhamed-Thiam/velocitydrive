<?php
/**
 * Constantes de l'application
 * Chemins, configurations globales, etc.
 */

// Chemins de l'application
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/car_showroom/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');
define('IMAGES_PATH', ASSETS_PATH . 'images/cars/');
define('UPLOAD_DIR', 'assets/images/cars/');

// URLs
define('BASE_URL', 'http://localhost/car_showroom/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('IMAGES_URL', ASSETS_URL . 'images/cars/');

// Configuration des uploads
define('MAX_FILE_SIZE', 5242880);        // 5 MB en bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// Configuration de session
define('SESSION_TIMEOUT', 3600);         // 1 heure en secondes

// Pagination
define('ITEMS_PER_PAGE', 12);

// Marques de voitures populaires
$BRANDS = [
    'Toyota', 'Honda', 'Renault', 'Peugeot', 'Citroën',
    'BMW', 'Mercedes', 'Audi', 'Volkswagen', 'Ford',
    'Fiat', 'Hyundai', 'Kia', 'Nissan', 'Mazda'
];

// Types de carburant
$FUEL_TYPES = ['Essence', 'Diesel', 'Hybride', 'Électrique'];

// Types de transmission
$TRANSMISSIONS = ['Manuelle', 'Automatique'];

// Types de véhicules
$VEHICLE_TYPES = ['Berline', 'Citadine', 'SUV', 'Break', 'Coupé', 'Monospace'];
?>