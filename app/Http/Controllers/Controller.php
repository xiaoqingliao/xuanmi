<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    protected $is_mobile;
    public function __construct()
    {
        $this->is_mobile = \Agent::isMobile();
    }
    
    public function setBackUrl($url=null)
    {
        if ($url == null) {
            $url = url()->previous();
        }
        $old = session('sys_backurl', null);
        if ($url != $old && $url != url()->current()) {
            session(['sys_backurl'=>$url]);
        }
    }

    public function getBackUrl($default)
    {
        return session('sys_backurl', $defaultUrl);
    }
}
