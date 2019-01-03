<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 用户标签点赞记录
 * @attribute id|integer
 * @attribute member_id|integer
 * @attribute tag_id|integer
 */
class MemberTagLike extends Model
{
    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'tag_id' => 'integer',
    ];
}
