<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueStatutMembre extends Model
{
    use HasFactory;

    protected $table = 'historique_statuts_membres';

    protected $fillable = [
        'organization_member_id',
        'ancien_statut',
        'nouveau_statut',
        'motif',
        'commentaire',
        'user_id',
        'date_effectif',
    ];

    protected $casts = [
        'date_effectif' => 'date',
    ];

    const MOTIF_EMBAUCHE = 'embauche';
    const MOTIF_PROMOTION = 'promotion';
    const MOTIF_MUTATION = 'mutation';
    const MOTIF_DEMISSION = 'demission';
    const MOTIF_LICENCIEMENT = 'licenciement';
    const MOTIF_RETRAITE = 'retraite';
    const MOTIF_DECES = 'deces';
    const MOTIF_FIN_CONTRAT = 'fin_contrat';
    const MOTIF_AUTRE = 'autre';

    public function organizationMember(): BelongsTo
    {
        return $this->belongsTo(OrganizationMember::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMotifLibelleAttribute(): string
    {
        return match($this->motif) {
            self::MOTIF_EMBAUCHE => 'Embauche',
            self::MOTIF_PROMOTION => 'Promotion',
            self::MOTIF_MUTATION => 'Mutation',
            self::MOTIF_DEMISSION => 'Démission',
            self::MOTIF_LICENCIEMENT => 'Licenciement',
            self::MOTIF_RETRAITE => 'Retraite',
            self::MOTIF_DECES => 'Décès',
            self::MOTIF_FIN_CONTRAT => 'Fin de contrat',
            default => 'Autre',
        };
    }
}
