<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Repo;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Member;
use App\Models\MemberGroup;
use App\Models\MemberAccountLog;
use App\Models\CompanyFinanceLog;

/**
 * 分成计算
 */
class OrderRepo
{
    /**
     * 支付完成
     * @param $order_or_id  Order/integer 订单或订单id
     * @param $out_trade_no string  微信订单号
     */
    public function payed($order_or_id, $out_trade_no)
    {
        if (is_numeric($order_or_id)) {
            $order = Order::find($order_or_id);
        } else {
            $order = $order_or_id;
        }
        
        $order->status = Order::STATUS_PAYED;
        $order->out_trade_no = $out_trade_no;
        $order->pay_time = $order->freshTimestamp()->timestamp;
        $order->save();

        switch($order->type) {
            case Order::TYPE_REG:
            $member = $order->member;
            $member->probation = false; //转为正式会员
            if ($member->proxy_end_time > 0) {
                $member->proxy_end_time = $member->proxy_end_time + 365 * 24 * 3600;
            } else {
                $member->proxy_start_time = $order->freshTimestamp()->timestamp;
                $member->proxy_end_time = $order->freshTimestamp()->addYear()->timestamp;
            }
            $member->save();

            $msg = str_replace('{name}', $member->name, config('noticeparam.member_online_pay', '用户充值'));
            MemberAccountLog::add($member, MemberAccountLog::CATEGORY_REGISTER, $order->online_balance, $msg);
            $msg = str_replace('{name}', $member->name, config('noticeparam.member_register', '用户注册'));
            MemberAccountLog::sub($member, MemberAccountLog::CATEGORY_REGISTER, $order->online_balance, $msg);
            CompanyFinanceLog::add($member->id, $order->online_balance, 'order', $msg, $order->toArray());

            $divide = new DivideRepo();
            $divide->regDivide($order);
            break;
            case Order::TYPE_RENEW:
            //用户续费一年，有效期增加365天
            $member = $order->member;
            $member->proxy_end_time += 365*24*3600;
            $member->save();
            
            $msg = str_replace('{name}', $member->name, config('noticeparam.member_online_pay', '用户充值'));
            MemberAccountLog::add($member, MemberAccountLog::CATEGORY_REGISTER, $order->online_balance, $msg);
            $msg = str_replace('{name}', $member->name, config('noticeparam.member_renew', '用户续费'));
            MemberAccountLog::sub($member, MemberAccountLog::CATEGORY_REGISTER, $order->online_balance, $msg);
            CompanyFinanceLog::add($member->id, $order->online_balance, 'order', $msg, $order->toArray());

            $divide = new DivideRepo();
            $divide->renewDivide($order);
            break;

            case Order::TYPE_OTHER;
            $member = $order->member;
            $msg = str_replace('{name}', $member->name, config('noticeparam.member_online_pay', '用户充值'));
            MemberAccountLog::add($member, MemberAccountLog::CATEGORY_REGISTER, $order->online_balance, $msg);
            $msg = str_replace('{name}', $member->name, config('noticeparam.member_pay', '购物消费'));
            MemberAccountLog::sub($member, MemberAccountLog::CATEGORY_REGISTER, $order->online_balance, $msg);

            $rebate_log = [];
            $rebate = config('site.order_rate', 0);
            $sys_commission = 0;
            $merchant_commission = [];
            $details = OrderDetail::where('order_id', $order->id)->get();
            foreach($details as $detail) {
                $merchant_id = $detail->merchantId;
                if (isset($merchant_commission[$merchant_id]) == false) {
                    $merchant_commission[$merchant_id] = 0;
                }

                $_earning = $detail->price;
                if ($rebate > 0) {
                    $_commission = round($detail->price * $rebate / 100, 2);
                    $_earning = $detail->price - $_commission;
                    $sys_commission += $_commission;
                }
                $merchant_commission[$merchant_id] += $_earning;
            }
            \Log::info($merchant_commission);
            
            if (count($merchant_commission) > 0) {
                $merch_ids = array_keys($merchant_commission);
                $merchants = Member::whereIn('id', $merch_ids)->get()->getDictionary();
                foreach($merchant_commission as $id=>$earning) {
                    if (isset($merchants[$id])) {
                        $msg = str_replace('{name}', $member->name, config('noticeparam.member_sale', '销售收入'));
                        $merchants[$id]->addCash($earning, MemberAccountLog::CATEGORY_MERCHANT, $msg, $order->toJson());
                        $rebate_log[] = [
                            'member' => [
                                'id' => $id,
                                'nickname' => $merchants[$id]->nickname,
                                'name' => $merchants[$id]->name,
                            ],
                            'money' => $earning,
                        ];
                    }
                }
            }
            if ($sys_commission > 0) {
                $msg = str_replace('{name}', $member->name, config('noticeparam.member_pay', '用户购物消费平台抽成'));
                CompanyFinanceLog::add($member->id, $sys_commission, 'order', $msg, $order->toJson());
            }
            break;
        }
    }
}