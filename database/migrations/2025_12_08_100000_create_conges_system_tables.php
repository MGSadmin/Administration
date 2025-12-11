<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des congés
        Schema::create('conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_member_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Créateur de la demande
            $table->foreignId('validateur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', [
                'conge_annuel',
                'conge_maladie',
                'conge_maternite',
                'conge_paternite',
                'conge_sans_solde',
                'permission',
                'autre'
            ]);
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('nb_jours');
            $table->text('motif');
            $table->text('commentaire_rh')->nullable();
            $table->enum('statut', ['en_attente', 'approuve', 'refuse', 'annule'])->default('en_attente');
            $table->string('fichier_justificatif')->nullable(); // Pour certificat médical, etc.
            $table->timestamp('date_validation')->nullable();
            $table->timestamps();
        });

        // Table des demandes d'absence (vue globale)
        Schema::create('demandes_absence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_member_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'absence_justifiee',
                'absence_non_justifiee',
                'retard',
                'sortie_anticipee',
                'teletravail',
                'mission_externe',
                'formation'
            ]);
            $table->date('date');
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();
            $table->text('motif');
            $table->text('commentaire_rh')->nullable();
            $table->enum('statut', ['en_attente', 'approuve', 'refuse'])->default('en_attente');
            $table->string('fichier_justificatif')->nullable();
            $table->foreignId('validateur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_validation')->nullable();
            $table->timestamps();
        });

        // Table des documents d'employés
        Schema::create('documents_employe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_member_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Demandeur
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Créateur (RH)
            $table->enum('type_document', [
                // Documents en cours d'emploi
                'contrat_travail',
                'avenant_contrat',
                'fiche_poste',
                'attestation_travail',
                'certificat_emploi',
                'bulletin_paie',
                'attestation_salaire',
                'releve_annuel_salaires',
                'etat_conges',
                'etat_heures_supplementaires',
                'reglement_interieur',
                'pv_entretien_annuel',
                'decision_disciplinaire',
                'autorisation_absence',
                'note_service',
                // Documents de fin de contrat
                'certificat_travail_fin',
                'attestation_fin_contrat',
                'solde_tout_compte',
                'releve_droits_conges',
                'attestation_cnaps',
                'attestation_ostie',
                'lettre_licenciement',
                'lettre_recommandation',
                'certificat_non_dettes',
                'attestation_remise_materiel',
                // Autres
                'justificatif_remboursement',
                'attestation_versement_indemnites',
                'attestation_stage',
                'autre'
            ]);
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('fichier'); // Chemin du fichier
            $table->date('date_emission');
            $table->date('date_validite')->nullable();
            $table->enum('statut', ['actif', 'archive', 'perime'])->default('actif');
            $table->boolean('accessible_employe')->default(false); // L'employé peut-il le télécharger?
            $table->timestamp('date_demande')->nullable(); // Date de demande par l'employé
            $table->timestamp('date_remise')->nullable(); // Date de remise du document
            $table->timestamps();
        });

        // Table de l'historique des statuts des membres
        Schema::create('historique_statuts_membres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_member_id')->constrained()->onDelete('cascade');
            $table->string('ancien_statut');
            $table->string('nouveau_statut');
            $table->enum('motif', [
                'embauche',
                'promotion',
                'mutation',
                'demission',
                'licenciement',
                'retraite',
                'deces',
                'fin_contrat',
                'autre'
            ]);
            $table->text('commentaire')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // RH qui a fait le changement
            $table->date('date_effectif');
            $table->timestamps();
        });

        // Table du solde de congés
        Schema::create('solde_conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_member_id')->unique()->constrained()->onDelete('cascade');
            $table->integer('conges_annuels_totaux')->default(30); // 30 jours par an
            $table->integer('conges_annuels_pris')->default(0);
            $table->integer('conges_annuels_restants')->default(30);
            $table->integer('conges_maladie_pris')->default(0);
            $table->integer('permissions_prises')->default(0);
            $table->integer('annee')->default(2025);
            $table->date('date_derniere_mise_a_jour');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solde_conges');
        Schema::dropIfExists('historique_statuts_membres');
        Schema::dropIfExists('documents_employe');
        Schema::dropIfExists('demandes_absence');
        Schema::dropIfExists('conges');
    }
};
