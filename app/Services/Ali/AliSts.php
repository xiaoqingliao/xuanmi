<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/

namespace App\Services\Ali;

use GuzzleHttp\Client as GuzzleClient;

class AliSts extends AliBase
{
    public function __construct()
    {
        parent::__construct();
        $this->server = 'sts.aliyuncs.com';
        $this->appkey = 'LTAIosLDWqXIXi4Z';
        $this->appsecret = 'MABY1AWcEIo7bPSpsWj8X8bAKuhUsT';
        $this->arn = 'acs:ram::1249013832900674:role/vod';
        $this->name = 'video';
        $this->version = '2015-04-01';
    }

    public function getToken()
    {
        $params = [
            'Action' => 'AssumeRole',
            'AccessKeyId' => $this->appkey,
            'RoleArn' => $this->arn,
            'RoleSessionName' => $this->name,
            'DurationSeconds' => '3600',
            'Format' => 'JSON',
            'Version' => $this->version,
            //'Signature' => '',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureVersion' => '1.0',
            'SignatureNonce' => uniqid(mt_rand(0,0xffff), true),
            'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
        ];
        $params['Signature'] = $this->getSign($params);
        try {
            $result = $this->client->get('https://' . $this->server . '/', ['query'=>$params]);
            if ($result->getStatusCode() == 200) {
                return json_decode((string)$result->getBody(), true);
            }
        } catch(\Exception $e) {
            return null;
        }
        return null;
    }
}