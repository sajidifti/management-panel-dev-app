<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
            // Management routes handle their own middleware independently (no web middleware)
            require base_path('routes/management.php');
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuthenticate::class,
            'management.auth' => \App\Http\Middleware\ManagementAuth::class,
            'management.guest' => \App\Http\Middleware\RedirectIfManagementAuthenticated::class,
            'management.session' => \App\Http\Middleware\ManagementSessionHandler::class,
            'management.errors' => \App\Http\Middleware\ShareManagementErrors::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
        
        // Exclude management session cookie from encryption (needs to be plain text for file storage)
        $middleware->encryptCookies(except: [
            'management_session_id',
        ]);
        
        // Exclude management panel from maintenance mode (using env since config isn't loaded yet)
        $prefix = env('MANAGEMENT_ROUTE_PREFIX', 'management/secret');
        $middleware->preventRequestsDuringMaintenance(except: [
            $prefix,
            $prefix . '/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
