<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/

namespace App\Models;

/**
 * 系统状态常量
 */
class AppConstants
{
    const DRAFT = 9;    //草稿
    const PENDING = 1;  //待审核
    const ACCEPTED = 2; //审核通过
    const REJECTED = 5; //审核拒绝

    const SENDING = 6;  //发送中
    const SENDED = 7;   //发送成功
    const FAILED = 8;   //发送失败

    public static function getStatusText($status)
    {
        switch($status) {
            case self::PENDING:
            return '待审核';
            case self::ACCEPTED:
            return '审核通过';
            case self::REJECTED:
            return '审核拒绝';
            case self::SENDING:
            return '正在发放';
            case self::SENDED:
            return '发放成功';
            case self::FAILED:
            return '发放失败';
            default:
            return '草稿';
        }
    }
}