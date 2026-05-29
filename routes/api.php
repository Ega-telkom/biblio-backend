<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\ReadlistController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

// Firebase auth (user)
Route::post('/auth/firebase', [AuthController::class, 'firebaseLogin']);

// Payment
Route::post('/payment/webhook', [PaymentController::class, 'webhook']);

// Authenticated
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'me']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar']);
    
    Route::post('/progress', [ProgressController::class, 'upsert']);
    Route::delete('/progress', [ProgressController::class, 'destroy']);

    Route::apiResource('/books', BookController::class)->only(['index', 'show']);
    Route::get('/genres/{genre}/books', [BookController::class, 'byGenre']);
    Route::get('/genres/with-books', [GenreController::class, 'withBooks']);
    Route::apiResource('/genres', GenreController::class)->only(['index', 'show']);
    
    // Readlist
    Route::apiResource('/readlists', ReadlistController::class);
    Route::post('/readlists/{readlist}/books', [ReadlistController::class, 'addBook']);
    Route::delete('/readlists/{readlist}/books', [ReadlistController::class, 'removeBook']);

    Route::middleware('subscribed')->group(function () {
        Route::get('/books/{book}/download', [BookController::class, 'download']);
    });
    
    Route::post('/payment/subscribe', [PaymentController::class, 'subscribe']);

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('/books', BookController::class)->except(['index', 'show']);
        Route::apiResource('/genres', GenreController::class)->except(['index', 'show']);
    });
});
