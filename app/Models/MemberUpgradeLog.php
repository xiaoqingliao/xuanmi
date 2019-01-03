<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 会员代理级别升级日志
 * @attribute id|integer
 * @attribute member_id|integer             会员
 * @attribute old_group_id|integer          原代理级别
 * @attribute new_group_id|integer          升级后代理级别
 * @attribute type|integer                  升级方式
 * @attribute userid|integer                升级处理人员
 */
class MemberUpgradeLog extends Model
{
    const TYPE_APPLY = 1;   //用户申请后升级
    const TYPE_ADMIN = 2;   //管理员后台设置升级

    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'old_group_id' => 'integer',
        'new_group_id' => 'integer',
        'type' => 'integer',
    ];
    
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'userid', 'id');
    }
    
    public function oldGroup()
    {
        return $this->belongsTo(MemberGroup::class, 'old_group_id', 'id');
    }
    
    public function newGroup()
    {
        return $this->belongsTo(MemberGroup::class, 'new_group_id', 'id');
    }
}
