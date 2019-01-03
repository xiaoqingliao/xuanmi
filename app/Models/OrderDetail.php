<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 订单详情
 * @attribute id|integer
 * @attribute order_id|integer      订单id
 * @attribute member_id|integer     下单人id
 * @attribute model_type|string     类型
 * @attribute model_id|integer      类型id
 * @attribute sku_id|integer        sku id
 * @attribute price|real            下单价格
 * @attribute number|integer        数量
 * @attribute snapshot|array        快照
 */
class OrderDetail extends Model
{
    protected $fillable = [];
    protected $casts = [
        'order_id' => 'integer',
        'member_id' => 'integer',
        'model_id' => 'integer',
        'sku_id' => 'integer',
        'price' => 'real',
        'number' => 'integer',
        'snapshot' => 'array'
    ];

    public function model()
    {
        return $this->morphTo();
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function sku()
    {
        return $this->belongsTo(MeetingSku::class, 'sku_id', 'id');
    }

    /**
     * 获取商品商家id
     */
    public function getMerchantIdAttribute()
    {
        if (isset($this->snapshot['course'])) {
            $course = json_decode($this->snapshot['course'], true);
            return $course['member_id'];
        } else if (isset($this->snapshot['meeting'])){
            $meeting = json_decode($this->snapshot['meeting'], true);
            return $meeting['member_id'];
        }
        return 0;
    }
}
