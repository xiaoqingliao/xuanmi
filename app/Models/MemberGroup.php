<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 会员级别
 * @attribute id|integer
 * @attribute title|string          级别名称
 * @attribute code|string           级别代码，唯一
 * @attribute params|array          级别奖励设置
 * @attribute orderindex|integer    排序
 * @attribute same|boolean          是否可发展同级用户
 * @attribute icon|string           级别图标
 * @attribute price|string          价格
 * @attribute money|real            计算用价格
 * @attribute copyright|string      权益
 * @attribute description|string    条款说明
 * @attribute contract|string       合同图片
 */
class MemberGroup extends Model
{
    const NORMAL_GROUP = 1;
    const PERSONAL_GROUP = 2;
    const AREA_GROUP = 3;
    const CITY_GROUP = 4;
    const PROVINCE_GROUP = 5;
    
    protected $fillable = ['title', 'code', 'params', 'icon', 'contract', 'price', 'money', 'copyright', 'description'];
    protected $casts = [
        'params' => 'array',
        'orderindex' => 'integer',
        'same' => 'boolean',
        'money' => 'real',
    ];

    public function getParams($key, $default='')
    {
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }
}
