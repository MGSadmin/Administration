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
        Schema::table('demandes_fourniture', function (Blueprint $table) {
            $table->string('designation')->after('objet')->comment('Désignation précise du matériel/fourniture');
            $table->foreignId('fournisseur_id')->nullable()->after('fournisseur')->constrained('fournisseurs')->onDelete('set null')->comment('Fournisseur sélectionné');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes_fourniture', function (Blueprint $table) {
            $table->dropForeign(['fournisseur_id']);
            $table->dropColumn(['designation', 'fournisseur_id']);
        });
    }
};
