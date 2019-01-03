<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiErrorCode;

class GroupAuthenticate
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
        $member = auth('api')->user();
        if ($member == null || $member->group_id <= 0) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEMBER_GROUP_ERROR]);
        }
        return $next($request);
    }
}
