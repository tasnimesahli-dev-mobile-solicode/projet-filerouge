-- ========================================================
-- S3/backend/data/db.sql
-- Base de données : helpdesk — Version 3 (Architecture 3 Tiers)
-- ========================================================

CREATE DATABASE IF NOT EXISTS helpdesk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE helpdesk;

-- --------------------------------------------------------
-- Table `utilisateur`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom            VARCHAR(100) NOT NULL,
    prenom         VARCHAR(100) NOT NULL,
    email          VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe   VARCHAR(255) NOT NULL,
    role           ENUM('Client', 'Support', 'Admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `categorie`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS categorie (
    id_categorie  INT AUTO_INCREMENT PRIMARY KEY,
    nom_categorie VARCHAR(100) UNIQUE NOT NULL,
    description   VARCHAR(255) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `ticket`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS ticket (
    id_ticket      INT AUTO_INCREMENT PRIMARY KEY,
    titre          VARCHAR(200) NOT NULL,
    description    TEXT NOT NULL,
    date_creation  DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut         ENUM('Ouvert', 'En cours', 'Résolu', 'Fermé') DEFAULT 'Ouvert',
    id_utilisateur INT NOT NULL,
    id_categorie   INT NOT NULL,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_categorie)   REFERENCES categorie(id_categorie)     ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Données initiales
-- --------------------------------------------------------
INSERT INTO utilisateur (id_utilisateur, nom, prenom, email, mot_de_passe, role) VALUES
(1, 'Admin',  'System', 'admin@helpdesk.com',   '123456', 'Admin'),
(2, 'Ahmed',  'Karim',  'support@helpdesk.com', '123456', 'Support'),
(3, 'Sara',   'Amina',  'sara@helpdesk.com',    '123456', 'Client')
ON DUPLICATE KEY UPDATE email = VALUES(email);

INSERT INTO categorie (id_categorie, nom_categorie, description) VALUES
(1, 'Matériel', 'Problèmes liés au matériel informatique'),
(2, 'Logiciel', 'Problèmes logiciels et applicatifs'),
(3, 'Réseau',   'Problèmes de connectivité et réseau'),
(4, 'Autre',    NULL)
ON DUPLICATE KEY UPDATE nom_categorie = VALUES(nom_categorie);

ALTER TABLE categorie AUTO_INCREMENT = 5;

INSERT INTO ticket (id_ticket, titre, description, statut, id_utilisateur, id_categorie) VALUES
(1, 'Problème de connexion WiFi', 'Impossibilité de se connecter au réseau wifi local au 2ème étage.', 'Ouvert', 3, 3)
ON DUPLICATE KEY UPDATE titre = VALUES(titre);

