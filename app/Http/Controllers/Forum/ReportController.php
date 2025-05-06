<?php

namespace App\Http\Controllers\Forum;

use App\Models\Report;
use App\Models\Warning;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Notifications\ContentAuthorNotification;
use App\Notifications\ContentDeletedNotification;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'reportable_type' => 'required|string|in:Post,Comment',
                'reportable_id' => 'required|integer',
                'reason' => 'required|string|in:spam,harassment,inappropriate,other',
                'description' => 'nullable|string|max:1000'
            ]);

            $reportableType = "App\\Models\\" . $validated['reportable_type'];
            $reportable = $reportableType::findOrFail($validated['reportable_id']);

            // Check if user has already reported this content
            $existingReport = Report::where([
                'user_id' => auth()->id(),
                'reportable_type' => $reportableType,
                'reportable_id' => $validated['reportable_id'],
                'status' => 'pending'
            ])->first();

            if ($existingReport) {
                return response()->json([
                    'message' => 'Vous avez déjà signalé ce contenu'
                ], 422);
            }

            // Create the report
            $report = Report::create([
                'user_id' => auth()->id(),
                'reportable_type' => $reportableType,
                'reportable_id' => $validated['reportable_id'],
                'reason' => $validated['reason'],
                'description' => $validated['description'],
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contenu signalé avec succès'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du signalement:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Une erreur est survenue lors du signalement'
            ], 500);
        }
    }

    public function index()
    {
        $reports = Report::with(['user', 'reportable.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.Reports.reports', compact('reports'));
    }

    public function update(Report $report, Request $request)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:resolved,dismissed'
            ]);

            if (!$report->status === 'pending') {
                return response()->json([
                    'message' => 'Ce signalement a déjà été traité'
                ], 422);
            }

            $report->update($validated);
            $report->refresh();

            // Notifier l'utilisateur qui a fait le signalement
            $report->user->notify(new ReportResolved($report));

            return response()->json([
                'message' => 'Statut du signalement mis à jour',
                'report' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour du statut',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteContent(Report $report)
    {
        try {
            if (!$report->reportable) {
                return response()->json([
                    'message' => 'Contenu non trouvé'
                ], 404);
            }

            \DB::beginTransaction();
            try {
                // Récupérer l'auteur du contenu
                $contentAuthor = $report->reportable->user;
                $contentType = class_basename($report->reportable_type);
                
                // Vérifier si c'est le premier avertissement
                $warningsCount = Warning::where('user_id', $contentAuthor->id)->count();
                $isFirstWarning = $warningsCount === 0;

                // Créer l'avertissement
                Warning::create([
                    'user_id' => $contentAuthor->id,
                    'admin_id' => auth()->id(),
                    'reason' => 'Contenu supprimé suite à un signalement',
                    'description' => 'Violation des règles de la communauté'
                ]);

                // Notifier l'auteur du contenu (notification en temps réel + email)
                $contentAuthor->notify(new ContentAuthorNotification(
                    $contentType,
                    $isFirstWarning
                ));

                // Notifier le reporteur
                $report->user->notify(new ContentDeletedNotification(
                    $contentType,
                    $report->id
                ));

                // Supprimer le contenu et mettre à jour le statut
                $report->reportable->delete();
                $report->update(['status' => 'resolved']);

                \DB::commit();

                return response()->json([
                    'message' => 'Contenu supprimé et notifications envoyées',
                    'success' => true,
                    'warningCount' => $warningsCount + 1
                ]);
            } catch (\Exception $e) {
                \DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du contenu:', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Erreur lors de la suppression du contenu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCount()
    {
        $count = Report::where('status', 'pending')->count();
        return response()->json(['count' => $count]);
    }
}
