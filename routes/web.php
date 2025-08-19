<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('superadmin.login');
Route::post('/login', [AuthController::class, 'login'])->name('superadmin.login');
Route::get('/superadmin', [AuthController::class, 'dashboard'])->name('superadmin.dashboard');
Route::post('/superadmin/logout', [AuthController::class, 'logout'])->name('superadmin.logout');
Route::post('/users', [AuthController::class, 'store']); // Add this line for signup
Route::get('/users', [AuthController::class, 'getUsers']);
Route::get('/users/search', [AuthController::class, 'searchUsers']);
Route::get('/users/{userId}', [AuthController::class, 'getUser']);



Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (file_exists($fullPath)) {
        return response()->file($fullPath);
    }
    return response()->json(['message' => 'File not found'], 404);
})->where('path', '.*');
