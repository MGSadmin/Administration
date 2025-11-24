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
        Schema::create('patrimoines', function (Blueprint $table) {
            $table->id();
            $table->string('code_materiel')->unique()->comment('Code unique du matériel');
            $table->string('designation')->comment('Désignation du matériel');
            $table->text('description')->nullable()->comment('Description détaillée');
            $table->enum('categorie', [
                'informatique',
                'mobilier',
                'vehicule',
                'equipement_bureau',
                'autres'
            ])->default('autres')->comment('Catégorie du matériel');
            $table->string('marque')->nullable()->comment('Marque du matériel');
            $table->string('modele')->nullable()->comment('Modèle du matériel');
            $table->string('numero_serie')->nullable()->comment('Numéro de série');
            $table->decimal('prix_achat', 15, 2)->nullable()->comment('Prix d\'achat');
            $table->date('date_achat')->comment('Date d\'achat du matériel');
            $table->foreignId('validateur_id')->nullable()->constrained('users')->onDelete('set null')->comment('Validateur de l\'achat');
            $table->date('date_validation')->nullable()->comment('Date de validation');
            $table->foreignId('utilisateur_id')->nullable()->constrained('users')->onDelete('set null')->comment('Utilisateur actuel du matériel');
            $table->date('date_attribution')->nullable()->comment('Date d\'attribution à l\'utilisateur');
            $table->enum('etat', [
                'neuf',
                'bon',
                'moyen',
                'mauvais',
                'en_reparation',
                'hors_service'
            ])->default('neuf')->comment('État du matériel');
            $table->enum('statut', [
                'disponible',
                'en_utilisation',
                'en_maintenance',
                'reforme'
            ])->default('disponible')->comment('Statut du matériel');
            $table->string('localisation')->nullable()->comment('Emplacement physique');
            $table->text('observation')->nullable()->comment('Observations');
            $table->string('facture')->nullable()->comment('Référence de la facture');
            $table->string('fournisseur')->nullable()->comment('Nom du fournisseur');
            $table->integer('duree_garantie_mois')->nullable()->comment('Durée de garantie en mois');
            $table->date('date_fin_garantie')->nullable()->comment('Date de fin de garantie');
            $table->timestamps();
            $table->softDeletes();

            // Index pour améliorer les performances
            $table->index('utilisateur_id');
            $table->index('validateur_id');
            $table->index('categorie');
            $table->index('statut');
            $table->index('date_achat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patrimoines');
    }
};
