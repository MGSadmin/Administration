<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Récupérer l'URL intended AVANT l'authentification
        $intendedUrl = $request->session()->get('url.intended');
        
        // Log pour debug
        \Log::info('Login attempt', [
            'intended_url' => $intendedUrl,
            'session_id' => $request->session()->getId()
        ]);
        
        $request->authenticate();

        $request->session()->regenerate();

        // Vérifier s'il y a une URL de redirection vers une autre application
        if ($intendedUrl && (
            str_contains($intendedUrl, 'gestion-dossier.mgs-local.mg') || 
            str_contains($intendedUrl, 'commercial.mgs-local.mg')
        )) {
            \Log::info('Redirecting to intended URL', ['url' => $intendedUrl]);
            return redirect()->away($intendedUrl);
        }

        \Log::info('Redirecting to dashboard');
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
