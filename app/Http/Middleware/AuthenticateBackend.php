<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AuthenticateBackend
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest() || !Auth::guard($guard)->user()->isAdmin() || !Auth::guard($guard)->user()->active) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }
            if(!Auth::guard($guard)->guest() && Auth::guard($guard)->user()->isAdmin() && !Auth::guard($guard)->user()->active){
                Auth::guard($guard)->logout();
            }
            return redirect('admin/login');
        }
        return $next($request);
    }
}
