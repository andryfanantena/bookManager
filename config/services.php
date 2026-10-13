<?php

// Configuration des services externes utilisés par BookManager.

return [

    'google_books' => [
        'api_key' => env('GOOGLE_BOOKS_API_KEY'),
    ],

    'gallica' => [
        'base_url' => env('GALLICA_API_URL', 'https://gallica.bnf.fr/SRU'),
    ],

];
