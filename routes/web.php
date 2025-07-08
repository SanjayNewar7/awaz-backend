<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('superadmin.login');
Route::post('/login', [AuthController::class, 'login'])->name('superadmin.login');
Route::get('/superadmin', [AuthController::class, 'dashboard'])->name('superadmin.dashboard');
Route::post('/superadmin/logout', [AuthController::class, 'logout'])->name('superadmin.logout');
Route::get('/api/users', [AuthController::class, 'getUsers']);
Route::get('/api/users/search', [AuthController::class, 'searchUsers']);
Route::get('/api/users/{userId}', [AuthController::class, 'getUser']);
