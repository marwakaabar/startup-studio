<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;  
use App\Http\Controllers\Forum\ReportController;
use App\Http\Controllers\Forum\WarningController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Middleware\EnsureUserIsApproved;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\Request;
use App\Http\Controllers\NotificationController;


Route::get('/test-s3', function () {
    try {
        $files = Storage::disk('s3')->files('/');
        return response()->json(['success' => true, 'files' => $files]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
});

Route::middleware(['auth'])->group(function () {
    // Notifications routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::patch('/notifications/{id}', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    
    Route::middleware([EnsureUserIsApproved::class])->group(function () {
        // Route dashboard générale (redirection selon le rôle)
        Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

        // Routes pour les profils utilisateur
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [ProfileController::class, 'updateInfo'])->name('profile.info');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.updateProfile');

        // Routes spécifiques pour le coach
        Route::prefix('coach')->name('coach.')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'coachDashboard'])->name('dashboard');
            Route::get('/setting', function () {
                return view('coach.setting.index');
            })->name('setting');
            
            // Routes pour les formations
            Route::get('/formation', [App\Http\Controllers\coachController::class, 'formation'])->name('formation');
            Route::get('/formation/details', [App\Http\Controllers\coachController::class, 'detailsFormation'])->name('formation.details');
            Route::get('/formation/add', [App\Http\Controllers\coachController::class, 'addFormation'])->name('formation.add');
            
            // Routes pour les ressources
            Route::get('/ressources', [App\Http\Controllers\coachController::class, 'ressources'])->name('ressources');
            
            // Routes pour le forum coach
            Route::get('/forum', [App\Http\Controllers\coachController::class, 'forum'])->name('forum');
            
            // Routes pour la messagerie
            Route::get('/messagerie', [App\Http\Controllers\coachController::class, 'messagerie'])->name('messagerie');
            
            // Routes pour le calendrier
            Route::get('/calendrier', [App\Http\Controllers\coachController::class, 'calendrier'])->name('calendrier');
            
            // Routes pour agent IA
            Route::get('/agentia', [App\Http\Controllers\coachController::class, 'agentia'])->name('agentia');
            Route::get('/agentia/add', [App\Http\Controllers\coachController::class, 'addAgentia'])->name('agentia.add');
            Route::get('/agentia/details', [App\Http\Controllers\coachController::class, 'detailsAgentia'])->name('agentia.details');
            
            // Garder les anciennes routes comme alias pour éviter les problèmes
            Route::get('/agent-ia', [App\Http\Controllers\coachController::class, 'agentia'])->name('agent-ia');
            Route::get('/agent-ia/add', [App\Http\Controllers\coachController::class, 'addAgentia'])->name('agent-ia.add');
            Route::get('/agent-ia/details', [App\Http\Controllers\coachController::class, 'detailsAgentia'])->name('agent-ia.details');
            
            // Routes pour agent IA général
            Route::get('/agent-ia-general', [App\Http\Controllers\coachController::class, 'agentIAgeneral'])->name('agent-ia-general');
        });

        // Routes spécifiques pour la startup
        Route::prefix('startup')->name('startup.')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'startupDashboard'])->name('dashboard');
        });

        // Routes spécifiques pour l'investisseur
        Route::prefix('investisseur')->name('investisseur.')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'investisseurDashboard'])->name('dashboard');
        });

        // Routes pour les notifications personnalisées
        Route::post('/notifications/warning/{user}', function ($user) {
            $user = App\Models\User::find($user);
            if ($user) {
                $user->notify(new App\Notifications\WarningNotification(request('reason')));
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false], 404);
        })->name('notifications.warning');

        Route::post('/notifications/content-deleted/{user}', function ($user) {
            $user = App\Models\User::find($user);
            if ($user) {
                $user->notify(new App\Notifications\ContentDeletedNotification(
                    request('content_type'),
                    request('report_id')
                ));
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false], 404);
        })->name('notifications.content-deleted');
    });
});

// Routes pour administrateurs avec middleware de vérification de rôle
Route::prefix('admin')->middleware(['auth', 'verified'])->name('admin.')->group(function () {
    Route::middleware([EnsureUserIsApproved::class, RoleMiddleware::class.':admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Routes pour les signalements
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/count', [ReportController::class, 'getCount'])->name('reports.count');
        Route::post('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
        Route::delete('/reports/{report}/content', [ReportController::class, 'deleteContent'])->name('reports.delete-content');
        
        // Routes pour les avertissements
        Route::get('/users/{user}/warnings', [WarningController::class, 'getUserWarnings'])->name('warnings.get');
        Route::post('/warnings', [WarningController::class, 'store'])->name('warnings.store');
        
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/coachs', [AdminDashboardController::class, 'viewCoachs'])->name('coachs');
            Route::get('/investors', [AdminDashboardController::class, 'viewInvestors'])->name('investors');
            Route::get('/startups', [AdminDashboardController::class, 'viewStartups'])->name('startups');
            Route::post('/approve-user/{id}', [AdminDashboardController::class, 'approve'])->name('approve-user');
        });

        // Routes pour la modération IA
        Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation.index');
        Route::get('/moderation/stats', [ModerationController::class, 'stats'])->name('moderation.stats');
        Route::get('/moderation/{id}', [ModerationController::class, 'show'])->name('moderation.show');
        Route::put('/moderation/{id}', [ModerationController::class, 'update'])->name('moderation.update');
        Route::post('/moderation/{id}/replay', [ModerationController::class, 'replay'])->name('moderation.replay');
    });
});

Route::get('/', function () {
    return view('home');
})->name('home');

// Route de test pour le middleware approved
Route::get('/test-middleware', function() {
    return 'Le middleware fonctionne correctement!';
})->middleware(['auth', EnsureUserIsApproved::class])->name('test.middleware');

// Route de test pour le middleware role
Route::get('/test-role-middleware', function() {
    return 'Le middleware de rôle fonctionne correctement!';
})->middleware(['auth', RoleMiddleware::class.':admin'])->name('test.role.middleware');

// Routes pour rediriger les utilisateurs selon leur rôle après la connexion
Route::middleware(['auth'])->group(function() {
    Route::get('/redirect', function() {
        $user = auth()->user();

        if (!$user->approved) {
            return redirect()->route('verification.notice')
                ->with('message', 'Votre compte est en attente d\'approbation par l\'administrateur.');
        }

        // Redirection en fonction du rôle de l'utilisateur
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'coach':
                return redirect()->route('coach.dashboard');
            case 'startup':
                return redirect()->route('startup.dashboard');
            case 'investisseur':
                return redirect()->route('investisseur.dashboard');
            default:
                return redirect()->route('home');
        }
    })->name('redirect');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/forum.php';
