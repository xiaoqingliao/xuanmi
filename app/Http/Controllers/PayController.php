<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers;

use App\Services\miniapp\WechatPay;
use App\Repo\OrderRepo;

/**
 * 微信支付操作 
 */
class PayController extends Controller
{
    /**
     * 支付完成后的回调
     */
    public function notify()
    {
        $result = request()->getContent();
        $pay = new WechatPay();
        $result = $pay->notify($result, function($data, $successful){
            if ($successful) {  //验证通过
                $trade_no = $data['out_trade_no'];
                list($order_sn, $order_id, $logid) = explode('|', $trade_no);
                $wechat_sn = $data['transaction_id'];
                $repo = new OrderRepo();
                $repo->payed($order_id, $wechat_sn);
                //todo msg

                //todo 增加公司财务日志
            }
        });

        return $result;
    }
}