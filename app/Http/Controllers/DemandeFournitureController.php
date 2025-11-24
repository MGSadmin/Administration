<?php

namespace App\Http\Controllers;

use App\Models\DemandeFourniture;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandeFournitureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DemandeFourniture::with(['demandeur', 'validateur', 'acheteur', 'userANotifier']);

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }

        if ($request->filled('type_fourniture')) {
            $query->where('type_fourniture', $request->type_fourniture);
        }

        if ($request->filled('demandeur_id')) {
            $query->where('demandeur_id', $request->demandeur_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_demande', 'like', "%{$search}%")
                  ->orWhere('objet', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Utilisateur peut voir ses propres demandes ou toutes si admin/direction/rh
        if (!Auth::user()->hasAnyRole(['administrateur', 'admin', 'direction', 'rh'])) {
            $query->where('demandeur_id', Auth::id());
        }

        $demandes = $query->latest()->paginate(20);
        $utilisateurs = User::orderBy('name')->get();

        return view('demandes-fourniture.index', compact('demandes', 'utilisateurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $utilisateurs = User::orderBy('name')->get();
        return view('demandes-fourniture.create', compact('utilisateurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'objet' => 'required|string|max:255',
            'designation' => 'required|string|max:500',
            'description' => 'required|string',
            'type_fourniture' => 'required|in:materiel_informatique,fourniture_bureau,mobilier,equipement,consommable,autres',
            'quantite' => 'required|integer|min:1',
            'priorite' => 'required|in:faible,normale,urgente',
            'justification' => 'nullable|string',
            'budget_estime' => 'nullable|numeric|min:0',
            'notifier_user_id' => 'nullable|exists:users,id',
            'observation' => 'nullable|string',
        ]);

        $validated['demandeur_id'] = Auth::id();
        $validated['statut'] = 'en_attente';

        $demande = DemandeFourniture::create($validated);

        // Envoyer notification à la personne désignée
        $demande->envoyerNotification('creee');

        return redirect()->route('demandes-fourniture.index')
            ->with('success', 'Demande créée avec succès. Numéro: ' . $demande->numero_demande);
    }

    /**
     * Display the specified resource.
     */
    public function show(DemandeFourniture $demandeFourniture)
    {
        $demandeFourniture->load(['demandeur', 'validateur', 'acheteur', 'userANotifier']);
        
        // Vérifier les permissions - Admins, direction, rh voient tout, autres voient leurs demandes
        if (!Auth::user()->hasAnyRole(['administrateur', 'admin', 'direction', 'rh']) && $demandeFourniture->demandeur_id != Auth::id()) {
            abort(403, 'Non autorisé à voir cette demande.');
        }

        return view('demandes-fourniture.show', compact('demandeFourniture'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DemandeFourniture $demandeFourniture)
    {
        // Seul le demandeur peut éditer si la demande est encore en attente
        if ($demandeFourniture->demandeur_id != Auth::id() && !Auth::user()->hasAnyRole(['administrateur', 'admin', 'direction', 'rh'])) {
            abort(403, 'Non autorisé à modifier cette demande.');
        }

        if (!in_array($demandeFourniture->statut, ['en_attente', 'rejetee'])) {
            return back()->with('error', 'Cette demande ne peut plus être modifiée.');
        }

        $utilisateurs = User::orderBy('name')->get();
        return view('demandes-fourniture.edit', compact('demandeFourniture', 'utilisateurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DemandeFourniture $demandeFourniture)
    {
        if ($demandeFourniture->demandeur_id != Auth::id() && !Auth::user()->hasAnyRole(['administrateur', 'admin', 'direction', 'rh'])) {
            abort(403, 'Non autorisé à modifier cette demande.');
        }

        if (!in_array($demandeFourniture->statut, ['en_attente', 'rejetee'])) {
            return back()->with('error', 'Cette demande ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'objet' => 'required|string|max:255',
            'description' => 'required|string',
            'type_fourniture' => 'required|in:materiel_informatique,fourniture_bureau,mobilier,equipement,consommable,autres',
            'quantite' => 'required|integer|min:1',
            'priorite' => 'required|in:faible,normale,urgente',
            'justification' => 'nullable|string',
            'budget_estime' => 'nullable|numeric|min:0',
            'notifier_user_id' => 'nullable|exists:users,id',
            'observation' => 'nullable|string',
        ]);

        // Si la demande était rejetée, la remettre en attente
        if ($demandeFourniture->statut == 'rejetee') {
            $validated['statut'] = 'en_attente';
            $validated['motif_rejet'] = null;
        }

        $demandeFourniture->update($validated);

        return redirect()->route('demandes-fourniture.index')
            ->with('success', 'Demande mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DemandeFourniture $demandeFourniture)
    {
        if ($demandeFourniture->demandeur_id != Auth::id() && !Auth::user()->hasAnyRole(['administrateur', 'admin', 'direction', 'rh'])) {
            abort(403, 'Non autorisé à supprimer cette demande.');
        }

        // Rafraîchir pour avoir le statut le plus récent
        $demandeFourniture->refresh();

        if ($demandeFourniture->statut != 'en_attente') {
            return back()->with('error', 'Seules les demandes en attente peuvent être supprimées. Statut actuel: ' . $demandeFourniture->statut);
        }

        $demandeFourniture->delete();

        return redirect()->route('demandes-fourniture.index')
            ->with('success', 'Demande supprimée avec succès.');
    }

    /**
     * Valider une demande
     */
    public function valider(Request $request, DemandeFourniture $demandeFourniture)
    {
        // Seuls Direction, RH et Admin peuvent valider
        if (!Auth::user()->can('valider_demande_fourniture')) {
            abort(403, 'Non autorisé à valider cette demande.');
        }

        $request->validate([
            'commentaire' => 'nullable|string',
        ]);

        $demandeFourniture->valider(Auth::user(), $request->commentaire);

        return back()->with('success', 'Demande validée avec succès.');
    }

    /**
     * Rejeter une demande
     */
    public function rejeter(Request $request, DemandeFourniture $demandeFourniture)
    {
        // Seuls Direction, RH et Admin peuvent rejeter
        if (!Auth::user()->can('rejeter_demande_fourniture')) {
            abort(403, 'Non autorisé à rejeter cette demande.');
        }

        $request->validate([
            'motif_rejet' => 'required|string',
        ]);

        $demandeFourniture->rejeter(Auth::user(), $request->motif_rejet);

        return back()->with('success', 'Demande rejetée.');
    }

    /**
     * Commander
     */
    public function commander(Request $request, DemandeFourniture $demandeFourniture)
    {
        // Seuls Admin et RH peuvent commander
        if (!Auth::user()->can('commander_fourniture')) {
            abort(403, 'Non autorisé à commander des fournitures.');
        }

        $request->validate([
            'fournisseur' => 'required|string|max:255',
            'montant_reel' => 'required|numeric|min:0',
            'bon_commande' => 'nullable|string|max:255',
        ]);

        if ($demandeFourniture->statut != 'validee') {
            return back()->with('error', 'La demande doit être validée avant de commander.');
        }

        $demandeFourniture->commander(
            Auth::id(),
            $request->fournisseur,
            $request->montant_reel
        );

        if ($request->filled('bon_commande')) {
            $demandeFourniture->update(['bon_commande' => $request->bon_commande]);
        }

        return back()->with('success', 'Commande enregistrée avec succès.');
    }

    /**
     * Marquer comme reçue
     */
    public function marquerRecue(Request $request, DemandeFourniture $demandeFourniture)
    {
        // Seuls Admin et RH peuvent marquer comme reçue
        if (!Auth::user()->hasAnyRole(['administrateur', 'admin', 'rh'])) {
            abort(403, 'Non autorisé à réceptionner des fournitures.');
        }

        $request->validate([
            'facture' => 'nullable|string|max:255',
        ]);

        if ($demandeFourniture->statut != 'commandee') {
            return back()->with('error', 'La fourniture doit être commandée avant d\'être réceptionnée.');
        }

        $demandeFourniture->marquerRecue();

        if ($request->filled('facture')) {
            $demandeFourniture->update(['facture' => $request->facture]);
        }

        return back()->with('success', 'Fourniture marquée comme réceptionnée.');
    }

    /**
     * Livrer
     */
    public function livrer(DemandeFourniture $demandeFourniture)
    {
        // Seuls Admin et RH peuvent livrer
        if (!Auth::user()->can('livrer_fourniture')) {
            abort(403, 'Non autorisé à livrer des fournitures.');
        }

        if ($demandeFourniture->statut != 'recue') {
            return back()->with('error', 'La fourniture doit être reçue avant d\'être livrée.');
        }

        $demandeFourniture->livrer();

        return back()->with('success', 'Fourniture livrée avec succès.');
    }

    /**
     * Statistiques
     */
    public function statistiques()
    {
        $stats = [
            'total' => DemandeFourniture::count(),
            'en_attente' => DemandeFourniture::where('statut', 'en_attente')->count(),
            'validees' => DemandeFourniture::where('statut', 'validee')->count(),
            'rejetees' => DemandeFourniture::where('statut', 'rejetee')->count(),
            'commandees' => DemandeFourniture::where('statut', 'commandee')->count(),
            'livrees' => DemandeFourniture::where('statut', 'livree')->count(),
            'urgentes' => DemandeFourniture::where('priorite', 'urgente')->where('statut', '!=', 'livree')->count(),
            'par_type' => DemandeFourniture::selectRaw('type_fourniture, count(*) as total')
                ->groupBy('type_fourniture')
                ->get()
                ->pluck('total', 'type_fourniture'),
            'budget_total' => DemandeFourniture::sum('montant_reel'),
            'budget_estime_total' => DemandeFourniture::sum('budget_estime'),
        ];

        return view('demandes-fourniture.statistiques', compact('stats'));
    }
}
