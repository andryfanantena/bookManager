# BookManager — Installation

Ce dossier contient les fichiers Laravel Blade (contrôleurs, modèles, vues,
migrations, routes et services de catalogue) à intégrer dans un projet Laravel. Il ne
contient **pas** un projet Laravel complet (pas de `vendor/`, pas de
`composer.json`) : il est conçu pour être copié dans un projet existant ou
nouvellement créé.

## 1. Créer le projet Laravel (si ce n'est pas déjà fait)

```bash
composer create-project laravel/laravel bookmanager
cd bookmanager
```

## 2. Copier les fichiers de ce dossier

Copiez-collez (en fusionnant, sans écraser vos fichiers existants si vous en
avez déjà) :

```
app/Http/Controllers/*.php   -> app/Http/Controllers/
app/Models/*.php             -> app/Models/
app/Services/*.php           -> app/Services/          (créer le dossier)
app/Exports/*.php            -> app/Exports/            (créer le dossier)
database/migrations/*.php    -> database/migrations/
database/seeders/*.php       -> database/seeders/
resources/views/*            -> resources/views/
routes/web.php                -> routes/web.php (remplace le fichier existant)
```

⚠️ **`config/services.php`** : le fichier fourni ici n'est qu'un EXTRAIT.
Ouvrez votre `config/services.php` existant et ajoutez simplement la clé
`'google_books' => [...]` dedans (ne remplacez pas tout le fichier).

## 3. Installer les dépendances nécessaires

```bash
composer require barryvdh/laravel-dompdf     # export PDF
composer require maatwebsite/excel           # export Excel
```

## 4. Configurer l'environnement

Ajoutez dans votre fichier `.env` (voir `.env.example.add` fourni ici) :

```env
GOOGLE_BOOKS_API_KEY=
GALLICA_API_URL=https://gallica.bnf.fr/SRU
FILESYSTEM_DISK=public
```

## 5. Configurer Google Books, Open Library et Gallica

- **Google Books** fournit les informations principales (titre, auteur,
  éditeur, date d'édition, lien, image de couverture). Une clé
  `GOOGLE_BOOKS_API_KEY` est recommandée (quota plus confortable) mais pas
  obligatoire — l'API fonctionne aussi sans clé pour un usage léger.
- **Open Library** fournit la **cote Dewey** (champ `dewey_decimal_class` des
  éditions cataloguées). Aucune clé, aucune inscription : c'est une API
  publique gratuite. Elle remplace l'API **OCLC Classify**, définitivement
  fermée le 31 janvier 2024 et qui n'est plus accessible, gratuitement ou non.
- Si Google Books ne trouve pas le livre, le service se replie entièrement
  sur Open Library (titre, auteur, éditeur... et cote Dewey si disponible).
- Si Google Books et Open Library ne trouvent pas le livre, le service
  interroge le service SRU public de **Gallica (BnF)** par ISBN. Il récupère
  les métadonnées disponibles et le lien vers le document numérisé, sans clé
  API. `GALLICA_API_URL` permet de modifier l'URL du service si nécessaire.
- La couverture Dewey n'est pas garantie à 100 % : seuls les livres
  catalogués par une bibliothèque partenaire d'Open Library l'ont. Si le
  champ est absent, la cote reste vide dans le formulaire pour saisie
  manuelle — ce n'est pas une erreur.
- Ne commitez jamais une clé API ; le fichier `.env` doit rester hors du
  dépôt.

## 6. Base de données et stockage

```bash
php artisan migrate
php artisan db:seed --class=CategorySeeder   # rubriques de démo (optionnel)
php artisan storage:link                     # indispensable pour afficher les images scannées
```

## 7. Lancer le serveur

```bash
php artisan serve
```

Puis ouvrez `http://127.0.0.1:8000` — vous serez redirigé vers `/dashboard`.

## Fonctionnement de la recherche ISBN

1. Sur la page **Scanner** (`/scan`), l'utilisateur saisit un ISBN.
2. L'ISBN est envoyé en `POST` vers `/scan/analyze`
   (`ScanController::analyze`).
3. `BookCatalogService::findByIsbn()` :
   - interroge **Google Books** en premier (titre, auteur, éditeur, date,
     lien, image de couverture, catégories) ;
   - si Google Books répond, interroge ensuite **Open Library** de façon
     ciblée pour récupérer uniquement la **cote Dewey** (sans refaire tout
     le travail de résolution des auteurs, déjà obtenus via Google Books) ;
   - si Google Books ne trouve rien, **Open Library sert de repli complet**
     (titre, auteur, éditeur, date **et** cote Dewey si disponible) ;
   - si Open Library ne trouve rien non plus, le service SRU de **Gallica
     (BnF)** est interrogé pour rechercher l'ISBN et récupérer le document
     numérisé correspondant lorsqu'il est disponible.
4. Les métadonnées disponibles préremplissent la fiche, y compris la cote
   Dewey quand Open Library l'a trouvée et la couverture disponible
   (miniature Google Books ou Gallica, affichée sans téléchargement local).
5. Un enregistrement `Book` est créé avec le statut `a_verifier`, et
   l'utilisateur est redirigé vers la page de **vérification/modification**
   pré-remplie (`/books/{id}/edit`) pour corriger et valider les données —
   en particulier la cote Dewey et le rayon si Open Library n'avait rien
   trouvé.
6. Cliquer sur **"Enregistrer et valider"** passe le statut à `valide`.

## Pages livrées (correspondant aux maquettes fournies)

| Maquette                                   | Route          | Vue                          |
|---------------------------------------------|----------------|-------------------------------|
| Tableau de bord (stats + graphiques)         | `/dashboard`   | `dashboard/index.blade.php`   |
| Scan (caméra / import)                       | `/scan`        | `scan/index.blade.php`        |
| Liste de livres                              | `/books`       | `books/index.blade.php`       |
| Aperçu et export Excel                       | `/books/export` | `books/export.blade.php`     |
| Vérification / modification détaillée        | `/books/{id}/edit` | `books/edit.blade.php`    |

Toutes les vues sont **responsive** (grille fluide sur mobile, sidebar
desktop fixe + navigation basse sur mobile) et utilisent Tailwind CSS (CDN)
avec le design system exact fourni dans `BookManager/DESIGN.md` (couleurs,
typographie, arrondis, espacements).

## Ce qu'il reste à faire / points d'attention

- **Reconnaissance de code-barres** : pour lire un ISBN depuis une photo, vous
   pouvez ajouter une librairie JS comme
  `@zxing/browser` ou `quagga2` pour décoder le code-barres côté navigateur
  avant d'envoyer l'image.
- **Import Excel/PDF du catalogue** : le bouton "Importer (Excel/PDF)" de la
  maquette n'est pas encore branché côté backend (scaffold prêt côté
  contrôleur si vous voulez l'ajouter avec `maatwebsite/excel` en lecture).
- **Authentification** : aucune gestion d'utilisateurs n'a été ajoutée ici
  (le projet se concentre sur la fonctionnalité de scan/catalogue). Ajoutez
  Laravel Breeze/Fortify si vous avez besoin de comptes utilisateurs.
- **Design final** : vous avez mentionné qu'il reste des modifications de
  design à faire — la structure Blade est volontairement simple à modifier
  (chaque page = un seul fichier `.blade.php`) pour itérer facilement.
