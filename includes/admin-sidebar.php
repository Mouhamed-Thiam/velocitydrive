<?php
/**
 * Barre latérale administrateur
 * Menu de navigation du panel admin
 */
?>
<nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>" 
                   href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
            </li>
            
            <!-- Gestion des voitures -->
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'add-car.php' ? 'active' : '' ?>" 
                   href="add-car.php">
                    <i class="fas fa-plus-circle"></i> Ajouter une voiture
                </a>
            </li>
            
            <!-- Liste des voitures -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php#cars">
                    <i class="fas fa-list"></i> Mes voitures
                </a>
            </li>
            
            <!-- Séparateur -->
            <hr class="my-3">
            
            <!-- Déconnexion -->
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
    .sidebar {
        position: fixed;
        top: 56px;
        bottom: 0;
        left: 0;
        z-index: 100;
        padding-top: 48px;
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, 0.1);
    }
    
    .sidebar .nav-link {
        color: #333;
        padding: 0.75rem 1rem;
        transition: all 0.3s;
        border-left: 3px solid transparent;
    }
    
    .sidebar .nav-link:hover {
        color: #667eea;
        background-color: rgba(102, 126, 234, 0.1);
        border-left-color: #667eea;
    }
    
    .sidebar .nav-link.active {
        color: white;
        background-color: #667eea;
        border-left-color: white;
    }
    
    @media (max-width: 768px) {
        .sidebar {
            position: relative;
            top: auto;
            padding-top: 0;
        }
    }
</style>