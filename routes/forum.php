<?php

use App\Http\Controllers\Forum\CommentController;
use App\Http\Controllers\Forum\ForumController;
use App\Http\Controllers\Forum\HashtagController;
use App\Http\Controllers\Forum\LikeController;
use App\Http\Controllers\Forum\PostController;
use App\Http\Controllers\Forum\SubscriptionController;
use App\Http\Controllers\Forum\TopicController;
use App\Http\Controllers\Forum\TagController;

use Illuminate\Support\Facades\Route;

// Routes pour les forums
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/forums', [ForumController::class, 'index'])->name('forums.index');
    Route::get('/forums/create', [ForumController::class, 'create'])->name('forums.create');
    Route::post('/forums', [ForumController::class, 'store'])->name('forums.store');
    Route::get('/forums/{forum}', [ForumController::class, 'show'])->name('forums.show');
    Route::get('/forums/search', [ForumController::class, 'search'])->name('forums.search');

// Routes pour les topics
    Route::post('/forums/{forum}/topics', [TopicController::class, 'store'])->name('topics.store');
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

// Routes pour les likes
    Route::post('/like', [LikeController::class, 'toggleLike'])->name('likes.toggle');
    
// Routes pour les abonnements
    Route::post('/topics/{topic}/subscribe', [SubscriptionController::class, 'toggleSubscription'])->name('subscriptions.toggle');



});

