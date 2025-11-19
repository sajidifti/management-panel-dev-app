<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Management Panel Route Prefix
    |--------------------------------------------------------------------------
    |
    | The URL prefix for accessing the management panel. Change this to
    | something unique and hard to guess for security purposes.
    |
    */
    'route_prefix' => env('MANAGEMENT_ROUTE_PREFIX', 'management/secret'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Credentials
    |--------------------------------------------------------------------------
    |
    | Credentials for accessing the management panel. These are stored in
    | the .env file and are independent of database authentication.
    |
    */
    'username' => env('MANAGEMENT_USERNAME', 'admin'),
    'password' => env('MANAGEMENT_PASSWORD', 'password'),

    /*
    |--------------------------------------------------------------------------
    | Session Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the file-based session system used by the management
    | panel. This is completely independent of the main application's session.
    |
    */
    'session' => [
        'lifetime' => env('MANAGEMENT_SESSION_LIFETIME', 120), // minutes
        'path' => storage_path('framework/management_sessions'),
        'cookie' => 'management_session_id',
        'gc_probability' => 2, // 2% chance of garbage collection on each request
    ],
];
