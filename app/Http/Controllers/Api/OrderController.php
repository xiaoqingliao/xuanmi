<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Services\miniapp\WechatPay;
use App\Models\ApiErrorCode;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Cart;
use App\Models\Member;
use App\Models\PhoneCode;

/**
 * 订单接口
 */
class OrderController extends BaseController
{
    /**
     * 我的订单列表
     */
    public function index()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));
        //$type = intval(request('type'));
        $status = intval(request('status'));
        $sn = request('sn');

        $cursor = Order::where('member_id', $this->member->id);
        $cursor->where('type', Order::TYPE_OTHER);
        if ($status > 0) {
            $cursor->where('status', $status);
        }
        if ($sn != '') {
            $cursor->where('sn', 'like', '%'. $sn .'%');
        }
        $count = $cursor->count();
        $orders = $cursor->orderBy('id', 'desc')->paginate($pagesize);
        $pages = $orders->lastPage();

        $orders = $orders->map(function($order){
            return [
                'id' => $order->id,
                'sn' => $order->sn,
                'title' => $order->title,
                'type' => $order->type,
                'price' => $order->price,
                'status' => $order->status,
            ];
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$orders, 'count'=>$count, 'pages'=>$pages]);
    }
    
    /**
     * 代理注册购买
     */
    public function proxy_register()
    {
        if ($this->member->group_id > 0 && $this->member->probation == false) { //已注册非试用用户,续费
            return $this->proxy_renew(intval(request('use_balance')));
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::ORDER_REG_ERROR]);
        }

        $order = Order::where('member_id', $this->member->id)->where('type', Order::TYPE_REG)->first();
        if ($order != null) {
            switch($order->status) {
                case Order::STATUS_PAYED:
                case Order::STATUS_FINISHED:
                return response()->json(['error'=>false, 'code'=>ApiErrorCode::ORDER_PAYED]);
                case Order::STATUS_CANCELED:
                $order = null;
                break;
            }
        }
        
        if ($order == null) {
            $order = new Order();
            $order->member_id = $this->member->id;
            $order->merchant_id = 0;
            $order->sn = Order::buildSn();
            $order->type = Order::TYPE_REG;
            $order->title = '会员注册';
            $order->price = config('site.reg_price', 198);
            $order->balance = 0;
            $order->online_balance = $order->price;
            $order->status = Order::STATUS_NEW;
            $order->content = [];
            $order->name = $this->member->name;
            $order->phone = $this->member->phone;
            $order->rebate = [];
            $order->pay_time = 0;
            $order->cancel_time = 0;
            $order->pay_type = '';
            $order->out_trade_no = '';
            $order->remark = '';
            $order->member_remark = request('remark');
            $order->extensions = [];
            $order->save();
        }

        if (config('app.debug') && env('VIRTUAL_ORDER', false)) {
            $repo = new \App\Repo\OrderRepo();
            $repo->payed($order, 'test_' . uniqid());
        }
        
        return $this->prepay($order);
    }
    
    /**
     * 代理续费订单
     * @param   boolean $use_balance 是否用余额续费
     */
    private function proxy_renew($use_balance=false)
    {
        $order = new Order();
        $order->member_id = $this->member->id;
        $order->merchant_id = 0;
        $order->sn = Order::buildSn();
        $order->type = Order::TYPE_RENEW;
        $order->title = '会员续费';
        $order->price = config('site.renew_price', 98);
        $order->balance = 0;
        $order->online_balance = $order->price;
        $order->status = Order::STATUS_NEW;
        $order->content = [];
        $order->name = $this->member->name;
        $order->phone = $this->member->phone;
        $order->rebate = [];
        $order->pay_time = 0;
        $order->cancel_time = 0;
        $order->pay_type = '';
        $order->out_trade_no = '';
        $order->remark = '';
        $order->member_remark = request('remark');
        $order->extensions = [];
        if ($use_balance) {
            $member = $this->member;
            if ($member->proxy_balance > $order->price) {
                $order->balance = $order->price;
            } else {
                $order->balance = $member->proxy_balance;
            }
            $order->online_balance = $order->price - $order->balance;
            $member->subCash($order->balance, MemberAccountLog::CATEGORY_RENEW, '余额续费');
        }
        $order->save();

        if (config('app.debug') && env('VIRTUAL_ORDER', false)) {
            $repo = new \App\Repo\OrderRepo();
            $repo->payed($order, 'test_' . uniqid());
        }

        return $this->prepay($order);
    }
    
    /**
     * 创建课程/会议订单
     */
    public function store()
    {
        $cart_ids = request('cart_ids');
        if (is_string($cart_ids)) {
            $cart_ids = explode(',', $cart_ids);
        }
        if (is_array($cart_ids) == false) $cart_ids = [];

        $carts = Cart::where('member_id', $this->member->id)->whereIn('id', $cart_ids)->with('model', 'sku')->get();
        $carts = $carts->filter(function($cart){
            if ($cart->model_type == 'meeting') {
                return $cart->model != null && $cart->sku != null;
            }
            return $cart->model != null;
        });
        if (empty($carts)) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::ORDER_CART_EMPTY]);
        }

        \DB::beginTransaction();
        try {
            $order = new Order();
            $order->member_id = $this->member->id;
            $order->merchant_id = 0;
            $order->sn = Order::buildSn();
            $order->type = Order::TYPE_OTHER;
            $order->title = '';
            $order->price = 0;
            $order->balance = 0;
            $order->online_balance = 0;
            $order->status = Order::STATUS_NEW;
            $order->content = [];
            $order->name = $this->member->name;
            $order->phone = $this->member->phone;
            $order->rebate = [];
            $order->pay_time = 0;
            $order->cancel_time = 0;
            $order->pay_type = '';
            $order->out_trade_no = '';
            $order->remark = '';
            $order->member_remark = request('remark');
            $order->extensions = [];
            $order->save();

            $details = [];
            $total_price = 0;
            $order_title = '';
            foreach($carts as $cart) {
                if ($cart->model == null) continue;
                
                if ($order_title == '') {
                    $order_title = $cart->model->title;
                } else {
                    $order_title .= '等';
                }
                
                $price = 0;
                $snaps = [];
                if ($cart->model_type == 'meeting') {
                    $price = $cart->sku->price;
                    $snaps = [
                        'meeting' => $cart->model->toJson(),
                        'sku' => $cart->sku->toJson(),
                    ];
                } else if ($cart->model_type == 'course') {
                    if ($cart->model->onsale) {
                        $price = $cart->model->price;
                    } else {
                        $price = $cart->model->market_price;
                    }
                    $snaps = [
                        'course' => $cart->model->toJson(),
                    ];
                }
                
                $detail = new OrderDetail();
                $detail->order_id = $order->id;
                $detail->member_id = $order->member_id;
                $detail->model_type = $cart->model_type;
                $detail->model_id = $cart->model_id;
                $detail->sku_id = $cart->sku_id;
                $detail->price = $price;
                $detail->number = $cart->number;
                $detail->snapshot = $snaps;
                $detail->save();
                $details[] = $detail;
                
                $total_price += $detail->price;
                $cart->delete();
            }
            if (count($details) <= 0) {
                \DB::rollBack();
                return response()->json(['error'=>true, 'code'=>ApiErrorCode::ORDER_CART_EMPTY]);
            }

            $order->title = $order_title;
            $order->price = $total_price;
            if ($order->price <= 0) {
                $order->status = Order::STATUS_PAYED;
            }
            $order->save();
            //$carts->delete();

            \DB::commit();
            return response()->json(['error'=>false, 'orderid'=>$order->id]);
        } catch(\Exception $e) {
            \DB::rollBack();
            \Log::info('购物订单创建失败');
            \Log::info($e);
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::ORDER_STORE_ERROR]);
        }
    }

    /**
     * 订单支付
     */
    public function pay($id)
    {
        $order = Order::find($id);
        if ($order == null || $order->member_id != $this->member->id) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }
        if ($order->status != Order::STATUS_NEW) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::ORDER_PAYED]);
        }

        $use_balance = intval(request('balance'));
        $order->balance = 0;
        $order->online_balance = $order->price;

        if ($use_balance) {
            $member = $this->member;
            if ($member->proxy_balance > $order->price) {
                $order->balance = $order->price;
            } else {
                $order->balance = $member->proxy_balance;
            }
            $order->online_balance = $order->price - $order->balance;
            $member->subCash($order->balance, MemberAccountLog::CATEGORY_COURSE, '购物消费');
        }
        $order->save();

        /*if (config('app.debug')) {
            $repo = new \App\Repo\OrderRepo();
            $repo->payed($order, 'test_' . uniqid());
        }*/

        return $this->prepay($order);
    }
    
    /**
     * 取消订单
     */
    public function cancel($id)
    {
        $order = Order::find($id);
        if ($order == null || $order->member_id != $this->member->id) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }

        $r = $order->cancel();

        if ($r) {
            return response()->json(['error'=>false]);
        }
        
        return response()->json(['error'=>true, 'code'=>ApiErrorCode::ORDER_ERROR]);
    }
    
    /**
     * 订单详情
     */
    public function show($id)
    {
        $order = Order::find($id);
        if ($order == null || $order->member_id != $this->member->id) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }

        $_merchant = null;
        if ($order->merchant != null) {
            $_merchant = [
                'id' => $order->merchant->id,
                'nickname' => $order->merchant->name,
                'company' => $order->merchant->company,
                'avatar' => image_url($order->merchant->avatar),
                'phone' => $order->merchant->phone,
            ];
        }
        $order = [
            'id' => $order->id,
            'title' => $order->title,
            'sn' => $order->sn,
            'merchant' => $_merchant,
            'type' => $order->type,
            'price' => $order->price,
            'status' => $order->status,
            'content' => $order->content,
            'pay_time' => $order->pay_time,
            'cancel_time' => $order->cancel_time,
            'pay_type' => $order->pay_type,
            'sys_remark' => $order->remark,
            'member_remark' => $order->member_remark,
        ];
    }

    public function payTest()
    {
        if (config('app.debug') == false) return '';
        $order = Order::where('member_id', $this->member->id)->where('status', Order::STATUS_NEW)->orderBy('id', 'desc')->first();
        if ($order == null) {
            $order = new Order();
            $order->member_id = $this->member->id;
            $order->merchant_id = 0;
            $order->sn = Order::buildSn();
            $order->type = Order::TYPE_REG;
            $order->title = '会员注册';
            $order->price = 0.01;
            $order->balance = 0;
            $order->online_balance = $order->price;
            $order->status = Order::STATUS_NEW;
            $order->content = [];
            $order->name = '测试';
            $order->phone = '测试';
            $order->rebate = [];
            $order->pay_time = 0;
            $order->cancel_time = 0;
            $order->pay_type = '';
            $order->out_trade_no = '';
            $order->remark = '';
            $order->member_remark = request('remark');
            $order->extensions = [];
            $order->save();
        }

        return $this->prepay($order);
    }

    /**
     * 为订单支付生成微信预支付码
     */
    private function prepay($order_or_id)
    {
        if (is_numeric($order_or_id)) {
            $order = Order::find($id);
        } else {
            $order = $order_or_id;
        }
        if ($order == null || $order->member_id != $this->member->id) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }

        try {
            $pay = new WechatPay();
            $notify_url = route('weixin.pay.notify');
            request()->setTrustedProxies([env('PROXY_IP')]);
            $ip = request()->getClientIp();
            $prepay_id = $pay->prepay($order->tradeNo, $this->member->openid, $order->online_balance, $order->title, $order->body, $notify_url, $ip);
            if ($prepay_id == null) {
                return response()->json(['error'=>true, 'code'=>ApiErrorCode::PREPAY_ERROR]);
            }
            $order->paytype = 'wechat';
            $order->prepayid = $prepay_id;
            $order->save();
            $payParams = $pay->buildMiniPayParams($prepay_id);
            return response()->json(['error'=>false, 'pay_params'=>$payParams]);
        } catch(\Exception $e) {
            \Log::info($e);
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::PREPAY_ERROR]);
        }
    }
}
