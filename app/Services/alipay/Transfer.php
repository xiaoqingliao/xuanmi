<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Services\alipay;

use GuzzleHttp\Client as GuzzleClient;

class Transfer extends AliPay
{
    private $client;
    public function __construct($params)
    {
        $params['return_url'] = '';
        $params['notify_url'] = '';
        parent::__construct($params);
        $this->method = 'alipay.fund.trans.toaccount.transfer';

        $this->client = new GuzzleClient();
    }

    public function pay($order)
    {
        $data = $this->buildParams($order);
        unset($data['return_url']);
        unset($data['notify_url']);

        $params = [
            'query' => $data,
        ];

        $res = $this->client->get($this->url, $params);

        $file_content = '';
        if ($res->getStatusCode() == 200) {
            $file_content = (string)$res->getBody();
        }
        
        if (empty($file_content)) return null;

        $json = json_decode($file_content, true);
        if (isset($json['alipay_fund_trans_toaccount_transfer_response'])) {
            return $json['alipay_fund_trans_toaccount_transfer_response'];
        }
        
        return null;
    }
}