<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 提现申请
 * 提现完成后需要添加财务日志todo
 * @attribute id|integer
 * @attribute member_id|integer     用户id
 * @attribute money|real            提现金额
 * @attribute type|string           提现帐户
 * @attribute status|integer        状态
 * @attribute string|remark         处理结果
 * @attribute integer|userid        处理人
 * @attribute fee|real              手续费
 * @attribute actual|real           实际提现金额
 * @attribute balance|real          处理完成后用户剩余金额
 * @attribute logs|string           提现记录
 */
class MemberWithdraw extends Model
{
    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'money' => 'real',
        'status' => 'integer',
        'userid' => 'integer',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'userid', 'id');
    }

    public function getMoneyFeeAttribute()
    {
        if ($this->status == AppConstants::SENDED) {
            return $this->fee;
        }
        return round($this->money * config('site.withdraw_money_fee', 0) / 100, 2);
    }
    
    public function getActualMoneyAttribute()
    {
        if ($this->status == AppConstants::SENDED) {
            return $this->actual;
        }
        return $this->money - $this->moneyFee;
    }

    public function getTypeTextAttribute()
    {
        $types = [
            'alipay' => '支付宝',
            'bank' => '银行',
        ];
        return isset($types[$this->type]) ? $types[$this->type] : $this->type;
    }
}
