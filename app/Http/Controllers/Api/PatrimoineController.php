<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patrimoine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PatrimoineController extends Controller
{
    /**
     * Récupérer tous les patrimoines
     */
    public function index(Request $request)
    {
        $query = Patrimoine::query();

        // Filtres optionnels
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->has('utilisateur_id')) {
            $query->where('utilisateur_id', $request->utilisateur_id);
        }

        $patrimoines = $query->with(['utilisateur', 'validateur'])
            ->get();

        return response()->json([
            'data' => $patrimoines,
        ]);
    }

    /**
     * Récupérer un patrimoine par ID
     */
    public function show(Patrimoine $patrimoine)
    {
        return response()->json($patrimoine->load(['utilisateur', 'validateur']));
    }

    /**
     * Créer un nouveau patrimoine
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code_materiel' => 'nullable|string|unique:patrimoines',
            'designation' => 'required|string',
            'description' => 'nullable|string',
            'categorie' => 'required|in:informatique,mobilier,vehicule,equipement_bureau,autres',
            'marque' => 'nullable|string',
            'modele' => 'nullable|string',
            'numero_serie' => 'nullable|string|unique:patrimoines',
            'prix_achat' => 'nullable|numeric',
            'date_achat' => 'nullable|date',
            'validateur_id' => 'nullable|exists:users,id',
            'utilisateur_id' => 'nullable|exists:users,id',
            'etat' => 'required|in:neuf,bon,moyen,mauvais,en_reparation,hors_service',
            'statut' => 'required|in:disponible,en_utilisation,en_maintenance,reforme',
            'localisation' => 'required|string',
            'observation' => 'nullable|string',
            'facture' => 'nullable|string',
            'fournisseur' => 'nullable|string',
            'duree_garantie_mois' => 'nullable|integer|min:0',
        ]);

        $patrimoine = Patrimoine::create($validated);

        return response()->json($patrimoine->load(['utilisateur', 'validateur']), 201);
    }

    /**
     * Mettre à jour un patrimoine
     */
    public function update(Request $request, Patrimoine $patrimoine)
    {
        $validated = $request->validate([
            'code_materiel' => 'nullable|string|unique:patrimoines,code_materiel,' . $patrimoine->id,
            'designation' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'categorie' => 'sometimes|required|in:informatique,mobilier,vehicule,equipement_bureau,autres',
            'marque' => 'nullable|string',
            'modele' => 'nullable|string',
            'numero_serie' => 'nullable|string|unique:patrimoines,numero_serie,' . $patrimoine->id,
            'prix_achat' => 'nullable|numeric',
            'date_achat' => 'nullable|date',
            'validateur_id' => 'nullable|exists:users,id',
            'utilisateur_id' => 'nullable|exists:users,id',
            'etat' => 'sometimes|required|in:neuf,bon,moyen,mauvais,en_reparation,hors_service',
            'statut' => 'sometimes|required|in:disponible,en_utilisation,en_maintenance,reforme',
            'localisation' => 'sometimes|required|string',
            'observation' => 'nullable|string',
            'facture' => 'nullable|string',
            'fournisseur' => 'nullable|string',
            'duree_garantie_mois' => 'nullable|integer|min:0',
        ]);

        // Mettre à jour date_modification
        $validated['date_modification'] = now();

        $patrimoine->update($validated);

        return response()->json($patrimoine->load(['utilisateur', 'validateur']));
    }

    /**
     * Supprimer un patrimoine
     */
    public function destroy(Patrimoine $patrimoine)
    {
        $patrimoine->delete();
        return response()->noContent();
    }

    /**
     * Uploader une photo pour un patrimoine
     */
    public function uploadPhoto(Request $request, Patrimoine $patrimoine)
    {
        $validated = $request->validate([
            'photo' => 'required|image|max:5120', // 5MB max
        ]);

        $file = $request->file('photo');
        $path = $file->store('patrimoines/' . $patrimoine->id, 'public');
        $url = Storage::disk('public')->url($path);

        // Sauvegarder la référence dans la BD si nécessaire
        // Pour cet exemple, on retourne juste l'URL

        return response()->json([
            'url' => $url,
            'path' => $path,
        ], 201);
    }

    /**
     * Récupérer les photos d'un patrimoine
     */
    public function getPhotos(Patrimoine $patrimoine)
    {
        $files = Storage::disk('public')->files('patrimoines/' . $patrimoine->id);
        
        $photos = array_map(function ($file) {
            return Storage::disk('public')->url($file);
        }, $files);

        return response()->json([
            'photos' => $photos,
        ]);
    }

    /**
     * Attribuer un patrimoine à un utilisateur
     */
    public function attribuer(Request $request, Patrimoine $patrimoine)
    {
        $validated = $request->validate([
            'utilisateur_id' => 'required|exists:users,id',
        ]);

        $patrimoine->attribuerA(auth()->user(), $validated['utilisateur_id']);

        return response()->json($patrimoine->load(['utilisateur', 'validateur']));
    }

    /**
     * Libérer un patrimoine
     */
    public function liberer(Patrimoine $patrimoine)
    {
        $patrimoine->liberer();

        return response()->json($patrimoine->load(['utilisateur', 'validateur']));
    }

    /**
     * Mettre en maintenance
     */
    public function mettreEnMaintenance(Patrimoine $patrimoine)
    {
        $patrimoine->mettreEnMaintenance();

        return response()->json($patrimoine->load(['utilisateur', 'validateur']));
    }

    /**
     * Réformer un patrimoine
     */
    public function reformer(Patrimoine $patrimoine)
    {
        $patrimoine->reformer();

        return response()->json($patrimoine->load(['utilisateur', 'validateur']));
    }
}
