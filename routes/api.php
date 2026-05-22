<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\ReadlistController;
use Illuminate\Support\Facades\Route;

// Admin auth
Route::post('/auth/login', [AuthController::class, 'login']);

// Firebase auth (user)
Route::post('/auth/firebase', [AuthController::class, 'firebaseLogin']);

// Authenticated
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::apiResource('/books', BookController::class)->only(['index', 'show']);
    Route::get('/genres/{genre}/books', [BookController::class, 'byGenre']);
    Route::get('/genres/with-books', [GenreController::class, 'withBooks']);
    Route::apiResource('/genres', GenreController::class)->only(['index', 'show']);
    Route::get('/books/{book}/download', [BookController::class, 'download']);
    
    // Readlist
    Route::apiResource('/readlists', ReadlistController::class);
    Route::post('/readlists/{readlist}/books', [ReadlistController::class, 'addBook']);
    Route::delete('/readlists/{readlist}/books', [ReadlistController::class, 'removeBook']);

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('/books', BookController::class)->except(['index', 'show']);
        Route::apiResource('/genres', GenreController::class)->except(['index', 'show']);
    });
});