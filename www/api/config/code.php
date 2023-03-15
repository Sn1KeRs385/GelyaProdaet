<?php

use Laravel\Sanctum\Sanctum;

return [
    /*
    |--------------------------------------------------------------------------
    | Expiration Seconds
    |--------------------------------------------------------------------------
    */
    'expiration' => env('CODE_EXPIRATION', 300),
];
