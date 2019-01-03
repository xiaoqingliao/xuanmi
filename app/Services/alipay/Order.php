<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Services\alipay;

/**
 * 支付宝订单
 * @attribute subject|string               订单标题       必填
 * @attribute body|string                  订单描述       
 * @attribute out_trade_no|string          商户端订单号    必填
 * @attribute timeout_express|string       该笔订单允许的最晚付款时间     取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天
 * @attribute time_expire|string           绝对超时时间，格式为yyyy-MM-dd HH:mm。 注：1）以支付宝系统时间为准；2）如果和timeout_express参数同时传入，以time_expire为准。
 * @attribute total_amount|double          订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]    必填
 * @attribute auth_token|string            针对用户授权接口，获取用户相关数据时，用于标识用户授权关系 注：若不属于支付宝业务经理提供签约服务的商户，暂不对外提供该功能，该参数使用无效。
 * @attribute product_code|string          销售产品码，商家和支付宝签约的产品码。该产品请填写固定值：QUICK_WAP_WAY
 * @attribute goods_type|integer           商品主类型：0—虚拟类商品，1—实物类商品
 * @attribute passback_params|string       公用回传参数，如果请求时传递了该参数，则返回给商户时会回传该参数。支付宝会在异步通知时将该参数原样返回。本参数必须进行UrlEncode之后才可以发送给支付宝
 * @attribute promo_params|string          优惠参数 注：仅与支付宝协商后可用
 * @attribute extend_params|string         业务扩展参数，
 * @attribute enable_pay_channels|string   可用渠道，用户只能在指定渠道范围内支付 当有多个渠道时用“,”分隔
 * @attribute disable_pay_channels|string  禁用渠道，用户不可用指定渠道支付 当有多个渠道时用“,”分隔
 * @attribute store_id|string              商户门店编号。该参数用于请求参数中以区分各门店，非必传项。
 * @attribute quit_url|string              添加该参数后在h5支付收银台会出现返回按钮，可用于用户付款中途退出并返回到该参数指定的商户网站地址。
 */
class Order
{
    private $items = [];
    public function __construct($items)
    {
        $this->goods_type = 1;
        $this->timeout_express = '2h';  //默认付款时间更改为2小时
        $this->setItems($items);
        $this->product_code = 'QUICK_WAP_WAY';  //固定值
        
        if (is_null($this->subject) && $this->subject == '') {
            throw new AliPayException('Missing order param [subject]');
        }
        if(is_null($this->out_trade_no) && $this->out_trade_no == '') {
            throw new AliPayException('Missing order param [out_trade_no]');
        }
        if (is_null($this->total_amount) && $this->total_amount <= 0) {
            throw new AliPayException('Missing order param [total_amout]');
        }
    }

    public function getBizContent()
    {
        return json_encode($this->items, JSON_UNESCAPED_UNICODE);
    }

    private function setItems($items)
    {
        foreach($items as $key=>$val) {
            $this->items[$key] = $val;
        }
    }
    
    public function __get($key)
    {
        return isset($this->items[$key]) ? $this->items[$key] : null;
    }

    public function __set($key, $val)
    {
        $this->items[$key] = $val;
    }
}