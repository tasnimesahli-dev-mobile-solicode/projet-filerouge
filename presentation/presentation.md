---
marp: true
theme: default
paginate: true
size: 16:9
---

<!-- _class: lead -->
<!-- _paginate: false -->

# FILE ROUGE
## Système Helpdesk — Gestion des tickets

**Projet de fin d'études**

Tasnime Sahli  
Encadrant : ES-SARRAJ FOUAD  
Centre SoliCode Tanger • OFPPT • 2026

---

# 1. Présentation du projet

**File Rouge** est une application Helpdesk qui permet de :

- Créer des tickets.
- Organiser les demandes.
- Suivre leur traitement.

---

# 2. Problématique et solution

**Problématique :**

Le client rencontre un problème et ne dispose pas toujours d'un moyen simple pour le signaler en ligne et suivre sa résolution.

**Solution :**

le projet permet au client de :

- Signaler un problème en ligne.
- Créer un ticket.
- Suivre l'état de sa demande.

---

# 3. Objectifs

- Centraliser les demandes.
- Organiser les tickets par catégorie.
- Faciliter le suivi.
- Séparer les responsabilités dans le code.

---

# 4. Acteurs

| Acteur | Rôle |
|---|---|
| **Client** | Crée et suit ses tickets |
| **Support** | Traite les tickets |
| **Admin** | Gère le système |

---

# 5. Fonctionnalités principales

- Gestion des tickets.
- Gestion des catégories.
- Suivi de l'état des tickets.
- Échanges entre les utilisateurs et le support.

---

# 6. Stack technique

| Partie | Technologies |
|---|---|
| Frontend | HTML, CSS, Tailwind CSS |
| Interactivité | JavaScript, Alpine.js |
| Backend actuel | PHP Native |
| Base de données | MySQL, PDO |
| Backend prévu | Laravel |

---

# 7. Architecture — 3 tiers

```text
Frontend
   ↓
Controllers
   ↓
Services
   ↓
DAO + PDO
   ↓
MySQL