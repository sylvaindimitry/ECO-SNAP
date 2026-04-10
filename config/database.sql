-- ECO-SNAP Database Schema
-- Base de données pour la gestion des signalements de dépôts d'ordures

CREATE DATABASE IF NOT EXISTS ecosnap_mvc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecosnap_mvc;

-- Table: users (utilisateurs génériques)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255) NULL,
    google_id VARCHAR(255) NULL,
    remember_token VARCHAR(255) NULL,
    last_login TIMESTAMP NULL,
    role ENUM('habitant', 'chauffeur', 'admin') DEFAULT 'habitant',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table: zones (zones géographiques d'intervention)
CREATE TABLE zones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table: chauffeurs (informations spécifiques aux chauffeurs/équipes)
CREATE TABLE chauffeurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    zone_id INT NOT NULL,
    nom_equipe VARCHAR(100),
    vehicule_type VARCHAR(50),
    immatriculation VARCHAR(50),
    capacite INT,
    statut ENUM('actif', 'inactif', 'en_mission') DEFAULT 'actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (zone_id) REFERENCES zones(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Table: planning_travail (jours de travail déclarés par les chauffeurs)
CREATE TABLE planning_travail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chauffeur_id INT NOT NULL,
    jour_semaine ENUM('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche') NOT NULL,
    heure_debut TIME,
    heure_fin TIME,
    actif BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chauffeur_id) REFERENCES chauffeurs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_chauffeur_jour (chauffeur_id, jour_semaine)
) ENGINE=InnoDB;

-- Table: signalements (signalements de dépôts d'ordures)
CREATE TABLE signalements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    zone_id INT NOT NULL,
    ville VARCHAR(100) NOT NULL,
    quartier VARCHAR(100) NOT NULL,
    type_depot ENUM('terre', 'eau', 'mixte') NOT NULL,
    description TEXT,
    photo VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    statut ENUM('en_attente', 'pris_en_charge', 'en_cours', 'termine', 'annule') DEFAULT 'en_attente',
    chauffeur_id INT NULL,
    date_signalement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_prise_en_charge TIMESTAMP NULL,
    date_resolution TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (zone_id) REFERENCES zones(id) ON DELETE RESTRICT,
    FOREIGN KEY (chauffeur_id) REFERENCES chauffeurs(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Table: notifications (notifications en temps réel)
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    signalement_id INT,
    type ENUM('nouveau_signalement', 'signalement_pris', 'signalement_update', 'rappel') NOT NULL,
    message TEXT NOT NULL,
    lu BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (signalement_id) REFERENCES signalements(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Index pour optimiser les recherches
CREATE INDEX idx_signalements_zone ON signalements(zone_id);
CREATE INDEX idx_signalements_statut ON signalements(statut);
CREATE INDEX idx_chauffeurs_zone ON chauffeurs(zone_id);
CREATE INDEX idx_chauffeurs_statut ON chauffeurs(statut);
CREATE INDEX idx_planning_chauffeur ON planning_travail(chauffeur_id, jour_semaine);
CREATE INDEX idx_notifications_user ON notifications(user_id, lu);

-- Données initiales : zones
INSERT INTO zones (nom, ville, description) VALUES
('Bonamoussadi', 'Douala', 'Zone Bonamoussadi et environs'),
('Bonaberi', 'Douala', 'Zone Bonaberi et environs'),
('Akwa', 'Douala', 'Centre-ville Akwa'),
('Deido', 'Douala', 'Zone Deido'),
('Bali', 'Douala', 'Zone Bali'),
('New-Bell', 'Douala', 'Zone New-Bell'),
('Kotto', 'Douala', 'Zone Kotto');

-- Données de test : utilisateur habitant
INSERT INTO users (nom, prenom, email, telephone, password, role) VALUES
('Habitant', 'Test', 'habitant@ecosnap.com', '677000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'habitant'),
('Chauffeur', 'Team Alpha', 'chauffeur@ecosnap.com', '677000001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'chauffeur');

-- Données de test : chauffeur
INSERT INTO chauffeurs (user_id, zone_id, nom_equipe, vehicule_type, immatriculation, capacite, statut) VALUES
(2, 1, 'Team Alpha', 'Camion', 'AA-1234-BB', 10, 'actif');

-- Données de test : planning
INSERT INTO planning_travail (chauffeur_id, jour_semaine, heure_debut, heure_fin) VALUES
(1, 'lundi', '08:00:00', '17:00:00'),
(1, 'mardi', '08:00:00', '17:00:00'),
(1, 'mercredi', '08:00:00', '17:00:00'),
(1, 'jeudi', '08:00:00', '17:00:00'),
(1, 'vendredi', '08:00:00', '17:00:00'),
(1, 'samedi', '08:00:00', '14:00:00');
