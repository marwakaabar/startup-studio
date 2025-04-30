<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthenticateController extends Controller
{
    /**
     * Affiche la page de connexion.
     *
     * @return \Inertia\Response|\Illuminate\View\View
     */
    public function create()
    {
        // If the request wants JSON (from Inertia), render the Inertia page
        if (request()->wantsJson() || request()->header('X-Inertia')) {
            return Inertia::render('Auth/Login', [
                'status' => session('status')
            ]);
        }
        
        // Otherwise, render the Blade view
        return view('auth.login');
    }

    /**
     * Gère l'authentification de l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validation des informations de connexion
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Tentative d'authentification
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Vérifie si l'utilisateur est approuvé
            if (!$user->approved) {
                Auth::logout(); // Déconnecte immédiatement l'utilisateur

                return back()->withErrors([
                    'email' => 'Votre compte n\'a pas encore été activé par l\'administrateur.'
                ])->onlyInput('email');
            }
            
            // Régénération de la session pour éviter les attaques de fixation de session
            $request->session()->regenerate();

            // Vérification du rôle de l'utilisateur pour redirection
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');//redirection vers dashboard admin
            }
            
            return redirect()->intended('dashboard');//redirection vers dashboard user
        }
        
        if ($request->wantsJson() || $request->header('X-Inertia')) {
            return response()->json([
                'errors' => [
                    'email' => [__('Les identifiants fournis ne correspondent pas à nos enregistrements.')]
                ]
            ], 422);
        }
        
        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.'
        ])->onlyInput('email');
    }

    /**
     * Déconnecte l'utilisateur et invalide la session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::logout();   // Déconnexion de l'utilisateur
        
        // Invalidation de la session et régénération du token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
