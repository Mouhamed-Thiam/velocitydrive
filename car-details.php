<?php
/**
 * PAGE DE DÉTAILS D'UNE VOITURE
 * Affiche toutes les informations détaillées d'un véhicule
 */

require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'config/security.php';
require_once 'classes/Car.php';
require_once 'classes/Image.php';

$car = new Car($pdo);
$image = new Image($pdo);

// Récupérer l'ID de la voiture
$carId = intval($_GET['id'] ?? 0);

if ($carId === 0) {
    header('Location: cars.php');
    exit();
}

// Récupérer les données de la voiture
$carData = $car->getById($carId);

if (!$carData) {
    http_response_code(404);
    die('Voiture non trouvée');
}

// Récupérer les images de la voiture
$images = $image->getCarImages($carId);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($carData['marque'] . ' ' . $carData['modele'] . ' ' . $carData['annee']) ?> - Car Showroom</title>
    <meta name="description" content="<?= sanitize(substr($carData['description'], 0, 150)) ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .gallery-main {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            background: #f0f0f0;
            height: 500px;
            margin-bottom: 20px;
        }
        
        .gallery-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }
        
        .gallery-main img:hover {
            cursor: zoom-in;
        }
        
        .gallery-thumbnails {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding: 10px 0;
        }
        
        .gallery-thumbnails img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .gallery-thumbnails img:hover,
        .gallery-thumbnails img.active {
            border-color: #667eea;
            transform: scale(1.05);
        }
        
        .spec-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        
        .spec-label {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .spec-value {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
        }
        
        .price-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin: 30px 0;
        }
        
        .price-label {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        
        .price-value {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .action-buttons .btn {
            flex: 1;
            min-width: 150px;
        }
        
        .description-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin: 30px 0;
        }
        
        .description-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .specs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }
        
        .breadcrumb {
            margin: 20px 0;
            background: none;
            padding: 0;
        }
        
        .breadcrumb-item.active {
            color: #667eea;
        }
        
        .badge-stock {
            font-size: 1rem;
            padding: 8px 15px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .gallery-main {
                height: 300px;
            }
            
            .price-value {
                font-size: 2rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .action-buttons .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- NAVIGATION -->
    <?php include 'includes/header.php'; ?>
    
    <!-- BREADCRUMB -->
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
                <li class="breadcrumb-item"><a href="cars.php">Voitures</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= sanitize($carData['marque'] . ' ' . $carData['modele']) ?>
                </li>
            </ol>
        </nav>
    </div>
    
    <!-- CONTENU PRINCIPAL -->
    <div class="container my-5">
        <div class="row">
            <!-- GALERIE (Gauche) -->
            <div class="col-lg-7 mb-4 mb-lg-0">
                <!-- Image principale -->
                <div class="gallery-main">
                    <img 
                        id="mainImage"
                        src="<?= BASE_URL . ($carData['image'] ?: 'assets/images/placeholder.jpg') ?>" 
                        alt="<?= sanitize($carData['marque'] . ' ' . $carData['modele']) ?>"
                        data-lightbox="<?= BASE_URL . ($carData['image'] ?: 'assets/images/placeholder.jpg') ?>"
                    >
                </div>
                
                <!-- Miniatures -->
                <?php if (count($images) > 0): ?>
                <div class="gallery-thumbnails">
                    <?php foreach ($images as $img): ?>
                    <img 
                        src="<?= BASE_URL . $img['chemin_image'] ?>" 
                        alt="Thumbnail"
                        class="thumb-image <?= $loop->first ? 'active' : '' ?>"
                        data-image="<?= BASE_URL . $img['chemin_image'] ?>"
                        title="Cliquez pour agrandir"
                    >
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- INFO (Droite) -->
            <div class="col-lg-5">
                <!-- Badge disponibilité -->
                <span class="badge bg-success badge-stock">
                    <i class="fas fa-check-circle"></i> Disponible
                </span>
                
                <!-- Titre -->
                <h1 class="mb-3">
                    <?= sanitize($carData['marque']) ?><br>
                    <span class="text-muted" style="font-size: 1.5rem;">
                        <?= sanitize($carData['modele']) ?>
                    </span>
                </h1>
                
                <!-- Année et kilométrage -->
                <p class="text-muted mb-4">
                    <i class="fas fa-calendar"></i> <?= $carData['annee'] ?> - 
                    <i class="fas fa-tachometer-alt"></i> <?= number_format($carData['kilometrage']) ?> km
                </p>
                
                <!-- PRIX -->
                <div class="price-section">
                    <div class="price-label">Prix</div>
                    <div class="price-value"><?= number_format($carData['prix'], 0) ?> €</div>
                    <small class="d-block" style="opacity: 0.9;">TVA comprise</small>
                </div>
                
                <!-- SPÉCIFICATIONS CLÉS -->
                <div class="specs-grid">
                    <!-- Année -->
                    <div class="spec-card">
                        <div class="spec-label">
                            <i class="fas fa-calendar"></i> Année
                        </div>
                        <div class="spec-value"><?= $carData['annee'] ?></div>
                    </div>
                    
                    <!-- Kilométrage -->
                    <div class="spec-card">
                        <div class="spec-label">
                            <i class="fas fa-tachometer-alt"></i> Kilométrage
                        </div>
                        <div class="spec-value"><?= number_format($carData['kilometrage']) ?> km</div>
                    </div>
                    
                    <!-- Carburant -->
                    <div class="spec-card">
                        <div class="spec-label">
                            <i class="fas fa-gas-pump"></i> Carburant
                        </div>
                        <div class="spec-value"><?= sanitize($carData['carburant']) ?></div>
                    </div>
                    
                    <!-- Transmission -->
                    <div class="spec-card">
                        <div class="spec-label">
                            <i class="fas fa-cog"></i> Transmission
                        </div>
                        <div class="spec-value"><?= sanitize($carData['transmission']) ?></div>
                    </div>
                    
                    <!-- Couleur -->
                    <div class="spec-card">
                        <div class="spec-label">
                            <i class="fas fa-palette"></i> Couleur
                        </div>
                        <div class="spec-value"><?= sanitize($carData['couleur']) ?></div>
                    </div>
                    
                    <!-- Type de véhicule -->
                    <div class="spec-card">
                        <div class="spec-label">
                            <i class="fas fa-car"></i> Type
                        </div>
                        <div class="spec-value"><?= sanitize($carData['type_vehicule']) ?></div>
                    </div>
                </div>
                
                <!-- BOUTONS D'ACTION -->
                <div class="action-buttons mt-4">
                    <a href="contact.php?car=<?= $carId ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-envelope"></i> Nous contacter
                    </a>
                    <button class="btn btn-outline-primary btn-lg" onclick="window.print()">
                        <i class="fas fa-print"></i> Imprimer
                    </button>
                </div>
            </div>
        </div>
        
        <!-- SECTION DESCRIPTION -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="description-section">
                    <div class="description-title">
                        <i class="fas fa-align-left"></i> Description complète
                    </div>
                    <p class="lead">
                        <?= nl2br(sanitize($carData['description'])) ?: 'Aucune description disponible.' ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- CARACTÉRISTIQUES TECHNIQUES COMPLÈTES -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="description-section">
                    <div class="description-title">
                        <i class="fas fa-tools"></i> Caractéristiques techniques
                    </div>
                    
                    <div class="specs-grid">
                        <!-- Cylindrée -->
                        <?php if (!empty($carData['cylindree'])): ?>
                        <div class="spec-card">
                            <div class="spec-label">
                                <i class="fas fa-engine"></i> Cylindrée
                            </div>
                            <div class="spec-value"><?= sanitize($carData['cylindree']) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Puissance -->
                        <?php if (!empty($carData['puissance'])): ?>
                        <div class="spec-card">
                            <div class="spec-label">
                                <i class="fas fa-bolt"></i> Puissance
                            </div>
                            <div class="spec-value"><?= sanitize($carData['puissance']) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Portes -->
                        <?php if ($carData['portes'] > 0): ?>
                        <div class="spec-card">
                            <div class="spec-label">
                                <i class="fas fa-door-open"></i> Nombre de portes
                            </div>
                            <div class="spec-value"><?= $carData['portes'] ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- BOUTONS DE NAVIGATION -->
        <div class="row mt-5">
            <div class="col-12 text-center">
                <a href="cars.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>
    </div>
    
    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>