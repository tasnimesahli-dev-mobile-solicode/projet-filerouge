CREATE DATABASE helpdesk;
USE helpdesk;
CREATE TABLE utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('Client', 'Support', 'Admin') NOT NULL
);

CREATE TABLE categorie (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    nom_categorie VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE ticket (
    id_ticket INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('Ouvert', 'En cours', 'Résolu', 'Fermé') DEFAULT 'Ouvert',
    id_utilisateur INT NOT NULL,
    id_categorie INT NOT NULL,

    FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur),

    FOREIGN KEY (id_categorie)
        REFERENCES categorie(id_categorie)
);

CREATE TABLE commentaire (
    id_commentaire INT AUTO_INCREMENT PRIMARY KEY,
    contenu TEXT NOT NULL,
    date_commentaire DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_utilisateur INT NOT NULL,
    id_ticket INT NOT NULL,

    FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur),

    FOREIGN KEY (id_ticket)
        REFERENCES ticket(id_ticket)
);
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role)
VALUES ('Admin', 'System', 'admin@helpdesk.com', '123456', 'Admin');
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role)
VALUES ('Ahmed', 'Karim', 'support@helpdesk.com', '123456', 'Support');
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role)
VALUES ('Sara', 'Amina', 'sara@helpdesk.com', '123456', 'Client');