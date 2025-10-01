<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Google OAuth routes (web middleware, no /api prefix)
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
