# Exemples d'implémentation des permissions dans les contrôleurs

## PatrimoineController - Exemple avec autorisations

```php
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
        // Vérifier l'autorisation
        $this->authorize('viewAny', Patrimoine::class);

        $query = Patrimoine::with(['utilisateur', 'validateur']);

        // Si l'utilisateur ne peut voir que ses patrimoines
        if (!auth()->user()->can('Voir Tous Patrimoines')) {
            $query->where('utilisateur_id', auth()->id());
        }

        // Filtres...
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
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
        $this->authorize('create', Patrimoine::class);

        $utilisateurs = User::orderBy('name')->get();
        $validateurs = User::role(['super-admin', 'administrateur', 'rh'])->orderBy('name')->get();

        return view('patrimoines.create', compact('utilisateurs', 'validateurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Patrimoine::class);

        $validated = $request->validate([
            'code_materiel' => 'required|string|unique:patrimoines',
            'designation' => 'required|string',
            // ... autres validations
        ]);

        $patrimoine = Patrimoine::create($validated);

        return redirect()->route('patrimoines.index')
            ->with('success', 'Patrimoine créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patrimoine $patrimoine)
    {
        $this->authorize('view', $patrimoine);

        return view('patrimoines.show', compact('patrimoine'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patrimoine $patrimoine)
    {
        $this->authorize('update', $patrimoine);

        $utilisateurs = User::orderBy('name')->get();
        $validateurs = User::role(['super-admin', 'administrateur', 'rh'])->orderBy('name')->get();

        return view('patrimoines.edit', compact('patrimoine', 'utilisateurs', 'validateurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patrimoine $patrimoine)
    {
        $this->authorize('update', $patrimoine);

        $validated = $request->validate([
            'code_materiel' => 'required|string|unique:patrimoines,code_materiel,' . $patrimoine->id,
            'designation' => 'required|string',
            // ... autres validations
        ]);

        $patrimoine->update($validated);

        return redirect()->route('patrimoines.show', $patrimoine)
            ->with('success', 'Patrimoine modifié avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patrimoine $patrimoine)
    {
        $this->authorize('delete', $patrimoine);

        $patrimoine->delete();

        return redirect()->route('patrimoines.index')
            ->with('success', 'Patrimoine supprimé avec succès');
    }

    /**
     * Attribuer un patrimoine à un utilisateur
     */
    public function attribuer(Request $request, Patrimoine $patrimoine)
    {
        $this->authorize('assign', Patrimoine::class);

        $validated = $request->validate([
            'utilisateur_id' => 'required|exists:users,id',
            'date_attribution' => 'required|date',
        ]);

        $patrimoine->update([
            'utilisateur_id' => $validated['utilisateur_id'],
            'statut' => 'attribue',
            'date_attribution' => $validated['date_attribution'],
        ]);

        return redirect()->route('patrimoines.show', $patrimoine)
            ->with('success', 'Patrimoine attribué avec succès');
    }

    /**
     * Libérer un patrimoine
     */
    public function liberer(Patrimoine $patrimoine)
    {
        $this->authorize('liberate', Patrimoine::class);

        $patrimoine->update([
            'utilisateur_id' => null,
            'statut' => 'disponible',
            'date_attribution' => null,
        ]);

        return redirect()->route('patrimoines.show', $patrimoine)
            ->with('success', 'Patrimoine libéré avec succès');
    }

    /**
     * Afficher les statistiques
     */
    public function statistiques()
    {
        $this->authorize('viewStatistics', Patrimoine::class);

        $stats = [
            'total' => Patrimoine::count(),
            'disponible' => Patrimoine::where('statut', 'disponible')->count(),
            'attribue' => Patrimoine::where('statut', 'attribue')->count(),
            // ... autres statistiques
        ];

        return view('patrimoines.statistiques', compact('stats'));
    }
}
```

## DemandeFournitureController - Exemple

