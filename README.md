# 🚗 Car Showroom - Application de Gestion de Concession Automobile

Une application web complète pour gérer et présenter un catalogue de véhicules d'occasion.

## ✨ Caractéristiques Principales

### 🌐 Partie Publique
- **Page d'accueil** avec bannière héro et véhicules en avant
- **Listing des voitures** avec recherche et filtrage avancés
  - Filtrage par marque, prix, carburant
  - Pagination automatique
  - Recherche textuelle
- **Fiche détaillée** du véhicule
  - Galerie d'images avec zoom
  - Spécifications complètes
  - Description détaillée
- **Page de contact** pour les demandes de renseignements
- **Design responsive** mobile-first

### 🔐 Panel Administrateur
- **Authentification sécurisée** avec protection CSRF
- **Tableau de bord** avec statistiques
- **Gestion complète du catalogue**
  - Ajout de voitures
  - Modification des détails
  - Suppression avec nettoyage automatique
- **Gestion des images**
  - Upload multiple avec drag & drop
  - Galerie avec prévisualisation
  - Suppression d'images
  - Réorganisation de la galerie

## 🛠 Technologies Utilisées

- **Backend**: PHP 7.4+
- **Base de données**: MySQL/MariaDB
- **Frontend**: Bootstrap 5, HTML5, CSS3
- **JavaScript**: Vanilla JS, jQuery (optionnel)
- **Sécurité**: PDO, prepared statements, CSRF tokens, bcrypt

## 📋 Architecture du Projet

```
velocitydrive/
├── config/                 # Configuration
│   ├── database.php       # Connexion BD
│   ├── constants.php      # Constantes globales
│   └── security.php       # Fonctions de sécurité
├── classes/               # Classes PHP
│   ├── Car.php           # Gestion des voitures (CRUD)
│   ├── Image.php         # Gestion des images
│   └── Admin.php         # Authentification
├── admin/                 # Pages administrateur
│   ├── login.php         # Connexion
│   ├── dashboard.php     # Tableau de bord
│   ├── add-car.php       # Ajouter voiture
│   ├── edit-car.php      # Modifier voiture
│   ├── delete-car.php    # Supprimer voiture
│   ├── manage-images.php # Gérer images
│   ├── logout.php        # Déconnexion
│   └── session-check.php # Vérification session
├── includes/             # Fichiers include
│   ├── header.php        # Navigation publique
│   ├── admin-header.php  # Navigation admin
│   ├── admin-sidebar.php # Barre latérale admin
│   └── footer.php        # Pied de page
├── assets/               # Ressources statiques
│   ├── css/
│   │   ├── style.css     # Styles généraux
│   │   └── admin.css     # Styles admin
│   ├── js/
│   │   ├── script.js     # Scripts généraux
│   │   └── validation.js # Validation formulaires
│   └── images/           # Images du site
├── uploads/              # Dossier uploads (créé automatiquement)
├── index.php             # Page d'accueil
├── cars.php              # Listing voitures
├── car-details.php       # Détails voiture
├── contact.php           # Page contact
└── README.md            # Ce fichier
```

## 🚀 Installation

### Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Un serveur web (Apache, Nginx, etc.)

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone https://github.com/yourusername/velocitydrive.git
cd velocitydrive
```

2. **Configurer la base de données**
```bash
# Créer une base de données
CREATE DATABASE velocitydrive;
USE velocitydrive;
```

3. **Importer le schéma SQL**
```bash
mysql -u root -p velocitydrive < database.sql
```

4. **Configurer la connexion BD**
Modifier `config/database.php` :
```php
$host = 'localhost';
$db_name = 'velocitydrive';
$username = 'root';
$password = 'your_password';
```

5. **Créer les dossiers nécessaires**
```bash
mkdir uploads
chmod 755 uploads
```

6. **Accéder à l'application**
- Site public: `http://localhost/velocitydrive/`
- Panel admin: `http://localhost/velocitydrive/admin/login.php`

## 👤 Compte de Test

**Utilisateur**: `admin`
**Mot de passe**: `admin123`

