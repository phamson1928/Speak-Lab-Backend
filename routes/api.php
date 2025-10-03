<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomParticipantController;

Route::get('/rooms', [RoomController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('checkAdmin')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::put('/{id}', [UserController::class, 'update']);
            Route::delete('/{id}', [UserController::class, 'destroy']);
        });

        Route::get('/rooms/{room}', [RoomController::class, 'show']);
        Route::put('/rooms/{room}', [RoomController::class, 'update']);
    });

    Route::post('/rooms', [RoomController::class, 'store']);
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy']); 
    Route::post('/rooms/{roomId}/join', [RoomController::class, 'join']);

    Route::get('/room-participants', [RoomParticipantController::class, 'index']);
    Route::get('/room-participants/{roomParticipant}', [RoomParticipantController::class, 'show']);
    Route::post('/room-participants', [RoomParticipantController::class, 'store']);
    Route::put('/room-participants/{roomParticipant}', [RoomParticipantController::class, 'update']);
    Route::delete('/room-participants/{roomParticipant}', [RoomParticipantController::class, 'destroy']);
    
});