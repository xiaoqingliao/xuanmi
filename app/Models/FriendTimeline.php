<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 关注好友动态
 * @attribute id|integer
 * @attribute member_id|integer         发布人
 * @attribute model_type|string         发布动态类型
 * @attribute model_id|integer          发布动态关联id
 * @attribute title|string              标题
 * @attribute cover|string              封面图
 * @attribute content|string            动态信息
 */
class FriendTimeline extends Model
{
    public $fillable = [];
    public $casts = [
        'member_id' => 'integer',
        'model_id' => 'integer',
    ];
    
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function model()
    {
        return $this->morphTo();
    }
}
