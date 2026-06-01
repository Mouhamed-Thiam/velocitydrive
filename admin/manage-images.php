<?php
/**
 * Page de gestion des images d'une voiture
 * Upload multiple, suppression, réorganisation
 */

require_once 'session-check.php';
require_once '../classes/Car.php';
require_once '../classes/Image.php';

$car = new Car($pdo);
$image = new Image($pdo);

$error = '';
$success = '';
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

// Récupérer les images existantes
$images = $image->getCarImages($carId);

// Traiter l'upload de fichiers
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Action : Upload multiple d'images
    if ($_POST['action'] === 'upload' && isset($_FILES['images'])) {
        
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            $error = 'Erreur de sécurité. Veuillez réessayer.';
        } else {
            // Nombre de fichiers uploadés
            $fileCount = count($_FILES['images']['name']);
            $uploadedCount = 0;
            
            // Boucler sur chaque fichier
            for ($i = 0; $i < $fileCount; $i++) {
                // Créer un tableau pour chaque fichier
                $file = [
                    'name' => $_FILES['images']['name'][$i],
                    'tmp_name' => $_FILES['images']['tmp_name'][$i],
                    'size' => $_FILES['images']['size'][$i],
                    'error' => $_FILES['images']['error'][$i],
                    'type' => $_FILES['images']['type'][$i]
                ];
                
                // Essayer d'uploader le fichier
                if ($image->upload($file, $carId)) {
                    $uploadedCount++;
                }
            }
            
            if ($uploadedCount > 0) {
                $success = "$uploadedCount image(s) téléchargée(s) avec succès!";
                // Rafraîchir la liste des images
                $images = $image->getCarImages($carId);
            } else {
                $error = 'Erreur lors du téléchargement des images.';
            }
        }
    }
    
    // Action : Supprimer une image
    elseif ($_POST['action'] === 'delete' && isset($_POST['image_id'])) {
        
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            $error = 'Erreur de sécurité. Veuillez réessayer.';
        } else {
            $imageId = intval($_POST['image_id']);
            
            if ($image->delete($imageId)) {
                $success = 'Image supprimée avec succès!';
                $images = $image->getCarImages($carId);
            } else {
                $error = 'Erreur lors de la suppression de l\'image.';
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
    <title>Gérer les images - Car Showroom Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .gallery-item {
            position: relative;
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }
        
        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
            gap: 10px;
        }
        
        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }
        
        .gallery-overlay button {
            background: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .gallery-overlay button:hover {
            background: #f0f0f0;
        }
        
        .dropzone {
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background: #f9f9ff;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .dropzone:hover,
        .dropzone.dragover {
            background: #f0f0ff;
            border-color: #764ba2;
        }
        
        .dropzone input {
            display: none;
        }
    </style>
</head>
<body>
    <?php include '../includes/admin-header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/admin-sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h1 class="h2 mb-4">
                    <i class="fas fa-images"></i> Gérer les images - 
                    <span class="text-muted"><?= sanitize($carData['marque'] . ' ' . $carData['modele']) ?></span>
                </h1>
                
                <!-- Afficher les messages -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Section d'upload -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-cloud-upload-alt"></i> Ajouter des images
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <input type="hidden" name="action" value="upload">
                            
                            <!-- Zone de drag & drop -->
                            <div class="dropzone" id="dropzone">
                                <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                <p class="mb-2"><strong>Cliquez ou glissez des images ici</strong></p>
                                <p class="text-muted small">Format: JPG, PNG, GIF | Taille max: 5MB par image</p>
                                <input type="file" id="images" name="images[]" multiple accept="image/*">
                            </div>
                            
                            <!-- Apérçu des fichiers sélectionnés -->
                            <div id="filePreview" class="mt-3"></div>
                            
                            <!-- Bouton d'upload -->
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>
                                    <i class="fas fa-upload"></i> Télécharger les images
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Galerie d'images existantes -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-photo-video"></i> Galerie (<?= count($images) ?> image(s))
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($images) > 0): ?>
                            <div class="row">
                                <?php foreach ($images as $img): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="gallery-item">
                                        <img src="<?= BASE_URL . $img['chemin_image'] ?>" alt="Image de voiture">
                                        <div class="gallery-overlay">
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                                                <button type="submit" class="btn-delete" onclick="return confirm('Êtes-vous sûr?')">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle"></i> Aucune image pour cette voiture. Ajoutez-en au moins une!
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Lien de retour -->
                <div class="mt-4">
                    <a href="edit-car.php?id=<?= $carId ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la modification
                    </a>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Zone de drag & drop
        const dropzone = document.getElementById('dropzone');
        const input = document.getElementById('images');
        const filePreview = document.getElementById('filePreview');
        const uploadBtn = document.getElementById('uploadBtn');
        
        // Ouvrir le sélecteur de fichiers au clic
        dropzone.addEventListener('click', () => input.click());
        
        // Empêcher le comportement par défaut du drag & drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // Gérer le drag over
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => {
                dropzone.classList.add('dragover');
            });
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => {
                dropzone.classList.remove('dragover');
            });
        });
        
        // Gérer les fichiers lâchés
        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            input.files = dt.files;
            updateFilePreview();
        });
        
        // Gérer la sélection de fichiers
        input.addEventListener('change', updateFilePreview);
        
        function updateFilePreview() {
            filePreview.innerHTML = '';
            let hasFiles = false;
            
            if (input.files.length > 0) {
                hasFiles = true;
                const html = `<div class="alert alert-info">
                    <i class="fas fa-check-circle"></i> ${input.files.length} fichier(s) sélectionné(s)
                </div>`;
                filePreview.innerHTML = html;
            }
            
            uploadBtn.disabled = !hasFiles;
        }
    </script>
</body>
</html>