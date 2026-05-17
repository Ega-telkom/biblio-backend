<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\GenreController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Authenticated
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // User & Admin - read only
    Route::apiResource('/books',  BookController::class)->only(['index', 'show']);
    Route::apiResource('/genres', GenreController::class)->only(['index', 'show']);
    Route::get('/books/{book}/download', [BookController::class, 'download']);
    Route::get('/books/{book}/cover',    [BookController::class, 'cover']);

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('/books',  BookController::class)->except(['index', 'show']);
        Route::apiResource('/genres', GenreController::class)->except(['index', 'show']);
    });
});