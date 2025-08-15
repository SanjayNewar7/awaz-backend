<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ProfileController;

Route::prefix('users')->middleware('api')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::get('/search', [UserController::class, 'search']);
});

Route::post('/users', [AuthController::class, 'store']);
Route::post('/register', [AuthController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/user-login', [AuthController::class, 'userLogin']);
Route::get('/users', [AuthController::class, 'getUsers']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [AuthController::class, 'getUsers']);
    Route::get('/users/{userId}', [AuthController::class, 'getUser']);
    Route::post('/issues', [IssueController::class, 'store']);
    Route::get('/users/me', [ProfileController::class, 'getCurrentUser']);
    Route::put('/users/me', [ProfileController::class, 'updateProfile']);
});
