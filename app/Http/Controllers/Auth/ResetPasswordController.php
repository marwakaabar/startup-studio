<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ResetPasswordController extends Controller
{
     /**
     * Affiche la page de demande de réinitialisation du mot de passe.
     * 
     * @return \Illuminate\View\View
     */
    public function requestPass()
    {
        return view('auth.forget-password', [
            'status' => session('status')
        ]);
    }

    /**
     * Envoie un lien de réinitialisation du mot de passe à l'adresse e-mail fournie.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Si c'est une requête AJAX, renvoyer une réponse JSON
        if ($request->expectsJson()) {
            return response()->json([
                'status' => $status === Password::RESET_LINK_SENT ? 'success' : 'error',
                'message' => __($status)
            ]);
        }

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Affiche le formulaire de réinitialisation du mot de passe.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function resetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'email' => $request->email,
            'token' => $token
        ]);
    }


    /**
     * Gère la réinitialisation du mot de passe.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetHandler(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Pour le débogage
        Log::info('Reset password data received', [
            'email' => $request->email,
            'token_length' => strlen($request->token)
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
                
                // Pour le débogage
                Log::info('Password reset successfully for user', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Si c'est une requête AJAX, renvoyer une réponse JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'La réinitialisation de votre mot de passe a réussi.'
                ]);
            }
            
            return redirect()->route('login')->with('status', __('La réinitialisation de votre mot de passe a réussi.'));
        }
        
        // Pour le débogage
        Log::warning('Password reset failed', [
            'status' => $status,
            'email' => $request->email
        ]);

        // Si c'est une requête AJAX, renvoyer une réponse JSON
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => __($status)
            ], 400);
        }

        return back()->withErrors(['email' => [__($status)]]);
    }
}
