<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Repo;

use App\Models\AppConstants;
use App\Models\Member;
use App\Models\Order;
use App\Models\MemberGroup;
use App\Models\MemberAccountLog;
use App\Models\MemberProxyApply;

class DivideRepo
{
    private $notice = null;
    public function __construct()
    {
        $this->notice = new NoticeRepo();
    }
    
    /**
     * 新用户注册分成
     */
    public function regDivide($order_or_id)
    {
        if (is_numeric($order_or_id)) {
            $order = Order::find($order_or_id);
        } else {
            $order = $order_or_id;
        }

        if ($order == null || $order->type != Order::TYPE_REG) {    //非注册订单
            return;
        }

        if (!empty($order->rebate)) {   //已分过
            return;
        }
        
        $member = $order->member;
        if ($member->parent_id <= 0) {  //无上线用户
            return;
        }
        
        \DB::beginTransaction();
        try {
            $parent = null;     //直推人
            $grandpa = null;    //直推首层代理
            $grandpapa = null;  //直推次层代理
            $parent_ids = array_reverse(explode(',', $member->parent_path));
            if (count($parent_ids) > 0) {
                $parent_list = Member::whereIn('id', $parent_ids)->with('group')->get();
                $parent_list1 = [];
                foreach($parent_list as $_parent) {
                    $parent_list1[$_parent->id] = $_parent;
                }
                $parents = [];
                foreach($parent_ids as $_id) {
                    if (isset($parent_list1[$_id])) {
                        $parents[] = $parent_list1[$_id];
                    }
                }

                foreach($parents as $idx=>$_member) {
                    if ($idx == 0) {
                        $parent = $_member;
                    } else if ($_member->group->id > 1) {
                        if ($grandpa == null) {
                            $grandpa = $_member;
                            if ($parent->group->id > 1) {  //直推人是代理
                                $grandpapa = $grandpa;
                                $grandpa = null;
                                break;
                            }
                            continue;
                        } 
                        if ($grandpa != null && $grandpapa == null) {
                            $grandpapa = $_member;
                            break;
                        }
                    }
                }
            }
            
            $rebate_log = [];
            $assign_percent = 0;
            if ($parent != null) {  //上线
                $number = $parent->group->getParams($member->group->code . '_number', 0);
                $rebatea = $parent->group->getParams($member->group->code . '_rebatea', 0);
                $rebateb = $parent->group->getParams($member->group->code . '_rebateb', 0);

                if ($number > 0) {
                    $child_number = Member::where('parent_id', $parent->id)->count();
                    if ($child_number > $number) {  //按rebateb分成
                        $money = round($order->price * $rebateb / 100, 2);
                        $rebate_log[] = [
                            'member' => [
                                'id' => $parent->id,
                                'nickname' => $parent->nickname,
                                'name' => $parent->name,
                            ],
                            'number' => $child_number,
                            'rebateb' => $rebateb,
                            'money' => $money,
                        ];
                        if ($money > 0) {
                            $msg = str_replace(['{name}', '{rebate}'], [$member->nickname, $rebateb], config('noticeparam.child_new2', '新用户注册'));
                            $parent->addCash($money, MemberAccountLog::CATEGORY_NEW, $msg);
                            $this->notice->divide($parent, $money, $msg);
                        }
                        $assign_percent = $rebateb;
                    } else {    //按rebatea分成
                        $money = round($order->price * $rebatea / 100, 2);
                        $rebate_log[] = [
                            'member' => [
                                'id' => $parent->id,
                                'nickname' => $parent->nickname,
                                'name' => $parent->name,
                            ],
                            'rebatea1' => $rebatea,
                            'money' => $money,
                        ];
                        if ($money > 0) {
                            $msg = str_replace(['{name}', '{rebate}'], [$member->nickname, $rebatea], config('noticeparam.child_new1', '新用户注册'));
                            $parent->addCash($money, MemberAccountLog::CATEGORY_NEW, $msg);
                            $this->notice->divide($parent, $money, $msg);
                        }
                        $assign_percent = $rebatea;
                    }
                } else {    //按rebateb分成
                    $money = round($order->price * $rebateb / 100, 2);
                    $rebate_log[] = [
                        'member' => [
                            'id' => $parent->id,
                            'nickname' => $parent->nickname,
                            'name' => $parent->name,
                        ],
                        'rebateb' => $rebateb,
                        'money' => $money,
                    ];
                    if ($money > 0) {
                        $msg = str_replace(['{name}', '{rebate}'], [$member->nickname, $rebateb], config('noticeparam.child_new2', '新用户注册'));
                        $parent->addCash($money, MemberAccountLog::CATEGORY_NEW, $msg);
                        $this->notice->divide($parent, $money, $msg);
                    }
                    $assign_percent = $rebateb;
                }
            }

            if ($grandpa != null && $assign_percent < 100) {
                $rebate = $grandpa->group->getParams('proxy_reg_award1', 0);
                if (($rebate + $assign_percent) > 100) {
                    $rebate = 100 - $assign_percent;
                }
                $money = round($rebate * $order->price / 100, 2);
                $rebate_log[] = [
                    'member' => [
                        'id' => $grandpa->id,
                        'nickname' => $grandpa->nickname,
                        'name' => $grandpa->name,
                    ],
                    'rebate' => $rebate,
                    'money' => $money,
                ];
                if ($money > 0) {
                    $msg = str_replace(['{name}', '{rebate}'], [$member->nickname, $rebate], config('noticeparam.child_new_proxy1', '新用户注册'));
                    $grandpa->addCash($money, MemberAccountLog::CATEGORY_CHILD_NEW, $msg);
                    $this->notice->divide($grandpa, $money, $msg);
                }
                $assign_percent += $rebate;
            }
            
            if ($grandpapa != null && $assign_percent < 100) {
                $rebate = $grandpapa->group->getParams('proxy_reg_award2', 0);
                if (($rebate + $assign_percent) > 100) {
                    $rebate = 100 - $assign_percent;
                }
                $money = round($rebate * $order->price / 100, 2);
                $rebate_log[] = [
                    'member' => [
                        'id' => $grandpapa->id,
                        'nickname' => $grandpapa->nickname,
                        'name' => $grandpapa->name,
                    ],
                    'rebate' => $rebate,
                    'money' => $money,
                ];
                if ($money > 0) {
                    $msg = str_replace(['{name}', '{rebate}'], [$member->nickname, $rebate], config('noticeparam.child_new_proxy2', '新用户注册'));
                    $grandpapa->addCash($money, MemberAccountLog::CATEGORY_CHILD_NEW, $msg);
                    $this->notice->divide($grandpapa, $money, $msg);
                }
                $assign_percent += $rebate;
            }

            $order->rebate = $rebate_log;
            $order->save();
            
            \DB::commit();
        } catch(\Exception $e) {
            \Log::info('注册分成失败');
            \Log::info($e);
            \DB::rollBack();
        }
    }

