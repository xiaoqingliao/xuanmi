<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Services\Ali;

use GuzzleHttp\Client as GuzzleClient;

/**
 * 阿里云短信发送
 */
class AliSms extends AliBase
{
    public function __construct()
    {
        parent::__construct();
        $this->server = config('alisms.server', 'dysmsapi-vpc.cn-shanghai.aliyuncs.com');
        $this->appkey = config('alisms.appkey', '');
        $this->appsecret = config('alisms.appsecret', '');
        $this->signName = config('alisms.sign_name', '');
        $this->region = config('alisms.region', 'cn-shanghai');
        $this->version = '2017-05-25';
    }

    /**
     * 发送验证码
     */
    public function sendCode($phone, $code)
    {
        $template_code = config('alisms.verify_code', '');
        $template = [
            'code' => $code,
        ];
        
        return $this->sendSms($phone, $template_code, $template);
    }

    public function sendSms($phone, $template_code, $template)
    {
        if ($this->server == '') {
            throw new AliSmsException('sms server error');
        }
        if ($this->appkey == '') {
            throw new AliSmsException('sms AccessKeyId error');
        }
        if ($this->appsecret == '') {
            throw new AliSmsException('sms AccessSecret error');
        }
        if ($this->signName == '') {
            throw new AliSmsException('sms sign name error');
        }
        if ($phone == '') {
            throw new AliSmsException('未填写手机号码');
        }
        if ($template_code == '') {
            throw new AliSmsException('无短信模板code');
        }
        if (empty($template)) {
            throw new AliSmsException('无模板数据');
        }
        $data = [
            'AccessKeyId' => $this->appkey,
            'Action' => 'SendSms',
            'Format' => 'JSON',
            'PhoneNumbers' => $phone,
            'RegionId' => $this->region,
            'SignName' => $this->signName,
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce' => uniqid(mt_rand(0,0xffff), true),
            'SignatureVersion' => '1.0',
            'TemplateCode' => $template_code,
            'TemplateParam' => json_encode($template),
            'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'Version' => $this->version,
        ];
        $data['Signature'] = $this->getSign($data);
        $params = [
            'query' => $data,
        ];
        $result = $this->client->get('http://' . $this->server . '/', $params);
        if ($result != null) {
            \Log::info($result->getBody());
            $result = json_decode((string)$result->getBody(), true);
            return $result;
        }
        return null;
    }
}