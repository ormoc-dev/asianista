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

    /*
    | OpenRouter (OpenAI-compatible API). Keys: https://openrouter.ai/keys
    | Base URL must be OpenRouter’s API, e.g. https://openrouter.ai/api/v1
    */
    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),
        'base_url' => rtrim((string) env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'), '/'),
        'http_referer' => env('OPENROUTER_HTTP_REFERER', (string) env('APP_URL', '')),
        'app_title' => env('OPENROUTER_APP_TITLE', 'assianista'),
    ],

    /*
    | Quest “AI Forge” models (teacher quest create). Keys are sent as ai_model from the browser.
    | Optional per-model: 'json_object' => false if the model rejects JSON mode.
    */
    'quest_ai' => [
        'default' => env('QUEST_AI_DEFAULT', 'or-llama-33-70b-free'),
        'models' => [
            'groq-llama-33' => [
                'label' => 'Groq AI — Recommended',
                'provider' => 'groq',
                'model' => 'llama-3.3-70b-versatile',
                /* Groq is not in Simple Icons (trademark); use Font Awesome in the picker instead. */
                'icon_fa' => 'fas fa-bolt',
            ],
            'or-nemotron-120b' => [
                'label' => 'NVIDIA Nemotron 3 Super',
                'provider' => 'openrouter',
                'model' => 'nvidia/nemotron-3-super-120b-a12b:free',
                'brand_slug' => 'nvidia',
                'json_object' => false,
            ],
            'or-gpt-oss-120b' => [
                'label' => 'CHAT GPT',
                'provider' => 'openrouter',
                'model' => 'openai/gpt-oss-20b:free',
                'brand_slug' => 'openai',
                'json_object' => false,
            ],
            'or-gemma-31b' => [
                'label' => 'Gemma 4 ',
                'provider' => 'openrouter',
                'model' => 'google/gemma-4-31b-it:free',
                'brand_slug' => 'google',
                'json_object' => false,
            ],
            'or-llama-33-70b-free' => [
                'label' => 'Meta AI ',
                'provider' => 'openrouter',
                'model' => 'meta-llama/llama-3-8b-instruct',
                'brand_slug' => 'meta',
                'json_object' => false,
            ],
        ],
    ],

];
