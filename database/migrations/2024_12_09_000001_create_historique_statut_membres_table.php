<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historique_statut_membres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_member_id')->constrained('organization_members')->onDelete('cascade');
            $table->string('ancien_statut');
            $table->string('nouveau_statut');
            $table->string('motif'); // DEMISSION, LICENCIEMENT, RETRAITE, MUTATION, PROMOTION, REAFFECTATION
            $table->text('commentaire')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Utilisateur qui a fait le changement
            $table->date('date_effectif')->nullable(); // Date effective du changement
            $table->timestamps();
        });

        // Table pour gérer les affectations de postes
        Schema::create('position_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('positions')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('ACTIVE'); // ACTIVE, VACANT, PENDING
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null'); // Qui a assigné
            $table->timestamps();
            
            // Index pour optimiser les recherches (nom raccourci pour MySQL)
            $table->index(['position_id', 'status', 'date_debut', 'date_fin'], 'pos_assign_search_idx');
        });

        // Table pour les demandes de réaffectation
        Schema::create('reaffectation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_member_id')->constrained('organization_members')->onDelete('cascade');
            $table->foreignId('current_position_id')->constrained('positions')->onDelete('cascade');
            $table->foreignId('new_position_id')->constrained('positions')->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('PENDING'); // PENDING, APPROVED, REJECTED
            $table->text('motif');
            $table->date('date_souhaite')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('commentaire_approbation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reaffectation_requests');
        Schema::dropIfExists('position_assignments');
        Schema::dropIfExists('historique_statut_membres');
    }
};
