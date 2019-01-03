<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\Services\ipdb\IPService;

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
        if (Auth::guard($guard)->guest()) {
            return redirect('/sys/login');
        }

        if (empty(session('sysip'))) {
            $request->setTrustedProxies([env('PROXY_IP', '')]);
            $ip = $request->getClientIp();
            $areas = implode(',', IPService::find($ip));

            session(['sysip'=>$ip, 'sysareas'=>$areas]);
        }

        return $next($request);
    }
}
