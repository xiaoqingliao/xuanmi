<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 会员对话消息
 * @attribute id|integer
 * @attribute sender_id|integer     发送人id
 * @attribute receiver_id|integer   接收人id
 * @attribute content|string        内容
 * @attribute read_time|integer     阅读时间
 */
class Message extends Model
{
    protected $fillable = [];
    protected $casts = [
        'sender_id' => 'integer',
        'receiver_id' => 'integer',
        'read_time' => 'integer',
    ];
}
