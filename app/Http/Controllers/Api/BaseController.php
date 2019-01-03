<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected $member = null;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(function($request, $next){
            $this->member = auth('api')->user();

            $formid = $request->input('formid');
            if ($formid != '') {
                $this->member->setExtensions('wx_form_id', $formid);
                $this->member->save();
            }
            return $next($request);
        });
    }
}
