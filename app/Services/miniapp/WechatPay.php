<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Services\miniapp;

use GuzzleHttp\Client as GuzzleClient;

/**
 * 微信支付
 */
class WechatPay
{
    private $client;
    private $appid;
    private $secretkey;
    private $mchKey;
    private $mchid;
    private $server;
    
    public function __construct(){
        $this->server = 'https://api.mch.weixin.qq.com/';
        $this->client = new GuzzleClient();
        $this->appid = config('site.mini_appid');
        $this->secretKey = config('site.mini_secret');
        $this->mchid = config('site.pay_mchid');
        $this->mchKey = config('site.pay_secret');
    }

    /**
     * 小程序生成统一下单号
     */
    public function prepay($out_trade_no, $openid, $price, $body, $detail, $notify_url, $ip, $trade_type='JSAPI')
    {
        $params = [
            'appid' => $this->appid,
            'mch_id' => $this->mchid,
            'device_info' => 'mini',
            'nonce_str' => $this->getNoneStr(),
            'sign_type' => 'MD5',
            'body' => $body,
            'detail' => $detail,
            'out_trade_no' => $out_trade_no,
            'fee_type' => 'CNY',
            'total_fee' => $price * 100,
            'spbill_create_ip' => $ip,
            'notify_url' => $notify_url,
            'trade_type' => $trade_type,
            'openid' => $openid,
        ];
        $params['sign'] = $this->getSign($params);

        \Log::info('支付参数');
        \Log::info($params);
        $result = $this->client->post($this->server . 'pay/unifiedorder', ['body'=>XML::build($params)]);
        if ($result->getStatusCode() == 200) {
            \Log::info('预支付结果');
            \Log::info($result->getBody());
            $json = json_decode(json_encode(XML::parse((string)$result->getBody())), true);
            if ($json['return_code'] == 'SUCCESS' && $json['result_code'] == 'SUCCESS') {
                return $json['prepay_id'];
            }
        }
        return null;
    }

    /**
     * 生成小程序支付参数
     */
    public function buildMiniPayParams($prepay_id)
    {
        $params = [
            'appId' => $this->appid,
            'nonceStr' => $this->getNoneStr(),
            'package' => 'prepay_id=' . $prepay_id,
            'signType' => 'MD5',
            'timeStamp' => (string)time(),
        ];
        $sign = $this->getSign($params);
        $params['paySign'] = $sign;
        return $params;
    }
    
    /**
     * 回调通知处理
     */
    public function notify($result, $callback)
    {
        \Log::info('支付结果通知');
        \Log::info($result);
        $params = json_decode(json_encode(XML::parse($result)), true);
        if (isset($params['return_code']) && $params['return_code'] == 'SUCCESS') {
            $data = $params;
            $sign = $data['sign'];
            unset($data['sign']);

            $new_sign = $this->getSign($data);
            if ($sign == $new_sign) {
                $callback($data, true);
                return XML::build(['return_code'=>'SUCCESS', 'return_msg'=>'OK']);
            }
        }
        
        return null;
    }
    
    private function getNoneStr()
    {
        return substr(md5(uniqid()), 0, 32);
    }
    
    /**
     * 加密处理
     */
    private function getSign($params)
    {
        ksort($params);

        $tempStr = '';
        foreach($params as $key=>$val) {
            if ($val == '') continue;
            $tempStr .= $key . '=' . $val . '&';
        }
        $tempStr .= 'key=' . $this->mchKey;

        $sign = md5($tempStr);
        return strtoupper($sign);
    }
}