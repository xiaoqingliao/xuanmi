<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * 订单管理
 * @attribute id|integer
 * @attribute sn|string                 订单号
 * @attribute member_id|integer         下单会员id
 * @attribute merchant_id|integer       接收商户id
 * @attribute type|integer              订单类型   1:代理注册, 2:代理续费, 3:其它订单
 * @attribute title|string              标题
 * @attribute price|real                订单价格
 * @attribute balance|real              余额支付金额
 * @attribute online_balance|real       在线支付金额
 * @attribute status|integer            订单状态
 * @attribute content|array             订单内容
 * @attribute name|string               下单人姓名
 * @attribute phone|string              下单人手机
 * @attribute rebate|array              订单分成记录
 * @attribute pay_time|integer          支付时间
 * @attribute cancel_time|integer       取消时间
 * @attribute rate_time|integer         评价时间
 * @attribute pay_type|string           支付方式, wechat:微信支付, balance:账户余额
 * @attribute out_trade_no|string       外部订单号
 * @attribute remark|string             备注信息
 * @attribute member_remark|string      会员下单备注
 * @attribute extensions|array          其它扩展属性
 * @attribute paytype|string            支付方式
 * @attribute prepayid|string           微信预支付id
 */
class Order extends Model
{
    use ExtensionTrait;

    const TYPE_REG = 1; //注册订单
    const TYPE_RENEW = 2; //续费
    const TYPE_OTHER = 3; //其它

    const STATUS_NEW = 1;   //新下单
    const STATUS_PAYED = 2; //已支付
    const STATUS_FINISHED = 8;  //已归档
    const STATUS_CANCELED = 9;  //已取消

    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'merchant_id' => 'integer',
        'type' => 'integer',
        'price' => 'real',
        'balance' => 'real',
        'online_balance' => 'real',
        'status' => 'integer',
        'content' => 'array',
        'rebate' => 'array',
        'pay_time' => 'integer',
        'cancel_time' => 'integer',
        'rate_time' => 'integer',
        'extensions' => 'array',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    
    public function merchant()
    {
        return $this->belongsTo(Member::class, 'merchant_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function getTradeNoAttribute()
    {
        return implode('|', [$this->sn, $this->id, rand(10,99)]);
    }

    public function getTypeKeyAttribute()
    {
        switch($this->type) {
            case self::TYPE_REG:
            return 'reg';
            case self::TYPE_RENEW:
            return 'renew';
            case self::TYPE_OTHER:
            return 'index';
        }
        return '';
    }

    public function getBodyAttribute()
    {
        if (empty($this->content)) {
            return $this->title;
        }

        return json_encode($this->content, JSON_UNESCAPED_UNICODE);
    }

    public function scopeSearch($query, $filters)
    {
        if (isset($filters['sn']) && $filters['sn'] != '') {
            $query->where('sn', 'like', '%'. $filters['sn'] .'%');
        }
        if (isset($filters['start_date']) && $filters['start_date'] != '') {
            $query->where('created_at', '>=', $filters['start_date'] . ' 00:00:00');
        }
        if (isset($filters['end_date']) && $filters['end_date'] != '') {
            $query->where('created_at', '<=', $filters['end_date'] . ' 23:59:59');
        }
        return $query;
    }

    /**
     * 用户取消自己的订单
     */
    public function cancel()
    {
        if ($this->status == self::ORDER_NEW) {
            $this->status = self::ORDER_CANCELED;
            $this->cancel_time = $this->freshTimestamp()->timestamp;
            $this->save();

            return true;
        }
        return false;
    }

    public static function buildSn()
    {
        while(1) {
            $sn = date('YmdHis') . rand(100,999);
            $item = self::where('sn', $sn)->first();
            if ($item == null) {
                return $sn;
            }
        }
    }
}
