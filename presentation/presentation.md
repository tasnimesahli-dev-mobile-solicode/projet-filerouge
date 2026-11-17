---
marp: true
theme: default
size: 16:9
paginate: true
style: |
  section {
    font-family: "Aptos", "Segoe UI", sans-serif;
    background: #f8fafc;
    color: #172033;
    padding: 35px 55px;
    box-sizing: border-box;
    font-size: 22px;
    line-height: 1.3;
    overflow: hidden;
  }

  h1, h2 {
    color: #243b6b;
  }

  h1 {
    font-size: 2.1em;
    margin-bottom: 0.4em;
  }

  h2 {
    font-size: 1.5em;
    margin-top: 0;
    margin-bottom: 0.5em;
  }

  h3 {
    font-size: 1.1em;
    margin: 0.3em 0;
  }

  p, li {
    font-size: 0.9em;
    line-height: 1.35;
  }

  ul, ol {
    margin-top: 0.3em;
    padding-left: 1.2em;
  }

  li {
    margin-bottom: 0.2em;
  }

  table {
    width: 100%;
    font-size: 0.8em;
    border-collapse: collapse;
  }

  th, td {
    padding: 7px 10px;
    overflow-wrap: break-word;
  }

  section.image-only {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  section.image-only img {
    max-width: 95%;
    max-height: 75%;
    object-fit: contain;
  }
---

<!-- _class: lead -->
<!-- _paginate: false -->

# Rapport de projet

# FILE ROUGE

## Système Helpdesk — Gestion des tickets

**Réalisé par :** Tasnime Sahli

**Encadrant :** ES-SARRAJ Fouad

Centre SoliCode Tanger • OFPPT • 2026

---

## Remerciements

---

## Introduction

Présentation générale du projet File Rouge et de son objectif.

---

## Contexte du projet

Développement d'une application Helpdesk pour faciliter la déclaration et le suivi des problèmes en ligne.

---

## Objectif de formation (Solicode)

- Appliquer les compétences techniques acquises.
- Développer une application web.
- Organiser le travail en équipe.
- Découvrir une architecture logicielle.

---

## Cahier des charges

- Déclaration des problèmes en ligne.
- Création et gestion des tickets.
- Gestion des catégories.
- Suivi de l'état des tickets.
- Gestion des utilisateurs.

---

<!-- _class: image-only -->

## Scrum

![Méthode Scrum](../rapport/SCRUM/scrum.png)

---

<!-- _class: image-only -->

## Processus 2TUP

![Processus 2TUP](../rapport/2TUP/images.jpg)

---

<!-- _class: image-only -->

## Design Thinking

![Design Thinking](../rapport/design_thinking/design_thinking.jpg)

---

## Planification

---

## Branche fonctionnelle

- **Client :** déclarer un problème et suivre son ticket.
- **Support :** consulter et traiter les tickets.
- **Admin :** gérer les utilisateurs et les catégories.

---

## Carte d'empathie

**Utilisateur : Client**

- **Besoin :** signaler un problème facilement.
- **Difficulté :** suivre sa demande.
- **Attente :** recevoir une réponse et connaître l'état du ticket.

---

## Définition du problème

Le client rencontre un problème et ne dispose pas toujours d'une plateforme simple pour le signaler en ligne et suivre son traitement.

---

## Idéation

Créer une plateforme Helpdesk qui permet de :

- Déclarer un problème en ligne.
- Créer un ticket.
- Suivre son état.
- Communiquer avec le support.

---

## Diagramme de cas d'utilisation

---

## Stack technologique

- **Backend actuel :** PHP Native
- **Backend prévu :** Laravel
- **Frontend :** HTML · CSS · Tailwind CSS
- **Interactivité :** JavaScript · Alpine.js
- **Base de données :** MySQL · PDO
- **Diagrammes :** MCD · Diagramme de classes

---

## Architecture

**Présentation → Logique métier → Accès aux données**

Navigateur · HTML / CSS / JavaScript

↓

Controllers

↓

Services

↓

DAO · PDO

↓

MySQL

---

## Prototype Helpdesk

- Tickets · Catégories · Utilisateurs
- Création · Consultation · Modification · Suppression
- Statuts · Suivi des tickets
- Échanges entre client et support

---

## Relations des données

- Utilisateur
- Ticket
- Catégorie
- Commentaire

*Les associations et cardinalités suivent le MCD validé du projet.*

---

## Structure technique

- **Frontend :** HTML · CSS / Tailwind · JavaScript · Alpine.js
- **Backend :** PHP Native · Controllers · Services · DAO
- **Accès aux données :** PDO
- **Données :** MySQL
- **Organisation :** Frontend et Backend séparés

---

## Diagramme de classes

---

## Maquettes

---

## Interfaces

---

## Conclusion

- Application Helpdesk pour la gestion des tickets.
- Déclaration des problèmes en ligne.
- Suivi des demandes.
- Base pour une évolution vers Laravel.

---

<!-- _class: lead -->
<!-- _paginate: false -->

# Merci pour votre attention !