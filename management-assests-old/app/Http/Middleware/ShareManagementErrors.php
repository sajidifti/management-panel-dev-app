<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;
use Symfony\Component\HttpFoundation\Response;

class ShareManagementErrors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Share errors and old input BEFORE handling the request
        $sessionData = $request->attributes->get('management_session', []);
        
        // Get errors from session (if any)
        $errors = $sessionData['errors'] ?? new ViewErrorBag();
        
        // Make errors available to all views
        view()->share('errors', $errors);
        
        // Share old input with views
        $oldInput = $sessionData['_old_input'] ?? [];
        view()->share('oldInput', $oldInput);
        
        $response = $next($request);
        
        // Clear errors and old input after the request (flash data pattern)
        if (isset($sessionData['errors']) || isset($sessionData['_old_input'])) {
            unset($sessionData['errors'], $sessionData['_old_input']);
            $request->attributes->set('management_session', $sessionData);
        }

        return $response;
    }
}
