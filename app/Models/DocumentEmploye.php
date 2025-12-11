<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentEmploye extends Model
{
    use HasFactory;

    protected $table = 'documents_employe';

    protected $fillable = [
        'organization_member_id',
        'user_id',
        'created_by',
        'type_document',
        'titre',
        'description',
        'fichier',
        'date_emission',
        'date_validite',
        'statut',
        'accessible_employe',
        'date_demande',
        'date_remise',
    ];

    protected $casts = [
        'date_emission' => 'date',
        'date_validite' => 'date',
        'accessible_employe' => 'boolean',
        'date_demande' => 'datetime',
        'date_remise' => 'datetime',
    ];

    // Types de documents en cours d'emploi
    const TYPE_CONTRAT_TRAVAIL = 'contrat_travail';
    const TYPE_AVENANT_CONTRAT = 'avenant_contrat';
    const TYPE_FICHE_POSTE = 'fiche_poste';
    const TYPE_ATTESTATION_TRAVAIL = 'attestation_travail';
    const TYPE_CERTIFICAT_EMPLOI = 'certificat_emploi';
    const TYPE_BULLETIN_PAIE = 'bulletin_paie';
    const TYPE_ATTESTATION_SALAIRE = 'attestation_salaire';
    const TYPE_RELEVE_ANNUEL_SALAIRES = 'releve_annuel_salaires';
    const TYPE_ETAT_CONGES = 'etat_conges';
    const TYPE_ETAT_HEURES_SUP = 'etat_heures_supplementaires';
    const TYPE_REGLEMENT_INTERIEUR = 'reglement_interieur';
    const TYPE_PV_ENTRETIEN = 'pv_entretien_annuel';
    const TYPE_DECISION_DISCIPLINAIRE = 'decision_disciplinaire';
    const TYPE_AUTORISATION_ABSENCE = 'autorisation_absence';
    const TYPE_NOTE_SERVICE = 'note_service';

    // Types de documents de fin de contrat
    const TYPE_CERTIFICAT_TRAVAIL_FIN = 'certificat_travail_fin';
    const TYPE_ATTESTATION_FIN_CONTRAT = 'attestation_fin_contrat';
    const TYPE_SOLDE_TOUT_COMPTE = 'solde_tout_compte';
    const TYPE_RELEVE_DROITS_CONGES = 'releve_droits_conges';
    const TYPE_ATTESTATION_CNAPS = 'attestation_cnaps';
    const TYPE_ATTESTATION_OSTIE = 'attestation_ostie';
    const TYPE_LETTRE_LICENCIEMENT = 'lettre_licenciement';
    const TYPE_LETTRE_RECOMMANDATION = 'lettre_recommandation';
    const TYPE_CERTIFICAT_NON_DETTES = 'certificat_non_dettes';
    const TYPE_ATTESTATION_REMISE_MATERIEL = 'attestation_remise_materiel';

    // Autres
    const TYPE_JUSTIFICATIF_REMBOURSEMENT = 'justificatif_remboursement';
    const TYPE_ATTESTATION_VERSEMENT = 'attestation_versement_indemnites';
    const TYPE_ATTESTATION_STAGE = 'attestation_stage';
    const TYPE_AUTRE = 'autre';

    const STATUT_ACTIF = 'actif';
    const STATUT_ARCHIVE = 'archive';
    const STATUT_PERIME = 'perime';

    public function organizationMember(): BelongsTo
    {
        return $this->belongsTo(OrganizationMember::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLibelleAttribute(): string
    {
        $types = [
            self::TYPE_CONTRAT_TRAVAIL => 'Contrat de travail',
            self::TYPE_AVENANT_CONTRAT => 'Avenant au contrat',
            self::TYPE_FICHE_POSTE => 'Fiche de poste',
            self::TYPE_ATTESTATION_TRAVAIL => 'Attestation de travail',
            self::TYPE_CERTIFICAT_EMPLOI => 'Certificat d\'emploi',
            self::TYPE_BULLETIN_PAIE => 'Bulletin de paie',
            self::TYPE_ATTESTATION_SALAIRE => 'Attestation de salaire',
            self::TYPE_RELEVE_ANNUEL_SALAIRES => 'Relevé annuel des salaires',
            self::TYPE_ETAT_CONGES => 'État des congés',
            self::TYPE_ETAT_HEURES_SUP => 'État des heures supplémentaires',
            self::TYPE_REGLEMENT_INTERIEUR => 'Règlement intérieur',
            self::TYPE_PV_ENTRETIEN => 'PV entretien annuel',
            self::TYPE_DECISION_DISCIPLINAIRE => 'Décision disciplinaire',
            self::TYPE_AUTORISATION_ABSENCE => 'Autorisation d\'absence',
            self::TYPE_NOTE_SERVICE => 'Note de service',
            self::TYPE_CERTIFICAT_TRAVAIL_FIN => 'Certificat de travail (fin)',
            self::TYPE_ATTESTATION_FIN_CONTRAT => 'Attestation de fin de contrat',
            self::TYPE_SOLDE_TOUT_COMPTE => 'Solde de tout compte',
            self::TYPE_RELEVE_DROITS_CONGES => 'Relevé des droits de congés',
            self::TYPE_ATTESTATION_CNAPS => 'Attestation CNAPS',
            self::TYPE_ATTESTATION_OSTIE => 'Attestation OSTIE',
            self::TYPE_LETTRE_LICENCIEMENT => 'Lettre de licenciement',
            self::TYPE_LETTRE_RECOMMANDATION => 'Lettre de recommandation',
            self::TYPE_CERTIFICAT_NON_DETTES => 'Certificat de non-dettes',
            self::TYPE_ATTESTATION_REMISE_MATERIEL => 'Attestation de remise du matériel',
            self::TYPE_JUSTIFICATIF_REMBOURSEMENT => 'Justificatif de remboursement',
            self::TYPE_ATTESTATION_VERSEMENT => 'Attestation de versement d\'indemnités',
            self::TYPE_ATTESTATION_STAGE => 'Attestation de stage',
            self::TYPE_AUTRE => 'Autre',
        ];

        return $types[$this->type_document] ?? $this->type_document;
    }

    public function scopeActif($query)
    {
        return $query->where('statut', self::STATUT_ACTIF);
    }

    public function scopeAccessibleEmploye($query)
    {
        return $query->where('accessible_employe', true);
    }
}
