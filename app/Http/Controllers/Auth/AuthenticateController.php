<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateController extends Controller
{
    /**
     * Affiche la page de connexion.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login', [
            'status' => session('status')
        ]);
    }

    /**
     * Gère l'authentification de l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Votre compte n\'est pas encore activé. Veuillez contacter l\'administrateur pour plus d\'informations.'
                    ], 403);
                }

                return back()->withErrors([
                    'email' => 'Votre compte n\'est pas encore activé. Veuillez contacter l\'administrateur pour plus d\'informations.'
                ])->onlyInput('email');
            }
            
            // Régénération de la session pour éviter les attaques de fixation de session
            $request->session()->regenerate();

            // Si c'est une requête AJAX, renvoyer une réponse JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie',
                    'redirect' => route('dashboard')
                ]);
            }

            // Redirection vers la route de redirection qui gère tous les cas
            return redirect()->route('redirect');
        }
        
        // Si c'est une requête AJAX, renvoyer une réponse JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiant ou mot de passe incorrect.'
            ], 401);
        }
        
        return back()->withErrors([
            'email' => 'Identifiant ou mot de passe incorrect.'
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

        // Si c'est une requête AJAX, renvoyer une réponse JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie',
                'redirect' => route('login')
            ]);
        }

        return redirect()->route('login');
    }
}
