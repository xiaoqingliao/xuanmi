<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @attribute id|integer
 * @attribute openid|string             微信openid
 * @attribute nickname|string           昵称
 * @attribute username|string           登录手机
 * @attribute name|string               姓名
 * @attribute avatar|string             头像本地文件
 * @attribute avatar_source|string      头像远程文件
 * @attribute gender|integer            性别 0:未知,1:男,2:女
 * @attribute group_id|integer          代理级别id
 * @attribute parent_id|integer         上级用户id
 * @attribute parent_path|string        上级用户id列表，按上下级顺序用逗号分隔开
 * @attribute company|string            公司名称
 * @attribute duty|string               职务
 * @attribute phone|string              手机
 * @attribute wechat|string             微信号
 * @attribute industry|string           行业id,逗号分隔开   10/10新增
 * @attribute summary|string            简介
 * @attribute userinfo|array            微信用户信息
 * @attribute extensions|array          其它扩展信息
 * @attribute logged|boolean            是否已读取微信用户信息
 * @attribute last_login|integer        最近登录时间
 * @attribute last_ip|string            最近登录ip
 * @attribute last_area|string          最近登录地区
 * @attribute last_buy|integer          最近购物时间
 * @attribute proxy_first_time|integer  成为代理时间
 * @attribute proxy_start_time|integer  代理时间
 * @attribute proxy_end_time|integer    代理时间
 * @attribute proxy_balance|real        代理账户余额
 * @attribute probation|boolean         试用代理
 * @attribute province|string           省      10/10 新增
 * @attribute city|string               市      10/10 新增
 * @attribute area|string               区      10/10 新增
 * @attribute address|string            地址      10/10 新增
 * @attribute school|string             学校    10/10 新增
 * @attribute nation_province|string    籍贯/省 10/10 新增
 * @attribute nation_city|string        籍贯/市 10/10 新增
 * @attribute nation_area|string        籍贯/区 10/10 新增
 * @attribute nation_address|string     籍贯/地域   10/10 新增
 * @attribute motto|string              个性签名    10/23 新增
 * @attribute renew_cash|real           续费分成金额    10/24 新增
 * @attribute renew_withdraw|real       续费提现金额    10/24 新增
 */
class Member extends Authenticatable implements JWTSubject
{
    use ExtensionTrait;
    protected $fillable = [];
    protected $casts = [
        'gender' => 'integer',
        'group_id' => 'integer',
        'parent_id' => 'integer',
        'userinfo' => 'array',
        'extensions' => 'array',
        'logged' => 'boolean',
        'last_login' => 'integer',
        'last_buy' => 'integer',
        'proxy_first_time' => 'integer',
        'proxy_start_time' => 'integer',
        'proxy_end_time' => 'integer',
        'proxy_balance' => 'real',
        'probation' => 'boolean',
        'renew_cash' => 'real',
        'renew_withdraw' => 'real',
    ];

    public function setRememberToken($token)
    {
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function group()
    {
        return $this->belongsTo(MemberGroup::class);
    }

    public function parent()
    {
        return $this->belongsTo(Member::class);
    }

    public function childs()
    {
        return $this->hasMany(Member::class, 'parent_id', 'id')->where('group_id', '>', 0);
    }

    public function tags()
    {
        return $this->hasMany(MemberTag::class);
    }

    public function getAvatarImageAttribute()
    {
        if ($this->avatar == '') {
            return asset('assets/front/img/avatar.jpg');
        } else if (starts_with($this->avatar, 'http')) {
            return $this->avatar;
        } else {
            return image_url($this->avatar, null, null, true);
        }
    }

    public function getIndustryListAttribute()
    {
        if (empty($this->industry)) return [];
        $ids = explode(',', $this->industry);
        $industries = Industry::whereIn('id', $ids)->orderBy('parent_id', 'asc')->orderBy('id', 'asc')->get();
        return $industries->map(function($industry){
            return [
                'id' => $industry->id,
                'title' => $industry->title,
            ];
        })->toArray();
    }

    /**
     * 会员是否到期
     */
    public function isExpired()
    {
        if ($this->group_id <= 0) return true;

        $time = $this->freshTimestamp()->timestamp;
        if ($time < $this->proxy_start_time || $time > $this->proxy_end_time) return true;

        return false;
    }

    /**
     * 续费收入剩余金额
     */
    public function getRenewMoneyAttribute()
    {
        $r = $this->renew_cash - $this->renew_withdraw;
        if ($r < 0) $r = 0;
        return $r;
    }

    /**
     * 余额增加
     */
    public function addCash($cash, $category, $remark='', $snap=[], $company=true)
    {
        if ($cash <= 0) return;
        \DB::beginTransaction();
        try {
            $this->proxy_balance += $cash;
            
            $log = new MemberAccountLog();
            $log->member_id = $this->id;
            $log->type = MemberAccountLog::TYPE_ADD;
            $log->category = $category;
            $log->cash = $cash;
            $log->remark = $remark;
            $log->snapshot = $snap;
            $log->save();

            if ($company) {
                CompanyFinanceLog::sub($this->id, $cash, 'MemberAccountLog', $remark, empty($snap) ? $log : $snap);
            }

            $this->save();
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw new \Exception($e);
        }
    }

    /**
     * 余额减少
     */
    public function subCash($cash, $category, $remark='', $snap=[], $company=true)
    {
        if ($cash <= 0) return;
        if ($this->proxy_balance < $cash) {
            throw new \Exception('会员:' . $this->id . '账户余额不足');
        }

        \DB::beginTransaction();
        try {
            $this->proxy_balance -= $cash;
            
            $log = new MemberAccountLog();
            $log->member_id = $this->id;
            $log->type = MemberAccountLog::TYPE_SUB;
            $log->category = $category;
            $log->cash = $cash;
            $log->remark = $remark;
            $log->snapshot = $snap;
            $log->save();

            if ($company) {
                CompanyFinanceLog::add($this->id, $cash, 'MemberAccountLog', $remark, empty($snap) ? $log : $snap);
            }

            $this->save();
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw new \Exception($e);
        }
    }

    /**
     * 续费分润
     */
    public function addRenew($cash, $remark='', $snap=[])
    {
        if ($cash <= 0) return;
        \DB::beginTransaction();
        try {
            $this->renew_cash += $cash;
            $this->save();

            $log = new MemberRenewLog();
            $log->member_id = $this->id;
            $log->type = MemberRenewLog::TYPE_ADD;
            $log->money = $cash;
            $log->userid = 0;
            $log->remark = $remark;
            $log->snapshot = $snap;
            $log->save();

            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();
            throw new \Exception($e);
        }
    }
    
    /**
     * 续费分成提现
     */
    public function subRenew($cash, $userid, $remark='')
    {
        if ($cash <= 0) return;
        $diff_cash = $this->renew_cash - $this->renew_withdraw;
        if ($cash > $diff_cash) {
            throw new \Exception('会员:' . $this->id . '续费分成余额不足提现金额');
        }
        
        \DB::beginTransaction();
        try {
            $this->renew_withdraw += $cash;
            $this->save();

            $log = new MemberRenewLog();
            $log->member_id = $this->id;
            $log->type = MemberRenewLog::TYPE_SUB;
            $log->money = $cash;
            $log->userid = $userid;
            $log->remark = $remark;
            $log->snapshot = [];
            $log->save();
            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();
            throw new \Exception($e);
        }
    }

    /**
     * 搜索权重增加
     */
    public function searchWeightIncrement($inc=1)
    {
        $this->search_weight += $inc;
        $this->save();
    }
}