    public function renewDivide($order_or_id)
    {
        if (is_numeric($order_or_id)) {
            $order = Order::find($order_or_id);
        } else {
            $order = $order_or_id;
        }

        if ($order == null || $order->type != Order::TYPE_RENEW) {    //非续费订单
            return;
        }

        if (!empty($order->rebate)) {   //已分过
            return;
        }

        $member = $order->member;
        if ($member->parent_id <= 0) {  //无上线用户
            return;
        }

        \DB::beginTransaction();
        try {
            $parent_ids = array_reverse(explode(',', $member->parent_path));
            if (empty($parent_ids)) {
                \DB::rollBack();
                return;
            }
            $parent_list = Member::whereIn('id', $parent_ids)->with('group')->get();
            $parent_list1 = [];
            foreach($parent_list as $_parent) {
                if ($_parent->group->getParams('proxy_fee_renew') > 0) {
                    $parent_list1[$_parent->id] = $_parent;
                }
            }
            $parents = [];
            foreach($parent_ids as $_id) {
                if (isset($parent_list1[$_id])) {
                    $parents[] = $parent_list1[$_id];
                }
            }
            unset($parent_list);
            unset($parent_list1);

            $rebate_log = [];
            foreach($parents as $idx=>$parent) {
                if ($parent->group != null) {
                    $cash = $parent->group->getParams('proxy_fee_renew', 0);
                    $level = $parent->group->getParams('proxy_fee_level', 0);
                    \Log::info($parent->id . ' cash:' . $cash . ' level:' . $level . ' idx:' . ($idx + 1));
                    if ($level >= ($idx + 1) && $cash > 0) {
                        $msg = str_replace(['{name}', '{money}'], [$member->nickname, $cash], config('noticeparam.child_renew', '用户{name}续费'));
                        //续费增加到会员表专门的续费金额字段
                        //$parent->addCash($cash, MemberAccountLog::CATEGORY_CHILD_RENEW, $msg);
                        //$this->notice->divide($parent, $cash, $msg);
                        $parent->addRenew($cash, $msg, ['member'=>$parent->toArray(), 'order'=>$order->toArray(), 'cash'=>$cash, 'level'=>$level]);
                        $rebate_log[] = [
                            'member' => [
                                'id' => $parent->id,
                                'nickname' => $parent->nickname,
                                'name' => $parent->name,
                            ],
                            'money' => $cash,
                        ];
                    }
                }
            }
            $order->rebate = $rebate_log;
            $order->save();
            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();
            \Log::info('续费分成失败');
            \Log::info($e);
        }
    }

