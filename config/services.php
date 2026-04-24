<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'remote_policy' => [
        'host' => rtrim((string) env('REMOTE_POLICY_HOST', env('SUPABASE_URL', '')), '/'),
        'key' => (string) env('REMOTE_POLICY_KEY', env('SUPABASE_ANON_KEY', '')),
        'resource' => env('REMOTE_POLICY_RESOURCE', env('SUPABASE_KILL_SWITCH_TABLE', 'app_kill_switch')),
        'cache_ttl' => (int) env('REMOTE_POLICY_CACHE_TTL', env('SUPABASE_KILL_SWITCH_CACHE_TTL', 300)),
        'cache_key' => env('REMOTE_POLICY_CACHE_KEY', 'rpctx.v1'),
    ],

];
