<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS)
    |--------------------------------------------------------------------------
    |
    | Erlaubt dem Vue-Frontend (localhost:5173) Requests an Laravel (localhost:8000).
    | supports_credentials MUSS true sein — sonst werden Session-Cookies
    | vom Browser nicht mitgeschickt.
    |
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie', // Pflicht: Vue muss diesen Endpoint aufrufen vor Login
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173', // Vite Dev Server (Standard)
        'http://localhost:3000', // Falls du einen anderen Port nutzt
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    /*
    | KRITISCH: Muss true sein damit Axios withCredentials: true funktioniert.
    | Ohne diese Option werden Session-Cookies blockiert → immer 401.
    */
    'supports_credentials' => true,

];