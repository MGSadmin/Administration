<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;

class SitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = [
            [
                'name' => 'Administration',
                'domain' => 'administration.mgs.mg',
                'code' => 'admin',
                'description' => 'Serveur central d\'authentification SSO et gestion des utilisateurs',
                'is_active' => true,
                'config' => [
                    'role' => 'Serveur Central SSO',
                    'features' => ['authentication', 'user_management', 'role_management', 'permissions'],
                ],
            ],
            [
                'name' => 'Commercial',
                'domain' => 'commercial.mgs.mg',
                'code' => 'commercial',
                'description' => 'Application de gestion commerciale (CRM, devis, opportunités)',
                'is_active' => true,
                'config' => [
                    'role' => 'Client SSO',
                    'features' => ['crm', 'quotes', 'opportunities', 'clients'],
                ],
            ],
            [
                'name' => 'Gestion Dossier',
                'domain' => 'debours.mgs.mg',
                'code' => 'debours',
                'description' => 'Application de gestion des dossiers et débours',
                'is_active' => true,
                'config' => [
                    'role' => 'Client SSO',
                    'features' => ['expenses', 'files', 'payments', 'documents'],
                ],
            ],
        ];

        foreach ($sites as $siteData) {
            Site::create($siteData);
        }

        $this->command->info('✓ Sites créés avec succès');
    }
}
