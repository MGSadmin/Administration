<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'organizationMember.position']);
        
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filtrer par rôle
        if ($request->filled('role')) {
            $query->role($request->role);
        }
        
        // Filtrer par statut membre
        if ($request->filled('member_status')) {
            if ($request->member_status === 'with_member') {
                $query->has('organizationMember');
            } elseif ($request->member_status === 'without_member') {
                $query->doesntHave('organizationMember');
            }
        }
        
        $users = $query->latest()->paginate(15);
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        
        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }
        
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Utilisateur créé');
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['roles.permissions', 'permissions', 'organizationMember.position.department']);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);
        
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);
        
        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }
        
        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }
        
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Utilisateur modifié');
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur modifié avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }
        
        $userName = $user->name;
        
        // Révoquer tous les tokens
        $user->tokens()->delete();
        
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Utilisateur supprimé');
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$userName} supprimé avec succès");
    }
    
    /**
     * Révoquer tous les tokens d'un utilisateur
     */
    public function revokeTokens(User $user)
    {
        $user->tokens()->delete();
        
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Tokens révoqués');
        
        return back()->with('success', 'Tous les tokens ont été révoqués');
    }
}
