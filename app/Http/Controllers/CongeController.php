<?php

namespace App\Http\Controllers;

use App\Models\Conge;
use App\Models\OrganizationMember;
use App\Models\SoldeConge;
use App\Models\User;
use App\Notifications\CongeDemandeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CongeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer l'organization_member de l'utilisateur
        $member = OrganizationMember::where('user_id', $user->id)->first();
        
        // Permissions
        $isRH = $user->hasRole(['RH', 'Ressources Humaines', 'Admin']);
        $isDirection = $user->hasRole(['Direction', 'Admin']);
        
        if ($isRH || $isDirection) {
            // RH et Direction voient toutes les demandes
            $conges = Conge::with(['organizationMember.position', 'user', 'validateur'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            $stats = [
                'en_attente' => Conge::where('statut', Conge::STATUT_EN_ATTENTE)->count(),
                'approuves' => Conge::where('statut', Conge::STATUT_APPROUVE)->count(),
                'refuses' => Conge::where('statut', Conge::STATUT_REFUSE)->count(),
            ];
        } else {
            // L'utilisateur ne voit que ses propres demandes
            if (!$member) {
                return view('conges.index', [
                    'conges' => collect(),
                    'stats' => null,
                    'member' => null,
                    'solde' => null,
                ]);
            }
            
            $conges = Conge::where('organization_member_id', $member->id)
                ->with(['validateur'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            $stats = null;
        }
        
        $solde = $member ? $member->soldeConges : null;
        
        return view('conges.index', compact('conges', 'stats', 'member', 'solde'));
    }

    public function create()
    {
        $user = Auth::user();
        $member = OrganizationMember::where('user_id', $user->id)->first();
        
        if (!$member) {
            return redirect()->route('conges.index')->with('error', 'Vous n\'êtes pas associé à un poste dans l\'organigramme.');
        }
        
        $solde = $member->soldeConges ?? SoldeConge::create([
            'organization_member_id' => $member->id,
            'date_derniere_mise_a_jour' => now(),
        ]);
        
        return view('conges.create', compact('member', 'solde'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $member = OrganizationMember::where('user_id', $user->id)->first();
        
        if (!$member) {
            return redirect()->route('conges.index')->with('error', 'Vous n\'êtes pas associé à un poste.');
        }
        
        $validated = $request->validate([
            'type' => 'required|in:conge_annuel,conge_maladie,conge_maternite,conge_paternite,conge_sans_solde,permission,autre',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'required|string',
            'fichier_justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        
        // Calculer le nombre de jours
        $dateDebut = new \DateTime($validated['date_debut']);
        $dateFin = new \DateTime($validated['date_fin']);
        $nbJours = $dateDebut->diff($dateFin)->days + 1;
        
        // Vérifier le solde pour congés annuels
        $solde = $member->soldeConges;
        if ($validated['type'] === Conge::TYPE_CONGE_ANNUEL && $solde) {
            if ($nbJours > $solde->conges_annuels_restants) {
                return back()->with('error', "Vous n'avez que {$solde->conges_annuels_restants} jours de congés restants.");
            }
        }
        
        // Gérer le fichier
        $fichierPath = null;
        if ($request->hasFile('fichier_justificatif')) {
            $fichierPath = $request->file('fichier_justificatif')->store('conges/justificatifs', 'public');
        }
        
        $conge = Conge::create([
            'organization_member_id' => $member->id,
            'user_id' => $user->id,
            'type' => $validated['type'],
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'],
            'nb_jours' => $nbJours,
            'motif' => $validated['motif'],
            'fichier_justificatif' => $fichierPath,
            'statut' => Conge::STATUT_EN_ATTENTE,
        ]);
        
        // Envoyer les notifications
        $this->sendCongeNotifications($conge, $member);
        
        return redirect()->route('conges.index')->with('success', 'Demande de congé créée avec succès.');
    }

    public function show(Conge $conge)
    {
        $user = Auth::user();
        $isRH = $user->hasRole(['RH', 'Ressources Humaines', 'Admin']);
        $isDirection = $user->hasRole(['Direction', 'Admin']);
        
        // Vérifier les permissions
        if (!$isRH && !$isDirection && $conge->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }
        
        $conge->load(['organizationMember.position.department', 'user', 'validateur']);
        
        return view('conges.show', compact('conge'));
    }

    public function approve(Conge $conge)
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['RH', 'Ressources Humaines', 'Direction', 'Admin'])) {
            abort(403, 'Accès non autorisé.');
        }
        
        $conge->update([
            'statut' => Conge::STATUT_APPROUVE,
            'validateur_id' => $user->id,
            'date_validation' => now(),
        ]);
        
        // Mettre à jour le solde de congés
        $solde = $conge->organizationMember->soldeConges;
        if ($solde) {
            $solde->updateAfterCongeApproved($conge);
        }
        
        return redirect()->route('conges.show', $conge)->with('success', 'Demande de congé approuvée.');
    }

    public function reject(Request $request, Conge $conge)
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['RH', 'Ressources Humaines', 'Direction', 'Admin'])) {
            abort(403, 'Accès non autorisé.');
        }
        
        $validated = $request->validate([
            'commentaire_rh' => 'required|string',
        ]);
        
        $conge->update([
            'statut' => Conge::STATUT_REFUSE,
            'validateur_id' => $user->id,
            'date_validation' => now(),
            'commentaire_rh' => $validated['commentaire_rh'],
        ]);
        
        return redirect()->route('conges.show', $conge)->with('success', 'Demande de congé refusée.');
    }

    public function destroy(Conge $conge)
    {
        $user = Auth::user();
        
        // Seul le créateur peut annuler si en attente
        if ($conge->user_id !== $user->id || $conge->statut !== Conge::STATUT_EN_ATTENTE) {
            abort(403, 'Vous ne pouvez pas annuler cette demande.');
        }
        
        $conge->update(['statut' => Conge::STATUT_ANNULE]);
        
        return redirect()->route('conges.index')->with('success', 'Demande de congé annulée.');
    }
    
    /**
     * Envoyer les notifications de demande de congé
     * - Au supérieur hiérarchique (validateur principal)
     * - Aux utilisateurs RH (en copie)
     */
    private function sendCongeNotifications(Conge $conge, OrganizationMember $member)
    {
        // 1. Trouver le supérieur hiérarchique
        $superior = $member->getSuperior();
        
        if ($superior && $superior->user) {
            // Notification au supérieur hiérarchique
            $superior->user->notify(new CongeDemandeNotification($conge, false));
        }
        
        // 2. Envoyer en copie aux RH
        $rhUsers = User::role(['RH', 'Ressources Humaines'])->get();
        
        foreach ($rhUsers as $rhUser) {
            $rhUser->notify(new CongeDemandeNotification($conge, true));
        }
        
        // Si pas de supérieur, notifier directement les RH
        if (!$superior && $rhUsers->isEmpty()) {
            // Notifier les admins en dernier recours
            $admins = User::role(['Admin', 'super-admin'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new CongeDemandeNotification($conge, false));
            }
        }
    }
}
