<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Services\baidu;

use GuzzleHttp\Client;

/**
 * 百度地图服务
 */
class BaiduService
{
    private $url = 'http://api.map.baidu.com/';
    private $ak;
    private $sk;
    private $method = '';
    private $client = null;

    public function __construct($ak, $sk)
    {
        $this->ak = $ak;
        $this->sk = $sk;
        
        $this->client = new Client();
    }

    private function execute($params, $method='GET')
    {
        $params['ak'] = $this->ak;
        $params['output'] = 'json';
        $params['sn'] = $this->getSign($params, $method);
        //$content = file_get_contents($this->url . $this->method . '?' . http_build_query($params));
        if ($method == 'GET') {
            $response = $this->client->get($this->url . $this->method, ['query'=>$params]);
        } else {
            $response = $this->client->post($this->url . $this->method, ['form_params'=>$params]);
        }

        if ($response->getStatusCode() == 200) {
            //dd($response->getBody());
            $str = (string)$response->getBody();
            return json_decode($str, true);
        }
        
        return null;
    }

    /**
     * 计算sn
     */
    private function getSign($params, $method='GET')
    {
        if ($method == 'POST') {
            ksort($params);
        }
        $querystring = http_build_query($params);
        return md5(urlencode('/' . $this->method . '?' . $querystring . $this->sk));
    }

    /**
     * 坐标转换
     */
    public function convert($lat, $lng, $from='wgs84', $to='bd09ll')
    {
        $this->method = 'geoconv/v1/';
        $params = [
            'coords' => $lng . ',' . $lat,
            'from' => $from,
            'to' => $to,
        ];

        $result = $this->execute($params);
        if ($result != null && $result['status'] == 0) {
            return [
                'lat' => $result['result']['y'],
                'lng' => $result['result']['x']
            ];
        }

        return null;
    }
    
    /**
     * 获取物理地址
     */
    public function address($lng, $lat, $from='wgs84ll')
    {
        $this->method = 'geocoder/v2/';
        $params = [
            'location' => $lat . ',' . $lng,
            'coordtype' => $from,
            'latest_admin' => true,
        ];

        $result = $this->execute($params);
        if ($result != null && $result['status'] == 0) {
            $result = $result['result'];
            return [
                'province' => $result['addressComponent']['province'],
                'city' => $result['addressComponent']['city'],
                'area' => $result['addressComponent']['district'],
                'address' => $result['addressComponent']['town'] . $result['addressComponent']['street'] . $result['addressComponent']['street_number'],
            ];
        }

        return null;
    }
}