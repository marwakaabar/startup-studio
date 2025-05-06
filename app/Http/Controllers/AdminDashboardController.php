<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    // Affiche la liste des utilisateurs (sauf ceux ayant le rôle "admin")
    public function index()
    {
        $user = auth()->user();
        $users = User::where('role', '!=', 'admin')->get();

        return view('admin.dashboard', [
            'users' => $users,
            'user' => $user,
        ]);
    }

    public function coachs()
    {
        $coachs = User::where('role', 'coach')->get();
        
        return response()->json($coachs);
    }

    public function viewCoachs()
    {
        $coachs = User::where('role', 'coach')->get();
        foreach ($coachs as $coach) {
            $coach->image_url = $this->getUserImage($coach);
        }
        return view('admin.users.coachs', compact('coachs'));
    }

    public function investors()
    {
        $investors = User::where('role', 'investisseur')->get();
        return response()->json($investors);
    }

    public function viewInvestors()
    {
        $investors = User::where('role', 'investisseur')->get();
        foreach ($investors as $investor) {
            $investor->image_url = $this->getUserImage($investor);
        }
        
        return view('admin.users.investors', compact('investors'));
    }

    public function startups()
    {
        $startups = User::where('role', 'startup')->get();
        return response()->json($startups);
    }

    public function viewStartups()
    {
        $startups = User::where('role', 'startup')->with('startup')->get();
        
        // Add image URLs to each startup
        foreach ($startups as $startup) {
            $startup->image_url = $this->getUserImage($startup);
        }
        
        return view('admin.users.startups', compact('startups'));
    }
    
    /**
     * Approuver un utilisateur.
     */
    public function approve($id)
    {
        $user = User::findOrFail($id);

        // Commutation de l'état "approved"
        $user->approved = !$user->approved;
        $user->save();

        // Retourner une réponse JSON (utile pour Vue 3)
        $message = $user->approved
            ? "Startup approuvée avec succès !"
            : "Startup désapprouvée avec succès !";

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
    
    /**
     * Get the user image URL from S3
     */
    private function getUserImage($user)
    {
        $imagePath = null;

        if ($user->isCoach() && $user->coach->profile_image) {
            $imagePath = 'images/' . $user->coach->profile_image;
        } elseif ($user->isStartup() && $user->startup->logo_startup) {
            $imagePath = 'images/' . $user->startup->logo_startup;
        } elseif ($user->isInvestisseur() && $user->investisseur->profile_image) {
            $imagePath = 'images/' . $user->investisseur->profile_image;
        } elseif ($user->isAdmin() && $user->admin->profile_image) {
            $imagePath = $user->admin->profile_image;
        }

        if ($imagePath) {
            try {
                return Storage::disk('s3')->temporaryUrl($imagePath, now()->addMinutes(30));
            } catch (\Exception $e) {
                \Log::error('S3 temporary URL error: ' . $e->getMessage());
            }
        }

        // Fallback to default image if no image exists or error occurs
        return "https://eu.ui-avatars.com/api/?background=D43347&color=fff&bold=true&name=" . urlencode($user->name);
    }
}