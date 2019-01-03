<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Models\ApiErrorCode;
use App\Models\AppConstants;
use App\Services\miniapp\AppService;
use App\Services\miniapp\DituService;

/**
 * 课程接口
 */
class DemoController extends BaseController
{
    public function index()
    {
        $service = new AppService();
        $r = $service->sendTemplate('oCE0B5cojBVkx6QrDrACK1xZxucs', '10P24wRYNpCCowC1PX-N_bKmcslWmtQ0rsIA2ssq_fo', '/pages/index', 'test', [
            'keyword1' => ['value'=>'15'],
            'keyword2' => ['value'=>'用户续费'],
            'keyword3' => ['value'=>'2018-10-11'],
            'keyword4' => ['value'=>''],
        ]);
        dd($r);
    }

    public function map()
    {
        $addr = request('address');
        if (empty($addr)) $addr = '浙江省宁波市海曙区丽园北路668号';
        
        $ditu = new DituService();
        $r = $ditu->reverse($addr);
        dd($r);
    }
}
