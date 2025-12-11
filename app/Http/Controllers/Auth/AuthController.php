<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Affiche la page d'inscription
     */
    public function showRegisterForm(Request $request)
    {
        return view('auth.register');
    }

    /**
     * Continue le processus de déconnexion après avoir déconnecté un site
     */
    public function continueLogout(Request $request)
    {
        $step = $request->get('step', '1');
        return view('auth.global-logout', compact('step'));
    }
}
