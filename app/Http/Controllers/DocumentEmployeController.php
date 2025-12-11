<?php

namespace App\Http\Controllers;

use App\Models\DocumentEmploye;
use App\Models\OrganizationMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentEmployeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = OrganizationMember::where('user_id', $user->id)->first();
        
        $isRH = $user->hasRole(['RH', 'Ressources Humaines', 'Admin']);
        $isDirection = $user->hasRole(['Direction', 'Admin']);
        
        if ($isRH || $isDirection) {
            // RH et Direction voient tous les documents
            $documents = DocumentEmploye::with(['organizationMember.position', 'user', 'createdBy'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            $membres = OrganizationMember::with('position')->where('status', 'ACTIVE')->get();
        } else {
            // L'utilisateur ne voit que ses propres documents accessibles
            if (!$member) {
                return view('documents.index', [
                    'documents' => collect(),
                    'member' => null,
                    'membres' => collect(),
                ]);
            }
            
            $documents = DocumentEmploye::where('organization_member_id', $member->id)
                ->where('accessible_employe', true)
                ->with(['createdBy'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            $membres = collect();
        }
        
        return view('documents.index', compact('documents', 'member', 'membres'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['RH', 'Ressources Humaines', 'Admin'])) {
            abort(403, 'Seul le RH peut créer des documents.');
        }
        
        $memberId = $request->query('member_id');
        $member = null;
        
        if ($memberId) {
            $member = OrganizationMember::findOrFail($memberId);
        }
        
        $membres = OrganizationMember::with('position')->get();
        
        return view('documents.create', compact('membres', 'member'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['RH', 'Ressources Humaines', 'Admin'])) {
            abort(403, 'Seul le RH peut créer des documents.');
        }
        
        $validated = $request->validate([
            'organization_member_id' => 'required|exists:organization_members,id',
            'type_document' => 'required|string',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fichier' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'date_emission' => 'required|date',
            'date_validite' => 'nullable|date',
            'accessible_employe' => 'boolean',
        ]);
        
        // Enregistrer le fichier
        $fichierPath = $request->file('fichier')->store('documents/employes', 'public');
        
        $document = DocumentEmploye::create([
            'organization_member_id' => $validated['organization_member_id'],
            'created_by' => $user->id,
            'type_document' => $validated['type_document'],
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'fichier' => $fichierPath,
            'date_emission' => $validated['date_emission'],
            'date_validite' => $validated['date_validite'] ?? null,
            'accessible_employe' => $request->boolean('accessible_employe'),
            'statut' => DocumentEmploye::STATUT_ACTIF,
        ]);
        
        return redirect()->route('documents.index')->with('success', 'Document créé avec succès.');
    }

    public function show(DocumentEmploye $document)
    {
        $user = Auth::user();
        $isRH = $user->hasRole(['RH', 'Ressources Humaines', 'Admin']);
        $isDirection = $user->hasRole(['Direction', 'Admin']);
        
        // Vérifier les permissions
        $member = OrganizationMember::where('user_id', $user->id)->first();
        
        if (!$isRH && !$isDirection) {
            if (!$member || $document->organization_member_id !== $member->id || !$document->accessible_employe) {
                abort(403, 'Accès non autorisé.');
            }
        }
        
        $document->load(['organizationMember.position.department', 'user', 'createdBy']);
        
        return view('documents.show', compact('document'));
    }

    public function download(DocumentEmploye $document)
    {
        $user = Auth::user();
        $isRH = $user->hasRole(['RH', 'Ressources Humaines', 'Admin']);
        $isDirection = $user->hasRole(['Direction', 'Admin']);
        
        $member = OrganizationMember::where('user_id', $user->id)->first();
        
        if (!$isRH && !$isDirection) {
            if (!$member || $document->organization_member_id !== $member->id || !$document->accessible_employe) {
                abort(403, 'Accès non autorisé.');
            }
        }
        
        return Storage::disk('public')->download($document->fichier, $document->titre);
    }

    public function requestDocument(Request $request)
    {
        $user = Auth::user();
        $member = OrganizationMember::where('user_id', $user->id)->first();
        
        if (!$member) {
            return redirect()->route('documents.index')->with('error', 'Vous n\'êtes pas associé à un poste.');
        }
        
        $validated = $request->validate([
            'type_document' => 'required|string',
            'description' => 'nullable|string',
        ]);
        
        // Créer une demande de document (qui sera traitée par le RH)
        DocumentEmploye::create([
            'organization_member_id' => $member->id,
            'user_id' => $user->id,
            'created_by' => $user->id, // Temporaire, sera changé par le RH
            'type_document' => $validated['type_document'],
            'titre' => 'Demande: ' . $validated['type_document'],
            'description' => $validated['description'] ?? 'Document demandé par l\'employé',
            'fichier' => 'pending', // Fichier en attente
            'date_emission' => now(),
            'statut' => 'en_attente',
            'accessible_employe' => false,
            'date_demande' => now(),
        ]);
        
        return redirect()->route('documents.index')->with('success', 'Demande de document envoyée au service RH.');
    }

    public function destroy(DocumentEmploye $document)
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['RH', 'Ressources Humaines', 'Admin'])) {
            abort(403, 'Seul le RH peut supprimer des documents.');
        }
        
        // Supprimer le fichier
        if ($document->fichier && $document->fichier !== 'pending') {
            Storage::disk('public')->delete($document->fichier);
        }
        
        $document->delete();
        
        return redirect()->route('documents.index')->with('success', 'Document supprimé avec succès.');
    }

    /**
     * Archiver un document
     */
    public function archive(DocumentEmploye $document)
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['RH', 'Ressources Humaines', 'Admin'])) {
            abort(403, 'Seul le RH peut archiver des documents.');
        }
        
        $document->update(['statut' => DocumentEmploye::STATUT_ARCHIVE]);
        
        return redirect()->route('documents.show', $document)->with('success', 'Document archivé.');
    }
}
