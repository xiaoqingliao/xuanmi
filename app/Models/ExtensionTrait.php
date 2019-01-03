<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/

namespace App\Models;

/**
 * 扩展属性操作方法
 */
trait ExtensionTrait
{
    public function getExtensions($key, $default='')
    {
        return isset($this->extensions[$key]) ? $this->extensions[$key] : $default;
    }
    
    public function setExtensions($key, $value)
    {
        $extensions = $this->extensions;
        if (!is_array($extensions)) $extensions = [];
        $extensions[$key] = $value;
        $this->extensions = $extensions;
    }
}