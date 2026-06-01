<?php
/**
 * PAGE LISTING DES VOITURES
 * Affiche toutes les voitures avec recherche et filtrage
 */

require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'config/security.php';
require_once 'classes/Car.php';

$car = new Car($pdo);

// Récupérer les paramètres de recherche/filtre
$search = sanitize($_GET['search'] ?? '');
$brand = sanitize($_GET['brand'] ?? '');
$minPrice = floatval($_GET['min_price'] ?? 0);
$maxPrice = floatval($_GET['max_price'] ?? 999999);
$fuelType = sanitize($_GET['fuel'] ?? '');

// Récupérer les marques et prix disponibles
$brands = $car->getBrands();
$priceRange = $car->getPriceRange();

// Effectuer la recherche
$cars = $car->search($brand, $minPrice, $maxPrice, $fuelType);

// Si recherche par texte, filtrer les résultats
if (!empty($search)) {
    $cars = array_filter($cars, function($item) use ($search) {
        return stripos($item['marque'], $search) !== false || 
               stripos($item['modele'], $search) !== false ||
               stripos($item['description'], $search) !== false;
    });
}

// Paginer les résultats
$totalCars = count($cars);
$page = intval($_GET['page'] ?? 1);
$itemsPerPage = ITEMS_PER_PAGE;
$totalPages = ceil($totalCars / $itemsPerPage);
$offset = ($page - 1) * $itemsPerPage;
$paginatedCars = array_slice($cars, $offset, $itemsPerPage);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Voitures - Car Showroom</title>
    <meta name="description" content="Parcourez notre sélection complète de véhicules d'occasion. Trouvez la voiture parfaite avec nos filtres avancés.">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .car-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            position: sticky;
            top: 80px;
        }
        
        .filter-title {
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-group {
            margin-bottom: 20px;
        }
        
        .filter-group label {
            font-size: 0.95rem;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-control,
        .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 15px;
        }
        
        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .price-range-display {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 15px 0;
            font-weight: 600;
            color: #667eea;
        }
        
        .car-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .car-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            grid-column: 1 / -1;
        }
        
        @media (max-width: 768px) {
            .filter-section {
                position: static;
            }
            
            .car-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
    </style>
</head>
<body>
    <!-- NAVIGATION -->
    <?php include 'includes/header.php'; ?>
    
    <!-- PAGE TITLE -->
    <section class="py-5 bg-light">
        <div class="container">
            <h1 class="mb-2">
                <i class="fas fa-list"></i> Nos Voitures
            </h1>
            <p class="text-muted">
                <?= $totalCars ?> voiture(s) disponible(s)
                <?php if (!empty($search)): ?>
                    pour "<strong><?= sanitize($search) ?></strong>"
                <?php endif; ?>
            </p>
        </div>
    </section>
    
    <!-- CONTENU PRINCIPAL -->
    <div class="container my-5">
        <div class="row">
            <!-- FILTRES (SIDEBAR) -->
            <div class="col-lg-3 mb-4">
                <div class="filter-section">
                    <form id="searchForm" method="GET" action="cars.php">
                        <!-- Titre -->
                        <div class="filter-title">
                            <i class="fas fa-sliders-h"></i> Filtres
                        </div>
                        
                        <!-- Recherche textuelle -->
                        <div class="filter-group">
                            <label for="searchQuery">Recherche</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="searchQuery" 
                                name="search"
                                placeholder="Marque, modèle..."
                                value="<?= sanitize($search) ?>"
                            >
                        </div>
                        
                        <!-- Filtre par marque -->
                        <div class="filter-group">
                            <label for="brand">Marque</label>
                            <select class="form-select" id="brand" name="brand">
                                <option value="">Toutes les marques</option>
                                <?php foreach ($brands as $b): ?>
                                    <option value="<?= $b ?>" <?= $brand === $b ? 'selected' : '' ?>>
                                        <?= $b ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Filtre par prix -->
                        <div class="filter-group">
                            <label for="minPrice">Prix minimum: <span id="minPrice-label"><?= $minPrice ?></span> €</label>
                            <input 
                                type="range" 
                                class="form-range price-slider" 
                                id="minPrice" 
                                name="min_price"
                                min="<?= $priceRange['min_price'] ?>" 
                                max="<?= $priceRange['max_price'] ?>"
                                value="<?= $minPrice ?>"
                            >
                        </div>
                        
                        <div class="filter-group">
                            <label for="maxPrice">Prix maximum: <span id="maxPrice-label"><?= $maxPrice ?></span> €</label>
                            <input 
                                type="range" 
                                class="form-range price-slider" 
                                id="maxPrice" 
                                name="max_price"
                                min="<?= $priceRange['min_price'] ?>" 
                                max="<?= $priceRange['max_price'] ?>"
                                value="<?= $maxPrice ?>"
                            >
                        </div>
                        
                        <!-- Filtre par carburant -->
                        <div class="filter-group">
                            <label for="fuel">Carburant</label>
                            <select class="form-select" id="fuel" name="fuel">
                                <option value="">Tous les carburants</option>
                                <?php foreach ($FUEL_TYPES as $fuel): ?>
                                    <option value="<?= $fuel ?>" <?= $fuelType === $fuel ? 'selected' : '' ?>>
                                        <?= $fuel ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Boutons -->
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                        <a href="cars.php" class="btn btn-secondary w-100">
                            <i class="fas fa-times"></i> Réinitialiser
                        </a>
                    </form>
                </div>
            </div>
            
            <!-- GRILLE DE VOITURES -->
            <div class="col-lg-9">
                <?php if (count($paginatedCars) > 0): ?>
                    <div class="car-grid">
                        <?php foreach ($paginatedCars as $vehicle): ?>
                        <div class="card car-card">
                            <!-- Image -->
                            <div style="height: 200px; overflow: hidden; background: #f0f0f0; position: relative;">
                                <img 
                                    src="<?= BASE_URL . ($vehicle['image'] ?: 'assets/images/placeholder.jpg') ?>" 
                                    alt="<?= sanitize($vehicle['marque'] . ' ' . $vehicle['modele']) ?>"
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                >
                            </div>
                            
                            <!-- Infos -->
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?= sanitize($vehicle['marque']) ?> <br>
                                    <small class="text-muted"><?= sanitize($vehicle['modele']) ?></small>
                                </h5>
                                
                                <div class="my-3">
                                    <div class="h4 text-primary mb-0">
                                        <?= number_format($vehicle['prix'], 0) ?> €
                                    </div>
                                </div>
                                
                                <!-- Spécifications -->
                                <div class="small text-muted mb-3">
                                    <p class="mb-1">
                                        <i class="fas fa-calendar"></i> <?= $vehicle['annee'] ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="fas fa-tachometer-alt"></i> <?= number_format($vehicle['kilometrage']) ?> km
                                    </p>
                                    <p class="mb-1">
                                        <i class="fas fa-gas-pump"></i> <?= sanitize($vehicle['carburant']) ?>
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-cog"></i> <?= sanitize($vehicle['transmission']) ?>
                                    </p>
                                </div>
                                
                                <!-- Bouton détails -->
                                <a 
                                    href="car-details.php?id=<?= $vehicle['id'] ?>" 
                                    class="btn btn-primary btn-sm w-100"
                                >
                                    <i class="fas fa-arrow-right"></i> Voir les détails
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- PAGINATION -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Pagination">
                        <ul class="pagination justify-content-center">
                            <!-- Page précédente -->
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="cars.php?page=<?= max(1, $page - 1) ?>">
                                    <i class="fas fa-chevron-left"></i> Précédent
                                </a>
                            </li>
                            
                            <!-- Numéros de pages -->
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                                    <a class="page-link" href="cars.php?page=<?= $i ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Page suivante -->
                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="cars.php?page=<?= min($totalPages, $page + 1) ?>">
                                    Suivant <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                
                <?php else: ?>
                    <!-- Aucun résultat -->
                    <div class="no-results">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="mb-3">Aucune voiture trouvée</h4>
                        <p class="text-muted mb-3">
                            Essayez de modifier vos critères de recherche ou de filtrage.
                        </p>
                        <a href="cars.php" class="btn btn-primary">
                            <i class="fas fa-redo"></i> Réinitialiser les filtres
                        </a>
                    </div>
                <?php endif; ?>
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