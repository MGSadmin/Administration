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
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique()->comment('Nom du fournisseur');
            $table->string('contact')->nullable()->comment('Personne de contact');
            $table->string('telephone')->nullable()->comment('Numéro de téléphone');
            $table->string('email')->nullable()->comment('Email');
            $table->text('adresse')->nullable()->comment('Adresse complète');
            $table->string('nif')->nullable()->comment('Numéro NIF');
            $table->string('stat')->nullable()->comment('Numéro STAT');
            $table->enum('type', ['local', 'international'])->default('local')->comment('Type de fournisseur');
            $table->text('specialites')->nullable()->comment('Spécialités/domaines');
            $table->boolean('actif')->default(true)->comment('Fournisseur actif');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('nom');
            $table->index('actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fournisseurs');
    }
};
