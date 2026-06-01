<?php
/**
 * En-tête public (client)
 * Barre de navigation principale du site
 */
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>">
            <i class="fas fa-car"></i> Car Showroom
        </a>
        
        <!-- Bouton toggle mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>cars.php">
                        <i class="fas fa-list"></i> Nos voitures
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>contact.php">
                        <i class="fas fa-envelope"></i> Contact
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>admin/login.php" target="_blank">
                        <i class="fas fa-lock"></i> Admin
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>