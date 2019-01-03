<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable;

    public function __construct()
    {
        $this->extensions = [];
        $this->permissions = [];
        parent::__construct();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'extensions', 'permissions',
    ];

    protected $casts = [
        'disabled' => 'boolean',
        'extensions' => 'array',
        'permissions' => 'array',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getExtension($key, $default='')
    {
        return isset($this->extensions[$key]) ? $this->extensions[$key] : $default;
    }
    
    public function getPermissionListAttribute()
    {
        $permissions = $this->permissions ?: [];
        /*if ($this->role != null) {
            $permissions = array_merge($this->role->permissions, $permissions);
        }*/
        
        return $permissions;
    }

    public function has($key)
    {
        if ($this->id == 1) return true;
        
        foreach($this->permissions as $_key) {
            if (starts_with($_key, $key)) return true;
        }
        
        return false;
    }

    public function permission($key)
    {
        //return true;
        //if ($this->id == 1) return true;
        if ($this->username == 'root' || $this->username == 'admin') return true;
        
        return in_array($key, $this->permissions);
    }
}
