<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/

namespace App\Http\Controllers\Api;

use App\Services\Ali\AliSts;

class VideoController extends BaseController
{
    public function upload()
    {
        $sts = new AliSts();
        $result = $sts->getToken();
        if ($result == null || isset($result['Credentials']) == false) {
            return 'token get error';
        }
        $credentials = $result['Credentials'];
        return view('video.upload', ['credentials'=>$credentials]);
    }
}