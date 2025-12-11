<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions pour Administration
        $adminPermissions = [
            'admin.view_dashboard',
            'admin.manage_users',
            'admin.create_user',
            'admin.edit_user',
            'admin.delete_user',
            'admin.manage_roles',
            'admin.create_role',
            'admin.edit_role',
            'admin.delete_role',
            'admin.manage_permissions',
            'admin.manage_sites',
            'admin.view_logs',
            'admin.manage_patrimoines',
            'admin.manage_demandes',
        ];

        // Permissions pour Commercial
        $commercialPermissions = [
            'commercial.view_dashboard',
            'commercial.manage_clients',
            'commercial.create_client',
            'commercial.edit_client',
            'commercial.delete_client',
            'commercial.view_clients',
            'commercial.manage_devis',
            'commercial.create_devis',
            'commercial.edit_devis',
            'commercial.delete_devis',
            'commercial.view_devis',
            'commercial.manage_opportunities',
            'commercial.create_opportunity',
            'commercial.edit_opportunity',
            'commercial.delete_opportunity',
            'commercial.view_opportunities',
            'commercial.view_reports',
            'commercial.export_data',
        ];

        // Permissions pour Gestion Dossier (Débours)
        $deboursPermissions = [
            'debours.view_dashboard',
            'debours.view_expenses',
            'debours.create_expense',
            'debours.edit_expense',
            'debours.delete_expense',
            'debours.approve_expenses',
            'debours.reject_expenses',
            'debours.create_payment',
            'debours.view_payments',
            'debours.manage_dossiers',
            'debours.create_dossier',
            'debours.edit_dossier',
            'debours.delete_dossier',
            'debours.view_dossiers',
            'debours.view_reports',
            'debours.export_data',
        ];

        // Créer toutes les permissions
        foreach (array_merge($adminPermissions, $commercialPermissions, $deboursPermissions) as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Créer les rôles

        // Super Admin - Accès total à tous les sites
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - Accès administration uniquement
        $admin = Role::create(['name' => 'Administrateur']);
        $admin->givePermissionTo($adminPermissions);

        // Manager Commercial - Accès complet au commercial
        $commercialManager = Role::create(['name' => 'Manager Commercial']);
        $commercialManager->givePermissionTo($commercialPermissions);

        // Commercial - Accès limité au commercial
        $commercial = Role::create(['name' => 'Commercial']);
        $commercial->givePermissionTo([
            'commercial.view_dashboard',
            'commercial.view_clients',
            'commercial.create_client',
            'commercial.edit_client',
            'commercial.view_devis',
            'commercial.create_devis',
            'commercial.view_opportunities',
            'commercial.create_opportunity',
        ]);

        // Gestionnaire Débours - Accès complet gestion dossier
        $deboursManager = Role::create(['name' => 'Gestionnaire Débours']);
        $deboursManager->givePermissionTo($deboursPermissions);

        // Assistant Débours - Accès limité
        $deboursAssistant = Role::create(['name' => 'Assistant Débours']);
        $deboursAssistant->givePermissionTo([
            'debours.view_dashboard',
            'debours.view_expenses',
            'debours.create_expense',
            'debours.view_payments',
            'debours.view_dossiers',
        ]);

        // Comptable - Accès multi-sites pour la comptabilité
        $comptable = Role::create(['name' => 'Comptable']);
        $comptable->givePermissionTo([
            'admin.view_dashboard',
            'commercial.view_reports',
            'commercial.view_devis',
            'commercial.export_data',
            'debours.view_reports',
            'debours.view_expenses',
            'debours.view_payments',
            'debours.export_data',
        ]);

        // Créer un utilisateur Super Admin par défaut
        $superAdminUser = User::create([
            'name' => 'Super Administrateur',
            'email' => 'admin@mgs.mg',
            'password' => Hash::make('password'),
        ]);
        $superAdminUser->assignRole('Super Admin');

        $this->command->info('✓ Rôles et permissions créés avec succès');
        $this->command->info('✓ Utilisateur Super Admin créé: admin@mgs.mg / password');
    }
}
