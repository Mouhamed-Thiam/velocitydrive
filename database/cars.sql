-- ============================================
-- BASE DE DONNÉES : car_showroom
-- ============================================

CREATE DATABASE IF NOT EXISTS car_showroom;
USE car_showroom;

-- ============================================
-- TABLE : administrateurs
-- ============================================
CREATE TABLE administrateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE,
    nom VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    statut ENUM('actif', 'inactif') DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : voitures
-- ============================================
CREATE TABLE voitures (
    id INT PRIMARY KEY AUTO_INCREMENT,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    annee INT NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    kilometrage INT NOT NULL,
    carburant VARCHAR(50) NOT NULL,
    transmission VARCHAR(50) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    couleur VARCHAR(50),
    type_vehicule VARCHAR(50),
    portes INT DEFAULT 4,
    cylindree VARCHAR(50),
    puissance VARCHAR(50),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    statut ENUM('disponible', 'vendue', 'en_reparation') DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : images_voitures
-- ============================================
CREATE TABLE images_voitures (
    id INT PRIMARY KEY AUTO_INCREMENT,
    car_id INT NOT NULL,
    chemin_image VARCHAR(255) NOT NULL,
    ordre INT DEFAULT 0,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES voitures(id) ON DELETE CASCADE,
    INDEX (car_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INDEX POUR OPTIMISATION
-- ============================================
CREATE INDEX idx_marque ON voitures(marque);
CREATE INDEX idx_prix ON voitures(prix);
CREATE INDEX idx_annee ON voitures(annee);
CREATE INDEX idx_statut ON voitures(statut);

-- ============================================
-- DONNÉES INITIALES
-- ============================================

-- Admin de test (password: admin123)
INSERT INTO administrateurs (username, password, email, nom) 
VALUES ('admin', '$2y$10$YIjlrBJZZ8F8VK6Q9K7P.eK7K8K9K8K9K8K9K8K9K8K9K8K9K8K9K', 'admin@showroom.com', 'Administrateur');

-- Voitures de test
INSERT INTO voitures (marque, modele, annee, prix, kilometrage, carburant, transmission, description, couleur, type_vehicule, portes, cylindree, puissance) VALUES
('Toyota', 'Corolla', 2022, 18500.00, 15000, 'Essence', 'Automatique', 'Voiture fiable et économique, parfaite pour la ville', 'Gris', 'Berline', 4, '1.8L', '140 ch'),
('Honda', 'Civic', 2023, 22000.00, 8000, 'Essence', 'Manuelle', 'Design élégant et technologie moderne', 'Bleu', 'Berline', 4, '1.5L', '130 ch'),
('Renault', 'Clio', 2021, 15000.00, 25000, 'Diesel', 'Manuelle', 'Petite citadine agile et maniable', 'Blanc', 'Citadine', 3, '1.5L', '75 ch'),
('BMW', 'Serie 3', 2022, 35000.00, 12000, 'Diesel', 'Automatique', 'Luxe et performance réunis', 'Noir', 'Berline', 4, '2.0L', '190 ch'),
('Mercedes', 'C-Class', 2023, 42000.00, 5000, 'Essence', 'Automatique', 'Confort et sophistication', 'Argent', 'Berline', 4, '2.0L', '205 ch');