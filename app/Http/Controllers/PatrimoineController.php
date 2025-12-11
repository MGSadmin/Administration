<?php

namespace App\Http\Controllers;

use App\Models\Patrimoine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatrimoineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Patrimoine::with(['utilisateur', 'validateur']);

        // Filtres
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('etat')) {
            $query->where('etat', $request->etat);
        }

        if ($request->filled('utilisateur_id')) {
            $query->where('utilisateur_id', $request->utilisateur_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code_materiel', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('marque', 'like', "%{$search}%")
                  ->orWhere('modele', 'like', "%{$search}%");
            });
        }

        $patrimoines = $query->latest()->paginate(20);
        $utilisateurs = User::orderBy('name')->get();

        return view('patrimoines.index', compact('patrimoines', 'utilisateurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $utilisateurs = User::orderBy('name')->get();
        
        // Récupérer les utilisateurs avec les rôles admin ou gestionnaires
        try {
            $validateurs = User::role(['Super Admin', 'Administrateur', 'Gestionnaire Débours'])->orderBy('name')->get();
        } catch (\Exception $e) {
            // Si erreur, prendre tous les users
            $validateurs = User::orderBy('name')->get();
        }
        
        if ($validateurs->isEmpty()) {
            $validateurs = User::orderBy('name')->get();
        }

        return view('patrimoines.create', compact('utilisateurs', 'validateurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'categorie' => 'required|in:informatique,mobilier,vehicule,equipement_bureau,autres',
            'marque' => 'nullable|string|max:255',
            'modele' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'prix_achat' => 'nullable|numeric|min:0',
            'date_achat' => 'required|date',
            'validateur_id' => 'nullable|exists:users,id',
            'utilisateur_id' => 'nullable|exists:users,id',
            'etat' => 'required|in:neuf,bon,moyen,mauvais,en_reparation,hors_service',
            'statut' => 'required|in:disponible,en_utilisation,en_maintenance,reforme',
            'localisation' => 'nullable|string|max:255',
            'observation' => 'nullable|string',
            'facture' => 'nullable|string|max:255',
            'fournisseur' => 'nullable|string|max:255',
            'duree_garantie_mois' => 'nullable|integer|min:0',
        ]);

        // Si un validateur est défini, ajouter la date de validation
        if ($request->filled('validateur_id')) {
            $validated['date_validation'] = now();
        }

        // Si un utilisateur est attribué, ajouter la date d'attribution et changer le statut
        if ($request->filled('utilisateur_id')) {
            $validated['date_attribution'] = now();
            $validated['statut'] = 'en_utilisation';
        }

        $patrimoine = Patrimoine::create($validated);

        return redirect()->route('patrimoines.index')
            ->with('success', 'Patrimoine créé avec succès. Code: ' . $patrimoine->code_materiel);
    }

    /**
     * Display the specified resource.
     */
    public function show(Patrimoine $patrimoine)
    {
        $patrimoine->load(['utilisateur', 'validateur']);
        return view('patrimoines.show', compact('patrimoine'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patrimoine $patrimoine)
    {
        $utilisateurs = User::orderBy('name')->get();
        
        // Récupérer les utilisateurs avec les rôles admin ou gestionnaires
        try {
            $validateurs = User::role(['Super Admin', 'Administrateur', 'Gestionnaire Débours'])->orderBy('name')->get();
        } catch (\Exception $e) {
            // Si erreur, prendre tous les users
            $validateurs = User::orderBy('name')->get();
        }
        
        if ($validateurs->isEmpty()) {
            $validateurs = User::orderBy('name')->get();
        }

        return view('patrimoines.edit', compact('patrimoine', 'utilisateurs', 'validateurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patrimoine $patrimoine)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'categorie' => 'required|in:informatique,mobilier,vehicule,equipement_bureau,autres',
            'marque' => 'nullable|string|max:255',
            'modele' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'prix_achat' => 'nullable|numeric|min:0',
            'date_achat' => 'required|date',
            'validateur_id' => 'nullable|exists:users,id',
            'utilisateur_id' => 'nullable|exists:users,id',
            'etat' => 'required|in:neuf,bon,moyen,mauvais,en_reparation,hors_service',
            'statut' => 'required|in:disponible,en_utilisation,en_maintenance,reforme',
            'localisation' => 'nullable|string|max:255',
            'observation' => 'nullable|string',
            'facture' => 'nullable|string|max:255',
            'fournisseur' => 'nullable|string|max:255',
            'duree_garantie_mois' => 'nullable|integer|min:0',
        ]);

        // Gérer la date de validation
        if ($request->filled('validateur_id') && !$patrimoine->date_validation) {
            $validated['date_validation'] = now();
        }

        // Gérer le changement d'utilisateur
        if ($request->utilisateur_id != $patrimoine->utilisateur_id) {
            if ($request->filled('utilisateur_id')) {
                $validated['date_attribution'] = now();
                $validated['statut'] = 'en_utilisation';
            } else {
                $validated['date_attribution'] = null;
                $validated['statut'] = 'disponible';
            }
        }

        $patrimoine->update($validated);

        return redirect()->route('patrimoines.index')
            ->with('success', 'Patrimoine mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patrimoine $patrimoine)
    {
        $patrimoine->delete();

        return redirect()->route('patrimoines.index')
            ->with('success', 'Patrimoine supprimé avec succès.');
    }

    /**
     * Attribuer un matériel à un utilisateur
     */
    public function attribuer(Request $request, Patrimoine $patrimoine)
    {
        $request->validate([
            'utilisateur_id' => 'required|exists:users,id',
        ]);

        $utilisateur = User::findOrFail($request->utilisateur_id);
        $patrimoine->attribuerA($utilisateur);

        return back()->with('success', 'Matériel attribué à ' . $utilisateur->name);
    }

    /**
     * Libérer un matériel
     */
    public function liberer(Patrimoine $patrimoine)
    {
        $patrimoine->liberer();

        return back()->with('success', 'Matériel libéré avec succès.');
    }

    /**
     * Statistiques des patrimoines
     */
    public function statistiques()
    {
        $stats = [
            'total' => Patrimoine::count(),
            'disponibles' => Patrimoine::where('statut', 'disponible')->count(),
            'en_utilisation' => Patrimoine::where('statut', 'en_utilisation')->count(),
            'en_maintenance' => Patrimoine::where('statut', 'en_maintenance')->count(),
            'reformes' => Patrimoine::where('statut', 'reforme')->count(),
            'par_categorie' => Patrimoine::selectRaw('categorie, count(*) as total')
                ->groupBy('categorie')
                ->get()
                ->pluck('total', 'categorie'),
            'valeur_totale' => Patrimoine::sum('prix_achat'),
            'sous_garantie' => Patrimoine::where('date_fin_garantie', '>=', now())->count(),
        ];

        return view('patrimoines.statistiques', compact('stats'));
    }
}
