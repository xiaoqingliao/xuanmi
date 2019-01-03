<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 前台用户登录日志
 * @attribute id|integer
 * @attribute member_id|integer
 * @attribute ip|string
 * @attribute areas|string
 */
class MemberLoginRecord extends Model
{
    protected $fillable = ['member_id', 'ip', 'areas'];
    protected $casts = [
        'member_id' => 'integer',
    ];
}
