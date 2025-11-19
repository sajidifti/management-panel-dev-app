<?php

use Illuminate\Support\Facades\Route;
use Sajidifti\ManagementPanel\Http\Controllers\ManagementAuthController;
use Sajidifti\ManagementPanel\Http\Controllers\ManagementController;

$prefix = config('management.route_prefix', 'management/secret');

Route::prefix($prefix)
    ->middleware([
        'management.session',
        'management.errors',
    ])
    ->group(function () {
        // Guest routes (login)
        Route::middleware('management.guest')->group(function () {
            Route::get('/login', [ManagementAuthController::class, 'showLoginForm'])->name('management.login');
            Route::post('/login', [ManagementAuthController::class, 'login'])->name('management.login.submit');
        });

        // Authenticated routes
        Route::middleware('management.auth')->group(function () {
            Route::get('/', [ManagementController::class, 'index'])->name('management.index');
            Route::post('/logout', [ManagementAuthController::class, 'logout'])->name('management.logout');
            
            // System operations
            Route::post('/execute-command', [ManagementController::class, 'executeCommand'])->name('management.execute-command');
            Route::post('/unlink-storage', [ManagementController::class, 'unlinkStorage'])->name('management.unlink-storage');
            Route::get('/system-info', [ManagementController::class, 'getSystemInfo'])->name('management.system-info');
            
            // Environment management
            Route::get('/env-settings', [ManagementController::class, 'getEnvSettings'])->name('management.env-settings');
            Route::post('/env-settings', [ManagementController::class, 'updateEnvSettings'])->name('management.env-settings.update');
            Route::get('/env-file', [ManagementController::class, 'getEnvFile'])->name('management.env-file');
            Route::post('/env-file', [ManagementController::class, 'updateEnvFile'])->name('management.env-file.update');
            
            // Maintenance mode
            Route::post('/maintenance/toggle', [ManagementController::class, 'toggleMaintenance'])->name('management.maintenance.toggle');
            Route::get('/maintenance/status', [ManagementController::class, 'getMaintenanceStatus'])->name('management.maintenance.status');
        });
    });
