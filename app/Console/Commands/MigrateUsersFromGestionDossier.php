<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserApplication;
use Spatie\Permission\Models\Role;

class MigrateUsersFromGestionDossier extends Command
{
    protected $signature = 'migrate:users-from-gestion-dossier';
    protected $description = 'Migrate users and roles from gestion_dossiers database to administration';

    public function handle()
    {
        $this->info('🚀 Migration des utilisateurs depuis gestion_dossiers...');
        
        // Connexion à la base de données gestion_dossiers
        $gestionUsers = DB::connection('mysql')->table('gestion_dossiers.users')->get();
        
        $this->info("📊 {$gestionUsers->count()} utilisateur(s) trouvé(s) dans gestion_dossiers");
        
        $migrated = 0;
        $skipped = 0;
        
        foreach ($gestionUsers as $gestionUser) {
            // Vérifier si l'utilisateur existe déjà
            $existingUser = User::where('email', $gestionUser->email)->first();
            
            if ($existingUser) {
                $this->warn("⏭️  Utilisateur {$gestionUser->email} existe déjà - ignoré");
                $skipped++;
                continue;
            }
            
            // Créer l'utilisateur
            $user = User::create([
                'name' => $gestionUser->name,
                'email' => $gestionUser->email,
                'password' => $gestionUser->password, // Déjà hashé
                'email_verified_at' => $gestionUser->email_verified_at,
                'is_active' => true,
            ]);
            
            // Récupérer les rôles de l'utilisateur depuis gestion_dossiers
            $userRoles = DB::connection('mysql')
                ->table('gestion_dossiers.model_has_roles')
                ->where('model_id', $gestionUser->id)
                ->where('model_type', 'App\Models\User')
                ->pluck('role_id');
            
            foreach ($userRoles as $roleId) {
                $roleName = DB::connection('mysql')
                    ->table('gestion_dossiers.roles')
                    ->where('id', $roleId)
                    ->value('name');
                
                // Assigner le rôle (s'il existe dans administration)
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $user->assignRole($role);
                    $this->info("   ✓ Rôle '{$roleName}' assigné");
                } else {
                    // Créer le rôle s'il n'existe pas
                    $newRole = Role::create(['name' => $roleName]);
                    $user->assignRole($newRole);
                    $this->info("   ✓ Rôle '{$roleName}' créé et assigné");
                }
            }
            
            // Donner accès à gestion-dossier par défaut
            UserApplication::create([
                'user_id' => $user->id,
                'application' => 'gestion-dossier',
                'role' => $userRoles->isNotEmpty() ? 
                    DB::connection('mysql')->table('gestion_dossiers.roles')->where('id', $userRoles->first())->value('name') : 
                    'user',
                'status' => 'active',
            ]);
            
            $this->info("✅ Utilisateur {$user->email} migré avec succès");
            $migrated++;
        }
        
        $this->newLine();
        $this->info("📈 Résumé de la migration:");
        $this->table(
            ['Statut', 'Nombre'],
            [
                ['Migrés', $migrated],
                ['Ignorés (déjà existants)', $skipped],
                ['Total', $gestionUsers->count()],
            ]
        );
        
        $this->newLine();
        $this->info('✨ Migration terminée avec succès!');
        
        return Command::SUCCESS;
    }
}
