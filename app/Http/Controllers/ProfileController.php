<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Investisseur;
use App\Models\Startup;
use App\Models\Coach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProfileController extends Controller
{

    /**
     * Affiche la page de modification du profil.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */

    public function edit(Request $request)
    {
        $user = $request->user()->load(['investisseur', 'startup', 'coach']);

        return Inertia::render('Profile/Edit', [
            'user' => $user,
            'status' => session('status')
        ]);
    }


    /**
     * Met à jour les informations de l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
   public function updateInfo(Request $request)
{
    $user = $request->user();

    // Validation des champs communs
    $fields = $request->validate([
        'name' => ['nullable', 'max:255'],
        'email' => ['nullable', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
    ]);

    // Mise à jour des informations de base
    if ($request->has('name')) {
        $user->name = $fields['name'];
    }

    if ($request->has('email') && $user->email !== $fields['email']) {
        $user->email = $fields['email'];
        $user->email_verified_at = null;
    }

    // Gestion des rôles spécifiques
    switch ($user->role) {
        case 'investisseur':
            $investisseur = Investisseur::updateOrCreate(
                ['user_id' => $user->id],
                ['visibility' => $request->input('visibility', null)]
            );

            if ($request->hasFile('profile_image')) {
                $request->validate([
                    'profile_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                // Sauvegarde de la nouvelle image
                $imageName = time() . '_' . $request->file('profile_image')->getClientOriginalName();
                $request->file('profile_image')->storeAs('images', $imageName, 'public');

                $investisseur->update(['profile_image' => $imageName]);
            }
            break;

        case 'coach':
            $coach = Coach::updateOrCreate(
                ['user_id' => $user->id],
                ['specialty' => $request->input('specialty', null)]
            );

            if ($request->hasFile('profile_image')) {
                $request->validate([
                    'profile_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                $imageName = time() . '_' . $request->file('profile_image')->getClientOriginalName();
                $request->file('profile_image')->storeAs('images', $imageName, 'public');

                $coach->update(['profile_image' => $imageName]);
            }
            break;

        case 'startup':
            $startup = Startup::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'domain_name' => $request->input('domain_name', null),
                    'phone_number' => $request->input('phone_number', null),
                ]
            );

            if ($request->hasFile('logo_startup')) {
                $request->validate([
                    'logo_startup' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                $logoName = time() . '_' . $request->file('logo_startup')->getClientOriginalName();
                $request->file('logo_startup')->storeAs('images', $logoName, 'public');

                $startup->update(['logo_startup' => $logoName]);
            }
            break;
    }

    // Enregistrement des informations de l'utilisateur
    $user->save();

    return redirect()->route('profile.edit')->with('success', 'Informations mises à jour avec succès.');
}

    
    
    /**
     * Met à jour le mot de passe de l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $fields = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:3']
        ]);

        $request->user()->update([
            'password' => Hash::make($fields['password'])
        ]);

        return redirect()->route('profile.edit');
    }


    /**
     * Supprime le compte de l'utilisateur après validation du mot de passe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password']
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}