<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 审核记录
 * @attribute id|integer
 * @attribute user_id|integer           审核用户id
 * @attribute member_id|integer         上传用户id
 * @attribute model_type|string         model类型
 * @attribute model_id|integer          model id
 * @attribute prev_status|integer       之前状态
 * @attribute new_status|integer        新状态
 * @attribute remark|string             备注信息
 */
class AuditLog extends Model
{
    protected $fillable = [];
    protected $casts = [
        'user_id' => 'integer',
        'member_id' => 'integer',
        'model_id' => 'integer',
        'prev_status' => 'integer',
        'new_status' => 'integer',
    ];
}
