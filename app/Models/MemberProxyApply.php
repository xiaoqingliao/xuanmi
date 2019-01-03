<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 代理申请
 * @attribute id|integer
 * @attribute member_id|integer         申请用户id
 * @attribute group_id|integer          申请代理级别id
 * @attribute status|integer            处理状态
 * @attribute remark|string             处理备注
 * @attribute money|real                代理价格
 * @attribute rebate|array              分成记录
 * @attribute userid|integer            处理人
 * @attribute contract|string           代理合同
 * @attribute bank|string               银行回执单
 */
class MemberProxyApply extends Model
{
    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'group_id' => 'integer',
        'status' => 'integer',
        'money' => 'real',
        'rebate' => 'array',
        'userid' => 'integer',
    ];
    
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function group()
    {
        return $this->belongsTo(MemberGroup::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'userid', 'id');
    }
}
