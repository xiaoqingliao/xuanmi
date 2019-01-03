<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 课程
 * @attribute id|integer
 * @attribute member_id|integer             发布会员id
 * @attribute title|string                  标题
 * @attribute category_id|integer           类型id
 * @attribute cover|string                  封面
 * @attribute banners|string                滚动banner图
 * @attribute video|string                  视频
 * @attribute content|array                 内容
 * @attribute price|real                    价格
 * @attribute market_price|real             市场价格
 * @attribute discount_start_time|integer   优惠开始时间
 * @attribute discount_end_time|integer     优惠结束时间
 * @attribute orderindex|integer            排序
 * @attribute status|integer                审核状态
 * @attribute extensions|array              扩展属性
 * @attribute times|integer                 时长，分钟
 * @attribute scores|integer                评分
 * @attribute score_count|integer           评分人数
 * @attribute buy_number|integer            购买过的人
 * @attribute base_clicks|integer           购置的基础点击量，造假用
 * @attribute clicks|integer                点击量
 * @attribute relate_courses|string         关联课程
 */
class Course extends Model
{
    use SoftDeletes;
    use ExtensionTrait;
    use BaseTrait;
    use ContentSaveTrait;
    
    protected $fillable = ['title', 'category_id', 'cover', 'banners', 'video', 'content', 'price',
        'market_price', 'extensions', 'times', 'score', 'buy_number', 'clicks'];
    protected $casts = [
        'member_id' => 'integer',
        'category_id' => 'integer',
        'price' => 'real',
        'discount_start_time' => 'integer',
        'discount_end_time' => 'integer',
        'market_price' => 'real',
        'orderindex' => 'integer',
        'status' => 'integer',
        'extensions' => 'array',
        'times' => 'integer',
        'scores' => 'integer',
        'score_count' => 'integer',
        'buy_number' => 'integer',
        'clicks' => 'integer',
        'content' => 'array',
    ];

    public function toArrayShow()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'cover' => image_url($this->cover, null, null, true),
            'banners' => $this->bannerList,
            'video' => media_url($this->video),
            'content' => content_show($this->content),
            'price' => $this->price,
            'market_price' => $this->market_price,
            'start_time' => $this->discount_start_time,
            'end_time' => $this->discount_end_time,
            'onsale' => $this->onsale,
            'times' => $this->times,
            'score' => $this->score,
            'buy_number' => $this->buy_number,
            'views' => $this->views,
            //'extensions' => $this->extensions,
        ];
    }
    
    public function getOnsaleAttribute()
    {
        $now = $this->freshTimestamp()->timestamp;

        if ($now >= $this->discount_start_time && $now <= $this->discount_end_time) {
            return true;
        }
        return false;
    }
}
