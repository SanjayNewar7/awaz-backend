<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\IssueWebController;
use App\Http\Controllers\VerificationController;

/*
|--------------------------------------------------------------------------
| AUTH CONTROLLER ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'showLoginForm'])->name('superadmin.login');
Route::post('/login', [AuthController::class, 'login'])->name('superadmin.login.post');
Route::post('/superadmin/logout', [AuthController::class, 'logout'])->name('superadmin.logout');
Route::get('/users', [AuthController::class, 'getUsers']);
Route::get('/superadmin/dashboard', [AuthController::class, 'dashboard'])->name('superadmin.dashboard');
Route::get('/usersdetail/{userId}', [AuthController::class, 'getUserdetail']);
Route::put('/usersdetail/{userId}', [AuthController::class, 'updateUserweb'])->name('updateUser');
Route::get('/superadmin/users', [AuthController::class, 'users'])->name('superadmin.users');
Route::get('/superadmin/analytics', [AuthController::class, 'analytics'])->name('superadmin.analytics');
Route::get('/superadmin/issues', [AuthController::class, 'issues'])->name('superadmin.issues');
Route::get('/api/analytics/users', [AnalyticsController::class, 'getUserAnalytics']);
Route::get('/api/analytics/user-growth', [AnalyticsController::class, 'getUserGrowthChart']);
Route::get('/api/analytics/verification-stats', [AnalyticsController::class, 'getVerificationStats']);
// Analytics routes
Route::get('/api/analytics', [AnalyticsController::class, 'getAnalytics']);
Route::get('/api/analytics/filter-options', [AnalyticsController::class, 'getFilterOptions']);


// Verification
Route::get('/superadmin/verification', [VerificationController::class, 'index'])->name('superadmin.verification');
Route::get('/api/verification/users', [VerificationController::class, 'getUsers']);
Route::get('/api/verification/users/{id}', [VerificationController::class, 'getUser']);
Route::post('/api/verification/users/{id}/verify', [VerificationController::class, 'verifyUser']);
Route::get('/api/verification/export', [VerificationController::class, 'exportCSV']);

Route::get('/analytics/data', [AnalyticsController::class, 'getAnalytics'])->name('superadmin.analytics.data');
Route::get('/regions/{district}', [AnalyticsController::class, 'getRegions'])->name('superadmin.regions');
Route::get('/wards/{district}/{region}', [AnalyticsController::class, 'getWards'])->name('superadmin.wards');
Route::get('/api/analytics/community-engagement', [AnalyticsController::class, 'getCommunityEngagement']);
// Route::get('/api/analytics/top-reporters', [AnalyticsController::class, 'getTopReporters']);

Route::get('/api/issues', [IssueWebController::class, 'index'])->name('api.issues.index');
Route::get('/api/issues/{id}', [IssueWebController::class, 'show'])->name('api.issues.show');
// Update issue status
Route::put('/api/issues/{id}/status', [IssueWebController::class, 'updateStatus'])->name('api.issues.updateStatus');
Route::prefix('api/analytics')->group(function () {
    Route::get('/issues-by-type', [IssueWebController::class, 'getIssuesByType']);
    Route::get('/issue-growth', [IssueWebController::class, 'getIssueGrowth']);
     Route::get('/issues-by-status', [IssueWebController::class, 'getIssuesByStatus']);
});


/*
|--------------------------------------------------------------------------
| SUPERADMIN CONTROLLER ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('superadmin')->name('superadmin.')->group(function () {
    // Route::get('/verification', [SuperAdminController::class, 'verification'])->name('verification');
    Route::get('/notifications', [SuperAdminController::class, 'notifications'])->name('notifications');
    Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
});

/*
|--------------------------------------------------------------------------
| STORAGE FILE ROUTE
|--------------------------------------------------------------------------
*/
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



