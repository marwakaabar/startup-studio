<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Coach;
use App\Models\Startup;
use App\Models\Investisseur;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Récupérer l'utilisateur authentifié
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Vérifier si l'utilisateur est approuvé
        if (!$user->approved) {
            // Rediriger vers la page de notification avec un message
            return redirect()->route('verification.notice')
                ->with('message', 'Votre compte est en attente d\'approbation par l\'administrateur.');
        }
        
        // Rediriger vers le dashboard spécifique en fonction du rôle
        switch ($user->role) {
            case 'coach':
                return redirect()->route('coach.dashboard');
            case 'startup':
                return redirect()->route('startup.dashboard');
            case 'investisseur':
                return redirect()->route('investisseur.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            default:
                return redirect()->route('home');
        }
    }

    /**
     * Affiche le tableau de bord pour les coachs
     */
    public function coachDashboard()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'coach') {
            return redirect()->route('login');
        }
        
        $roleData = Coach::where('user_id', $user->id)->first();
        
        return view('coach.dashboard', [
            'user' => $user,
            'roleData' => $roleData
        ]);
    }

    /**
     * Affiche le tableau de bord pour les startups
     */
    public function startupDashboard()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'startup') {
            return redirect()->route('login');
        }
        
        $roleData = Startup::where('user_id', $user->id)->first();
        
        return view('startup.dashboard', [
            'user' => $user,
            'roleData' => $roleData
        ]);
    }

    /**
     * Affiche le tableau de bord pour les investisseurs
     */
    public function investisseurDashboard()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'investisseur') {
            return redirect()->route('login');
        }
        
        $roleData = Investisseur::where('user_id', $user->id)->first();
        
        return view('investisseur.dashboard', [
            'user' => $user,
            'roleData' => $roleData
        ]);
    }
}
