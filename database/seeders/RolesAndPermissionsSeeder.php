<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\UserApplication;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ========================================
        // PERMISSIONS POUR GESTION-DOSSIER
        // ========================================
        $gestionDossierPermissions = [
            // Dossiers
            'dossier.view' => 'Voir les dossiers',
            'dossier.create' => 'Créer des dossiers',
            'dossier.edit' => 'Modifier des dossiers',
            'dossier.delete' => 'Supprimer des dossiers',
            
            // Factures
            'facture.view' => 'Voir les factures',
            'facture.create' => 'Créer des factures',
            'facture.edit' => 'Modifier des factures',
            'facture.delete' => 'Supprimer des factures',
            
            // Règlements
            'reglement.view' => 'Voir les règlements',
            'reglement.create' => 'Créer des règlements',
            'reglement.edit' => 'Modifier des règlements',
            'reglement.delete' => 'Supprimer des règlements',
            
            // Cotations
            'cotation.view' => 'Voir les cotations',
            'cotation.create' => 'Créer des cotations',
            'cotation.edit' => 'Modifier des cotations',
            'cotation.delete' => 'Supprimer des cotations',
            
            // Production
            'production.view' => 'Voir le tableau de production',
            
            // Situations
            'situation.view' => 'Voir les situations',
            'situation.create' => 'Créer des situations',
            'situation.edit' => 'Modifier des situations',
            'situation.delete' => 'Supprimer des situations',
            'situation.assign' => 'Assigner des situations',
            
            // Débours
            'debours.view' => 'Voir les débours',
            'debours.create' => 'Créer des débours',
            'debours.edit' => 'Modifier des débours',
            'debours.delete' => 'Supprimer des débours',
            'debours.assign' => 'Assigner des débours',
            'debours.validate' => 'Valider des débours',
        ];

        // ========================================
        // PERMISSIONS POUR COMMERCIAL
        // ========================================
        $commercialAppPermissions = [
            // Devis commerciaux
            'devis.view' => 'Voir les devis',
            'devis.create' => 'Créer des devis',
            'devis.edit' => 'Modifier des devis',
            'devis.delete' => 'Supprimer des devis',
            'devis.validate' => 'Valider des devis',
            
            // Clients
            'client.view' => 'Voir les clients',
            'client.create' => 'Créer des clients',
            'client.edit' => 'Modifier des clients',
            'client.delete' => 'Supprimer des clients',
            
            // Statistiques commerciales
            'commercial.stats' => 'Voir les statistiques commerciales',
        ];

        // ========================================
        // PERMISSIONS POUR ADMINISTRATION
        // ========================================
        $administrationPermissions = [
            // Utilisateurs
            'users.view' => 'Voir les utilisateurs',
            'users.create' => 'Créer des utilisateurs',
            'users.edit' => 'Modifier des utilisateurs',
            'users.delete' => 'Supprimer des utilisateurs',
            'users.active.view' => 'Voir les utilisateurs actifs',
            'users.status.manage' => 'Gérer le statut des utilisateurs',
            
            // Rôles
            'roles.view' => 'Voir les rôles',
            'roles.create' => 'Créer des rôles',
            'roles.edit' => 'Modifier des rôles',
            'roles.delete' => 'Supprimer des rôles',
            
            // Permissions
            'permissions.view' => 'Voir les permissions',
            'permissions.create' => 'Créer des permissions',
            'permissions.delete' => 'Supprimer des permissions',
            
            // Système
            'system.backup' => 'Gérer les sauvegardes',
            'system.logs' => 'Voir les logs système',
            'system.settings' => 'Modifier les paramètres système',
        ];

        // Créer toutes les permissions
        $allPermissions = array_merge(
            $gestionDossierPermissions,
            $commercialAppPermissions,
            $administrationPermissions
        );

        foreach ($allPermissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
        }

        $this->command->info('✓ ' . count($allPermissions) . ' permissions créées');

        // ========================================
        // CRÉATION DES RÔLES
        // ========================================

        // 1. SUPER ADMIN - Accès total à tout
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->syncPermissions(Permission::all());
        $this->command->info('✓ Rôle super-admin créé avec toutes les permissions');

        // 2. ADMIN - Accès administration + lecture des autres apps
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $adminPermissions = array_merge(
            array_keys($administrationPermissions),
            ['dossier.view', 'facture.view', 'reglement.view', 'production.view', 'devis.view', 'client.view']
        );
        $admin->syncPermissions($adminPermissions);
        $this->command->info('✓ Rôle admin créé');

        // 3. DIRECTION - Tout voir, gérer l'essentiel (pas admin système)
        $direction = Role::firstOrCreate(['name' => 'direction']);
        $directionPermissions = [];
        foreach (Permission::all() as $perm) {
            // Direction a tout sauf la gestion système
            if (!str_starts_with($perm->name, 'system.') && 
                !in_array($perm->name, ['users.delete', 'roles.delete', 'permissions.delete'])) {
                $directionPermissions[] = $perm->name;
            }
        }
        $direction->syncPermissions($directionPermissions);
        $this->command->info('✓ Rôle direction créé');

        // 4. COMMERCIAL - Gestion complète dossiers et devis
        $commercial = Role::firstOrCreate(['name' => 'commercial']);
        $commercial->syncPermissions([
            // Gestion-dossier
            'dossier.view', 'dossier.create', 'dossier.edit', 'dossier.delete',
            'cotation.view', 'cotation.create', 'cotation.edit', 'cotation.delete',
            'facture.view', 'reglement.view',
            'situation.view', 'situation.create', 'situation.edit', 'situation.assign',
            'debours.view', 'debours.create', 'debours.edit', 'debours.assign',
            'users.active.view',
            // Commercial
            'devis.view', 'devis.create', 'devis.edit', 'devis.delete',
            'client.view', 'client.create', 'client.edit',
            'commercial.stats',
        ]);
        $this->command->info('✓ Rôle commercial créé');

        // 5. FACTURE - Gestion des factures uniquement
        $facture = Role::firstOrCreate(['name' => 'facture']);
        $facture->syncPermissions([
            'dossier.view',
            'facture.view', 'facture.create', 'facture.edit', 'facture.delete',
            'reglement.view',
            'cotation.view',
        ]);
        $this->command->info('✓ Rôle facture créé');

        // 6. COMPTABLE - Gestion financière et règlements
        $comptable = Role::firstOrCreate(['name' => 'comptable']);
        $comptable->syncPermissions([
            'dossier.view',
            'facture.view',
            'reglement.view', 'reglement.create', 'reglement.edit', 'reglement.delete',
            'situation.view',
            'debours.view', 'debours.validate',
            'users.active.view',
            'production.view',
        ]);
        $this->command->info('✓ Rôle comptable créé');

        // 7. PRODUCTION - Suivi de production et situations
        $production = Role::firstOrCreate(['name' => 'production']);
        $production->syncPermissions([
            'dossier.view',
            'situation.view', 'situation.create', 'situation.edit', 'situation.assign',
            'debours.view',
            'production.view',
            'users.active.view',
        ]);
        $this->command->info('✓ Rôle production créé');

        // 8. CONSULTATION - Lecture seule
        $consultation = Role::firstOrCreate(['name' => 'consultation']);
        $consultation->syncPermissions([
            'dossier.view',
            'facture.view',
            'reglement.view',
            'cotation.view',
            'situation.view',
            'debours.view',
            'production.view',
            'devis.view',
            'client.view',
        ]);
        $this->command->info('✓ Rôle consultation créé');

        // Créer un utilisateur super-admin si aucun n'existe
        if (!User::where('email', 'admin@mgs-local.mg')->exists()) {
            $superAdminUser = User::create([
                'name' => 'Admin',
                'prenom' => 'Système',
                'matricule' => 'ADM001',
                'email' => 'admin@mgs-local.mg',
                'password' => Hash::make('Admin@2025'),
                'telephone' => '+261 34 00 000 00',
                'poste' => 'Administrateur Système',
                'departement' => 'IT',
                'date_embauche' => now(),
                'is_active' => true,
            ]);

            $superAdminUser->assignRole('super-admin');

            // Donner accès à toutes les applications
            UserApplication::create([
                'user_id' => $superAdminUser->id,
                'application' => 'administration',
                'role' => 'super-admin',
                'status' => 'active',
            ]);

            UserApplication::create([
                'user_id' => $superAdminUser->id,
                'application' => 'commercial',
                'role' => 'admin',
                'status' => 'active',
            ]);

            UserApplication::create([
                'user_id' => $superAdminUser->id,
                'application' => 'gestion-dossier',
                'role' => 'admin',
                'status' => 'active',
            ]);

            $this->command->info('✓ Super Admin créé - Email: admin@mgs-local.mg / Password: Admin@2025');
        }

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('✓ Tous les rôles et permissions ont été créés avec succès !');
        $this->command->info('========================================');
        $this->command->info('Rôles créés : super-admin, admin, direction, commercial, facture, comptable, production, consultation');
        $this->command->info('Total permissions : ' . Permission::count());
    }
}
