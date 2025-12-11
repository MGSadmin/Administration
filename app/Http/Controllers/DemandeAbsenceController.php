<?php

namespace App\Http\Controllers;

use App\Models\DemandeAbsence;
use App\Models\OrganizationMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandeAbsenceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = OrganizationMember::where('user_id', $user->id)->first();
        
        $isRH = $user->hasRole(['RH', 'Ressources Humaines', 'Admin']);
        $isDirection = $user->hasRole(['Direction', 'Admin']);
        
        if ($isRH || $isDirection) {
            // RH et Direction voient toutes les demandes
            $demandes = DemandeAbsence::with(['organizationMember.position', 'user', 'validateur'])
                ->orderBy('date', 'desc')
                ->paginate(20);
            
            $stats = [
                'en_attente' => DemandeAbsence::where('statut', DemandeAbsence::STATUT_EN_ATTENTE)->count(),
                'approuvees' => DemandeAbsence::where('statut', DemandeAbsence::STATUT_APPROUVE)->count(),
                'refusees' => DemandeAbsence::where('statut', DemandeAbsence::STATUT_REFUSE)->count(),
            ];
        } else {
            // L'utilisateur ne voit que ses propres demandes
            if (!$member) {
                return view('absences.index', [
                    'demandes' => collect(),
                    'stats' => null,
                    'member' => null,
                ]);
            }
            
            $demandes = DemandeAbsence::where('organization_member_id', $member->id)
                ->with(['validateur'])
                ->orderBy('date', 'desc')
                ->paginate(20);
            
            $stats = null;
        }
        
        return view('absences.index', compact('demandes', 'stats', 'member'));
    }

    public function create()
    {
        $user = Auth::user();
        $member = OrganizationMember::where('user_id', $user->id)->first();
        
        if (!$member) {
            return redirect()->route('absences.index')->with('error', 'Vous n\'êtes pas associé à un poste.');
        }
        
        return view('absences.create', compact('member'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $member = OrganizationMember::where('user_id', $user->id)->first();
        
        if (!$member) {
            return redirect()->route('absences.index')->with('error', 'Vous n\'êtes pas associé à un poste.');
        }
        
        $validated = $request->validate([
            'type' => 'required|in:absence_justifiee,absence_non_justifiee,retard,sortie_anticipee,teletravail,mission_externe,formation',
            'date' => 'required|date',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i',
            'motif' => 'required|string',
            'fichier_justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        
        $fichierPath = null;
        if ($request->hasFile('fichier_justificatif')) {
            $fichierPath = $request->file('fichier_justificatif')->store('absences/justificatifs', 'public');
        }
        
        DemandeAbsence::create([
            'organization_member_id' => $member->id,
            'user_id' => $user->id,
            'type' => $validated['type'],
            'date' => $validated['date'],
            'heure_debut' => $validated['heure_debut'] ?? null,
            'heure_fin' => $validated['heure_fin'] ?? null,
            'motif' => $validated['motif'],
            'fichier_justificatif' => $fichierPath,
            'statut' => DemandeAbsence::STATUT_EN_ATTENTE,
        ]);
        
        return redirect()->route('absences.index')->with('success', 'Demande d\'absence créée avec succès.');
    }

    public function show(DemandeAbsence $absence)
    {
        $user = Auth::user();
        $isRH = $user->hasRole(['RH', 'Ressources Humaines', 'Admin']);
        $isDirection = $user->hasRole(['Direction', 'Admin']);
        
        if (!$isRH && !$isDirection && $absence->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }
        
        $absence->load(['organizationMember.position.department', 'user', 'validateur']);
        
        return view('absences.show', compact('absence'));
    }

    public function approve(DemandeAbsence $absence)
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['RH', 'Ressources Humaines', 'Direction', 'Admin'])) {
            abort(403, 'Accès non autorisé.');
        }
        
        $absence->update([
            'statut' => DemandeAbsence::STATUT_APPROUVE,
            'validateur_id' => $user->id,
            'date_validation' => now(),
        ]);
        
        return redirect()->route('absences.show', $absence)->with('success', 'Demande d\'absence approuvée.');
    }

    public function reject(Request $request, DemandeAbsence $absence)
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['RH', 'Ressources Humaines', 'Direction', 'Admin'])) {
            abort(403, 'Accès non autorisé.');
        }
        
        $validated = $request->validate([
            'commentaire_rh' => 'required|string',
        ]);
        
        $absence->update([
            'statut' => DemandeAbsence::STATUT_REFUSE,
            'validateur_id' => $user->id,
            'date_validation' => now(),
            'commentaire_rh' => $validated['commentaire_rh'],
        ]);
        
        return redirect()->route('absences.show', $absence)->with('success', 'Demande d\'absence refusée.');
    }
}
