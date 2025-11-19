<?php

use App\Http\Controllers\Management\ManagementAuthController;
use App\Http\Controllers\Management\ManagementController;
use Illuminate\Support\Facades\Route;

// Management Routes - Completely independent from main app (no web middleware, no database dependency)
Route::prefix(config('management.route_prefix'))->name('management.')
    ->withoutMiddleware(['web']) // Remove web middleware to avoid DB dependencies
    ->middleware(['management.session', 'management.errors'])
    ->group(function () {
    // Login Routes (accessible without authentication)
    Route::middleware('management.guest')->group(function () {
        Route::get('/login', [ManagementAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [ManagementAuthController::class, 'login'])->name('login.submit');
    });

    // Protected Management Routes
    Route::middleware('management.auth')->group(function () {
        // Logout Route
        Route::post('/logout', [ManagementAuthController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/', [ManagementController::class, 'index'])->name('index');
        
        // Command Execution Routes (GET for SSE streaming)
        Route::get('/execute-command', [ManagementController::class, 'executeCommand'])->name('execute-command');
        Route::post('/unlink-storage', [ManagementController::class, 'unlinkStorage'])->name('unlink-storage');
        Route::get('/system-info', [ManagementController::class, 'getSystemInfo'])->name('system-info');
        
        // Environment Settings Routes
        Route::get('/env-settings', [ManagementController::class, 'getEnvSettings'])->name('env-settings');
        Route::post('/env-settings', [ManagementController::class, 'updateEnvSettings'])->name('env-settings.update');
        Route::get('/env-file', [ManagementController::class, 'getEnvFile'])->name('env-file');
        Route::post('/env-file', [ManagementController::class, 'updateEnvFile'])->name('env-file.update');
        
        // Maintenance Mode Routes
        Route::post('/maintenance/toggle', [ManagementController::class, 'toggleMaintenance'])->name('maintenance.toggle');
        Route::get('/maintenance/status', [ManagementController::class, 'getMaintenanceStatus'])->name('maintenance.status');
    });
});
