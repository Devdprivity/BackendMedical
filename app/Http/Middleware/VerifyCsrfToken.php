<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'booking/api/*/reserve',
        'booking/api/*/info',
        'booking/api/*/locations',
        'booking/api/*/doctors',
        'booking/api/*/availability',
    ];
}
