<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 验证码
 * @attribute id|integer
 * @attribute member_id|integer
 * @attribute phone|string
 * @attribute code|string
 * @attribute used_time|integer
 */
class PhoneCode extends Model
{
    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'used_time' => 'integer',
    ];

    public function randCode($len=6)
    {
        $str = '0123456789';
        $r = '';
        for($i=0; $i<$len; $i++) {
            $r .= $str[rand(0,9)];
        }
        return $r;
    }
}
