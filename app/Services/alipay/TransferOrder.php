<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Services\alipay;

class TransferOrder
{
    private $items = [];
    public function __construct($items)
    {
        $this->setItems($items);
        $this->payee_type = 'ALIPAY_LOGONID';
        
        if (is_null($this->out_biz_no) && $this->out_biz_no == '') {
            throw new AliPayException('Missing order param [out_biz_no]');
        }
        /*if(is_null($this->payee_type) && $this->payee_type == '') {
            throw new AliPayException('Missing order param [payee_type]');
        }*/
        if (is_null($this->payee_account) && $this->payee_account <= 0) {
            throw new AliPayException('Missing order param [payee_account]');
        }
        if (is_null($this->amount) && $this->amount <= 0) {
            throw new AliPayException('Missing order param [amount]');
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