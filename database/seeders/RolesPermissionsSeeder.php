<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🔄 Création des permissions...');

        // ==========================================
        // PERMISSIONS POUR ADMINISTRATION
        // ==========================================
        
        // Gestion des utilisateurs
        $adminPermissions = [
            'voir_utilisateurs',
            'creer_utilisateurs',
            'modifier_utilisateurs',
            'supprimer_utilisateurs',
            'voir_roles',
            'creer_roles',
            'modifier_roles',
            'supprimer_roles',
        ];

        // Gestion des patrimoines
        $patrimoinePermissions = [
            'voir_patrimoine',
            'creer_patrimoine',
            'modifier_patrimoine',
            'supprimer_patrimoine',
            'valider_patrimoine',
            'attribuer_patrimoine',
            'voir_statistiques_patrimoine',
        ];

        // Gestion des demandes de fourniture
        $demandePermissions = [
            'voir_toutes_demandes_fourniture',
            'voir_mes_demandes_fourniture',
            'creer_demande_fourniture',
            'modifier_demande_fourniture',
            'supprimer_demande_fourniture',
            'valider_demande_fourniture',
            'rejeter_demande_fourniture',
            'commander_fourniture',
            'livrer_fourniture',
        ];

        // Inventaire
        $inventairePermissions = [
            'voir_inventaire',
            'creer_inventaire',
            'modifier_inventaire',
            'supprimer_inventaire',
        ];

        // ==========================================
        // PERMISSIONS POUR GESTION-DOSSIER
        // ==========================================
        
        $gestionDossierPermissions = [
            // Dossiers
            'voir_dossiers',
            'creer_dossiers',
            'modifier_dossiers',
            'supprimer_dossiers',
            
            // Cotations
            'voir_cotations',
            'creer_cotations',
            'modifier_cotations',
            'supprimer_cotations',
            
            // Règlements débours
            'voir_reglements_debours',
            'creer_reglements_debours',
            'modifier_reglements_debours',
            'supprimer_reglements_debours',
            'valider_reglements_debours',
            
            // Règlements clients
            'voir_reglements_clients',
            'creer_reglements_clients',
            'modifier_reglements_clients',
            'supprimer_reglements_clients',
            
            // Factures
            'voir_factures',
            'creer_factures',
            'modifier_factures',
            'supprimer_factures',
        ];

        // ==========================================
        // PERMISSIONS POUR COMMERCIAL
        // ==========================================
        
        $commercialPermissions = [
            'voir_prospects',
            'creer_prospects',
            'modifier_prospects',
            'supprimer_prospects',
            'voir_clients',
            'creer_clients',
            'modifier_clients',
            'supprimer_clients',
            'voir_devis',
            'creer_devis',
            'modifier_devis',
            'supprimer_devis',
            'voir_contrats',
            'creer_contrats',
            'modifier_contrats',
            'supprimer_contrats',
        ];

        // Créer toutes les permissions
        $allPermissions = array_merge(
            $adminPermissions,
            $patrimoinePermissions,
            $demandePermissions,
            $inventairePermissions,
            $gestionDossierPermissions,
            $commercialPermissions
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->info('✅ ' . count($allPermissions) . ' permissions créées');

        // ==========================================
        // CRÉATION DES RÔLES
        // ==========================================
        
        $this->command->info('🔄 Création des rôles...');

        // 1. ADMINISTRATEUR - Accès total
        $adminRole = Role::firstOrCreate(['name' => 'administrateur', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());
        $this->command->info('✅ Rôle Administrateur créé (toutes les permissions)');

        // 2. DIRECTION - Validation fournitures + Lecture seule sauf gestion-dossier
        $directionRole = Role::firstOrCreate(['name' => 'direction', 'guard_name' => 'web']);
        $directionRole->syncPermissions([
            // Administration - Lecture seule
            'voir_utilisateurs',
            'voir_roles',
            'voir_patrimoine',
            'voir_statistiques_patrimoine',
            
            // Demandes fourniture - Validation
            'voir_toutes_demandes_fourniture',
            'valider_demande_fourniture',
            'rejeter_demande_fourniture',
            
            // Gestion-Dossier - Lecture + situations
            'voir_dossiers',
            'modifier_dossiers', // Pour les situations uniquement
            'voir_cotations',
            'voir_reglements_debours',
            'voir_reglements_clients',
            'voir_factures',
            
            // Commercial - Lecture seule
            'voir_prospects',
            'voir_clients',
            'voir_devis',
            'voir_contrats',
        ]);
        $this->command->info('✅ Rôle Direction créé');

        // 3. EMPLOYE COMMERCIAL - Full access commercial + Cotations gestion-dossier
        $commercialRole = Role::firstOrCreate(['name' => 'employe_commercial', 'guard_name' => 'web']);
        $commercialRole->syncPermissions([
            // Commercial - Tous les droits
            'voir_prospects',
            'creer_prospects',
            'modifier_prospects',
            'supprimer_prospects',
            'voir_clients',
            'creer_clients',
            'modifier_clients',
            'supprimer_clients',
            'voir_devis',
            'creer_devis',
            'modifier_devis',
            'supprimer_devis',
            'voir_contrats',
            'creer_contrats',
            'modifier_contrats',
            'supprimer_contrats',
            
            // Gestion-Dossier - Cotations
            'voir_cotations',
            'creer_cotations',
            'modifier_cotations',
            'supprimer_cotations',
            'voir_dossiers',
            
            // Demandes fourniture - Ses demandes
            'voir_mes_demandes_fourniture',
            'creer_demande_fourniture',
            'modifier_demande_fourniture',
        ]);
        $this->command->info('✅ Rôle Employé Commercial créé');

        // 4. RH - Gestion utilisateurs + Patrimoine + Inventaire
        $rhRole = Role::firstOrCreate(['name' => 'rh', 'guard_name' => 'web']);
        $rhRole->syncPermissions([
            // Gestion des utilisateurs - Complet
            'voir_utilisateurs',
            'creer_utilisateurs',
            'modifier_utilisateurs',
            'supprimer_utilisateurs',
            'voir_roles',
            
            // Patrimoine et Inventaire - Complet
            'voir_patrimoine',
            'creer_patrimoine',
            'modifier_patrimoine',
            'supprimer_patrimoine',
            'attribuer_patrimoine',
            'voir_statistiques_patrimoine',
            'voir_inventaire',
            'creer_inventaire',
            'modifier_inventaire',
            'supprimer_inventaire',
            
            // Demandes fourniture
            'voir_toutes_demandes_fourniture',
            'creer_demande_fourniture',
            'modifier_demande_fourniture',
            
            // Commercial - Lecture seule
            'voir_prospects',
            'voir_clients',
            'voir_devis',
            'voir_contrats',
            
            // Gestion-Dossier - Lecture seule
            'voir_dossiers',
            'voir_cotations',
            'voir_reglements_debours',
            'voir_reglements_clients',
            'voir_factures',
        ]);
        $this->command->info('✅ Rôle RH créé');

        // 5. SALES - Création/Modification dossiers
        $salesRole = Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);
        $salesRole->syncPermissions([
            // Gestion-Dossier - Création et modification
            'voir_dossiers',
            'creer_dossiers',
            'modifier_dossiers',
            'voir_cotations',
            
            // Commercial - Lecture clients/prospects
            'voir_prospects',
            'voir_clients',
            
            // Demandes fourniture - Ses demandes
            'voir_mes_demandes_fourniture',
            'creer_demande_fourniture',
            'modifier_demande_fourniture',
        ]);
        $this->command->info('✅ Rôle Sales créé');

        // 6. COMPTABLE - Règlements et factures
        $comptableRole = Role::firstOrCreate(['name' => 'comptable', 'guard_name' => 'web']);
        $comptableRole->syncPermissions([
            // Règlements débours - Complet
            'voir_reglements_debours',
            'creer_reglements_debours',
            'modifier_reglements_debours',
            'supprimer_reglements_debours',
            'valider_reglements_debours',
            
            // Règlements clients - Complet
            'voir_reglements_clients',
            'creer_reglements_clients',
            'modifier_reglements_clients',
            'supprimer_reglements_clients',
            
            // Factures - Complet
            'voir_factures',
            'creer_factures',
            'modifier_factures',
            'supprimer_factures',
            
            // Dossiers - Lecture seule
            'voir_dossiers',
            'voir_cotations',
            
            // Demandes fourniture - Ses demandes
            'voir_mes_demandes_fourniture',
            'creer_demande_fourniture',
            'modifier_demande_fourniture',
        ]);
        $this->command->info('✅ Rôle Comptable créé');

        // ==========================================
        // RÉSUMÉ
        // ==========================================
        
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('✨ SYSTÈME DE RÔLES ET PERMISSIONS CRÉÉ AVEC SUCCÈS');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('📊 STATISTIQUES:');
        $this->command->info('  • Permissions: ' . Permission::count());
        $this->command->info('  • Rôles: ' . Role::count());
        $this->command->info('');
        $this->command->info('👥 RÔLES CRÉÉS:');
        $this->command->info('  1. Administrateur - Accès complet à tout');
        $this->command->info('  2. Direction - Validation fournitures + lecture seule');
        $this->command->info('  3. Employé Commercial - Full commercial + cotations');
        $this->command->info('  4. RH - Gestion users + patrimoine + inventaire');
        $this->command->info('  5. Sales - Création/modification dossiers');
        $this->command->info('  6. Comptable - Règlements + factures');
        $this->command->info('');
        $this->command->info('📝 NOTES IMPORTANTES:');
        $this->command->info('  • Toutes les personnes voient uniquement LEURS demandes de fourniture');
        $this->command->info('  • Direction et RH peuvent voir TOUTES les demandes');
        $this->command->info('  • Direction peut valider les demandes de fourniture');
        $this->command->info('');
    }
}
