<?php

/*
|--------------------------------------------------------------------------
| Cloudinary Configuration — cloudinary-labs/cloudinary-laravel v3
|--------------------------------------------------------------------------
| Le package v3 lit ses credentials depuis ce fichier.
| Mettre dans Railway : Settings → Variables
|   CLOUDINARY_CLOUD_NAME=...
|   CLOUDINARY_API_KEY=...
|   CLOUDINARY_API_SECRET=...
|
| Alternativement, une seule variable CLOUDINARY_URL suffit :
|   CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name
|--------------------------------------------------------------------------
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Cloud Name
    |--------------------------------------------------------------------------
    */
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),

    /*
    |--------------------------------------------------------------------------
    | Cloudinary API Key
    |--------------------------------------------------------------------------
    */
    'api_key' => env('CLOUDINARY_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Cloudinary API Secret
    |--------------------------------------------------------------------------
    */
    'api_secret' => env('CLOUDINARY_API_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Cloudinary URL (alternative aux 3 variables séparées)
    | Format : cloudinary://api_key:api_secret@cloud_name
    |--------------------------------------------------------------------------
    */
    'url' => env('CLOUDINARY_URL'),

    /*
    |--------------------------------------------------------------------------
    | Dossier de base pour toutes les images du projet
    |--------------------------------------------------------------------------
    */
    'upload_folder' => env('CLOUDINARY_FOLDER', 'discovtrip'),

    /*
    |--------------------------------------------------------------------------
    | Options d'upload par défaut
    |--------------------------------------------------------------------------
    */
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', null),

    'secure' => true,

    'notification_url' => null,

];