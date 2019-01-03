<?php
namespace App\Services\miniapp;

use GuzzleHttp\Client as GuzzleClient;

class DituService
{
    private $client = null;
    private $key = '';
    public function __construct()
    {
        $this->client = new GuzzleClient();
        $this->key = env('MAP_KEY', '');
    }

    public function reverse($address)
    {
        if ($this->key == '') return null;
        $result = $this->client->get('https://apis.map.qq.com/ws/geocoder/v1/?address=' . $address . '&key=' . $this->key);
        
        if ($result->getStatusCode() == 200) {
            \Log::info($result->getBody());
            $result = json_decode((string)$result->getBody(), true);

            if (empty($result)) {
                return null;
            }

            if (isset($result['result']['location']['lat'])) {
                return [
                    'lat' => $result['result']['location']['lat'],
                    'lng' => $result['result']['location']['lng'],
                ];
            }
        }
        return null;
    }
}