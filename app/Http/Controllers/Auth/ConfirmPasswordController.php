<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ConfirmPasswordController extends Controller
{
    /**
     * Affiche la page de confirmation du mot de passe.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Auth/ConfirmPassword');
    }


    /**
     * Vérifie si le mot de passe fourni correspond à celui de l'utilisateur connecté.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Vérifie si le mot de passe fourni correspond à celui de l'utilisateur
        if (! Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors([
                'password' => ['The provided password does not match our records.']
            ]);
        }
        // Marque la session comme ayant confirmé le mot de passe
        $request->session()->passwordConfirmed();

       // Redirige l'utilisateur vers la destination prévue après confirmation
        return redirect()->intended();
    }
}
