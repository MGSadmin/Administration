<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Configuration des tokens Passport
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        // Activer les routes Passport
        Passport::enablePasswordGrant();
        
        // Définir les scopes si nécessaire
        Passport::tokensCan([
            'user-read' => 'Lire les informations utilisateur',
            'user-write' => 'Modifier les informations utilisateur',
            'notifications' => 'Gérer les notifications',
        ]);

        Passport::setDefaultScope([
            'user-read',
        ]);
    }
}
