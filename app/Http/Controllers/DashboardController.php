<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Récupérer l'utilisateur authentifié
        $user = auth()->user();  // On récupère l'utilisateur authentifié

        // Passer l'utilisateur et ses informations à la vue via Inertia
        return Inertia::render('Dashboard', [
            'user' => $user,  // Ici, on passe l'utilisateur complet, y compris son rôle
        ]);
    }
}
