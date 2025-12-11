<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Affiche la page de connexion
     */
    public function showLoginForm(Request $request)
    {
        return view('auth.login');
    }

    /**
     * Authentifie l'utilisateur
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Logger l'activité
            activity()
                ->causedBy(Auth::user())
                ->withProperties(['ip' => $request->ip()])
                ->log('Connexion réussie');

            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => ['Les identifiants fournis sont incorrects.'],
        ]);
    }

    /**
     * Affiche la page d'inscription
     */
    public function showRegisterForm(Request $request)
    {
        return view('auth.register');
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        // Logger l'activité
        activity()
            ->causedBy($user)
            ->withProperties(['ip' => $request->ip()])
            ->log('Inscription réussie');

        return redirect()->route('dashboard');
    }

    /**
     * Déconnecte l'utilisateur
     */
    public function logout(Request $request)
    {
        // Logger l'activité avant de déconnecter
        if (Auth::check()) {
            activity()
                ->causedBy(Auth::user())
                ->withProperties(['ip' => $request->ip()])
                ->log('Déconnexion');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }

    /**
     * Continue le processus de déconnexion après avoir déconnecté un site
     */
    public function continueLogout(Request $request)
    {
        $step = $request->get('step', '1');
        return view('auth.global-logout', compact('step'));
    }

    /**
     * Déconnexion finale de tous les sites
     */
    public function finalLogout(Request $request)
    {
        // Logger l'activité avant de déconnecter
        if (Auth::check()) {
            activity()
                ->causedBy(Auth::user())
                ->withProperties(['ip' => $request->ip()])
                ->log('Déconnexion globale de tous les sites');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('success', 'Déconnexion réussie de tous les sites');
    }
}
