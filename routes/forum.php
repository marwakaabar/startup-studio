<?php

use App\Http\Controllers\Forum\CommentController;
use App\Http\Controllers\Forum\ForumController;
use App\Http\Controllers\Forum\HashtagController;
use App\Http\Controllers\Forum\LikeController;
use App\Http\Controllers\Forum\PostController;
use App\Http\Controllers\Forum\SubscriptionController;
use App\Http\Controllers\Forum\TopicController;
use App\Http\Controllers\Forum\TagController;
use App\Http\Controllers\Forum\ReportController;
use App\Http\Controllers\Forum\WarningController;
use App\Http\Controllers\Forum\ReactionController;
use Illuminate\Support\Facades\Route;

// Routes pour les forums
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/forums', [ForumController::class, 'index'])->name('forums.index');
    Route::get('/forums/create', [ForumController::class, 'create'])->name('forums.create');
    Route::post('/forums', [ForumController::class, 'store'])->name('forums.store');
    Route::get('/forums/{forum}', [ForumController::class, 'show'])->name('forums.show');
    Route::get('/forums/search', [ForumController::class, 'search'])->name('forums.search');
    Route::post('/forums/{forum}/request-participation', [ForumController::class, 'requestParticipation'])
        ->name('forums.request-participation');
    Route::post('/forums/{forum}/respond-participation', [ForumController::class, 'respondToParticipation'])
        ->name('forums.respond-participation');
    Route::get('/forums/{forum}/participation-status', [ForumController::class, 'getParticipationStatus'])
        ->name('forums.participation-status');

// Routes pour les topics
    Route::post('/forums/{forum}/topics', [TopicController::class, 'store'])->name('topics.store');
    Route::get('/forums/{forum}/topics', [TopicController::class, 'index'])->name('topics.index'); // Nouvelle route
    Route::get('/topics/{topic}', [TopicController::class, 'show'])->name('topics.show');
    Route::get('/forums/{forum}/topics/create', [TopicController::class, 'create'])->name('topics.create');

// Routes pour les posts
    Route::post('/topics/{topic}/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/user/image', [TopicController::class, 'getUserImage']);
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update'); 
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/upload-image', [PostController::class, 'uploadImage'])->name('posts.upload-image');
    Route::post('/posts/{post}/best-answer', [PostController::class, 'toggleBestAnswer'])->name('posts.best-answer');

// Routes pour les commentaires
    Route::get('/posts/{post}/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

// Routes pour les hashtags
    Route::get('/hashtags', [HashtagController::class, 'index'])->name('hashtags.index');
    Route::post('/hashtags', [HashtagController::class, 'store'])->name('hashtags.store');
    Route::delete('/hashtags/{hashtag}', [HashtagController::class, 'destroy'])->name('hashtags.destroy');
    Route::get('/tags/search', [TagController::class, 'search']);

// Routes pour les abonnements
    Route::post('/topics/{topic}/subscribe', [SubscriptionController::class, 'toggleSubscription'])->name('subscriptions.toggle');
    Route::get('/topics/{topic}/subscription-status', [SubscriptionController::class, 'getSubscriptionStatus'])->name('subscriptions.status');

// Routes pour les réactions
    Route::post('/react', [ReactionController::class, 'toggle'])->name('reactions.toggle');

    Route::get('/notifications', function () {
        return auth()->user()->notifications()->latest()->get();
    });

    Route::patch('/notifications/{id}', function ($id) {
        auth()->user()->notifications()->findOrFail($id)->markAsRead();
        return response()->noContent();
    });
    
    Route::get('/forums/{forum}/participation-status', [ForumController::class, 'getParticipationStatus']);
    Route::post('/forums/{forum}/request-participation', [ForumController::class, 'requestParticipation']);
    
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});


