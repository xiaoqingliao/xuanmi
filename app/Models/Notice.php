<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 站内消息通知
 * @attribute id|integer
 * @attribute member_id|integer         接收会员id
 * @attribute title|string              标题
 * @attribute content|string            内容
 * @attribute read_time|integer         读取时间
 */
class Notice extends Model
{
    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'read_time' => 'integer',
    ];
}
