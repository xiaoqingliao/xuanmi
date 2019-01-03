<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 购物车
 * @attribute id|integer
 * @attribute member_id|integer
 * @attribute model_type|string
 * @attribute model_id|integer
 * @attribute sku_id|integer
 * @attribute number|integer
 */
class Cart extends Model
{
    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'model_id' => 'integer',
        'sku_id' => 'integer',
        'number' => 'integer',
    ];

    public function model()
    {
        return $this->morphTo();
    }

    public function sku()
    {
        return $this->belongsTo(MeetingSku::class, 'sku_id', 'id');
    }
}
