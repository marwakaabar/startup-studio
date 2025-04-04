<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;  

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'updateInfo'])->name('profile.info');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.updateProfile');

});

Route::prefix('admin')->middleware(['auth', 'verified'])->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/coachs', [AdminDashboardController::class, 'coachs'])->name('coachs');
        Route::get('/investors', [AdminDashboardController::class, 'investors'])->name('investors');
        Route::get('/startups', [AdminDashboardController::class, 'startups'])->name('startups');
        Route::post('/approve-user/{id}', [AdminDashboardController::class, 'approve'])->name('users.approve-user');
    });
});


Route::inertia('/', 'Home')->name('home');
require __DIR__ . '/auth.php';
require __DIR__ . '/forum.php';
