<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('superadmin.login');
Route::post('/login', [AuthController::class, 'login'])->name('superadmin.login.post');
Route::post('/superadmin/logout', [AuthController::class, 'logout'])->name('superadmin.logout');

// SuperAdmin Dashboard Routes (protected by superadmin middleware)
Route::middleware(['superadmin'])->group(function () {
    Route::get('/superadmin', [AuthController::class, 'dashboard'])->name('superadmin.dashboard');

    // API routes for SuperAdmin dashboard
    Route::get('/api/users', [UserController::class, 'index']);
    Route::get('/api/users/search', [UserController::class, 'search']);
    Route::get('/api/users/{userId}', [UserController::class, 'show']);
    Route::post('/api/users', [AuthController::class, 'store']);
});

Route::group([], function () {
    Route::get('/storage/{path}', function ($path) {
        $fullPath = storage_path('app/public/' . $path);
        \Illuminate\Support\Facades\Log::info('Attempting to serve file: ' . $fullPath);
        if (file_exists($fullPath)) {
            \Illuminate\Support\Facades\Log::info('File found, serving: ' . $fullPath);
            $mimeType = mime_content_type($fullPath);
            return response()->file($fullPath, ['Content-Type' => $mimeType]);
        }
        \Illuminate\Support\Facades\Log::warning('File not found: ' . $fullPath);
        return response()->json(['message' => 'File not found'], 404);
    })->where('path', '.*');
});
?>
