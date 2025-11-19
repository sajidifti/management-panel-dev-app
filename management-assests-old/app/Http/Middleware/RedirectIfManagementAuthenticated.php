<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfManagementAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get management session data
        $sessionData = $request->attributes->get('management_session', []);
        
        // If already authenticated, redirect to management dashboard
        if (isset($sessionData['authenticated']) && $sessionData['authenticated'] === true) {
            return redirect()->route('management.index');
        }

        return $next($request);
    }
}
