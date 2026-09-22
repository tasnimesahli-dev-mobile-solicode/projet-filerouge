# Helpdesk - Administration

Plateforme web de gestion et d'administration du système **Helpdesk (Gestion des tickets et des problèmes utilisateurs)** développée en **PHP (PDO)**, **MySQL** et **HTML5/CSS3**.

---

## 1. Présentation du projet

L'application **Helpdesk** est une plateforme centralisée permettant de superviser et de gérer l'ensemble des tickets d'assistance, des catégories d'incidents, des utilisateurs et des commentaires au sein d'une organisation. Cette version fournit une interface d'administration complète et épurée pour administrer l'ensemble des données du système.

---

## 2. Fonctionnalités d'administration (CRUD)

L'administration permet de gérer directement les 4 entités du système :

* **Utilisateurs :**
  * **Afficher :** Liste complète des comptes avec ID, nom, prénom, email et rôle.
  * **Ajouter :** Création d'un utilisateur avec validation des données et hashage du mot de passe.
  * **Modifier :** Mise à jour du nom, prénom, email et mot de passe optionnel.
  * **Supprimer :** Suppression d'un utilisateur avec nettoyage en cascade des enregistrements associés.

* **Catégories :**
  * **Afficher :** Tableau des catégories d'incidents avec décompte des tickets associés.
  * **Ajouter :** Création d'une nouvelle catégorie unique.
  * **Modifier :** Modification du nom d'une catégorie existante.
  * **Supprimer :** Suppression sécurisée (bloquée si des tickets sont rattachés à la catégorie).

* **Tickets :**
  * **Afficher :** Consultation globale de tous les tickets signalés (ID, titre, catégorie, auteur, date, statut et décompte des commentaires).
  * **Supprimer :** Suppression définitive d'un ticket et de ses commentaires associés.

* **Commentaires :**
  * **Afficher :** Consultation de l'ensemble des commentaires ou filtrage par ticket associé.
  * **Supprimer :** Modération et suppression directe de n'importe quel commentaire.

---

## 3. Structure de la base de données

La base de données MySQL `helpdesk` comprend les 4 tables suivantes :

```
utilisateur (1) ───< ticket (N) >─── (1) categorie
     │                     │
     │                     │
     └───< commentaire (N) >
```

* **`utilisateur`** : comptes système (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`).
* **`categorie`** : typologies d'incidents (`id_categorie`, `nom_categorie`).
* **`ticket`** : tickets enregistrés (`id_ticket`, `titre`, `description`, `date_creation`, `statut`, `id_utilisateur`, `id_categorie`).
* **`commentaire`** : messages et réponses (`id_commentaire`, `contenu`, `date_commentaire`, `id_utilisateur`, `id_ticket`).

---

## 4. Structure des fichiers du projet

```plaintext
projet-filerouge/
├── backend/
│   ├── ajouter_categorie.php    # Formulaire d'ajout d'une catégorie
│   ├── ajouter_utilisateur.php  # Formulaire d'ajout d'un utilisateur
│   ├── categories.php           # Liste et suppression des catégories
│   ├── commentaires.php         # Consultation et suppression des commentaires
│   ├── config.php               # Connexion PDO à la base de données MySQL
│   ├── index.php                # Page d'accueil et tableau de bord Admin
│   ├── modifier_categorie.php   # Formulaire de modification d'une catégorie
│   ├── modifier_utilisateur.php # Formulaire de modification d'un utilisateur
│   ├── style.css                # Feuille de style globale
│   ├── tickets.php              # Liste et suppression des tickets
│   └── utilisateurs.php         # Liste et suppression des utilisateurs
├── database/
│   ├── db.sql                   # Script de création de la base de données
│   └── mcdfilerouge.png         # Modèle Conceptuel de Données
├── index.php                    # Redirection vers backend/index.php
├── projet.txt
└── README.md
```
