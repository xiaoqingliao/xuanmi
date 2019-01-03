<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 用户标签
 * @attribute id|integer
 * @attribute member_id|integer
 * @attribute title|string
 * @attribute likes|integer
 */
class MemberTag extends Model
{
    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'likes' => 'integer',
    ];
    
    public function member()
    {
        return $this->belongsTo(Member::Class);
    }
}
