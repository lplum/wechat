<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->session()->has('u_id')) {
//            echo "您的用户名为".session('user_name');
//            echo "<br />";
        }else{
            return redirect('/zhou/login');
        }

        return $next($request);
    }
}
