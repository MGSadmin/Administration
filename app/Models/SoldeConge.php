<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoldeConge extends Model
{
    use HasFactory;

    protected $table = 'solde_conges';

    protected $fillable = [
        'organization_member_id',
        'conges_annuels_totaux',
        'conges_annuels_pris',
        'conges_annuels_restants',
        'conges_maladie_pris',
        'permissions_prises',
        'annee',
        'date_derniere_mise_a_jour',
    ];

    protected $casts = [
        'date_derniere_mise_a_jour' => 'date',
    ];

    public function organizationMember(): BelongsTo
    {
        return $this->belongsTo(OrganizationMember::class);
    }

    /**
     * Mettre à jour le solde de congés après validation
     */
    public function updateAfterCongeApproved(Conge $conge): void
    {
        if ($conge->type === Conge::TYPE_CONGE_ANNUEL) {
            $this->conges_annuels_pris += $conge->nb_jours;
            $this->conges_annuels_restants = $this->conges_annuels_totaux - $this->conges_annuels_pris;
        } elseif ($conge->type === Conge::TYPE_CONGE_MALADIE) {
            $this->conges_maladie_pris += $conge->nb_jours;
        } elseif ($conge->type === Conge::TYPE_PERMISSION) {
            $this->permissions_prises += $conge->nb_jours;
        }

        $this->date_derniere_mise_a_jour = now();
        $this->save();
    }

    /**
     * Réinitialiser les congés pour une nouvelle année
     */
    public function resetForNewYear(int $year): void
    {
        $this->annee = $year;
        $this->conges_annuels_pris = 0;
        $this->conges_annuels_restants = $this->conges_annuels_totaux;
        $this->conges_maladie_pris = 0;
        $this->permissions_prises = 0;
        $this->date_derniere_mise_a_jour = now();
        $this->save();
    }
}
