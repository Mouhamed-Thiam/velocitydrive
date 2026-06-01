/**
 * VALIDATION DE FORMULAIRES
 * Validation client-side avant soumission
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Valider le formulaire d'ajout/modification de voiture
    const carForm = document.getElementById('addCarForm') || document.getElementById('editCarForm');
    
    if (carForm) {
        carForm.addEventListener('submit', function(e) {
            
            // Récupérer les valeurs
            const marque = document.getElementById('marque')?.value.trim();
            const modele = document.getElementById('modele')?.value.trim();
            const annee = parseInt(document.getElementById('annee')?.value);
            const prix = parseFloat(document.getElementById('prix')?.value);
            const kilometrage = parseInt(document.getElementById('kilometrage')?.value);
            
            // Valider les champs obligatoires
            if (!marque || !modele || !annee || !prix) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
                return false;
            }
            
            // Valider l'année
            const currentYear = new Date().getFullYear();
            if (annee < 1900 || annee > currentYear + 1) {
                e.preventDefault();
                alert('Année invalide. Veuillez entrer une année entre 1900 et ' + (currentYear + 1));
                return false;
            }
            
            // Valider le prix
            if (prix < 0) {
                e.preventDefault();
                alert('Le prix ne peut pas être négatif.');
                return false;
            }
            
            // Valider le kilométrage
            if (kilometrage < 0) {
                e.preventDefault();
                alert('Le kilométrage ne peut pas être négatif.');
                return false;
            }
            
            return true;
        });
    }
    
    // Formatage automatique des nombres
    const priceInput = document.getElementById('prix');
    if (priceInput) {
        priceInput.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    }
    
    const kilometrageInput = document.getElementById('kilometrage');
    if (kilometrageInput) {
        kilometrageInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    }
    
    // Validation en temps réel pour la marque et le modèle
    const marqueInput = document.getElementById('marque');
    const modeleInput = document.getElementById('modele');
    
    [marqueInput, modeleInput].forEach(input => {
        if (input) {
            input.addEventListener('blur', function() {
                if (this.value.length < 2) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }
    });
});

/**
 * Fonction pour afficher un message de confirmation
 * @param {string} message - Message à afficher
 * @returns {boolean} - Confirmation de l'utilisateur
 */
function confirmAction(message) {
    return confirm(message || 'Êtes-vous sûr de vouloir continuer?');
}

/**
 * Fonction pour copier du texte dans le presse-papiers
 * @param {string} text - Texte à copier
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Copié dans le presse-papiers!');
    }).catch(() => {
        alert('Erreur lors de la copie.');
    });
}