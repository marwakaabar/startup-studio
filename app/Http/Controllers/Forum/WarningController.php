<?php

namespace App\Http\Controllers\Forum;

use App\Models\Warning;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WarningController extends Controller
{
    public function store(Request $request)
    {
        try {
            $warning = Warning::create([
                'user_id' => $request->user_id,
                'admin_id' => auth()->id(),
                'reason' => $request->reason,
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'warning' => $warning
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'avertissement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUserWarnings($userId)
    {
        $warnings = Warning::where('user_id', $userId)
            ->with('admin')
            ->latest()
            ->get();

        return response()->json(['warnings' => $warnings]);
    }
}
