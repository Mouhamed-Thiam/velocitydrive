<?php
/**
 * Page d'ajout d'une nouvelle voiture
 * Formulaire complet avec validation
 */

require_once 'session-check.php';
require_once '../classes/Car.php';
require_once '../classes/Image.php';

$car = new Car($pdo);
$image = new Image($pdo);
$error = '';
$success = '';

// Charger les constantes
global $FUEL_TYPES, $TRANSMISSIONS, $VEHICLE_TYPES;

// Traiter la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Erreur de sécurité. Veuillez réessayer.';
    } else {
        // Récupérer et valider les données
        $marque = sanitize($_POST['marque'] ?? '');
        $modele = sanitize($_POST['modele'] ?? '');
        $annee = intval($_POST['annee'] ?? 0);
        $prix = floatval($_POST['prix'] ?? 0);
        $kilometrage = intval($_POST['kilometrage'] ?? 0);
        $carburant = sanitize($_POST['carburant'] ?? '');
        $transmission = sanitize($_POST['transmission'] ?? '');
        $couleur = sanitize($_POST['couleur'] ?? '');
        $type_vehicule = sanitize($_POST['type_vehicule'] ?? '');
        $portes = intval($_POST['portes'] ?? 4);
        $cylindree = sanitize($_POST['cylindree'] ?? '');
        $puissance = sanitize($_POST['puissance'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        
        // Valider les champs obligatoires
        if (empty($marque) || empty($modele) || $annee === 0 || $prix === 0) {
            $error = 'Veuillez remplir tous les champs obligatoires.';
        } elseif ($prix < 0 || $kilometrage < 0 || $annee < 1900 || $annee > date('Y') + 1) {
            $error = 'Veuillez vérifier les valeurs saisies.';
        } else {
            // Gérer l'upload de l'image principale
            $mainImage = 'assets/images/placeholder.jpg';
            
            // Préparer les données pour la création
            $data = [
                'marque' => $marque,
                'modele' => $modele,
                'annee' => $annee,
                'prix' => $prix,
                'kilometrage' => $kilometrage,
                'carburant' => $carburant,
                'transmission' => $transmission,
                'description' => $description,
                'image' => $mainImage,
                'couleur' => $couleur,
                'type_vehicule' => $type_vehicule,
                'portes' => $portes,
                'cylindree' => $cylindree,
                'puissance' => $puissance
            ];
            
            // Créer la voiture
            $carId = $car->create($data);
            
            if ($carId) {
                $success = 'Voiture ajoutée avec succès!';
                // Redirection après succès
                header('Location: manage-images.php?id=' . $carId . '&success=1');
                exit();
            } else {
                $error = 'Erreur lors de l\'ajout de la voiture.';
            }
        }
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Voiture - Car Showroom Admin</title>
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
                <h1 class="h2 mb-4">
                    <i class="fas fa-plus"></i> Ajouter une nouvelle voiture
                </h1>
                
                <!-- Afficher les erreurs -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Afficher les succès -->
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Formulaire d'ajout -->
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="addCarForm">
                            <!-- Token CSRF -->
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            
                            <!-- Informations principales -->
                            <h5 class="card-title mb-4">
                                <i class="fas fa-info-circle"></i> Informations principales
                            </h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="marque" class="form-label">Marque <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="marque" name="marque" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="modele" class="form-label">Modèle <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="modele" name="modele" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="annee" class="form-label">Année <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="annee" name="annee" min="1900" max="2099" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="prix" class="form-label">Prix (€) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="prix" name="prix" step="0.01" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="kilometrage" class="form-label">Kilométrage (km) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="kilometrage" name="kilometrage" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="couleur" class="form-label">Couleur</label>
                                    <input type="text" class="form-control" id="couleur" name="couleur">
                                </div>
                            </div>
                            
                            <!-- Spécifications -->
                            <h5 class="card-title mb-4 mt-4">
                                <i class="fas fa-cog"></i> Spécifications techniques
                            </h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="carburant" class="form-label">Carburant</label>
                                    <select class="form-select" id="carburant" name="carburant">
                                        <option value="">Sélectionnez...</option>
                                        <?php foreach ($FUEL_TYPES as $fuel): ?>
                                            <option value="<?= $fuel ?>"><?= $fuel ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="transmission" class="form-label">Transmission</label>
                                    <select class="form-select" id="transmission" name="transmission">
                                        <option value="">Sélectionnez...</option>
                                        <?php foreach ($TRANSMISSIONS as $transmission): ?>
                                            <option value="<?= $transmission ?>"><?= $transmission ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="type_vehicule" class="form-label">Type de véhicule</label>
                                    <select class="form-select" id="type_vehicule" name="type_vehicule">
                                        <option value="">Sélectionnez...</option>
                                        <?php foreach ($VEHICLE_TYPES as $type): ?>
                                            <option value="<?= $type ?>"><?= $type ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="portes" class="form-label">Nombre de portes</label>
                                    <input type="number" class="form-control" id="portes" name="portes" value="4" min="2" max="8">
                                </div>
                                <div class="col-md-4">
                                    <label for="cylindree" class="form-label">Cylindrée</label>
                                    <input type="text" class="form-control" id="cylindree" name="cylindree" placeholder="1.6L">
                                </div>
                                <div class="col-md-4">
                                    <label for="puissance" class="form-label">Puissance</label>
                                    <input type="text" class="form-control" id="puissance" name="puissance" placeholder="120 ch">
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <h5 class="card-title mb-4 mt-4">
                                <i class="fas fa-align-left"></i> Description
                            </h5>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description de la voiture</label>
                                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Décrivez les caractéristiques, l'état général, les options, etc."></textarea>
                            </div>
                            
                            <!-- Boutons d'action -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Ajouter la voiture
                                </button>
                                <a href="dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/validation.js"></script>
</body>
</html>