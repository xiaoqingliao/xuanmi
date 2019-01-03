<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Services\Ali;

use GuzzleHttp\Client as GuzzleClient;

class AliBase
{
    protected $client;
    protected $server;
    protected $appkey;
    protected $appsecret;
    public function __construct()
    {
        $this->client = new GuzzleClient();
    }
    
    protected function getSign($params, $method='GET')
    {
        ksort($params);
        $canonicalizedQueryString = '';
	    foreach($params as $key => $value)
	    {
			$canonicalizedQueryString .= '&' . $this->percentEncode($key). '=' . $this->percentEncode($value);
	    }
	    $stringToSign = $method.'&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
	    $signature = $this->signString($stringToSign, $this->appsecret."&");

	    return $signature;
    }

    private function percentEncode($str)
	{
	    $res = urlencode($str);
	    $res = preg_replace('/\+/', '%20', $res);
	    $res = preg_replace('/\*/', '%2A', $res);
	    $res = preg_replace('/%7E/', '~', $res);
	    return $res;
    }
    
    private function signString($source, $secret)
    {
        return base64_encode(hash_hmac('sha1', $source, $secret, true));
    }
}