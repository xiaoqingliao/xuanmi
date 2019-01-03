<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/

namespace App\Services\miniapp;

use GuzzleHttp\Client as GuzzleClient;

/**
 * 微信小程序服务
 */
class AppService 
{
    private $appid;
    private $secret;
    private $client;
    public function __construct()
    {
        $this->appid = config('site.mini_appid');
        $this->secret = config('site.mini_secret');

        $this->client = new GuzzleClient();
    }

    private function buildToken()
    {
        $res = $this->client->get('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='. $this->appid .'&secret=' . $this->secret);
        if ($res->getStatusCode() == 200) {
            $content = (string)$res->getBody();
            $json = json_decode($content, true);
            if (isset($json['errcode'])) {
                throw new \Exception('get token error. code:' . $json['errcode'] . ', message:' . $json['errmsg']);
            }

            return $json;
        }
        throw new \Exception('get token error');
    }

    /**
     * 获取token
     */
    public function getToken()
    {
        $token = '';
        $token_file = storage_path('framework/cache/miniapp_' . $this->appid . '.json');
        if (file_exists($token_file)) {
            $json = json_decode(file_get_contents($token_file), true);
            $expires_time = isset($json['expires_time']) ? $json['expires_time'] : 0;
            if ($expires_time > time()) {
                $token = isset($json['access_token']) ? $json['access_token'] : '';
            }
        }
        
        if ($token == '') {
            $json = $this->buildToken();
            $token = $json['access_token'];
            $json['expires_time'] = time() + $json['expires_in'];
            file_put_contents($token_file, json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        
        return $token;
    }

    /**
     * 生成的小程序码
     * md5($path.$width)生成文件名保存
     */
    public function getImage($path, $width=430)
    {
        $filename = storage_path('app/public/' . md5($path . '|' . $width) . '.png');
        if (file_exists($filename)) {
            return $filename;
        }

        $token = $this->getToken();
        if (empty($token)) {
            throw new \Exception('access token error');
        }
        
        $body = [
            'path' => $path,
            'width' => intval($width),
        ];
        \Log::info($body);
        $res = $this->client->post('https://api.weixin.qq.com/wxa/getwxacode?access_token=' . $token, ['body'=>json_encode($body, JSON_UNESCAPED_UNICODE)]);
        if ($res->getStatusCode() == 200) {
            $content = $res->getBody();
            $json = json_decode((string)$content, true);
            if ($json !== false && is_array($json) && isset($json['errcode'])) {
                throw new \Exception('get qrcode error, code:' . $json['errcode'] . ', message:' . $json['errmsg']);
            }

            file_put_contents($filename, $content);
            return $filename;
        }
        throw new \Exception('get qrcode error');
    }

    /**
     * 发送模板消息
     */
    public function sendTemplate($openid, $template_id, $page, $form_id, $data)
    {
        $token = $this->getToken();
        if (empty($token) || empty($openid) || empty($template_id) || empty($page) || empty($form_id) || empty($data)) {
            return null;
        }
        $body = [
            'touser' => $openid,
            'template_id' => $template_id,
            'page' => $page,
            'form_id' => $form_id,
            'data' => $data,
            'emphasis_keyword' => '',
        ];
        $res = $this->client->post('https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . $token, ['body'=>json_encode($body, JSON_UNESCAPED_UNICODE)]);
        if ($res->getStatusCode() == 200) {
            return json_decode((string)$res->getBody(), JSON_UNESCAPED_UNICODE);
        }
        return null;
    }
}