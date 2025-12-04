<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Custom URLs for Administration project
    |--------------------------------------------------------------------------
    |
    | Put project-specific URLs here and override them per-environment in
    | your `.env` file. Example env keys: CUSTOM_API_URL, CUSTOM_FRONTEND_URL,
    | CUSTOM_MOBILE_URL
    |
    */

    'api_url' => env('CUSTOM_API_URL', 'https://administration.mgs.mg/api'),
    'frontend_url' => env('CUSTOM_FRONTEND_URL', 'https://administration.mgs.mg'),
    'mobile_url' => env('CUSTOM_MOBILE_URL', 'https://administration.mgs.mg'),
];
