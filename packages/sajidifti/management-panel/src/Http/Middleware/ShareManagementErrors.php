<?php

namespace Sajidifti\ManagementPanel\Http\Middleware;

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
        // Get management session data
        $sessionData = $request->attributes->get('management_session', []);

        // Share errors with views if they exist in session
        $errors = $sessionData['errors'] ?? new ViewErrorBag();
        view()->share('errors', $errors);

        // Share old input data for form repopulation
        $oldInput = $sessionData['_old_input'] ?? [];
        view()->share('old', $oldInput);

        return $next($request);
    }
}
