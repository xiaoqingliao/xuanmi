<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\Models\ApiErrorCode;
use App\Models\Customer;

/**
 * api接口appkey验证
 */
class AppkeyAuthenticated
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
        /*$key = $request->input('appkey');

        $customer = Customer::where('appkey', $key)->first();
        if ($customer == null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::APPKEY_ERROR]);
        }

        config(['app.customer_key'=>$key]);
        config(['app.customer'=>$customer]);*/

        return $next($request);
    }
}
