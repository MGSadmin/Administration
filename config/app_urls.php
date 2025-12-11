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

    'administration' => env('ADMIN_APP_URL', 'http://localhost/administration'),
    'commercial' => env('COMMERCIAL_APP_URL', 'http://localhost/commercial'),
    'gestion' => env('GESTION_DOSSIER_APP_URL', 'http://localhost/gestion-dossier'),

    'apps' => [
        'administration' => [
            'url' => env('ADMIN_APP_URL', 'http://localhost/administration'),
            'login' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/auth/login',
            'logout' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/auth/logout',
            'dashboard' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/dashboard',
        ],
        'commercial' => [
            'url' => env('COMMERCIAL_APP_URL', 'http://localhost/commercial'),
            'login' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/auth/login?site=commercial',
            'logout' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/auth/logout',
            'dashboard' => env('COMMERCIAL_APP_URL', 'http://localhost/commercial') . '/dashboard',
        ],
        'gestion-dossier' => [
            'url' => env('GESTION_DOSSIER_APP_URL', 'http://localhost/gestion-dossier'),
            'login' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/auth/login?site=gestion',
            'logout' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/auth/logout',
            'dashboard' => env('GESTION_DOSSIER_APP_URL', 'http://localhost/gestion-dossier') . '/dashboard',
        ],
    ],

    'domain' => env('SESSION_DOMAIN', '.mgs-local.mg'),

    /*
    |--------------------------------------------------------------------------
    | Sites disponibles pour l'authentification
    |--------------------------------------------------------------------------
    */

    'sites' => [
        'admin' => [
            'name' => 'Administration',
            'code' => 'admin',
            'url' => env('ADMIN_APP_URL', 'http://localhost/administration'),
            'icon' => 'fas fa-users-cog',
            'color' => '#667eea',
            'description' => 'Gestion du personnel, congés et organigramme',
        ],
        'commercial' => [
            'name' => 'Commercial',
            'code' => 'commercial',
            'url' => env('COMMERCIAL_APP_URL', 'http://localhost/commercial'),
            'icon' => 'fas fa-chart-line',
            'color' => '#10b981',
            'description' => 'CRM, devis et opportunités commerciales',
        ],
        'gestion' => [
            'name' => 'Gestion Dossier',
            'code' => 'gestion',
            'url' => env('GESTION_DOSSIER_APP_URL', 'http://localhost/gestion-dossier'),
            'icon' => 'fas fa-folder-open',
            'color' => '#f59e0b',
            'description' => 'Gestion des dossiers clients',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration SSO
    |--------------------------------------------------------------------------
    */

    'sso' => [
        'enabled' => env('SSO_ENABLED', true),
        'token_lifetime' => env('SSO_TOKEN_LIFETIME', 7), // Durée en jours
        'auto_redirect' => env('SSO_AUTO_REDIRECT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Helper functions
    |--------------------------------------------------------------------------
    */

    // Get URL for any app
    /*'app_url' => function ($app) {
        return config("app_urls.apps.{$app}.url");
    },

    // Get login URL for any app
    'login_url' => function ($app) {
        return config("app_urls.apps.{$app}.login");
    },

    // Get logout URL for any app
    'logout_url' => function ($app) {
        return config("app_urls.apps.{$app}.logout");
    },*/
];
