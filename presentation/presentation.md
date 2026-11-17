---
marp: true
theme: default
size: 16:9
paginate: true
style: |
  :root {
    --ink: #19333a;
    --teal: #16766f;
    --coral: #df7655;
    --paper: #f5f7f3;
    --white: #ffffff;
    --muted: #5d7073;
    --line: #d7e0da;
  }

  section {
    font-family: "Aptos", "Segoe UI", sans-serif;
    background: var(--paper);
    color: var(--ink);
    padding: 38px 58px 44px;
    box-sizing: border-box;
    font-size: 22px;
    line-height: 1.32;
    overflow: hidden;
    position: relative;
  }

  section::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 6px;
    background: var(--teal);
  }

  h1, h2, h3 {
    color: var(--ink);
    font-family: Georgia, "Times New Roman", serif;
    font-weight: 700;
  }

  h1 {
    font-size: 2.15em;
    line-height: 1.08;
    margin: 0 0 0.35em;
  }

  h2 {
    font-size: 1.48em;
    line-height: 1.12;
    margin: 0 0 0.65em;
    padding-bottom: 0.28em;
    border-bottom: 2px solid var(--line);
  }

  h3 {
    font-size: 1.05em;
    margin: 0.35em 0;
  }

  p {
    margin: 0.45em 0;
  }

  ul, ol {
    margin: 0.35em 0;
    padding-left: 1.25em;
  }

  li {
    margin-bottom: 0.3em;
    line-height: 1.35;
  }

  li::marker {
    color: var(--coral);
  }

  strong {
    color: var(--teal);
  }

  table {
    width: 100%;
    font-size: 0.82em;
    border-collapse: collapse;
  }

  th, td {
    padding: 8px 10px;
    overflow-wrap: break-word;
    border-bottom: 1px solid var(--line);
    text-align: left;
  }

  section img {
    display: block;
    max-width: 100%;
    max-height: 430px;
    width: auto;
    height: auto;
    object-fit: contain;
    margin-left: auto;
    margin-right: auto;
  }

  section.lead {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 34px 72px;
    background: var(--ink);
    color: var(--white);
    font-size: 20px;
    line-height: 1.22;
  }

  section.lead::before {
    height: 9px;
    background: var(--coral);
  }

  section.lead h1:first-of-type {
    margin: 0 0 0.12em;
    color: #a9d8cb;
    font-family: "Aptos", "Segoe UI", sans-serif;
    font-size: 1em;
    font-weight: 600;
    text-transform: uppercase;
  }

  section.lead h1:nth-of-type(2) {
    margin: 0 0 0.18em;
    color: var(--white);
    font-size: 2.6em;
    line-height: 1;
  }

  section.lead h2 {
    margin: 0 0 0.6em;
    padding: 0;
    border: 0;
    color: var(--white);
    font-family: "Aptos", "Segoe UI", sans-serif;
    font-size: 1.2em;
    font-weight: 500;
  }

  section.lead p {
    margin: 0.24em 0;
    color: #d5e2de;
  }

  section.lead strong {
    color: #a9d8cb;
  }

  .logo-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    width: 100%;
    margin: 0 0 34px;
    padding: 12px 18px;
    box-sizing: border-box;
    background: var(--white);
    border-radius: 4px;
  }

  section.lead .logo-row img {
    width: auto;
    height: 62px;
    max-width: 180px;
    max-height: 62px;
    margin: 0;
    object-fit: contain;
  }

  section.divider {
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--ink);
    color: var(--white);
  }

  section.divider::before {
    background: var(--coral);
  }

  section.divider h1, section.divider h2 {
    color: var(--white);
    border: 0;
    text-align: center;
  }

  section.closing {
    align-items: center;
    text-align: center;
  }

  section.closing h1 {
    margin: 0;
    color: var(--white);
    font-size: 2.6em;
  }

  section.agenda ol {
    padding-left: 1.5em;
  }

  section.agenda li {
    margin-bottom: 0.45em;
  }

  section.sprints strong {
    display: inline-block;
    margin-top: 0.22em;
    font-size: 1.03em;
  }

  section.architecture p {
    margin: 0.25em 0;
    text-align: center;
  }

  section.architecture p:first-of-type {
    margin-bottom: 0.5em;
    font-size: 1.08em;
  }

  section.lead img {
    margin: 0;
  }

  section.image-only {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px 42px 32px;
    background: var(--white);
  }

  section.image-only h2 {
    align-self: stretch;
    width: 100%;
    margin-bottom: 0.45em;
  }

  section.image-only p {
    margin: 0;
  }

  section.image-only img {
    max-width: 100%;
    max-height: 520px;
    width: auto;
    height: auto;
    object-fit: contain;
  }

  section.image-only.landscape-image img {
    max-width: 1040px;
    max-height: 480px;
  }

  section.image-only.portrait-image img {
    max-width: 540px;
    max-height: 520px;
  }

  section::after {
    color: var(--muted);
    font-family: "Aptos", "Segoe UI", sans-serif;
    font-size: 13px;
  }

  section.lead::after, section.divider::after {
    color: rgba(255, 255, 255, 0.68);
  }

  @media (max-width: 800px) {
    section {
      padding: 28px 36px 34px;
      font-size: 19px;
    }

    section.lead {
      padding: 28px 42px;
    }

    .logo-row {
      margin-bottom: 24px;
    }

    section.lead .logo-row img {
      height: 52px;
      max-height: 52px;
    }

    section.image-only {
      padding: 20px 28px 26px;
    }

    section.image-only img {
      max-height: 500px;
    }
  }
