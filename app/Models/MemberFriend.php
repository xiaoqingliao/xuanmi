<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 用户关注
 * @attribute id|integer
 * @attribute member_id|integer
 * @attribute friend_id|integer
 */
class MemberFriend extends Model
{
    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'friend_id' => 'integer',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class)->where('group_id', '>', 0);
    }
    
    public function friend()
    {
        return $this->belongsTo(Member::class, 'friend_id', 'id')->where('group_id', '>', 0);
    }
}
