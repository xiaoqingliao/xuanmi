<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 系统参数设置
 * @attribute id|integer
 * @attribute title|string       标题
 * @attribute code|string        唯一代码
 * @attribute params|object      参数
 */
class SiteConfig extends Model
{
    protected $casts = [
        'params' => 'array',
    ];
    
    public function getParam($key, $default='')
    {
        return isset($this->params[$key]) && $this->params[$key] !== '' ? trim($this->params[$key]) : $default;
    }

    public static function getParams($code)
    {
        $item = self::where('code', $code)->first();
        if ($item != null) {
            return $item->params;
        }
        
        return [];
    }
    
    public static function setParams($code, $title, $params=[])
    {
        $item = self::where('code', $code)->first();
        if ($item == null) {
            $item = new self();
            $item->code = $code;
            $item->title = $title;
            $item->params = $params;
            $item->save();
        } else {
            $item->params = $params;
            $item->save();
        }
    }
}
