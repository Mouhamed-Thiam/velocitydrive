<?php
/**
 * Page de connexion administrateur
 * Formulaire de login avec gestion des erreurs
 */

session_start();

// Inclure les configurations
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../config/security.php';
require_once '../classes/Admin.php';

// Créer une instance Admin
$admin = new Admin($pdo);

// Initialiser les variables
$error = '';
$username = '';

// Si déjà connecté, rediriger vers le dashboard
if ($admin->isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

// Traiter la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Erreur de sécurité. Veuillez réessayer.';
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Valider les champs
        if (empty($username) || empty($password)) {
            $error = 'Veuillez remplir tous les champs.';
        } else {
            // Tenter la connexion
            if ($admin->login($username, $password)) {
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Identifiants incorrects. Veuillez réessayer.';
            }
        }
    }
}

// Générer le token CSRF
$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Car Showroom</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        
        .login-container h1 {
            text-align: center;
            margin-bottom: 10px;
            color: #333;
            font-weight: 700;
        }
        
        .login-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Logo ou Titre -->
        <h1>🚗 Car Showroom</h1>
        <p class="login-subtitle">Espace Administrateur</p>
        
        <!-- Afficher les erreurs -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?= sanitize($error) ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulaire de connexion -->
        <form method="POST" action="">
            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <!-- Champ nom d'utilisateur -->
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="username" 
                    name="username" 
                    placeholder="Entrez votre nom d'utilisateur"
                    value="<?= sanitize($username) ?>"
                    required
                >
            </div>
            
            <!-- Champ mot de passe -->
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password" 
                    name="password" 
                    placeholder="Entrez votre mot de passe"
                    required
                >
            </div>
            
            <!-- Bouton de connexion -->
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </button>
        </form>
        
        <!-- Informations -->
        <hr class="my-4">
        <p class="text-center text-muted small">
            <strong>Compte de test:</strong><br>
            Utilisateur: <code>admin</code><br>
            Mot de passe: <code>admin123</code>
        </p>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>