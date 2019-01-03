<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Services\Ali;

class AliVideoTrack extends AliBase
{
    public function __construct()
    {
        parent::__construct();
        $this->server = 'vod.cn-shanghai.aliyuncs.com';
        $this->appkey = '';
        $this->appsecret = '';
        $this->version = '2017-03-21';
        $this->token = '';
    }
    
    public function setSecurityToken($token)
    {
        $this->token = $token;
    }
    
    /**
     * 发送剪辑请求
     */
    public function track($timeline)
    {
        $params = [
            'Action' => 'ProduceEditingProjectVideo',
            'AccessKeyId' => $this->appkey,
            'Timeline' => json_encode($timeline),
            'Format' => 'JSON',
            'Version' => $this->version,
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureVersion' => '1.0',
            'SignatureNonce' => uniqid(mt_rand(0,0xffff), true),
            'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
        ];
        $params['Signature'] = $this->getSign($params);
        \Log::info($params);
        try {
            $result = $this->client->get('https://' . $this->server . '/', ['query'=>$params]);
            if ($result->getStatusCode() == 200) {
                return json_decode((string)$result->getBody(), true);
            }
        } catch(\Exception $e) {
            \Log::info($e);
            return null;
        }
        return null;
    }
}