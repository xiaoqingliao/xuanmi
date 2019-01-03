<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * banner图
 * @attribute id|integer
 * @attribute member_id|integer         发布会员id
 * @attribute model_type|string         对应模型类型
 * @attribute model_id|integer          对应模型id
 * @attribute title|string              标题
 * @attribute cover|string              图片地址
 * @attribute video|string              视频地址
 * @attribute redirect|string           链接地址
 * @attribute orderindex|integer        排序
 */
class Banner extends Model
{
    const TYPE_COMPANY = 'company';

    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'model_id' => 'integer',
    ];
}
