<?php
/**
 * Pied de page
 * Pied de page commun à toutes les pages
 */
?>
<footer class="bg-dark text-white mt-5 py-4">
    <div class="container">
        <div class="row">
            <!-- À propos -->
            <div class="col-md-4 mb-3">
                <h5 class="mb-3">
                    <i class="fas fa-car"></i> Car Showroom
                </h5>
                <p class="text-muted">
                    Votre concession automobile de confiance avec une large sélection de véhicules de qualité.
                </p>
            </div>
            
            <!-- Liens rapides -->
            <div class="col-md-4 mb-3">
                <h5 class="mb-3">Liens rapides</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= BASE_URL ?>" class="text-muted text-decoration-none">Accueil</a></li>
                    <li><a href="<?= BASE_URL ?>cars.php" class="text-muted text-decoration-none">Nos voitures</a></li>
                    <li><a href="<?= BASE_URL ?>contact.php" class="text-muted text-decoration-none">Contact</a></li>
                </ul>
            </div>
            
            <!-- Contact -->
            <div class="col-md-4 mb-3">
                <h5 class="mb-3">Contact</h5>
                <p class="text-muted mb-1">
                    <i class="fas fa-phone"></i> +33 1 23 45 67 89
                </p>
                <p class="text-muted mb-1">
                    <i class="fas fa-envelope"></i> info@carshowroom.fr
                </p>
                <p class="text-muted">
                    <i class="fas fa-map-marker-alt"></i> 123 Rue de l'Automobile, 75000 Paris
                </p>
            </div>
        </div>
        
        <hr class="my-3">
        
        <!-- Copyright -->
        <div class="text-center text-muted">
            <p class="mb-0">&copy; 2026 Car Showroom. Tous droits réservés.</p>
        </div>
    </div>
</footer>