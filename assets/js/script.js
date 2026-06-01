/**
 * JAVASCRIPT GÉNÉRAL
 * Fonctionnalités communes du site
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // RECHERCHE ET FILTRAGE (cars.php)
    // ============================================
    
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Récupérer les valeurs de filtre
            const searchQuery = document.getElementById('searchQuery')?.value || '';
            const brand = document.getElementById('brand')?.value || '';
            const minPrice = document.getElementById('minPrice')?.value || '';
            const maxPrice = document.getElementById('maxPrice')?.value || '';
            
            // Construire l'URL avec les paramètres
            let url = 'cars.php?';
            if (searchQuery) url += 'search=' + encodeURIComponent(searchQuery) + '&';
            if (brand) url += 'brand=' + encodeURIComponent(brand) + '&';
            if (minPrice) url += 'min_price=' + encodeURIComponent(minPrice) + '&';
            if (maxPrice) url += 'max_price=' + encodeURIComponent(maxPrice);
            
            // Rediriger vers la page avec les filtres
            window.location.href = url;
        });
    }
    
    // ============================================
    // GALERIE D'IMAGES (car-details.php)
    // ============================================
    
    const mainImage = document.getElementById('mainImage');
    const thumbs = document.querySelectorAll('.thumb-image');
    
    thumbs.forEach(thumb => {
        thumb.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Récupérer le src de l'image
            const imageSrc = this.getAttribute('data-image');
            
            // Mettre à jour l'image principale
            if (mainImage) {
                mainImage.src = imageSrc;
                mainImage.style.transition = 'opacity 0.3s ease';
                mainImage.style.opacity = '0.5';
                setTimeout(() => {
                    mainImage.style.opacity = '1';
                }, 150);
            }
            
            // Mettre à jour la classe active
            thumbs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // ============================================
    // LIGHTBOX SIMPLE (car-details.php)
    // ============================================
    
    const lightboxImages = document.querySelectorAll('[data-lightbox]');
    
    lightboxImages.forEach(img => {
        img.addEventListener('click', function(e) {
            e.preventDefault();
            showLightbox(this.getAttribute('data-lightbox'));
        });
    });
    
    function showLightbox(imageSrc) {
        // Créer l'élément lightbox
        const lightbox = document.createElement('div');
        lightbox.className = 'lightbox';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <img src="${imageSrc}" alt="Image en plein écran">
                <button class="lightbox-close">&times;</button>
            </div>
        `;
        
        // Ajouter au DOM
        document.body.appendChild(lightbox);
        
        // Fermer la lightbox
        lightbox.addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('lightbox-close')) {
                this.remove();
            }
        });
        
        // Touche Échap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.querySelector('.lightbox')) {
                document.querySelector('.lightbox').remove();
            }
        });
    }
    
    // ============================================
    // SLIDER DE PRIX (cars.php)
    // ============================================
    
    const priceSliders = document.querySelectorAll('.price-slider');
    
    priceSliders.forEach(slider => {
        slider.addEventListener('input', function() {
            // Mettre à jour l'affichage en temps réel
            const value = this.value;
            const label = document.getElementById(this.id + '-label');
            if (label) {
                label.textContent = value + ' €';
            }
        });
    });
    
    // ============================================
    // ANIMATIONS AU DÉFILEMENT
    // ============================================
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.card').forEach(card => {
        observer.observe(card);
    });
    
    // ============================================
    // TOOLTIP BOOTSTRAP
    // ============================================
    
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

/**
 * Fonction pour formater un prix
 * @param {number} price - Prix à formater
 * @returns {string} - Prix formaté
 */
function formatPrice(price) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
}

/**
 * Fonction pour formater un nombre
 * @param {number} num - Nombre à formater
 * @returns {string} - Nombre formaté
 */
function formatNumber(num) {
    return new Intl.NumberFormat('fr-FR').format(num);
}

/**
 * Styles pour la lightbox
 */
const lightboxStyle = document.createElement('style');
lightboxStyle.textContent = `
    .lightbox {
        display: flex;
        align-items: center;
        justify-content: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        animation: fadeIn 0.3s ease;
    }
    
    .lightbox-content {
        position: relative;
        max-width: 90vw;
        max-height: 90vh;
    }
    
    .lightbox-content img {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }
    
    .lightbox-close {
        position: absolute;
        top: -30px;
        right: 0;
        background: none;
        border: none;
        font-size: 40px;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .lightbox-close:hover {
        transform: scale(1.2);
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
`;
document.head.appendChild(lightboxStyle);