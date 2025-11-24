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
        Schema::table('notifications', function (Blueprint $table) {
            // Ajouter le champ application pour filtrer les notifications
            // Valeurs possibles: 'administration', 'gestion-dossier', 'commercial', 'all'
            $table->string('application', 50)->default('all')->after('type');
            $table->index('application');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['application']);
            $table->dropColumn('application');
        });
    }
};
