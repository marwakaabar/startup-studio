<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Coach;
use App\Models\Investisseur;
use App\Models\Startup;

class RegisterController extends Controller
{
    /**
     * Affiche la page d'inscription.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Gère l'inscription d'un nouvel utilisateur.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validation des données reçues
        $credentials = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:startup,coach,investisseur',
        ]);

        //creation user
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
            'approved' => false
        ]);

        // Création des modèles associés en fonction du rôle

        if ($request->role === 'investisseur') {
            Investisseur::create([
                'user_id' => $user->id,
                'visibility' => $request->input('visibility'),
            ]);
        }

        if ($request->role === 'startup') {
            Startup::create([
                'user_id' => $user->id,
                'domain_name' => $request->input('domain_name'),
            ]);
        }

        if ($request->role === 'coach') {
            Coach::create([
                'user_id' => $user->id,
                'specialty' => $request->input('specialty'),
            ]);
        }

        event(new Registered($user));

        // Connecter l'utilisateur
        Auth::login($user);

        // Si c'est une requête AJAX, renvoyer une réponse JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Votre compte a été créé avec succès. Veuillez vérifier votre email et attendre l\'approbation de l\'administrateur.',
                'redirect' => route('verification.notice')
            ]);
        }

        // Redirection vers la page de vérification avec message d'information
        return redirect()->route('verification.notice')
            ->with('message', 'Votre compte a été créé avec succès. Veuillez vérifier votre email et attendre l\'approbation de l\'administrateur.');
    }
}
