<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ProfileController;

Route::prefix('users')->middleware('api')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::get('/search', [UserController::class, 'search']);
});

Route::post('/users', [AuthController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/user-login', [AuthController::class, 'userLogin']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [AuthController::class, 'getUsers']);
    Route::get('/users/{userId}', [AuthController::class, 'getUser']); // Handles /api/users/9
    Route::post('/issues', [IssueController::class, 'store']);
    Route::get('/users/me', [ProfileController::class, 'getCurrentUser']);
    Route::put('/users/me', [ProfileController::class, 'updateProfile']);
     Route::get('/users/me', [AuthController::class, 'getCurrentUser']); // Changed to AuthController
    Route::put('/users/me', [AuthController::class, 'updateProfile']);
});
