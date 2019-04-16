<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AuthenticateSystem
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest() || !Auth::guard($guard)->user()->isSuperAdmin()) {
            if ($request->ajax() || $request->wantsJson() || !Auth::guard($guard)->user()->isSuperAdmin()) {
                return response('Unauthorized.', 401);
            }           
            return redirect('admin/login');
        }
        return $next($request);   
    }
}
