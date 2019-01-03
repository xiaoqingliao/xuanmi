<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Services\alipay;

/**
 * 支付宝接口封装
 */
abstract class AliPay
{
    protected $url = 'https://openapi.alipay.com/gateway.do';
    /**
     * 支付宝APPID
     */
    private $app_id;
    /**
     * 商户私钥
     */
    private $private_key;
    /**
     * 商户公钥
     */
    private $public_key;
    /**
     * 支付成功后同步返回地址
     */
    private $return_url;
    /**
     * 支付成功后异步通知地址
     */
    private $notify_url;
    /**
     * 格式
     */
    private $format = 'JSON';
    /**
     * 编码
     */
    private $charset = 'utf-8';
    /**
     * 加密方式
     */
    private $sign_type = 'RSA2';
    /**
     * 版本号
     */
    private $version = '1.0';
    /**
     * 调用方法
     */
    protected $method = '';

    /**
     * $params|array       基础参数配置
     *  app_id
     */
    public function __construct($params)
    {
        $this->appid = $params['appid'];
        $this->private_key = $params['privatekey'];
        $this->public_key = $params['publickey'];
        $this->return_url = $params['return_url'];
        $this->notify_url = $params['notify_url'];
    }
    
    protected function buildParams($order)
    {
        $data = [
            'app_id' => $this->appid,
            'method' => $this->method,
            'format' => $this->format,
            'return_url' => is_null($order->return_url) ? $this->return_url : $order->return_url,
            'charset' => $this->charset,
            'sign_type' => $this->sign_type,
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => $this->version,
            'notify_url' => is_null($order->notify_url) ? $this->notify_url : $order->notify_url,
            'biz_content' => $order->getBizContent(),
        ];

        $data['sign'] = $this->getSign($data);

        return $data;
    }

    /**
     * 支付
     * var $data|array       支付参数
     * var $type|string      支付类型 wap:手机网站支付
     */
    abstract public function pay($data);

    /**
     * 验证
     */
    public function verify($data, $sign=null, $sync=false)
    {
        if (is_null($this->public_key)) {
            throw new AliPayException('Missing Config -- [public_key]');
        }

        $sign = is_null($sign) ? $data['sign'] : $sign;

        $res = "-----BEGIN PUBLIC KEY-----\n".
            wordwrap($this->public_key, 64, "\n", true).
            "\n-----END PUBLIC KEY-----";

        $toVerify = $sync ? json_encode($data) : $this->getSignContent($data, true);
        \Log::info('ali pay verify data:' . $toVerify);

        $result = openssl_verify($toVerify, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        return $result === 1 ? $data : false;
    }

    private function getSign($data)
    {
        if (is_null($this->private_key)) {
            throw new AliPayException('Missing Config -- [private_key]');
        }

        $res = "-----BEGIN RSA PRIVATE KEY-----\n".
            wordwrap($this->private_key, 64, "\n", true).
            "\n-----END RSA PRIVATE KEY-----";

        $datastr = $this->getSignContent($data);
        openssl_sign($datastr, $sign, $res, OPENSSL_ALGO_SHA256);

        return base64_encode($sign);
    }

    private function getSignContent(array $toBeSigned, $verify = false)
    {
        ksort($toBeSigned);
        
        $stringToBeSigned = '';
        foreach ($toBeSigned as $k => $v) {
            if ($verify && $k != 'sign' && $k != 'sign_type') {
                $stringToBeSigned .= $k.'='.$v.'&';
            }
            if (!$verify && $v !== '' && !is_null($v) && $k != 'sign' && '@' != substr($v, 0, 1)) {
                $stringToBeSigned .= $k.'='.$v.'&';
            }
        }
        $stringToBeSigned = substr($stringToBeSigned, 0, -1);
        unset($k, $v);

        return $stringToBeSigned;
    }
}