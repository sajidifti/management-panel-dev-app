<?php

namespace Sajidifti\ManagementPanel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class ManagementAuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('management-panel::auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        // Get credentials from config
        $configUsername = config('management.username');
        $configPassword = config('management.password');

        // Verify credentials
        if ($username === $configUsername && $password === $configPassword) {
            // Get current session data
            $sessionData = $request->attributes->get('management_session', []);
            
            // Set authentication data
            $sessionData['authenticated'] = true;
            $sessionData['username'] = $username;
            $sessionData['login_time'] = now()->timestamp;
            
            // Clear any previous errors
            unset($sessionData['errors'], $sessionData['_old_input']);
            
            // Update session data
            $request->attributes->set('management_session', $sessionData);

            return redirect()->route('management.index');
        }

        // Store errors in our custom session
        $sessionData = $request->attributes->get('management_session', []);
        $errors = new \Illuminate\Support\ViewErrorBag();
        $errors->put('default', new \Illuminate\Support\MessageBag(['username' => 'Invalid credentials provided.']));
        $sessionData['errors'] = $errors;
        $sessionData['_old_input'] = $request->only('username');
        $request->attributes->set('management_session', $sessionData);
        
        return redirect()->back();
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        // Get session ID and destroy the session file
        $sessionId = $request->attributes->get('management_session_id');
        
        if ($sessionId) {
            $handler = new \Sajidifti\ManagementPanel\Http\Middleware\ManagementSessionHandler();
            $handler->destroySession($sessionId);
        }
        
        // Clear session data
        $request->attributes->set('management_session', []);

        $cookieName = config('management.session.cookie');
        
        return redirect()->route('management.login')
            ->withCookie(cookie()->forget($cookieName))
            ->with('success', 'Successfully logged out!');
    }
}
