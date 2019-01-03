<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 续费分成和提取日志
 * @attribute id|integer
 * @attribute member_id|integer     会员id
 * @attribute money|real            金额
 * @attribute type|integer          类型/1=增加，2=减少
 * @attribute userid|integer        处理管理员id
 * @attribute remark|string         备注
 * @attribute snapshot|array        镜像信息
 */
class MemberRenewLog extends Model
{
    const TYPE_ADD = 1;
    const TYPE_SUB = 2;
    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'money' => 'real',
        'type' => 'integer',
        'userid' => 'integer',
        'snapshot' => 'array',
    ];
    
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'userid', 'id');
    }
}
