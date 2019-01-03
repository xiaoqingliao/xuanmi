<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Services\alipay;

/**
 * 支付宝手机支付
 */
class WapAliPay extends AliPay
{
    public function __construct($params)
    {
        parent::__construct($params);
        \Log::info('alipay param:' . json_encode($params, JSON_UNESCAPED_UNICODE));
        $this->method = 'alipay.trade.wap.pay';
    }

    public function pay($order)
    {
        $data = $this->buildParams($order);
        \Log::info('alipay param:' . json_encode($data, JSON_UNESCAPED_UNICODE));

        return $this->buildForm($data);
    }

    private function buildForm($data)
    {
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->url."?charset=". $data['charset'] ."' method='POST'>";
        foreach($data as $key=>$val) {
            $val = str_replace("'", '&apos;', $val);
            $sHtml .= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $sHtml .= "<input type='submit' value='ok' style='display:none;''></form>";
        $sHtml .= "<script>document.forms['alipaysubmit'].submit();</script>";

        return $sHtml;
    }
}