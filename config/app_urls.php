<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application URLs Configuration
    |--------------------------------------------------------------------------
    |
    | Centralized URLs for all applications in the MGS ecosystem.
    | Environment variables can override these defaults.
    |
    */

    'administration' => env('ADMIN_APP_URL'),
    'commercial' => env('COMMERCIAL_APP_URL'),
    'gestion-dossier' => env('GESTION_DOSSIER_APP_URL'),

    'apps' => [
        'administration' => [
            'url' => env('ADMIN_APP_URL'),
            'login' => env('ADMIN_APP_URL') . '/login',
            'logout' => env('ADMIN_APP_URL') . '/logout',
            'dashboard' => env('ADMIN_APP_URL') . '/dashboard',
        ],
        'commercial' => [
            'url' => env('COMMERCIAL_APP_URL'),
            'login' => env('COMMERCIAL_APP_URL') . '/login',
            'logout' => env('COMMERCIAL_APP_URL') . '/logout',
            'dashboard' => env('COMMERCIAL_APP_URL') . '/dashboard',
        ],
        'gestion-dossier' => [
            'url' => env('GESTION_DOSSIER_APP_URL'),
            'login' => env('GESTION_DOSSIER_APP_URL') . '/login',
            'logout' => env('GESTION_DOSSIER_APP_URL') . '/logout',
            'dashboard' => env('GESTION_DOSSIER_APP_URL') . '/dashboard',
        ],
    ],

    'domain' => env('SESSION_DOMAIN', '.mgs-local.mg'),

    /*
    |--------------------------------------------------------------------------
    | Helper functions
    |--------------------------------------------------------------------------
    */

    // Get URL for any app
    'app_url' => function ($app) {
        return config("app_urls.apps.{$app}.url");
    },

    // Get login URL for any app
    'login_url' => function ($app) {
        return config("app_urls.apps.{$app}.login");
    },

    // Get logout URL for any app
    'logout_url' => function ($app) {
        return config("app_urls.apps.{$app}.logout");
    },
];
