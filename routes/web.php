<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\coachController;
use App\Http\Controllers\Api\coach\TwoFactorController;
// Route::middleware('guest')->group(function () {

// //login
// Route::get('/login', [coachController::class, 'login'])->name('login');
// Route::get('/forgetPassword', [coachController::class, 'forgetPassword'])->name('forgot-password');
// Route::get('/resetPassword', [coachController::class, 'resetPassword'])->name('resetPassword');
// Route::get('/register', [coachController::class, 'register'])->name('register');
// Route::get('/register/entreprise', [coachController::class, 'registerEntrprise'])->name('register-entreprise');
// Route::get('/abonnements', [coachController::class, 'abonnements'])->name('abonnements');
// });

//notifications 
Route::get('/notifications', [coachController::class, 'notifications'])->name('coach.notifications');
//dashboard
Route::get('/dashboard', [coachController::class, 'dashboard'])->name('coach.dashboard');

//formation
Route::prefix('formation')->group(function () {
    Route::get('/', [coachController::class, 'formation'])->name('coach.formation');
    Route::get('/details', [coachController::class, 'detailsFormation'])->name('coach.formation.details');
    Route::get('/add', [coachController::class, 'addFormation'])->name('coach.formation.add');
});

//ressources
Route::prefix('ressources')->group(function () {
    Route::get('/', [coachController::class, 'ressources'])->name('coach.ressources');
});

//forum
Route::prefix('forum')->group(function () {
    Route::get('/', [coachController::class, 'forum'])->name('coach.forum');
});
//messagerie
Route::prefix('messagerie')->group(function () {
    Route::get('/', [coachController::class, 'messagerie'])->name('coach.messagerie');
});
//calendrier
Route::prefix('calendrier')->group(function () {
    Route::get('/', [coachController::class, 'calendrier'])->name('coach.calendrier');
});
//agentIA
Route::prefix('agentia')->group(function () {
    Route::get('/', [coachController::class, 'agentia'])->name('coach.agentia');
    Route::get('/details', [coachController::class, 'detailsAgentia'])->name('coach.agentia.details');
    Route::get('/add', [coachController::class, 'addAgentia'])->name('coach.agentia.add');
});
//agent-ia-general
Route::prefix('agent-ia-general')->group(function () {
    Route::get('/', [coachController::class, 'agentIAgeneral'])->name('coach.agent-ia-general');
});
//setting
Route::prefix('setting')->group(function () {
    Route::get('/', [coachController::class, 'setting'])->name('coach.setting');
});


require __DIR__ . '/forum.php';
require __DIR__ . '/auth.php';