> ⚠️ **IMPORTANT**: Changer le mot de passe après la première connexion!

## 📊 Structure de la Base de Données

### Table: `voitures`
```sql
CREATE TABLE voitures (
    id INT PRIMARY KEY AUTO_INCREMENT,
    marque VARCHAR(100),
    modele VARCHAR(100),
    annee INT,
    prix DECIMAL(10, 2),
    kilometrage INT,
    carburant VARCHAR(50),
    transmission VARCHAR(50),
    couleur VARCHAR(50),
    type_vehicule VARCHAR(50),
    portes INT,
    cylindree VARCHAR(50),
    puissance VARCHAR(50),
    description LONGTEXT,
    image VARCHAR(255),
    statut VARCHAR(20) DEFAULT 'disponible',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Table: `images_voitures`
```sql
CREATE TABLE images_voitures (
    id INT PRIMARY KEY AUTO_INCREMENT,
    car_id INT,
    chemin_image VARCHAR(255),
    ordre INT,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES voitures(id) ON DELETE CASCADE
);
```

### Table: `administrateurs`
```sql
CREATE TABLE administrateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE,
    email VARCHAR(100),
    password VARCHAR(255),
    statut VARCHAR(20) DEFAULT 'actif',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
```

## 🔐 Sécurité

L'application implémente plusieurs couches de sécurité:

- **CSRF Protection**: Tokens CSRF sur tous les formulaires
- **SQL Injection**: Requêtes préparées avec PDO
- **XSS Protection**: Sanitization des entrées utilisateur
- **Mot de passe**: Hashés avec bcrypt
- **Sessions**: Timeout automatique (30 minutes)
- **Upload files**: Vérification type MIME et extension

## 🎨 Personnalisation

### Couleurs du thème
Modifier `assets/css/style.css`:
```css
:root {
    --primary: #667eea;
    --primary-dark: #764ba2;
    --secondary: #f093fb;
    /* ... */
}
```

### Informations de contact
Modifier `includes/footer.php` et `contact.php`

### Nom du site
Modifier `config/constants.php`

## 📝 Fonctionnalités en Détail

### Recherche et Filtrage
- Recherche par texte (marque, modèle, description)
- Filtrage par marque
- Filtrage par gamme de prix (slider)
- Filtrage par type de carburant
- Combinaison de filtres
- Pagination des résultats

### Gestion des Images
- Upload multiple avec drag & drop
- Prévisualisation
- Suppression individuelle
- Réorganisation de l'ordre
- Compression automatique (optionnel)

### Authentification Admin
- Login sécurisé
- Géestion de session
- Timeout automatique
- "Mémoriser la connexion" (optionnel)

## 🐛 Dépannage

### Erreur: "Connexion à la BD impossible"
- Vérifier les identifiants MySQL
- S'assurer que MySQL est en cours d'exécution
- Vérifier que la base de données existe

### Erreur: "Impossible de créer le dossier uploads"
- Vérifier les permissions du serveur
- Exécuter: `chmod 755 uploads`

### Images ne s'affichent pas
- Vérifier le chemin d'accès BASE_URL dans `config/constants.php`
- Vérifier les permissions du dossier uploads

## 📚 Ressources Utiles

- [PHP Documentation](https://www.php.net/manual/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Bootstrap 5](https://getbootstrap.com/docs/5.0/)
- [MDN Web Docs](https://developer.mozilla.org/)

## 📄 Licence

Ce projet est distribué sous la licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 👨‍💻 Auteur

**Mouhamed Thiam**
- Email: mouhamedth230102@gmail.com
- GitHub: [@Mouhamed-Thiam](https://github.com/Mouhamed-Thiam)

## 🤝 Contribution

Les contributions sont bienvenues! Pour contribuer:

1. Fork le projet
2. Créez une branche (`git checkout -b feature/AmazingFeature`)
3. Commitez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Poussez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📞 Support

Pour toute question ou problème, veuillez ouvrir une issue sur le dépôt GitHub.

---

**Dernière mise à jour**: Juin 2026
**Version**: 1.0.0