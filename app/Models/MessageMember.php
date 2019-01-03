<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 消息会员列表
 * @attribute id|integer
 * @attribute member_id|integer             本人会员id
 * @attribute visit_member_id|integer       访客会员id
 * @attribute content|string                最后一条消息
 * @attribute unread_count|integer          未读消息数
 */
class MessageMember extends Model
{
    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'visit_member_id' => 'integer',
        'unread_count' => 'integer'
    ];

    public function visitor()
    {
        return $this->belongsTo(Member::class, 'visit_member_id', 'id');
    }
}
