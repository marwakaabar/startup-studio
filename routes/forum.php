<?php

use App\Http\Controllers\Forum\CommentController;
use App\Http\Controllers\Forum\ForumController;
use App\Http\Controllers\Forum\HashtagController;
use App\Http\Controllers\Forum\LikeController;
use App\Http\Controllers\Forum\PostController;
use App\Http\Controllers\Forum\SubscriptionController;
use App\Http\Controllers\Forum\TopicController;
use Illuminate\Support\Facades\Route;

// Routes pour les forums
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/forums', [ForumController::class, 'index'])->name('forums.index');
    Route::get('/forums/create', [ForumController::class, 'create'])->name('forums.create');
    Route::post('/forums', [ForumController::class, 'store'])->name('forums.store');
    Route::get('/forums/{forum}', [ForumController::class, 'show'])->name('forums.show');

// Routes pour les topics
    Route::post('/forums/{forum}/topics', [TopicController::class, 'store'])->name('topics.store');
    Route::get('/topics/{topic}', [TopicController::class, 'show'])->name('topics.show');


// Routes pour les posts
    Route::post('/topics/{topic}/posts', [PostController::class, 'store'])->name('posts.store');

// Routes pour les commentaires
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');


// Routes pour les hashtags
    Route::get('/hashtags', [HashtagController::class, 'index'])->name('hashtags.index');
    Route::post('/hashtags', [HashtagController::class, 'store'])->name('hashtags.store');
    Route::delete('/hashtags/{hashtag}', [HashtagController::class, 'destroy'])->name('hashtags.destroy');
    Route::get('/hashtags/search', [HashtagController::class, 'search']);

// Routes pour les likes
    Route::post('/posts/{post}/like', [LikeController::class, 'toggleLike'])->name('likes.toggle');

// Routes pour les abonnements
    Route::post('/topics/{topic}/subscribe', [SubscriptionController::class, 'toggleSubscription'])->name('subscriptions.toggle');

});

