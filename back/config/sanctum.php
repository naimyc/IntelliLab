<?php

/*
|--------------------------------------------------------------------------
| Sanctum Configuration (config/sanctum.php — relevant sections)
|--------------------------------------------------------------------------
|
| These values work with the .env settings you already have.
|
*/

return [

    /*
    | Stateful Domains
    | ─────────────────
    | Requests from these domains will authenticate using Laravel's
    | session cookies. Add your frontend dev URL here.
    |
    | Reads from: SANCTUM_STATEFUL_DOMAINS in .env
    */
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,localhost:5173,127.0.0.1,127.0.0.1:8000',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    ))),

    /*
    | Sanctum Guards
    | ───────────────
    | The authentication guard Sanctum will use when checking stateful
    | (session) requests. 'web' is correct for SPA cookie auth.
    */
    'guard' => ['web'],

    /*
    | Expiration Minutes
    | ───────────────────
    | null = tokens never expire (sessions are controlled by SESSION_LIFETIME).
    | For SPA cookie auth this setting is less relevant.
    */
    'expiration' => null,

    /*
    | Token Prefix
    | ─────────────
    | Prefix prepended to raw API tokens in the database for identification.
    */
    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    /*
    | Sanctum Middleware
    | ───────────────────
    | The middleware Sanctum will use for route-level cookie encryption
    | and CSRF verification on stateful domains.
    */
    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies'      => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token'  => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];