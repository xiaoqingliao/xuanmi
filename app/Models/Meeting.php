<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 会议
 * @attribute id|integer
 * @attribute member_id|integer             发布会员id
 * @attribute title|string                  标题
 * @attribute category_id|integer           类型id
 * @attribute cover|string                  封面图
 * @attribute banners|string                滚动图
 * @attribute video|string                  视频
 * @attribute content|array                 内容
 * @attribute price|real                    价格
 * @attribute start_time|datetime           会议时间
 * @attribute end_time|datetime             会议时间
 * @attribute province|string               省
 * @attribute city|string                   市
 * @attribute area|string                   区
 * @attribute address|string                会议地址
 * @attribute lat|real                      GPS坐标
 * @attribute lng|real                      GPS坐标
 * @attribute orderindex|integer            排序
 * @attribute status|integer                审核状态
 * @attribute extensions|array              扩展属性
 * @attribute base_clicks|integer           设置的基础点击量，造假用
 * @attribute clicks|integer                点击量
 * @attribute buy_number|integer            购买量
 * @attribute scores|integer                评分
 * @attribute score_count|integer           评分人数
 * @attribute sponor_ids|string             赞助商id列表
 */
class Meeting extends Model
{
    use SoftDeletes;
    use ExtensionTrait;
    use BaseTrait;
    use ContentSaveTrait;

    protected $fillable = ['title', 'category_id', 'cover', 'video', 'banners', 'content', 'price', 'start_time',
        'province', 'city', 'area', 'address', 'lat', 'lng', 'extensions'];
        
    protected $casts = [
        'member_id' => 'integer',
        'category_id' => 'integer',
        'price' => 'real',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'lat' => 'real',
        'lng' => 'real',
        'orderindex' => 'integer',
        'content' => 'array',
        'status' => 'integer',
        'extensions' => 'array',
        'clicks' => 'integer',
        'buy_number' => 'integer',
        'scores' => 'integer',
        'score_count' => 'integer',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    /**
     * 规格项目
     */
    public function skus()
    {
        return $this->hasMany(MeetingSku::class);
    }

    public function toArrayShow()
    {
        $_gps = null;
        if (!empty($this->lat) && !empty($this->lng)) {
            $_gps = [
                'lat' => $this->lat,
                'lng' => $this->lng,
            ];
        }
        $skus = $this->skus->map(function($sku){
            return [
                'id' => $sku->id,
                'title' => $sku->title,
                'price' => $sku->price,
            ];
        })->toArray();
        
        $sponors = [];
        $sponors_ids = explode(',', $this->sponor_ids);
        if (count($sponors_ids) > 0) {
            $sponors = Member::whereIn('id', $sponors_ids)->with('group')->where('group_id', '>', 0)->get();
            $sponors = $sponors->map(function($m){
                return [
                    'id' => $m->id,
                    'nickname' => $m->nickname,
                    'name' => $m->name,
                    'phone' => $m->phone,
                    'company' => $m->company,
                    'avatar' => image_url($m->avatar, null, null, true),
                    'group' => [
                        'id' => $m->group->id,
                        'title' => $m->group->title,
                    ],
                ];
            })->toArray();
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'cover' => image_url($this->cover, null, null, true),
            'banners' => $this->bannerList,
            'category' => [
                'id' => $this->category->id,
                'title' => $this->category->title,
            ],
            'start_time' => $this->start_time->timestamp,
            'end_time' => $this->end_time->timestamp,
            'price' => $this->price,
            'province' => $this->province,
            'city' => $this->city,
            'area' => $this->area,
            'address' => $this->address,
            'gps' => $_gps,
            'views' => $this->views,
            'buy_number' => $this->buy_number,
            'score' => $this->score,
            'content' => content_show($this->content),
            'skus' => $skus,
            'sponors' => $sponors,
        ];
    }
}
