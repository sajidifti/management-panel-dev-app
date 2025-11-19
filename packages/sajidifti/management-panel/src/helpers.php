<?php

if (!function_exists('management_session')) {
    /**
     * Get or set management session data
     * 
     * @param string|array|null $key
     * @param mixed $default
     * @return mixed
     */
    function management_session($key = null, $default = null)
    {
        $request = request();
        $sessionData = $request->attributes->get('management_session', []);

        // If no key provided, return all session data
        if ($key === null) {
            return $sessionData;
        }

        // If key is an array, set multiple values
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $sessionData[$k] = $v;
            }
            $request->attributes->set('management_session', $sessionData);
            return null;
        }

        // Get specific key
        return $sessionData[$key] ?? $default;
    }
}

if (!function_exists('management_session_forget')) {
    /**
     * Remove a key from management session
     * 
     * @param string|array $keys
     * @return void
     */
    function management_session_forget($keys)
    {
        $request = request();
        $sessionData = $request->attributes->get('management_session', []);

        foreach ((array) $keys as $key) {
            unset($sessionData[$key]);
        }

        $request->attributes->set('management_session', $sessionData);
    }
}

if (!function_exists('management_auth_user')) {
    /**
     * Get the authenticated management user's username
     * 
     * @return string|null
     */
    function management_auth_user()
    {
        return management_session('username');
    }
}

if (!function_exists('management_session_destroy')) {
    /**
     * Destroy the current management session and its file
     * 
     * @return void
     */
    function management_session_destroy()
    {
        $request = request();
        $sessionId = $request->attributes->get('management_session_id');
        
        if ($sessionId) {
            $handler = new \Sajidifti\ManagementPanel\Http\Middleware\ManagementSessionHandler();
            $handler->destroySession($sessionId);
        }
        
        $request->attributes->set('management_session', []);
    }
}
