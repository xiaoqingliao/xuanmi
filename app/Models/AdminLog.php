<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
* 管理员日志
* @attribute id|integer
* @attribute adminid|integer         管理员id
* @attribute operation|string        操作     bind=绑定微信,unbind=解除微信绑定,notice=打开微信通知,unnotice=关闭微信通知,login=登录成功,logout=用户退出,password=修改密码,insert=添加,update=更新,delete=删除,list=查看,order=更新排序,show=查看详情,default=设置默认,disable=禁用,restore=启用
* @attribute summary|string          操作数据表名
* @attribute content|array           操作数据
* @attribute created_at|timestamp    操作时间
* @attribute desc|string             描述
* @attribute ip|string               登录ip
* @attribute areas|string            登录地区
*/
class AdminLog extends Model
{
    protected $casts = [
        'content' => 'array',
    ];

    private $operations = [
        'bind' => '绑定微信',
        'unbind' => '解除微信绑定',
        'notice' => '打开微信通知',
        'unnotice' => '关闭微信通知',
        'login' => '用户登录成功',
        'logout' => '用户退出',
        'password' => '修改登录密码',
        'insert' => '添加数据',
        'update' => '更新数据',
        'delete' => '删除数据',
        'list' => '列表查看',
        'order' => '更新排序',
        'show' => '查看详情',
        'default' => '设置默认',
        'disable' => '禁用',
        'restore' => '启用',
    ];
    
    private $columns = [
        'admins' => '用户管理',
    ];

    public function getOperationTextAttribute()
    {
        return isset($this->operations[$this->operation]) ? $this->operations[$this->operation] : $this->operation;
    }

    public function getColumnTextAttribute()
    {
        return isset($this->columns[$this->summary]) ? $this->columns[$this->summary] : $this->summary;
    }

    public static function addLog($adminid, $operation, $table, $model=null, $desc='')
    {
        $log = new self();
        $log->adminid = $adminid;
        $log->operation = $operation;
        $log->summary = $table;
        if ($model instanceof Model) {
            $log->content = $model->getAttributes();
        } else if (is_array($model)) {
            $log->content = $model;
        } else {
            $log->content = [];
        }
        $log->created_at = $log->freshTimestamp();
        $log->desc = $desc;
        $log->ip = session('sysip', '');
        $log->areas = session('sysareas', '');
        $log->save();
    }
}
