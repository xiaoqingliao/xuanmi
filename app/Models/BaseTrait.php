<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/

namespace App\Models;

/**
 * 相同基础属性操作方法属性
 */
trait BaseTrait
{

    /**
     * 滚动banner图
     */
    public function getBannerListAttribute()
    {
        if ($this->banners == '') {
            return [image_url($this->cover, null, null, true)];
        }

        $new_banners = [];
        $banners = explode(',', $this->banners);
        foreach($banners as $_banner) {
            $new_banners[] = image_url($_banner, null, null, true);
        }
        return $new_banners;
    }

    /**
     * 审核状态文字
     */
    public function getStatusTextAttribute()
    {
        return AppConstants::getStatusText($this->status);
    }

    /**
     * 点击量
     */
    public function getViewsAttribute()
    {
        return $this->base_clicks + $this->clicks;
    }

    /**
     * 计算得分
     */
    public function getScoreAttribute()
    {
        if ($this->score_count <= 0) return 0;
        return round($this->scores / $this->score_count);
    }
}