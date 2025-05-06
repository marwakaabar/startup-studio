<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Affiche la notification demandant à l'utilisateur de vérifier son e-mail.
     *
     * @return \Illuminate\View\View
     */
    public function notice()
    {
        return view('auth.verify', [
            'status' => session('status')
        ]);
    }

    /**
     * Gère la vérification de l'e-mail après que l'utilisateur ait cliqué sur le lien de vérification.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handler(EmailVerificationRequest $request)
    {
        $request->fulfill();
        
        // Si c'est une requête AJAX, renvoyer une réponse JSON
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Votre adresse email a été vérifiée avec succès.',
                'redirect' => '/dashboard'
            ]);
        }
        
        // Après vérification, montrer la page de succès
        return redirect('/dashboard')->with('status', 'Votre adresse email a été vérifiée avec succès.');
    }

    /**
     * Réenvoie l'e-mail de vérification à l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        
        // Si c'est une requête AJAX, renvoyer une réponse JSON
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Un nouveau lien de vérification a été envoyé à votre adresse e-mail.'
            ]);
        }
        
        return back()->with('resent', true)
                    ->with('status', 'Un nouveau lien de vérification a été envoyé à votre adresse e-mail.');
    }
}
