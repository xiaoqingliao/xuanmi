<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 文章
 * @attribute id|integer
 * @attribute title|string              标题
 * @attribute member_id|integer         发布会员id
 * @attribute type|integer           大栏目id
 * @attribute category_id|integer       分类id
 * @attribute cover|string              封面
 * @attribute video|string              视频
 * @attribute banners|string            滚动图片
 * @attribute content|array            内容
 * @attribute extensions|array          扩展属性
 * @attribute orderindex|integer        排序
 * @attribute status|integer            审核状态
 * @attribute base_clicks|integer       设置的基础点击量，造假用
 * @attribute clicks|integer            实际点击量
 * @attribute scores|integer            用户评分
 * @attribute score_count|integer       评分人数
 */
class Article extends Model
{
    use SoftDeletes;
    use ExtensionTrait;
    use BaseTrait;
    use ContentSaveTrait;

    protected $fillable = ['title', 'category_id', 'cover', 'video', 'banners', 'content', 'extensions'];
    protected $casts = [
        'type' => 'integer',
        'member_id' => 'integer',
        'category_id' => 'integer',
        'extensions' => 'array',
        'orderindex' => 'integer',
        'status' => 'integer',
        'content' => 'array',
        'clicks' => 'integer',
        'scores' => 'integer',
        'score_count' => 'integer',
        'base_clicks' => 'integer',
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }
    
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function toArrayShow()
    {
        $_category = null;
        if ($this->category != null) {
            $_category = [
                'id' => $this->category->id,
                'title' => $this->category->title,
            ];
        }
        $item = [
            'id' => $this->id,
            'title' => $this->title,
            'cover' => image_url($this->cover, null, null, true),
            'category' => $_category,
            'video' => media_url($this->video),
            'banners' => $this->bannerList,
            'summary' => $this->getExtensions('summary', ''),
            'content' => content_show($this->content),
            //'extensions' => $this->extensions,
            'views' => $this->views,
            'score' => $this->score,
            'created' => $this->created_at->timestamp,
        ];
        if ($this->type == Category::TYPE_PRODUCT) {
            $item['origin'] = $this->getExtensions('origin', '');
            $item['price'] = $this->getExtensions('price', '');
        }
        return $item;
    }
}
