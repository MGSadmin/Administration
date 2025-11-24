<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'applications']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('matricule', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $query->role($request->role);
        }

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $applications = ['administration', 'commercial', 'gestion-dossier'];
        
        return view('users.create', compact('roles', 'applications'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'matricule' => 'nullable|string|unique:users,matricule',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:20',
            'poste' => 'nullable|string|max:255',
            'departement' => 'nullable|string|max:255',
            'date_embauche' => 'nullable|date',
            'is_active' => 'boolean',
            'roles' => 'array',
            'applications' => 'array',
            'applications.*.name' => 'required|string',
            'applications.*.role' => 'nullable|string',
            'applications.*.status' => 'required|in:active,inactive,blocked',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'prenom' => $validated['prenom'] ?? null,
            'matricule' => $validated['matricule'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'telephone' => $validated['telephone'] ?? null,
            'poste' => $validated['poste'] ?? null,
            'departement' => $validated['departement'] ?? null,
            'date_embauche' => $validated['date_embauche'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        if (isset($validated['applications'])) {
            foreach ($validated['applications'] as $app) {
                UserApplication::create([
                    'user_id' => $user->id,
                    'application' => $app['name'],
                    'role' => $app['role'] ?? null,
                    'status' => $app['status'],
                ]);
            }
        }

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'applications']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $applications = ['administration', 'commercial', 'gestion-dossier'];
        $user->load(['roles', 'applications']);
        
        return view('users.edit', compact('user', 'roles', 'applications'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'matricule' => 'nullable|string|unique:users,matricule,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:20',
            'poste' => 'nullable|string|max:255',
            'departement' => 'nullable|string|max:255',
            'date_embauche' => 'nullable|date',
            'is_active' => 'boolean',
            'roles' => 'array',
            'applications' => 'array',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'prenom' => $validated['prenom'] ?? null,
            'matricule' => $validated['matricule'] ?? null,
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'poste' => $validated['poste'] ?? null,
            'departement' => $validated['departement'] ?? null,
            'date_embauche' => $validated['date_embauche'] ?? null,
            'is_active' => $request->has('is_active'),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        if (isset($validated['applications'])) {
            $user->applications()->delete();
            foreach ($validated['applications'] as $app) {
                UserApplication::create([
                    'user_id' => $user->id,
                    'application' => $app, // Nom de la colonne est 'application'
                    'status' => 'active',
                ]);
            }
        }

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
