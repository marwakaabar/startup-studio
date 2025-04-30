<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailVerificationController extends Controller
{
    /**
     * Affiche la notification demandant à l'utilisateur de vérifier son e-mail.
     *
     * @return \Illuminate\View\View|\Inertia\Response
     */
    public function notice()
    {
        if (request()->wantsJson() || request()->header('X-Inertia')) {
            return Inertia::render('Auth/VerifyEmail', [
                'status' => session('status')
            ]);
        }
        
        return view('auth.verifyemail', [
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
        return redirect()->route('home');
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
        return back()->with('status', 'Verification link sent!');
    }
}