    public function upgradeDivide($apply)
    {
        $member = Member::where('id', $apply->member_id)->with('group')->first();
        if ($member == null) return;
        $parent = Member::where('id', $member->parent_id)->with('group')->first();
        if ($parent == null) return;
        
        $rebates = [];
        $number = $parent->group->getParams($member->group->code . '_number', 0);
        $rebatea = $parent->group->getParams($member->group->code . '_rebatea', 0);
        $rebateb = $parent->group->getParams($member->group->code . '_rebateb', 0);
        
        $count = MemberProxyApply::whereHas('member', function($q)use($parent){
            return $q->where('parent_id', $parent->id);
        })->where('group_id', $member->group_id)->where('status', AppConstants::ACCEPTED)->count();

        if ($count > $number) { //b方案分成
            $money = round($apply->money * $rebateb / 100, 2);
            $rebates = [
                'member' => [
                    'id' => $parent->id,
                    'name' => $parent->name,
                    'nickname' => $parent->nickname,
                ],
                'money' => $money,
                'rebate' => $rebateb
            ];
            $msg = str_replace(['{name}', '{group}', '{rebate}'], [$member->name, $member->group->title, $rebateb], config('noticeparam.child_proxy_upgrade1', '下级用户{name}升级为{group}'));
        } else {    //a方案分成
            $money = round($apply->money * $rebatea / 100, 2);
            $rebates = [
                'member' => [
                    'id' => $parent->id,
                    'name' => $parent->name,
                    'nickname' => $parent->nickname,
                ],
                'money' => $money,
                'rebate' => $rebatea
            ];
            $msg = str_replace(['{name}', '{group}', '{rebate}'], [$member->name, $member->group->title, $rebatea], config('noticeparam.child_proxy_upgrade2', '下级用户{name}升级为{group}'));
        }
        
        if ($money > 0) {
            $parent->addCash($money, MemberAccountLog::CATEGORY_GROUP_UPDATE, $msg);
            $this->notice->divide($parent, $money, $msg);
        }
        $apply->rebate = $rebates;
        $apply->save();
    }
}