<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Repo;

use App\Services\miniapp\AppService;
use App\Models\Notice;

class NoticeRepo
{
    private $service;
    public function __construct()
    {
        $this->service = new AppService();
    }

    public function divide($member, $price, $remark)
    {
        if ($price <= 0) return;    //金额小于0，不发消息
        $openid = $member->openid;
        $form_id = $member->getExtensions('wx_form_id', '');
        $this->service->sendTemplate($openid, 'oP4mrXVdsjHaA9wP-yIMZQJG0QfUS0gnGWwI735DWl4', '/pages/my/notice/notice', $form_id, [
            'keyword1' => ['value'=>$price . '元'],
            'keyword2' => ['value'=>$remark],
            'keyword3' => ['value'=>date('Y-m-d')],
        ]);

        $notice = new Notice();
        $notice->member_id = $member->id;
        $notice->title = '分成' . $price . '元';
        $notice->content = $remark;
        $notice->read_time = 0;
        $notice->save();
    }
}