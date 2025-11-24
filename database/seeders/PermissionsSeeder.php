<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions pour la gestion des patrimoines
        $patrimoinePermissions = [
            'voir_patrimoine',
            'creer_patrimoine',
            'modifier_patrimoine',
            'supprimer_patrimoine',
            'valider_patrimoine',
            'attribuer_patrimoine',
        ];

        foreach ($patrimoinePermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Permissions pour les demandes de fourniture
        $demandePermissions = [
            'voir_demande_fourniture',
            'creer_demande_fourniture',
            'modifier_demande_fourniture',
            'supprimer_demande_fourniture',
            'valider_demande_fourniture',
            'rejeter_demande_fourniture',
            'commander_fourniture',
            'livrer_fourniture',
        ];

        foreach ($demandePermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Permissions générales
        $generalPermissions = [
            'voir_utilisateurs',
            'creer_utilisateurs',
            'modifier_utilisateurs',
            'supprimer_utilisateurs',
            'voir_roles',
            'creer_roles',
            'modifier_roles',
            'supprimer_roles',
        ];

        foreach ($generalPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assigner toutes les permissions au rôle admin
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        $this->command->info('Permissions créées avec succès!');
        $this->command->info('Total: ' . Permission::count() . ' permissions');
    }
}