```php
<?php

namespace App\Http\Controllers;

use App\Models\DemandeFourniture;
use Illuminate\Http\Request;

class DemandeFournitureController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', DemandeFourniture::class);

        $query = DemandeFourniture::with('demandeur');

        // Si ne peut voir que ses demandes
        if (!auth()->user()->can('Voir Toutes Demandes Fourniture')) {
            $query->where('demandeur_id', auth()->id());
        }

        $demandes = $query->latest()->paginate(20);

        return view('demandes-fourniture.index', compact('demandes'));
    }

    public function create()
    {
        $this->authorize('create', DemandeFourniture::class);
        return view('demandes-fourniture.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', DemandeFourniture::class);

        $validated = $request->validate([
            'article' => 'required|string',
            'quantite' => 'required|integer|min:1',
            'justification' => 'required|string',
        ]);

        $demande = DemandeFourniture::create([
            ...$validated,
            'demandeur_id' => auth()->id(),
            'statut' => 'en_attente',
        ]);

        return redirect()->route('demandes-fourniture.index')
            ->with('success', 'Demande créée avec succès');
    }

    public function valider(DemandeFourniture $demandeFourniture)
    {
        $this->authorize('validate', DemandeFourniture::class);

        $demandeFourniture->update([
            'statut' => 'validee',
            'validateur_id' => auth()->id(),
            'date_validation' => now(),
        ]);

        return back()->with('success', 'Demande validée');
    }

    public function rejeter(Request $request, DemandeFourniture $demandeFourniture)
    {
        $this->authorize('reject', DemandeFourniture::class);

        $demandeFourniture->update([
            'statut' => 'rejetee',
            'validateur_id' => auth()->id(),
            'motif_rejet' => $request->motif_rejet,
        ]);

        return back()->with('success', 'Demande rejetée');
    }
}
```

## CongeController - Exemple

```php
<?php

namespace App\Http\Controllers;

use App\Models\Conge;
use Illuminate\Http\Request;

class CongeController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Conge::class);

        $query = Conge::with('user');

        // Si ne peut voir que ses congés
        if (!auth()->user()->can('Voir Tous Congés')) {
            $query->where('user_id', auth()->id());
        }

        $conges = $query->latest()->paginate(20);

        return view('conges.index', compact('conges'));
    }

    public function create()
    {
        $this->authorize('create', Conge::class);
        return view('conges.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Conge::class);

        $validated = $request->validate([
            'type' => 'required|in:conge_annuel,conge_maladie,conge_sans_solde',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'motif' => 'nullable|string',
        ]);

        $conge = Conge::create([
            ...$validated,
            'user_id' => auth()->id(),
            'statut' => 'en_attente',
        ]);

        return redirect()->route('conges.index')
            ->with('success', 'Demande de congé créée');
    }

    public function approve(Conge $conge)
    {
        $this->authorize('approve', Conge::class);

        $conge->update([
            'statut' => 'approuve',
            'approuve_par' => auth()->id(),
            'date_approbation' => now(),
        ]);

        return back()->with('success', 'Congé approuvé');
    }

    public function reject(Request $request, Conge $conge)
    {
        $this->authorize('reject', Conge::class);

        $conge->update([
            'statut' => 'rejete',
            'approuve_par' => auth()->id(),
            'motif_rejet' => $request->motif_rejet,
        ]);

        return back()->with('success', 'Congé rejeté');
    }

    public function destroy(Conge $conge)
    {
        $this->authorize('delete', $conge);

        $conge->delete();

        return redirect()->route('conges.index')
            ->with('success', 'Demande de congé supprimée');
    }
}
```

## Routes protégées - web.php

```php
// Avec middleware can
Route::middleware(['auth', 'can:Voir Patrimoine'])->group(function () {
    Route::get('/patrimoines', [PatrimoineController::class, 'index'])
        ->name('patrimoines.index');
});

// Avec middleware role
Route::middleware(['auth', 'role:administrateur|rh'])->group(function () {
    Route::resource('admin/users', UserController::class);
});

// Protection au niveau du resource controller
Route::middleware('auth')->group(function () {
    Route::resource('patrimoines', PatrimoineController::class);
    // Les autorisations sont vérifiées dans le contrôleur via $this->authorize()
});
```
