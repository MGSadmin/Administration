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
        // Récupérer l'URL redirect depuis le paramètre de query
        $redirectUrl = $request->query('redirect');
        
        // Récupérer l'URL intended depuis la session
        $intendedUrl = $request->session()->get('url.intended');
        
        // Log pour debug
        \Log::info('Login attempt', [
            'redirect_param' => $redirectUrl,
            'intended_url' => $intendedUrl,
            'session_id' => $request->session()->getId()
        ]);
        
        $request->authenticate();

        $request->session()->regenerate();

        // Priorité 1 : redirection depuis le paramètre query redirect (venant d'une autre app)
        if ($redirectUrl && (
            str_contains($redirectUrl, 'gestion-dossier.mgs.mg') || 
            str_contains($redirectUrl, 'commercial.mgs.mg')
        )) {
            \Log::info('Redirecting to redirect param', ['url' => $redirectUrl]);
            return redirect()->away($redirectUrl);
        }

        // Priorité 2 : Vérifier s'il y a une URL de redirection vers une autre application dans intended
        if ($intendedUrl && (
            str_contains($intendedUrl, 'gestion-dossier.mgs.mg') || 
            str_contains($intendedUrl, 'commercial.mgs.mg')
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
