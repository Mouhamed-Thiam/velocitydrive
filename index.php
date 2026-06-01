<?php
/**
 * PAGE D'ACCUEIL
 * Page principale du site avec bannière et présentation
 */

require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'config/security.php';
require_once 'classes/Car.php';

$car = new Car($pdo);

// Récupérer les dernières voitures (6 pour la page d'accueil)
$latestCars = $car->getAll(6, 0);

// Récupérer les statistiques
$totalCars = $car->countAll();
$priceRange = $car->getPriceRange();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Car Showroom | Votre Concession Automobile</title>
    <meta name="description" content="Découvrez notre large sélection de véhicules de qualité. Trouvez la voiture de vos rêves chez Car Showroom.">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }
        
        .hero .btn {
            margin: 0 10px;
            padding: 12px 30px;
            font-size: 1.1rem;
        }
        
        .btn-primary {
            background: white;
            color: #667eea;
            border: none;
        }
        
        .btn-primary:hover {
            background: #f0f0f0;
            color: #667eea;
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
        }
        
        .btn-secondary:hover {
            background: white;
            color: #667eea;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(30px); }
        }
        
        /* Section statistiques */
        .stats-section {
            padding: 50px 0;
            background: #f8f9fa;
        }
        
        .stat-item {
            text-align: center;
            padding: 30px;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .stat-label {
            font-size: 1.1rem;
            color: #666;
            margin-top: 10px;
        }
        
        /* Section voitures en avant */
        .featured-section {
            padding: 60px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
            padding-bottom: 20px;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
        
        .car-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .car-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        
        .car-card .car-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            position: relative;
            overflow: hidden;
        }
        
        .car-card .car-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0);
            transition: all 0.3s ease;
        }
        
        .car-card:hover .car-image::after {
            background: rgba(0, 0, 0, 0.2);
        }
        
        .car-info {
            padding: 20px;
        }
        
        .car-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .car-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .car-specs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
            color: #666;
        }
        
        .car-specs span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .car-card .btn {
            width: 100%;
            border-radius: 8px;
        }
        
        /* Call to action */
        .cta-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            border-radius: 12px;
            margin: 60px 0;
        }
        
        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: white;
        }
        
        .cta-section p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: rgba(255, 255, 255, 0.9);
        }
    </style>
</head>
<body>
    <!-- NAVIGATION -->
    <?php include 'includes/header.php'; ?>
    
    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-content">
            <div class="container">
                <h1>
                    <i class="fas fa-car"></i> Bienvenue chez Car Showroom
                </h1>
                <p>Découvrez notre sélection exclusive de véhicules de qualité</p>
                <div class="mt-4">
                    <a href="cars.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-search"></i> Voir nos voitures
                    </a>
                    <a href="contact.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-envelope"></i> Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- SECTION STATISTIQUES -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number"><?= $totalCars ?></div>
                        <div class="stat-label">Voitures disponibles</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">
                            <?= number_format($priceRange['min_price'], 0) ?> €
                        </div>
                        <div class="stat-label">À partir de</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">15 ans</div>
                        <div class="stat-label">D'expérience</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- SECTION VOITURES EN AVANT -->
    <section class="featured-section">
        <div class="container">
            <div class="section-title">
                <h2>Nos Voitures en Avant</h2>
                <p class="text-muted">Sélection de nos véhicules les plus populaires</p>
            </div>
            
            <?php if (count($latestCars) > 0): ?>
            <div class="row g-4">
                <?php foreach ($latestCars as $vehicle): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card car-card">
                        <!-- Image de la voiture -->
                        <div style="position: relative; height: 250px; overflow: hidden; background: #f0f0f0;">
                            <img 
                                src="<?= BASE_URL . ($vehicle['image'] ?: 'assets/images/placeholder.jpg') ?>" 
                                alt="<?= sanitize($vehicle['marque'] . ' ' . $vehicle['modele']) ?>"
                                class="car-image"
                                style="width: 100%; height: 100%; object-fit: cover;"
                            >
                            <!-- Badge pour les nouvelles voitures -->
                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                <i class="fas fa-star"></i> Populaire
                            </span>
                        </div>
                        
                        <!-- Infos de la voiture -->
                        <div class="car-info">
                            <h5 class="car-title">
                                <?= sanitize($vehicle['marque'] . ' ' . $vehicle['modele']) ?>
                            </h5>
                            
                            <div class="car-price">
                                <?= number_format($vehicle['prix'], 0) ?> €
                            </div>
                            
                            <!-- Spécifications -->
                            <div class="car-specs">
                                <span title="Année">
                                    <i class="fas fa-calendar"></i> <?= $vehicle['annee'] ?>
                                </span>
                                <span title="Kilométrage">
                                    <i class="fas fa-tachometer-alt"></i> <?= number_format($vehicle['kilometrage']) ?> km
                                </span>
                                <span title="Carburant">
                                    <i class="fas fa-gas-pump"></i> <?= sanitize($vehicle['carburant']) ?>
                                </span>
                            </div>
                            
                            <!-- Bouton détails -->
                            <a 
                                href="car-details.php?id=<?= $vehicle['id'] ?>" 
                                class="btn btn-primary"
                            >
                                <i class="fas fa-info-circle"></i> Voir les détails
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Bouton voir plus -->
            <div class="text-center mt-5">
                <a href="cars.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-list"></i> Voir toutes les voitures
                </a>
            </div>
            <?php else: ?>
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p>Aucune voiture disponible pour le moment.</p>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- SECTION APPEL À L'ACTION -->
    <section class="cta-section">
        <div class="container">
            <h2>Prêt à trouver votre voiture idéale?</h2>
            <p>Parcourez notre catalogu complet et découvrez les meilleures offres du marché.</p>
            <a href="cars.php" class="btn btn-light btn-lg">
                <i class="fas fa-search"></i> Commencer la recherche
            </a>
        </div>
    </section>
    
    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>