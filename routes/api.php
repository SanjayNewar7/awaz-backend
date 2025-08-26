<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\NotificationController;

Route::prefix('users')->middleware('api')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::get('/search', [UserController::class, 'search']);
});

Route::post('/users', [AuthController::class, 'store']);
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/user-login', [AuthController::class, 'userLogin'])->name('api.user-login');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [AuthController::class, 'getUsers']);
    Route::post('/users/{userId}/like', [ProfileController::class, 'toggleLike']);
    Route::get('/users/me', [AuthController::class, 'getCurrentUser']);
    Route::put('/users/me', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/users/{userId}', [AuthController::class, 'getUser']);
    Route::post('/issues', [IssueController::class, 'store']);
    Route::get('/issues', [IssueController::class, 'index']);
    Route::post('/issues/{id}/react', [IssueController::class, 'addReaction']);
    Route::post('/issues/{id}/comment', [IssueController::class, 'addComment']);
    Route::get('/issues/{id}/comments', [IssueController::class, 'getComments']);
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/by_issue/{issue_id}', [PostController::class, 'getByIssueId']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications', [NotificationController::class, 'index'])->name('api.notifications');
});