---

<!-- _class: lead -->
<!-- _paginate: false -->

<div class="logo-row">
  <img src="ofppt-logo.png" alt="Logo OFPPT">
  <img src="solicode-logo.png" alt="Logo SoliCode">
</div>

# Rapport de projet

# FILE ROUGE

## Système Helpdesk — Gestion des tickets

**Réalisé par :** Tasnime Sahli

**Encadrant :** ES-SARRAJ Fouad

Centre SoliCode Tanger • OFPPT • 2026

---

<!-- _class: divider -->

## Remerciements

---
<!-- _class: agenda -->
## Planification:

1. Plan de la présentation
2. Introduction et contexte du projet
3. Méthodologies et planification
4. Branche fonctionnelle
5. Compréhension du besoin : empathie, problématique et idéation
6. Conception : diagrammes de cas d'utilisation et de classes
7. Stack technologique et structure technique
8. Conclusion

---

## Introduction

Présentation générale du projet File Rouge et de son objectif.

---

## Contexte du projet

Développement d'une application Helpdesk pour faciliter la déclaration et le suivi des problèmes en ligne.

---

<!-- _class: divider -->

# Les Methode de Travail:

---

<!-- _class: image-only landscape-image -->

## Scrum

![Méthode Scrum](../rapport/SCRUM/scrum.png)

---

<!-- _class: image-only landscape-image -->

## Processus 2TUP

![Processus 2TUP](../rapport/2TUP/images.jpg)

---

<!-- _class: image-only landscape-image -->

## Design Thinking

![Design Thinking](../rapport/design_thinking/design_thinking.jpg)

---
<!-- _class: sprints -->

## Planification

Le projet est organisé en plusieurs sprints:


**Sprint 01 — Gestion des produits**
- CRUD Product.
- Affichage des produits dans la plateforme.
- Diagramme de cas d'utilisation.


**Sprint 02 — Gestion des catégories**
- CRUD Category.
- Organisation des fichiers.
- Séparation des responsabilités.

**Sprint 03 — Architecture**
- Utilisation du pattern DAO.
- Architecture en 3 couches.
- Séparation de la logique métier et des données.
  
------


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

<!-- _class: image-only portrait-image -->

## Diagramme de class
![Diagramme de class](diagram-class.png)

---
<!-- _class: image-only portrait-image -->
## Diagramme de cas d'utilisateur
![Diagramme de cas de utilisateur](diagram-cas-utilisation.png)

---
## Stack technologique

- **Backend actuel :** PHP Native
- **Backend prévu :** Laravel
- **Frontend :** HTML · CSS · Tailwind CSS
- **Interactivité :** JavaScript · Alpine.js
- **Base de données :** MySQL · PDO
- **Diagrammes :** MCD · Diagramme de classes

---

<!-- _class: architecture -->

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

<!-- _class: conclusion -->

## Conclusion

- Application Helpdesk pour la gestion des tickets.
- Déclaration des problèmes en ligne.
- Suivi des demandes.
- Base pour une évolution vers Laravel.

---

<!-- _class: lead closing -->
<!-- _paginate: false -->

# Merci pour votre attention !