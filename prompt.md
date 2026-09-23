Avant de commencer, explore et lis tout ce qui existe déjà dans ce dossier (script SQL de la base "helpdesk", ses tables et les données déjà insérées, et tout autre fichier présent). Base-toi sur cette structure existante et ne la modifie pas.
 Contraintes strictes :
- Ne modifie PAS la base de données (tables et données existantes, y compris les 3 utilisateurs de test déjà insérés).
- N'ajoute AUCUNE fonctionnalité en dehors de ce qui est demandé ci-dessous.
- PHP + MySQL (PDO, requêtes préparées) + HTML/CSS + JavaScript si nécessaire.
## Tâche
1. Crée un fichier **config.php** pour la connexion à la base de données (PDO, host=localhost, dbname=helpdesk).
2. Crée le **CRUD complet de l'utilisateur** :
   - **utilisateurs.php** → tableau listant tous les utilisateurs (id, nom, prénom, email, rôle), avec pour chaque ligne un lien "Modifier" (vers modifier_utilisateur.php?id=...) et un bouton "Supprimer" (confirmation JavaScript confirm() avant suppression).
   - **ajouter_utilisateur.php** → formulaire (nom, prénom, email, mot de passe, rôle en select Client/Support/Admin), validation basique côté PHP (champs non vides, email valide et unique), mot de passe hashé avec password_hash(), redirection vers utilisateurs.php après ajout.
   - **modifier_utilisateur.php** → récupère l'utilisateur via son id (GET), pré-remplit le formulaire (sauf mot de passe), mot de passe optionnel (vide = on garde l'ancien, rempli = hashé et mis à jour), redirection vers utilisateurs.php après modification.
3. Crée une **page d'accueil (index.php)** simple avec titre "Helpdesk", courte présentation du projet, et un lien/menu vers utilisateurs.php.
4. Crée un fichier **style.css** commun, simple et propre (tableau lisible, formulaires alignés, boutons colorés : ajouter = vert, modifier = bleu, supprimer = rouge).
Donne-moi le code complet de chaque fichier.

Modifie la page d’accueil pour qu’elle soit plus simple et professionnelle.

Le projet s’appelle **Gestion des tickets et des problèmes utilisateurs**.

Je veux :

* Un titre principal qui présente clairement le projet.
* Une courte description du système Helpdesk.
* Une section très simple qui explique le principe : signaler un problème → traitement par le Support → résolution.
* Un bouton principal **« Gestion des utilisateurs »** qui redirige vers `utilisateurs.php`, où se trouve le CRUD des utilisateurs.
* Un design moderne, propre et simple avec HTML/CSS.
* JavaScript seulement si nécessaire.

Ne mets pas encore les fonctionnalités de connexion ou d’inscription.
Ne rajoute pas d’autres pages ou fonctionnalités.


## Fonctionnement des commentaires

Les Commentaires servent à permettre la communication entre le Client et le Support à l'intérieur d'un Ticket.

Exemple :
Client → crée un Ticket → Support traite le Ticket → Support ajoute une réponse → Client peut répondre avec un Commentaire → Support peut répondre à nouveau.

Utilise la table `commentaire` existante. Ne crée aucune nouvelle table.

## Ce qu'il faut modifier dans le code existant

Dans les pages concernées comme :

* `tickets.php`
* `ajouter_ticket.php`
* `modifier_ticket.php`
* `commentaires.php`
* `ajouter_commentaire.php`
* `modifier_commentaire.php`
* etc.

Ajoute les vérifications nécessaires pour :

* Afficher ou masquer les boutons selon le rôle.
* Filtrer les Tickets visibles pour le Client afin qu'il voie uniquement ses propres Tickets.
* Filtrer les Commentaires accessibles selon les permissions.
* Bloquer côté serveur toute action non autorisée.
* Vérifier le rôle et l'id de l'utilisateur avant chaque `INSERT`, `UPDATE` ou `DELETE`.
* Permettre uniquement au Support et à l'Admin de modifier le statut d'un Ticket.
* Permettre au Client de commenter uniquement ses propres Tickets.
* Permettre au Support de commenter sur n'importe quel Ticket.
* Permettre à l'Admin de supprimer n'importe quel Commentaire.

Pour l'utilisateur actif, utiliser :

`?id_utilisateur=3`

Par exemple :

`tickets.php?id_utilisateur=3`

Récupérer ensuite son `role` depuis la table `utilisateur`.

