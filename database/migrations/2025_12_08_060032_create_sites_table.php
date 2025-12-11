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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Administration, Commercial, Gestion Dossier
            $table->string('domain')->unique(); // administration.mgs.mg
            $table->string('code')->unique(); // admin, commercial, debours
            $table->string('api_key')->unique(); // Clé API pour sécuriser les requêtes
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->json('config')->nullable(); // Configuration spécifique au site
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
