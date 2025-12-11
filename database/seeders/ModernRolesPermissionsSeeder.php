<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ModernRolesPermissionsSeeder extends Seeder
{
    /**
     * Système de permissions granulaires pour Administration
     * Format: <module>.<action>
     * Exemples: "Voir Organigramme", "Créer Patrimoine", "Modifier Congé", "Supprimer Utilisateur"
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🔄 Création des permissions granulaires...');

        // ==========================================
        // PERMISSIONS - MODULE ORGANIGRAMME
        // ==========================================
        $organigrammePermissions = [
            'Voir Organigramme',
            'Créer Département',
            'Modifier Département',
            'Supprimer Département',
            'Créer Position',
            'Modifier Position',
            'Supprimer Position',
            'Créer Membre',
            'Modifier Membre',
            'Supprimer Membre',
            'Assigner Membre',
            'Gérer Réaffectation',
            'Gérer Démission',
            'Gérer Licenciement',
            'Gérer Retraite',
        ];

        // ==========================================
        // PERMISSIONS - MODULE PATRIMOINE
        // ==========================================
        $patrimoinePermissions = [
            'Voir Patrimoine',
            'Voir Tous Patrimoines',
            'Créer Patrimoine',
            'Modifier Patrimoine',
            'Supprimer Patrimoine',
            'Attribuer Patrimoine',
            'Libérer Patrimoine',
            'Voir Statistiques Patrimoine',
        ];

        // ==========================================
        // PERMISSIONS - MODULE FOURNITURES
        // ==========================================
        $fourniturePermissions = [
            'Voir Demande Fourniture',
            'Voir Toutes Demandes Fourniture',
            'Créer Demande Fourniture',
            'Modifier Demande Fourniture',
            'Supprimer Demande Fourniture',
            'Valider Demande Fourniture',
            'Rejeter Demande Fourniture',
            'Commander Fourniture',
            'Marquer Fourniture Reçue',
            'Livrer Fourniture',
            'Voir Statistiques Fourniture',
        ];

        // ==========================================
        // PERMISSIONS - MODULE CONGÉS/ABSENCES
        // ==========================================
        $congePermissions = [
            'Voir Congé',
            'Voir Tous Congés',
            'Créer Congé',
            'Modifier Congé',
            'Supprimer Congé',
            'Approuver Congé',
            'Rejeter Congé',
            'Voir Absence',
            'Voir Toutes Absences',
            'Créer Absence',
            'Approuver Absence',
            'Rejeter Absence',
        ];

        // ==========================================
        // PERMISSIONS - MODULE PERSONNEL (RH)
        // ==========================================
        $personnelPermissions = [
            'Voir Personnel',
            'Voir Détails Personnel',
            'Modifier Statut Personnel',
            'Voir Historique Personnel',
            'Gérer Documents Personnel',
            'Créer Document Personnel',
            'Télécharger Document Personnel',
            'Archiver Document Personnel',
            'Supprimer Document Personnel',
        ];

        // ==========================================
        // PERMISSIONS - MODULE ADMINISTRATION
        // ==========================================
        $adminPermissions = [
            'Voir Dashboard',
            'Voir Utilisateurs',
            'Créer Utilisateur',
            'Modifier Utilisateur',
            'Supprimer Utilisateur',
            'Révoquer Tokens Utilisateur',
            'Voir Rôles',
            'Créer Rôle',
            'Modifier Rôle',
            'Supprimer Rôle',
            'Gérer Permissions',
            'Voir Notifications',
            'Gérer Notifications',
        ];

        // Créer toutes les permissions
        $allPermissions = array_merge(
            $organigrammePermissions,
            $patrimoinePermissions,
            $fourniturePermissions,
            $congePermissions,
            $personnelPermissions,
            $adminPermissions
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->info('✅ ' . count($allPermissions) . ' permissions créées');

        // ==========================================
        // CRÉATION DES RÔLES
        // ==========================================
        
        $this->command->info('🔄 Création des rôles...');

        // 1. SUPER ADMINISTRATEUR - Accès total
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());
        $this->command->info('✅ Rôle Super Administrateur créé (toutes les permissions)');

        // 2. ADMINISTRATEUR - Gestion complète du système
        $admin = Role::firstOrCreate(['name' => 'administrateur', 'guard_name' => 'web']);
        $admin->syncPermissions([
            // Dashboard
            'Voir Dashboard',
            
            // Utilisateurs
            'Voir Utilisateurs',
            'Créer Utilisateur',
            'Modifier Utilisateur',
            'Supprimer Utilisateur',
            'Révoquer Tokens Utilisateur',
            
            // Rôles
            'Voir Rôles',
            'Créer Rôle',
            'Modifier Rôle',
            'Supprimer Rôle',
            'Gérer Permissions',
            
            // Organigramme
            'Voir Organigramme',
            'Créer Département',
            'Modifier Département',
            'Supprimer Département',
            'Créer Position',
            'Modifier Position',
            'Supprimer Position',
            'Créer Membre',
            'Modifier Membre',
            'Supprimer Membre',
            'Assigner Membre',
            'Gérer Réaffectation',
            'Gérer Démission',
            'Gérer Licenciement',
            'Gérer Retraite',
            
            // Patrimoine
            'Voir Patrimoine',
            'Voir Tous Patrimoines',
            'Créer Patrimoine',
            'Modifier Patrimoine',
            'Supprimer Patrimoine',
            'Attribuer Patrimoine',
            'Libérer Patrimoine',
            'Voir Statistiques Patrimoine',
            
            // Fournitures
            'Voir Demande Fourniture',
            'Voir Toutes Demandes Fourniture',
            'Créer Demande Fourniture',
            'Modifier Demande Fourniture',
            'Supprimer Demande Fourniture',
            'Valider Demande Fourniture',
            'Rejeter Demande Fourniture',
            'Commander Fourniture',
            'Marquer Fourniture Reçue',
            'Livrer Fourniture',
            'Voir Statistiques Fourniture',
            
            // Congés
            'Voir Congé',
            'Voir Tous Congés',
            'Créer Congé',
            'Modifier Congé',
            'Supprimer Congé',
            'Approuver Congé',
            'Rejeter Congé',
            'Voir Absence',
            'Voir Toutes Absences',
            'Créer Absence',
            'Approuver Absence',
            'Rejeter Absence',
            
            // Personnel
            'Voir Personnel',
            'Voir Détails Personnel',
            'Modifier Statut Personnel',
            'Voir Historique Personnel',
            'Gérer Documents Personnel',
            'Créer Document Personnel',
            'Télécharger Document Personnel',
            'Archiver Document Personnel',
            'Supprimer Document Personnel',
            
            // Notifications
            'Voir Notifications',
            'Gérer Notifications',
        ]);
        $this->command->info('✅ Rôle Administrateur créé');

        // 3. RH (Ressources Humaines) - Gestion du personnel et organigramme
        $rh = Role::firstOrCreate(['name' => 'rh', 'guard_name' => 'web']);
        $rh->syncPermissions([
            'Voir Dashboard',
            
            // Organigramme
            'Voir Organigramme',
            'Créer Département',
            'Modifier Département',
            'Créer Position',
            'Modifier Position',
            'Créer Membre',
            'Modifier Membre',
            'Assigner Membre',
            'Gérer Réaffectation',
            'Gérer Démission',
            'Gérer Licenciement',
            'Gérer Retraite',
            
            // Patrimoine
            'Voir Patrimoine',
            'Voir Tous Patrimoines',
            'Créer Patrimoine',
            'Modifier Patrimoine',
            'Attribuer Patrimoine',
            'Libérer Patrimoine',
            'Voir Statistiques Patrimoine',
            
            // Fournitures
            'Voir Demande Fourniture',
            'Voir Toutes Demandes Fourniture',
            'Créer Demande Fourniture',
            'Commander Fourniture',
            'Marquer Fourniture Reçue',
            'Livrer Fourniture',
            'Voir Statistiques Fourniture',
            
            // Congés
            'Voir Congé',
            'Voir Tous Congés',
            'Créer Congé',
            'Approuver Congé',
            'Rejeter Congé',
            'Voir Absence',
            'Voir Toutes Absences',
            'Créer Absence',
            'Approuver Absence',
            'Rejeter Absence',
            
            // Personnel
            'Voir Personnel',
            'Voir Détails Personnel',
            'Modifier Statut Personnel',
            'Voir Historique Personnel',
            'Gérer Documents Personnel',
            'Créer Document Personnel',
            'Télécharger Document Personnel',
            'Archiver Document Personnel',
            'Supprimer Document Personnel',
            
            // Notifications
            'Voir Notifications',
            'Gérer Notifications',
        ]);
        $this->command->info('✅ Rôle RH créé');

        // 4. DIRECTION - Validation et supervision
        $direction = Role::firstOrCreate(['name' => 'direction', 'guard_name' => 'web']);
        $direction->syncPermissions([
            'Voir Dashboard',
            
            // Organigramme (lecture seule)
            'Voir Organigramme',
            
            // Patrimoine (lecture et statistiques)
            'Voir Patrimoine',
            'Voir Tous Patrimoines',
            'Voir Statistiques Patrimoine',
            
            // Fournitures (validation)
            'Voir Demande Fourniture',
            'Voir Toutes Demandes Fourniture',
            'Valider Demande Fourniture',
            'Rejeter Demande Fourniture',
            'Voir Statistiques Fourniture',
            
            // Congés (validation)
            'Voir Congé',
            'Voir Tous Congés',
            'Approuver Congé',
            'Rejeter Congé',
            'Voir Absence',
            'Voir Toutes Absences',
            'Approuver Absence',
            'Rejeter Absence',
            
            // Personnel (consultation)
            'Voir Personnel',
            'Voir Détails Personnel',
            'Voir Historique Personnel',
            'Télécharger Document Personnel',
            
            // Notifications
            'Voir Notifications',
        ]);
        $this->command->info('✅ Rôle Direction créé');

        // 5. CHEF DE DÉPARTEMENT - Gestion d'équipe
        $chefDept = Role::firstOrCreate(['name' => 'chef-departement', 'guard_name' => 'web']);
        $chefDept->syncPermissions([
            'Voir Dashboard',
            
            // Organigramme (son département)
            'Voir Organigramme',
            'Modifier Membre',
            
            // Patrimoine (de son département)
            'Voir Patrimoine',
            'Attribuer Patrimoine',
            'Libérer Patrimoine',
            
            // Fournitures (de son département)
            'Voir Demande Fourniture',
            'Créer Demande Fourniture',
            'Modifier Demande Fourniture',
            'Valider Demande Fourniture',
            
            // Congés (de son équipe)
            'Voir Congé',
            'Créer Congé',
            'Approuver Congé',
            'Rejeter Congé',
            'Voir Absence',
            'Approuver Absence',
            'Rejeter Absence',
            
            // Personnel (de son département)
            'Voir Personnel',
            'Voir Détails Personnel',
            
            // Notifications
            'Voir Notifications',
        ]);
        $this->command->info('✅ Rôle Chef de Département créé');

        // 6. EMPLOYÉ - Accès de base
        $employe = Role::firstOrCreate(['name' => 'employe', 'guard_name' => 'web']);
        $employe->syncPermissions([
            'Voir Dashboard',
            
            // Organigramme (lecture seule)
            'Voir Organigramme',
            
            // Patrimoine (ses propres patrimoines)
            'Voir Patrimoine',
            
            // Fournitures (ses propres demandes)
            'Voir Demande Fourniture',
            'Créer Demande Fourniture',
            'Modifier Demande Fourniture',
            
            // Congés (ses propres demandes)
            'Voir Congé',
            'Créer Congé',
            'Voir Absence',
            'Créer Absence',
            
            // Documents (ses propres documents)
            'Télécharger Document Personnel',
            
            // Notifications
            'Voir Notifications',
        ]);
        $this->command->info('✅ Rôle Employé créé');

        // ==========================================
        // CRÉER UTILISATEUR SUPER ADMIN PAR DÉFAUT
        // ==========================================
        
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@mgs.mg'],
            [
                'name' => 'Super Administrateur',
                'password' => Hash::make('Admin@2025'),
            ]
        );
        
        if (!$superAdminUser->hasRole('super-admin')) {
            $superAdminUser->assignRole('super-admin');
        }

        $this->command->info('✅ Utilisateur Super Admin créé/mis à jour');
        $this->command->info('   Email: admin@mgs.mg');
        $this->command->info('   Mot de passe: Admin@2025');
        
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('✓ Système de permissions moderne créé avec succès!');
        $this->command->info('✓ Total: ' . count($allPermissions) . ' permissions');
        $this->command->info('✓ Rôles: 6 (super-admin, administrateur, rh, direction, chef-departement, employe)');
        $this->command->info('═══════════════════════════════════════════════════════════');
    }
}