Pour l'instant, concentre-toi uniquement sur la gestion des permissions dans les CRUD existants.

Ne crée aucune fonctionnalité supplémentaire et surtout aucune page de connexion ou d'inscription.

Donne-moi le code complet et modifié de chaque fichier concerné, en conservant au maximum le code déjà existant.

Explore et lis tout ce qui existe déjà dans ce dossier : la structure de la base de données "helpdesk" et tout le code PHP déjà développé (CRUD de utilisateur, categorie, ticket, commentaire, et la gestion des permissions par rôle).

Génère un fichier **README.md** clair et bien structuré qui explique le fonctionnement complet de la plateforme "Helpdesk", basé sur le code réel présent dans ce dossier (n'invente rien qui n'existe pas dans le code).

Le fichier doit contenir :

1. **Présentation du projet** : c'est quoi le Helpdesk, à quoi ça sert, en 2-3 phrases.

2. **Les rôles et leurs permissions** : explique ce que peut faire chaque rôle (Client, Support, Admin) pour les tickets et les commentaires, sous forme de liste claire.

3. **Structure de la base de données** : liste des tables (utilisateur, categorie, ticket, commentaire) avec une courte explication de chacune et leurs liens (clés étrangères).

4. **Structure des fichiers du projet** : liste tous les fichiers PHP présents dans le dossier avec une ligne expliquant le rôle de chacun (ex : config.php = connexion à la base, tickets.php = affichage des tickets selon le rôle...).

5. **Comment tester la plateforme** : explique comment simuler un utilisateur actif avec le paramètre `?id_utilisateur=...` dans l'URL, avec des exemples concrets pour tester en tant que Client, Support et Admin (donne les id des 3 utilisateurs déjà insérés dans la base).

6. **Flux de fonctionnement** : décris étape par étape comment un problème est traité, du moment où le Client crée un ticket jusqu'à sa résolution par le Support.

Utilise un formatage Markdown propre (titres, sous-titres, listes à puces, tableaux si utile). Ne modifie aucun fichier de code existant, crée seulement le fichier README.md.

Avant de modifier le projet, lis et comprends la structure actuelle.

Je veux faire un grand nettoyage du projet.

### Objectif

Supprimer tout le code et toutes les pages qui ne sont pas nécessaires.

Je veux garder uniquement :

1. **La page d'accueil**
2. **La partie Admin**

### À supprimer

Supprime les pages et fonctionnalités liées aux rôles :

* Client
* Support
* simulation `?id_utilisateur=...`
* `auth_helper.php`
* barre de simulation des utilisateurs
* système de permissions Client / Support
* fonctionnalités de connexion ou d'inscription si elles existent
* toutes les pages ou fonctionnalités qui ne sont pas nécessaires à la partie Admin ou à la page d'accueil.

Supprime également le code devenu inutile à cause de cette modification.

### Partie Admin à conserver

L'Admin doit pouvoir gérer les données du système avec les CRUD nécessaires :

* Utilisateurs (afficher/ajouter /suprimer/editer)
* Catégories(afficher/ajouter/suprimer/editer)
* Tickets(Aficher/suprimer)
* Commentaires(Afficher/suprimer)

Conserve les pages Admin nécessaires pour :

* Afficher
* Ajouter
* Modifier
* Supprimer

les données concernées.

### Important

* **Ne supprime PAS la base de données.**
* **Ne modifie PAS la structure de la base de données.**
* Garde les 4 tables existantes :

 * `utilisateur`
  * `ticket`
  * `categorie`
  * `commentaire`
* Ne crée aucune nouvelle table.
* Ne crée pas de système de connexion.
* Ne crée pas de nouvelles fonctionnalités.
* Ne garde aucune logique de rôles Client ou Support.
* Ne garde aucune simulation avec `?id_utilisateur=...`.

### Structure souhaitée

Je veux une structure simple 

Adapte les noms de fichiers selon ceux qui existent déjà.

### Page d'accueil

Conserve une page d'accueil simple et professionnelle qui présente :

**Gestion des tickets et des problèmes utilisateurs**

Elle doit permettre d'accéder à la partie Admin.

### Règle importante

Ne réécris pas inutilement le projet depuis zéro.

Supprime uniquement ce qui est devenu inutile et conserve au maximum le code Admin et le code de connexion à la base de données déjà fonctionnels.

À la fin, vérifie qu'il ne reste aucune référence au Client, au Support, à `auth_helper.php` ou à `?id_utilisateur=...`.

Ne modifie pas la base de données.
