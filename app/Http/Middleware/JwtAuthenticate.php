<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\ApiErrorCode;

class JwtAuthenticate
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
        try{
            $user = auth('api')->setToken($request->input('token'))->user();
            if ($user == null) {
                return response()->json(['error'=>true, 'code'=>ApiErrorCode::TOKEN_ERROR]);
            }
        }catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['error'=>true, 'code'=>ApiErrorCode::TOKEN_ERROR]);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['error'=>true, 'code'=>ApiErrorCode::TOKEN_EXPIRED]);
            }else{
                return response()->json(['error'=>true, 'code'=>ApiErrorCode::COMMON_ERROR]);
            }
        }
        return $next($request);
    }
}
