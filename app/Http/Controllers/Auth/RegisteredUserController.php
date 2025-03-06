<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\Investisseur;
use App\Models\Startup;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'role' => 'required|in:startup,coach,investisseur',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Validation de l'image
    ]);

    // Gestion de l'image
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 'public'); // Stockage dans storage/app/public/images
    }

    // Création de l'utilisateur
    $user = User::create([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'password' => Hash::make($request->input('password')),
        'role' => $request->input('role'),
        'image' => $imagePath, // Ajout de l'image corrigée
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
    Auth::login($user);

    return redirect(route('profile.edit', absolute: false));
}

}
