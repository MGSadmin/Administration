<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Vérifier et ajouter les colonnes manquantes
            if (!Schema::hasColumn('users', 'matricule')) {
                $table->string('matricule')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'prenom')) {
                $table->string('prenom')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'telephone')) {
                $table->string('telephone')->nullable();
            }
            if (!Schema::hasColumn('users', 'poste')) {
                $table->string('poste')->nullable();
            }
            if (!Schema::hasColumn('users', 'departement')) {
                $table->string('departement')->nullable();
            }
            if (!Schema::hasColumn('users', 'date_embauche')) {
                $table->date('date_embauche')->nullable();
            }
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'matricule',
                'prenom',
                'telephone',
                'poste',
                'departement',
                'date_embauche',
                'photo',
                'is_active',
                'last_login_at'
            ]);
        });
    }
};
