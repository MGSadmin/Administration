<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            // Si c'est une requête AJAX
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Non authentifié.'], 401);
            }
            
            // Rediriger vers la page de login avec l'URL de retour
            return redirect()->route('auth.login', [
                'site' => 'admin',
                'callback' => $request->fullUrl()
            ])->with('info', 'Veuillez vous connecter pour accéder à cette page.');
        }

        return $next($request);
    }
}
