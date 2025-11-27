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
        // Ajouter les colonnes manquantes au modèle Patrimoine pour la synchronisation mobile
        Schema::table('patrimoines', function (Blueprint $table) {
            // Colonnes de synchronisation
            $table->timestamp('date_modification')->nullable()->after('date_fin_garantie');
            $table->timestamp('last_synced_at')->nullable()->after('date_modification');
            
            // Metadata pour le tracking
            $table->string('sync_source')->nullable()->comment('web, mobile, api')->after('last_synced_at');
            
            // Soft delete pour compatibilité
            if (!Schema::hasColumn('patrimoines', 'deleted_at')) {
                $table->softDeletes()->after('sync_source');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patrimoines', function (Blueprint $table) {
            $table->dropColumn('date_modification');
            $table->dropColumn('last_synced_at');
            $table->dropColumn('sync_source');
            $table->dropSoftDeletes();
        });
    }
};
