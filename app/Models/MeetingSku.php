<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 会议规格项目
 * @attribute id|integer
 * @attribute meeting_id|integer        会议id
 * @attribute title|string              标题
 * @attribute price|real                价格
 * @attribute market_price|real         市场价格
 * @attribute buy_number|integer        购买量
 */
class MeetingSku extends Model
{
    use SoftDeletes;

    protected $fillable = [];
    protected $casts = [
        'meeting_id' => 'integer',
        'price' => 'real',
        'market_price' => 'real',
        'buy_number' => 'integer',
    ];
}
