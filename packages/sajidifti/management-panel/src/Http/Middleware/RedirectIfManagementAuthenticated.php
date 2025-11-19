<?php

namespace Sajidifti\ManagementPanel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfManagementAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get management session data
        $sessionData = $request->attributes->get('management_session', []);

        // Check if user is already authenticated
        if (isset($sessionData['authenticated']) && $sessionData['authenticated'] === true) {
            return redirect()->route('management.index');
        }

        return $next($request);
    }
}
