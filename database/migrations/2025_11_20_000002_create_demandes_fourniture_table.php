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
        Schema::create('demandes_fourniture', function (Blueprint $table) {
            $table->id();
            $table->string('numero_demande')->unique()->comment('Numéro unique de la demande');
            $table->foreignId('demandeur_id')->constrained('users')->onDelete('cascade')->comment('Utilisateur demandeur');
            $table->string('objet')->comment('Objet de la demande');
            $table->text('description')->comment('Description détaillée de la demande');
            $table->enum('type_fourniture', [
                'materiel_informatique',
                'fourniture_bureau',
                'mobilier',
                'equipement',
                'consommable',
                'autres'
            ])->default('autres')->comment('Type de fourniture demandée');
            $table->integer('quantite')->default(1)->comment('Quantité demandée');
            $table->enum('priorite', ['faible', 'normale', 'urgente'])->default('normale')->comment('Niveau de priorité');
            $table->text('justification')->nullable()->comment('Justification de la demande');
            $table->decimal('budget_estime', 15, 2)->nullable()->comment('Budget estimé');
            
            // Workflow de validation
            $table->enum('statut', [
                'en_attente',
                'en_cours_validation',
                'validee',
                'rejetee',
                'en_cours_achat',
                'commandee',
                'recue',
                'livree',
                'annulee'
            ])->default('en_attente')->comment('Statut de la demande');
            
            $table->foreignId('validateur_id')->nullable()->constrained('users')->onDelete('set null')->comment('Validateur de la demande');
            $table->date('date_validation')->nullable()->comment('Date de validation');
            $table->text('motif_rejet')->nullable()->comment('Motif de rejet si applicable');
            $table->text('commentaire_validateur')->nullable()->comment('Commentaire du validateur');
            
            // Informations sur l'achat
            $table->foreignId('acheteur_id')->nullable()->constrained('users')->onDelete('set null')->comment('Responsable de l\'achat');
            $table->decimal('montant_reel', 15, 2)->nullable()->comment('Montant réel de l\'achat');
            $table->date('date_commande')->nullable()->comment('Date de commande');
            $table->date('date_reception')->nullable()->comment('Date de réception');
            $table->date('date_livraison')->nullable()->comment('Date de livraison');
            $table->string('fournisseur')->nullable()->comment('Fournisseur');
            $table->string('bon_commande')->nullable()->comment('Numéro de bon de commande');
            $table->string('facture')->nullable()->comment('Numéro de facture');
            
            // Notification
            $table->foreignId('notifier_user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Utilisateur à notifier');
            $table->boolean('notification_envoyee')->default(false)->comment('Notification envoyée');
            $table->timestamp('date_notification')->nullable()->comment('Date d\'envoi de la notification');
            
            $table->text('observation')->nullable()->comment('Observations générales');
            $table->timestamps();
            $table->softDeletes();

            // Index pour améliorer les performances
            $table->index('demandeur_id');
            $table->index('validateur_id');
            $table->index('statut');
            $table->index('priorite');
            $table->index('numero_demande');
            $table->index('notifier_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes_fourniture');
    }
};
