<?php
/**
 * Tableau de bord administrateur
 * Affiche les statistiques et actions de gestion
 */

require_once 'session-check.php';
require_once '../classes/Car.php';

// Créer les instances des classes
$car = new Car($pdo);

// Récupérer les statistiques
$totalCars = $car->countAll();
$priceRange = $car->getPriceRange();
$cars = $car->getAll(5, 0); // Dernières 5 voitures

// Récupérer les infos de l'admin connecté
$loggedInAdmin = $admin->getLoggedInAdmin();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Car Showroom Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <!-- Barre de navigation -->
    <?php include '../includes/admin-header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Barre latérale -->
            <?php include '../includes/admin-sidebar.php'; ?>
            
            <!-- Contenu principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- En-tête -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2">📊 Tableau de Bord</h1>
                    <a href="add-car.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter une voiture
                    </a>
                </div>
                
                <!-- Cartes de statistiques -->
                <div class="row mb-4">
                    <!-- Nombre total de voitures -->
                    <div class="col-md-4 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white-50">Voitures disponibles</h6>
                                        <h2 class="card-text"><?= $totalCars ?></h2>
                                    </div>
                                    <i class="fas fa-car fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Prix minimum -->
                    <div class="col-md-4 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white-50">Prix minimum</h6>
                                        <h2 class="card-text"><?= number_format($priceRange['min_price'], 0) ?> €</h2>
                                    </div>
                                    <i class="fas fa-arrow-down fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Prix maximum -->
                    <div class="col-md-4 mb-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white-50">Prix maximum</h6>
                                        <h2 class="card-text"><?= number_format($priceRange['max_price'], 0) ?> €</h2>
                                    </div>
                                    <i class="fas fa-arrow-up fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Dernières voitures ajoutées -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i> Dernières voitures ajoutées
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($cars) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Marque & Modèle</th>
                                            <th>Année</th>
                                            <th>Prix</th>
                                            <th>Kilométrage</th>
                                            <th>Carburant</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cars as $car_item): ?>
                                        <tr>
                                            <td>
                                                <strong><?= sanitize($car_item['marque']) ?></strong><br>
                                                <small class="text-muted"><?= sanitize($car_item['modele']) ?></small>
                                            </td>
                                            <td><?= $car_item['annee'] ?></td>
                                            <td><strong><?= number_format($car_item['prix'], 0) ?> €</strong></td>
                                            <td><?= number_format($car_item['kilometrage']) ?> km</td>
                                            <td><?= sanitize($car_item['carburant']) ?></td>
                                            <td>
                                                <a href="edit-car.php?id=<?= $car_item['id'] ?>" class="btn btn-sm btn-info" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="manage-images.php?id=<?= $car_item['id'] ?>" class="btn btn-sm btn-warning" title="Gérer les images">
                                                    <i class="fas fa-images"></i>
                                                </a>
                                                <a href="delete-car.php?id=<?= $car_item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr?')" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle"></i> Aucune voiture enregistrée. <a href="add-car.php">Ajouter la première</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>