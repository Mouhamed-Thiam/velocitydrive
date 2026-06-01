<?php
/**
 * PAGE DE CONTACT
 * Formulaire de contact simple
 */

require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'config/security.php';

$message = '';
$carId = intval($_GET['car'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $message = 'Erreur de sécurité. Veuillez réessayer.';
    } else {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $subject = sanitize($_POST['subject'] ?? '');
        $message_text = sanitize($_POST['message'] ?? '');
        
        // Validation basique
        if (empty($name) || empty($email) || empty($message_text)) {
            $message = 'Veuillez remplir tous les champs obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Adresse email invalide.';
        } else {
            // Envoyer l'email (à implémenter avec mail() ou une librairie)
            // Pour la démo, nous affichons simplement un message de succès
            $message = 'Merci! Votre message a été envoyé avec succès. Nous vous recontacterons bientôt.';
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
    <title>Contact - Car Showroom</title>
    <meta name="description" content="Contactez-nous pour plus d'informations sur nos véhicules et services.">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .contact-section {
            padding: 60px 0;
        }
        
        .contact-form {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .contact-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 40px;
            color: white;
        }
        
        .contact-info-item {
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
        }
        
        .contact-info-item i {
            font-size: 1.5rem;
            width: 30px;
        }
        
        .contact-info-item h5 {
            color: white;
            margin-bottom: 5px;
        }
        
        .form-control,
        .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 15px;
        }
        
        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
                <i class="fas fa-envelope"></i> Nous Contacter
            </h1>
            <p class="text-muted">Des questions? Nous sommes ici pour vous aider!</p>
        </div>
    </section>
    
    <!-- SECTION CONTACT -->
    <section class="contact-section">
        <div class="container">
            <div class="row g-4">
                <!-- FORMULAIRE -->
                <div class="col-lg-8">
                    <div class="contact-form">
                        <h3 class="mb-4">Envoyez-nous un message</h3>
                        
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> <?= $message ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            
                            <!-- Nom -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <!-- Téléphone -->
                            <div class="mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                            
                            <!-- Sujet -->
                            <div class="mb-3">
                                <label for="subject" class="form-label">Sujet</label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="Ex: Renseignements sur un véhicule">
                            </div>
                            
                            <!-- Message -->
                            <div class="mb-3">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="6" placeholder="Votre message..." required></textarea>
                            </div>
                            
                            <!-- Bouton -->
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Envoyer le message
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- INFORMATIONS DE CONTACT -->
                <div class="col-lg-4">
                    <div class="contact-info">
                        <h4 class="mb-4">Informations de Contact</h4>
                        
                        <!-- Téléphone -->
                        <div class="contact-info-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <h5>Téléphone</h5>
                                <p class="mb-0">+33 1 23 45 67 89</p>
                                <p class="mb-0 small" style="opacity: 0.9;">Lun-Ven: 9h-18h</p>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="contact-info-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h5>Email</h5>
                                <p class="mb-0">info@carshowroom.fr</p>
                                <p class="mb-0 small" style="opacity: 0.9;">Réponse sous 24h</p>
                            </div>
                        </div>
                        
                        <!-- Adresse -->
                        <div class="contact-info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <h5>Adresse</h5>
                                <p class="mb-0">123 Rue de l'Automobile</p>
                                <p class="mb-0">75000 Paris, France</p>
                            </div>
                        </div>
                        
                        <!-- Horaires -->
                        <div class="contact-info-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <h5>Horaires</h5>
                                <p class="mb-0 small">Lun-Ven: 9h00 - 18h00</p>
                                <p class="mb-0 small">Sam: 10h00 - 17h00</p>
                                <p class="mb-0 small">Dim: Fermé</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>