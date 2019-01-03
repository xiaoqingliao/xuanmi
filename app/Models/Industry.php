<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 行业分类
 * @attribute id|integer
 * @attribute parent_id|integer     上级分类id
 * @attribute title|string          行业名称
 * @attribute orderindex|integer    排序
 * @attribute hide|boolean          是否隐藏显示
 */
class Industry extends Model
{
    protected $fillable = ['parent_id', 'title'];
    protected $casts = [
        'parent_id' => 'integer',
        'orderindex' => 'integer',
        'hide' => 'boolean',
    ];
    
    public function parent()
    {
        return $this->belongsTo(Industry::class, 'parent_id', 'id');
    }
}
