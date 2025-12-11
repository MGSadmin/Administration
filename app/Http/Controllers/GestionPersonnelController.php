<?php

namespace App\Http\Controllers;

use App\Models\OrganizationMember;
use App\Models\HistoriqueStatutMembre;
use App\Models\DocumentEmploye;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GestionPersonnelController extends Controller
{
    /**
     * Afficher la page de gestion du personnel
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['RH', 'Ressources Humaines', 'Admin', 'super-admin'])) {
            abort(403, 'Accès réservé au service RH.');
        }
        
        $membres = OrganizationMember::with(['position.department', 'user', 'soldeConges'])
            ->orderBy('status')
            ->paginate(20);
        
        $stats = [
            'actifs' => OrganizationMember::where('status', OrganizationMember::STATUS_ACTIVE)->count(),
            'vacants' => OrganizationMember::where('status', OrganizationMember::STATUS_VACANT)->count(),
            'licencies' => OrganizationMember::where('status', OrganizationMember::STATUS_LICENCIE)->count(),
            'total' => OrganizationMember::count(),
        ];
        
        return view('personnel.index', compact('membres', 'stats'));
    }

    /**
     * Afficher le profil d'un membre
     */
    public function show(OrganizationMember $membre)
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['RH', 'Ressources Humaines', 'Direction', 'Admin', 'super-admin'])) {
            abort(403, 'Accès non autorisé.');
        }
        
        $membre->load([
            'position.department',
            'user',
            'soldeConges',
            'conges' => fn($q) => $q->orderBy('created_at', 'desc')->limit(10),
            'demandesAbsence' => fn($q) => $q->orderBy('date', 'desc')->limit(10),
            'documents' => fn($q) => $q->orderBy('created_at', 'desc'),
            'historiqueStatuts' => fn($q) => $q->orderBy('date_effectif', 'desc'),
        ]);
        
        return view('personnel.show', compact('membre'));
    }

    /**
     * Formulaire de changement de statut
     */
    public function changeStatusForm(OrganizationMember $membre)
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['RH', 'Ressources Humaines', 'Admin', 'super-admin'])) {
            abort(403, 'Accès réservé au service RH.');
        }
        
        $membre->load('position.department');
        
        return view('personnel.change-status', compact('membre'));
    }

    /**
     * Effectuer le changement de statut
     */
    public function changeStatus(Request $request, OrganizationMember $membre)
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['RH', 'Ressources Humaines', 'Admin', 'super-admin'])) {
            abort(403, 'Accès réservé au service RH.');
        }
        
        $validated = $request->validate([
            'nouveau_statut' => 'required|in:ACTIVE,VACANT,INTERIM,LICENCIE,DEMISSION,RETRAITE',
            'motif' => 'required|in:embauche,promotion,mutation,demission,licenciement,retraite,deces,fin_contrat,autre',
            'commentaire' => 'nullable|string',
            'date_effectif' => 'required|date',
        ]);
        
        $ancienStatut = $membre->status;
        
        // Enregistrer dans l'historique
        HistoriqueStatutMembre::create([
            'organization_member_id' => $membre->id,
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $validated['nouveau_statut'],
            'motif' => $validated['motif'],
            'commentaire' => $validated['commentaire'],
            'user_id' => $user->id,
            'date_effectif' => $validated['date_effectif'],
        ]);
        
        // Mettre à jour le statut du membre actuel (garder l'historique)
        $membre->update([
            'status' => $validated['nouveau_statut'],
            'end_date' => $validated['date_effectif'],
        ]);
        
        // Si licenciement, démission ou retraite, créer un poste vacant
        if (in_array($validated['nouveau_statut'], [
            OrganizationMember::STATUS_LICENCIE,
            OrganizationMember::STATUS_DEMISSION,
            OrganizationMember::STATUS_RETRAITE
        ])) {
            // Créer un nouveau membre VACANT pour le même poste
            OrganizationMember::create([
                'position_id' => $membre->position_id,
                'name' => 'VACANT',
                'status' => OrganizationMember::STATUS_VACANT,
                'user_id' => null,
                'email' => null,
                'phone' => null,
            ]);
            
            // Créer les documents obligatoires de fin de contrat pour licenciement
            if ($validated['nouveau_statut'] === OrganizationMember::STATUS_LICENCIE) {
                $this->createEndOfContractDocuments($membre, $user->id);
            }
        }
        
        return redirect()->route('personnel.show', $membre)
            ->with('success', 'Statut du membre modifié avec succès.');
    }

    /**
     * Créer les documents obligatoires de fin de contrat
     */
    private function createEndOfContractDocuments(OrganizationMember $membre, int $createdBy)
    {
        $documentsObligatoires = [
            [
                'type' => DocumentEmploye::TYPE_CERTIFICAT_TRAVAIL_FIN,
                'titre' => 'Certificat de travail',
            ],
            [
                'type' => DocumentEmploye::TYPE_ATTESTATION_FIN_CONTRAT,
                'titre' => 'Attestation de fin de contrat',
            ],
            [
                'type' => DocumentEmploye::TYPE_SOLDE_TOUT_COMPTE,
                'titre' => 'Solde de tout compte',
            ],
            [
                'type' => DocumentEmploye::TYPE_RELEVE_DROITS_CONGES,
                'titre' => 'Relevé des droits de congés',
            ],
        ];
        
        foreach ($documentsObligatoires as $doc) {
            DocumentEmploye::create([
                'organization_member_id' => $membre->id,
                'created_by' => $createdBy,
                'type_document' => $doc['type'],
                'titre' => $doc['titre'],
                'description' => 'Document de fin de contrat à générer',
                'fichier' => 'pending.pdf', // Placeholder
                'date_emission' => now(),
                'statut' => 'actif',
                'accessible_employe' => true,
            ]);
        }
    }

    /**
     * Historique des changements de statut
     */
    public function historique(OrganizationMember $membre)
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['RH', 'Ressources Humaines', 'Direction', 'Admin', 'super-admin'])) {
            abort(403, 'Accès non autorisé.');
        }
        
        $historique = HistoriqueStatutMembre::where('organization_member_id', $membre->id)
            ->with('user')
            ->orderBy('date_effectif', 'desc')
            ->get();
        
        $membre->load('position.department');
        
        return view('personnel.historique', compact('membre', 'historique'));
    }
}
