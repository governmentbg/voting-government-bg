<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AuthenticateApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            return (new \App\Http\Controllers\ApiController)->handleForbiddenRoutes($request);
        }
        return $next($request);
    }
}
