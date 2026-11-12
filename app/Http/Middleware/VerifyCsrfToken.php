<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

class VerifyCsrfToken extends PreventRequestForgery
{
    // The forms read the token from the <meta name="csrf-token"> tag, so the
    // XSRF-TOKEN cookie serves no purpose here — and it broadcasts the
    // framework. Skip it.
    protected $addHttpCookie = false;
}
