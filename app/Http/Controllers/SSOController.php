<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Site;
use App\Models\User;

class SSOController extends Controller
{
    /**
     * Affiche la page de connexion SSO
     */
    public function login(Request $request)
    {
        // Si déjà connecté, rediriger directement
        if (Auth::check()) {
            return $this->redirectWithToken($request);
        }
        
        $callback = $request->get('callback');
        $siteCode = $request->get('site');
        
        // Vérifier que le site existe
        $site = Site::where('code', $siteCode)->where('is_active', true)->first();
        
        if (!$site) {
            return view('sso.error', [
                'message' => 'Site non reconnu ou inactif'
            ]);
        }
        
        return view('sso.login', compact('callback', 'site'));
    }

    /**
     * Authentifie l'utilisateur et redirige avec un token
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'site_code' => 'required',
            'callback' => 'required|url',
        ]);
        
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $user = Auth::user();
            $site = Site::where('code', $credentials['site_code'])->first();
            
            if (!$site) {
                Auth::logout();
                return back()->with('error', 'Site invalide');
            }
            
            // Vérifier si l'utilisateur a accès à ce site
            $hasAccess = $user->getAllPermissions()
                ->filter(fn($p) => str_starts_with($p->name, $site->code . '.'))
                ->isNotEmpty();
            
            if (!$hasAccess) {
                Auth::logout();
                return back()->with('error', "Vous n'avez pas accès au site {$site->name}");
            }
            
            // Créer un token pour ce site
            $token = $user->createToken("sso-{$site->code}", ['*'], now()->addDays(7))
                ->plainTextToken;
            
            // Logger la connexion
            activity()
                ->causedBy($user)
                ->withProperties(['site' => $site->name, 'ip' => $request->ip()])
                ->log('Connexion SSO');
            
            // Construire l'URL de redirection avec le token
            // Décoder l'URL si elle était encodée
            $decodedCallback = urldecode($credentials['callback']);
            $separator = str_contains($decodedCallback, '?') ? '&' : '?';
            $redirectUrl = $decodedCallback . $separator . 'token=' . urlencode($token);
            
            // Utiliser redirect()->away() pour rediriger vers un domaine externe
            return redirect()->away($redirectUrl);
        }
        
        return back()->with('error', 'Identifiants incorrects')->withInput($request->only('email'));
    }
    
    /**
     * Redirige un utilisateur déjà authentifié avec un nouveau token
     */
    protected function redirectWithToken(Request $request)
    {
        $user = Auth::user();
        $siteCode = $request->get('site');
        $callback = $request->get('callback');
        
        $site = Site::where('code', $siteCode)->first();
        
        if (!$site) {
            return view('sso.error', ['message' => 'Site invalide']);
        }
        
        // Vérifier l'accès
        $hasAccess = $user->getAllPermissions()
            ->filter(fn($p) => str_starts_with($p->name, $site->code . '.'))
            ->isNotEmpty();
        
        if (!$hasAccess) {
            return view('sso.login', [
                'callback' => $callback,
                'site' => $site,
                'error' => "Vous n'avez pas accès au site {$site->name}"
            ]);
        }
        
        $token = $user->createToken("sso-{$site->code}", ['*'], now()->addDays(7))
            ->plainTextToken;
        
        // Décoder l'URL et construire la redirection
        $decodedCallback = urldecode($callback);
        $separator = str_contains($decodedCallback, '?') ? '&' : '?';
        $redirectUrl = $decodedCallback . $separator . 'token=' . urlencode($token);
        
        return redirect()->away($redirectUrl);
    }
    
    /**
     * Déconnecte l'utilisateur de tous les sites
     */
    public function logout(Request $request)
    {
        $callback = $request->get('callback');
        
        if (Auth::check()) {
            $user = Auth::user();
            
            // Révoquer tous les tokens de l'utilisateur
            $user->tokens()->delete();
            
            activity()
                ->causedBy($user)
                ->withProperties(['ip' => $request->ip()])
                ->log('Déconnexion SSO');
            
            Auth::logout();
        }
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        if ($callback) {
            return redirect($callback);
        }
        
        return redirect('/')->with('success', 'Vous avez été déconnecté avec succès');
    }

    /**
     * Révoque un token spécifique
     */
    public function revokeToken(Request $request)
    {
        $request->validate([
            'token_id' => 'required|exists:personal_access_tokens,id'
        ]);

        if (Auth::check()) {
            $user = Auth::user();
            $user->tokens()->where('id', $request->token_id)->delete();
            
            return response()->json(['success' => true, 'message' => 'Token révoqué']);
        }

        return response()->json(['success' => false, 'message' => 'Non autorisé'], 401);
    }
}
