<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 代理会员账户金额变更记录
 * @attribute id|integer
 * @attribute member_id|integer         代理用户id
 * @attribute type|integer              操作类型 1:增加金额 2:减少金额
 * @attribute category|integer          变更类型 1:新招代理 2:用户续费 3:
 * @attribute cash|real                 金额
 * @attribute remark|string             备注信息
 * @attribute snapshot|array            镜像信息
 */
class MemberAccountLog extends Model
{
    const TYPE_ADD = 1; 
    const TYPE_SUB = 2;

    const CATEGORY_NEW = 1; //新招代理
    const CATEGORY_RENEW = 2;   //用户续费
    const CATEGORY_CHILD_NEW = 3;   //下级新招代理
    const CATEGORY_CHILD_RENEW = 4; //下级用户续费
    const CATEGORY_GROUP_UPDATE = 5;    //用户代理升级
    const CATEGORY_COURSE = 6;  //课程收益/消费
    const CATEGORY_MEETING = 7; //会议收益/消费
    const CATEGORY_GIVES = 8;   //打赏
    const CATEGORY_WITHDRAW = 9;    //提现
    const CATEGORY_WITHDRAW_COMPLETE = 10;  //提现完成
    const CATEGORY_REGISTER = 11;   //用户注册
    const CATEGORY_MERCHANT = 12;   //用户消费收入
    const CATEGORY_SYS = 99;    //后台处理

    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'type' => 'integer',
        'category' => 'integer',
        'cash' => 'real',
        'snapshot' => 'array',
    ];

    public function getTitleAttribute()
    {
        $categories = [
            self::CATEGORY_NEW => '新招代理',
            self::CATEGORY_RENEW => '会员续费',
            self::CATEGORY_CHILD_NEW => '新招代理',
            self::CATEGORY_CHILD_RENEW => '会员续费',
            self::CATEGORY_GROUP_UPDATE => '代理升级',
            self::CATEGORY_COURSE => '课程',
            self::CATEGORY_MEETING => '会议',
            self::CATEGORY_GIVES => '打赏',
            self::CATEGORY_WITHDRAW => '提现申请',
            self::CATEGORY_WITHDRAW_COMPLETE => '提现完成',
            self::CATEGORY_REGISTER => '用户注册',
            self::CATEGORY_MERCHANT => '用户消费',
            self::CATEGORY_SYS => '后台处理',
        ];
        if (in_array($this->category, [self::CATEGORY_COURSE, self::CATEGORY_MEETING])) {
            return $categories[$this->category] . '' . ($this->type == self::TYPE_ADD ? '收益' : '消费');
        }
        return isset($categories[$this->category]) ? $categories[$this->category] : '其它事件';
    }

    public function getTypeKeyAttribute()
    {
        return $this->type == self::TYPE_ADD ? 'add' : 'sub';
    }

    public static function sub($member, $category, $price, $remark='', $snap=[])
    {
        $log = new self();
        $log->member_id = $member->id;
        $log->type = self::TYPE_SUB;
        $log->category = $category;
        $log->cash = $price;
        $log->remark = $remark;
        $log->snapshot = $snap;
        $log->save();

        return $log;
    }

    public static function add($member, $category, $price, $remark='', $snap=[])
    {
        $log = new self();
        $log->member_id = $member->id;
        $log->type = self::TYPE_ADD;
        $log->category = $category;
        $log->cash = $price;
        $log->remark = $remark;
        $log->snapshot = $snap;
        $log->save();

        return $log;
    }
}
