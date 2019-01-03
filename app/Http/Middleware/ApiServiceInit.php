<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiErrorCode;
use App\Models\SiteConfig;

class ApiServiceInit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $configs = SiteConfig::all();
        foreach($configs as $config){
            foreach($config->params as $_key=>$_val) {
                config([$config->code . '.' . $_key => $_val]);
            }
        }
        return $next($request);
    }
}
