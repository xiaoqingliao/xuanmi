<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;

/**
 * 抓取服务
 */
class RemoteCatchService
{
    private $client = null;
    public function __construct()
    {
        $this->client = new GuzzleClient();
    }

    /**
     * 抓取远程json
     */
    public function catchJson($url, $method='get', $params=[]) {
        if (empty($url)) return null;

        if ($method == 'get') {
            $res = $this->client->get($url);
        } else {
            $res = $this->client->post($url, $params);
        }

        $file_content = '';
        if ($res->getStatusCode() == 200) {
            $file_content = $res->getBody();
        }
        
        if (empty($file_content)) return null;

        return json_decode($file_content, true);
    }

    /**
     * 抓取图片
     */
    public function catchImage($url)
    {
        if (empty($url)) return '';

        $res = $this->client->get($url);
        $file_content = '';
        if ($res->getStatusCode() == 200) {
            $file_content = $res->getBody();
        }

        if (empty($file_content)) return '';

        $folder = date('Ymd');
        $new_filename = time() . uniqid() . '.jpg';
        $new_path = $folder . '/' . $new_filename;
        
        upload_save($file_content, $new_path);

        return $new_path;
    }
}