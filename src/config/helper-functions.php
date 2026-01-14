<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which route files should be loaded automatically.
    | You can disable auto-loading and publish routes to customize them.
    |
    */

    'routes' => [
        'web' => [
            'enabled' => true,
            'prefix' => null,
            'middleware' => ['web'],
        ],
        'api' => [
            'enabled' => true,
            'prefix' => 'api',
            'middleware' => ['api'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | ViewHelper Configuration
    |--------------------------------------------------------------------------
    |
    | Configure default behaviors for ViewHelper class methods.
    |
    */

    'view_helper' => [
        // Default JSON error messages
        'default_error_message' => 'Something went wrong. Please try again.',
        'no_data_message' => 'No Data found.',

        // Default success message
        'default_success_message' => 'Operation completed successfully.',

        // API detection patterns
        'api_patterns' => [
            '/api/',
        ],

        // Auth guard for API requests
        'api_guard' => 'sanctum',

        // Default auth guard
        'default_guard' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | OTP Configuration
    |--------------------------------------------------------------------------
    |
    | Configure OTP generation and validation settings.
    |
    */

    'otp' => [
        'length' => 4,
        'min' => 1000,
        'max' => 9999,
        'expiry_minutes' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Settings
    |--------------------------------------------------------------------------
    |
    | Add custom configuration options here.
	| 
	| Rest API calling endpoint domain and key.
    |
    */

    'custom' => [
        // custom config indexs
        'rest_api_domain'   => env('REST_API_DOMAIN', ''),
        'rest_api_key'      => env('REST_API_KEY', ''),
    ],

];
