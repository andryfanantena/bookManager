<?php

// Ce fichier est un EXTRAIT à fusionner avec votre config/services.php existant.
// Ajoutez simplement la clé 'google_books' ci-dessous dans le tableau retourné par
// votre config/services.php (ne remplacez pas tout le fichier si vous avez
// déjà d'autres services configurés comme mailgun, postmark, etc.)

return [

    // ... vos autres services (mailgun, postmark, ses, slack...) restent ici ...

    'google_books' => [
        'api_key' => env('GOOGLE_BOOKS_API_KEY'),
    ],

];
