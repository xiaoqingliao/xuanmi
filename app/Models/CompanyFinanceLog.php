<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 公司财务日志
 * @attribute id|integer
 * @attribute member_id|integer     关联用户
 * @attribute price|real            金额
 * @attribute model|string          模型
 * @attribute remark|string         备注
 * @attribute snapshot|array        详细内容
 */
class CompanyFinanceLog extends Model
{
    const TYPE_ADD = 1;
    const TYPE_SUB = 2;
    protected $casts = [
        'member_id' => 'integer',
        'price' => 'real',
        'snapshot' => 'array',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * 统计时间段内的收入
     */
    public static function income($start, $end)
    {
        $cursor = self::where('type', self::TYPE_ADD)->where('created_at', '<=', date('Y-m-d H:i:s', $end));
        if ($start > 0) {
            $cursor->where('created_at', '>', date('Y-m-d H:i:s', $start));
        }
        
        return $cursor->sum('price');
    }

    /**
     * 统计时间段内的支出
     */
    public static function expend($start, $end)
    {
        $cursor = self::where('type', self::TYPE_SUB)->where('created_at', '<=', date('Y-m-d H:i:s', $end));
        if ($start > 0) {
            $cursor->where('created_at', '>', date('Y-m-d H:i:s', $start));
        }
        return $cursor->sum('price');
    }

    public static function add($memberid, $price, $model, $remark, $obj=[])
    {
        $log = new self();
        $log->type = self::TYPE_ADD;
        $log->member_id = $memberid;
        $log->price = $price;
        $log->model = $model;
        $log->remark = $remark;
        $log->snapshot = $obj;
        $log->save();
    }

    public static function sub($memberid, $price, $model, $remark, $obj=[])
    {
        $log = new self();
        $log->type = self::TYPE_SUB;
        $log->member_id = $memberid;
        $log->price = $price;
        $log->model = $model;
        $log->remark = $remark;
        $log->snapshot = $obj;
        $log->save();
    }
}
