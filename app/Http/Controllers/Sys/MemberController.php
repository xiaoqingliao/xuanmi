<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Sys;

use App\Models\MemberGroup;
use App\Models\Member;
use App\Models\AdminLog;
use App\Models\MemberAccountLog;
use App\Models\MemberUpgradeLog;
use App\Models\MemberRenewLog;
use App\Models\Order;

/**
 * 会员管理
 */
class MemberController extends BaseController
{
    /**
     * 普通用户
     */
    public function normal()
    {
        $page = intval(request('page', 1));
        $pagesize = 20;

        $filters = [
            'nickname' => request('nickname'),
            'logged' => intval(request('logged')),
            'order' => request('order'),
        ];
        $cursor = Member::where('group_id', 0);
        if ($filters['nickname'] != '') {
            $cursor->where('nickname', 'like', '%'. $filters['nickname'] .'%');
        }
        
        switch($filters['order']) {
            case 'login':
            $cursor->orderBy('last_login', 'desc')->orderBy('id', 'desc');
            break;
            case 'buy':
            $cursor->orderBy('last_buy', 'desc')->orderBy('id', 'desc');
            break;
            case 'reg_asc':
            $cursor->orderBy('id', 'asc');
            break;
            default:
            $cursor->orderBy('id', 'desc');
            break;
        }
        $members = $cursor->with('parent')->paginate($pagesize);

        $values = [
            'start' => ($page - 1) * $pagesize,
            'filters' => $filters,
            'members' => $members,
        ];

        $this->setCurrentMenu('member:list');
        return view('member.normal', $values);
    }
    
    /**
     * 代理用户
     */
    public function proxy()
    {
        $page = intval(request('page', 1));
        $pagesize = 20;

        $filters = [
            'search_field' => request('search_field'),
            'parentid' => intval(request('parentid')),
            'start_date' => request('start_date'),
            'end_date' => request('end_date'),
            'key' => request('key'),
            'groupid' => intval(request('groupid')),
            'probation' => intval(request('probation')),
            'order' => request('order'),
            'export' => intval(request('export')),
        ];
        $cursor = Member::where('group_id', '>', 0)->where('probation', $filters['probation']);
        if ($filters['groupid'] > 0) {
            $cursor->where('group_id', $filters['groupid']);
        }
        $parent = null;
        if ($filters['parentid'] > 0) {
            $cursor->where('parent_id', $filters['parentid']);
            $parent = Member::find($filters['parentid']);
        }
        if ($filters['start_date'] != '') {
            $cursor->where('created_at', '>=', $filters['start_date']);
        }
        if ($filters['end_date'] != '') {
            $cursor->where('created_at', '<=', $filters['end_date']);
        }
        if ($filters['key'] != '' && $filters['search_field'] != '' && in_array($filters['search_field'], ['nickname', 'name', 'company', 'phone', 'wechat'])) {
            $cursor->where($filters['search_field'], 'like', '%'. $filters['key'] .'%');
        }
        switch($filters['order']) {
            case 'login':
            $cursor->orderBy('last_login', 'desc')->orderBy('id', 'desc');
            break;
            case 'buy':
            $cursor->orderBy('last_buy', 'desc')->orderBy('id', 'desc');
            break;
            case 'reg_asc':
            $cursor->orderBy('id', 'asc');
            break;
            case 'balance_asc':
            $cursor->orderBy('proxy_balance', 'asc')->orderBy('id', 'desc');
            break;
            case 'balance_desc':
            $cursor->orderBy('proxy_balance', 'desc')->orderBy('id', 'desc');
            break;
            case 'proxy_start_time_asc':
            $cursor->orderBy('proxy_start_time', 'asc')->orderby('id', 'desc');
            break;
            case 'proxy_start_time_desc':
            $cursor->orderBy('proxy_start_time', 'desc')->orderBy('id', 'desc');
            break;
            case 'proxy_end_time_asc':
            $cursor->orderBy('proxy_end_time', 'asc')->orderBy('id', 'desc');
            break;
            case 'proxy_end_time_desc':
            $cursor->orderBy('proxy_end_time', 'desc')->orderBy('id', 'desc');
            break;
            default:
            $cursor->orderBy('id', 'desc');
            break;
        }
        if ($filters['export']) {
            return $this->_export_excel($cursor->with('group', 'parent')->get());
        }
        $total_balance = $cursor->sum('proxy_balance');
        $total_cash = $cursor->sum('renew_cash');
        $total_withdraw = $cursor->sum('renew_withdraw');
        $members = $cursor->with('group', 'parent')->paginate($pagesize);

        $values = [
            'start' => ($page - 1) * $pagesize,
            'filters' => $filters,
            'fields' => [
                'nickname' => '昵称',
                'name' => '姓名',
                'company' => '公司',
                'phone' => '手机',
            ],
            'total' => [
                'balance' => $total_balance,
                'renew' => $total_cash - $total_withdraw,
            ],
            'members' => $members,
            'parent' => $parent,
            'groups' => MemberGroup::query()->orderBy('id', 'asc')->get(),
        ];

        $this->setCurrentMenu('member:proxy');
        return view('member.proxy', $values);
    }

    public function search()
    {
        $data = request()->all();
        unset($data['_token']);
        unset($data['_method']);

        return redirect()->route('sys.member.proxy', $data);
    }

    private function _export_excel($members)
    {
        \Excel::create('代理会员列表', function($excel)use($members){
            $excel->sheet('sheet', function($sheet)use($members){
                $sheet->setWidth([
                    'A' => 10,
                    'B' => 30,
                    'C' => 25,
                    'D' => 30,
                    'E' => 25,
                    'F' => 60,
                    'G' => 25,
                    'H' => 25,
                    'I' => 30,
                    'J' => 25,
                    'K' => 25,
                    'L' => 50,
                    'M' => 30,
                    'N' => 30,
                ]);
                $sheet->row(1, ['#', '昵称', '姓名', '上级会员', '手机', '公司', '职务', '微信', '代理级别', '代理账户余额', '续费分成余额', '代理时间', '注册时间', '最近登陆时间']);
                foreach($members as $idx=>$member) {
                    $sheet->appendRow([
                        $idx + 1,
                        $member->nickname,
                        $member->name,
                        $member->parent != null ? $member->parent->nickname : '',
                        $member->phone,
                        $member->company,
                        $member->duty,
                        $member->wechat,
                        $member->group != null ? $member->group->title : '',
                        $member->proxy_balance,
                        $member->renewMoney,
                        date('Y-m-d', $member->proxy_start_time) . ' - ' . date('Y-m-d', $member->proxy_end_time),
                        $member->created_at->format('Y-m-d H:i'),
                        date('Y-m-d H:i', $member->last_login)
                    ]);
                }
            });
        })->download('xlsx');
    }

    public function searchAjax()
    {
        $key = request('key');
        if ($key == '') {
            return response()->json(['error'=>true, 'message'=>'未填写搜索项']);
        }
        
        $members = Member::where('group_id', '>', 0)->where(function($q)use($key){
            return $q->where('nickname', 'like', '%'. $key .'%')->orWhere('name', 'like', '%'. $key .'%')->orWhere('phone', 'like', '%'. $key .'%');
        })->orderBy('id', 'asc')->get();
        $members = $members->map(function($m){
            return [
                'id' => $m->id,
                'nickname' => $m->nickname,
                'name' => $m->name,
                'phone' => $m->phone,
                'avatar' => $m->avatarImage,
            ];
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$members]);
    }
    
    /**
     * 修改上级用户
     */
    public function changeParent($id)
    {
        $member = Member::find($id);
        if ($member == null) {
            return response()->json(['error'=>true, 'message'=>'会员不存在']);
        }
        
        $parentid = intval(request('parentid'));
        $parent = null;
        if ($parentid != 0) {
            $parent = Member::find($parentid);
            if ($parent == null) {
                return response()->json(['error'=>true, 'message'=>'选定上级会员不存在']);
            }
            $parent_pids = explode(',', $parent->parent_path);
            if (in_array($member->id, $parent_pids)) {
                return response()->json(['error'=>true, 'message'=>'不能将自己的下级会员设为上级']);
            }
        }
        
        \DB::beginTransaction();
        try {
            $member->parent_id = $parentid;
            $member->parent_path = $parent != null ? ($parent->parent_path != '' ? $parent->parent_path . ',' . $parent->id : $parent->id) : '';
            $member->save();
            
            $members = Member::whereRaw('find_in_set('. $member->id .', parent_path)')->orderBy('id', 'desc')->get();
            foreach($members as $_member) {
                $_path = explode(',', $_member->parent_path);
                $_idx = array_search($member->id, $_path);
                $_new_path = array_slice($_path, $_idx);
                $_member->parent_path = $member->parent_path != '' ? $member->parent_path . ',' . implode(',', $_new_path) : implode(',', $_new_path);
                $_member->save();
            }
            
            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();
            \Log::info('上级用户修改失败');
            \Log::info($e);
            return response()->json(['error'=>true, 'message'=>'修改失败']);
        }
        
        session()->flash('message', '修改成功');
        return response()->json(['error'=>false]);
    }

    /**
     * 试用用户转正式用户
     */
    public function conversion($id)
    {
        $member = Member::find($id);
        if ($member == null) {
            return response()->json(['error'=>true, 'message'=>'用户不存在']);
        }
        if ($member->probation == false) {
            return response()->json(['error'=>true, 'message'=>'用户已是正式会员']);
        }

        $member->probation = false;
        $member->proxy_end_time = $member->proxy_end_time + 365 * 3600 * 24;
        $member->save();

        $profit = intval(request('profit'));    //是否需要分润
        if ($profit) {
            $order = new Order();
            $order->member_id = $member->id;
            $order->merchant_id = 0;
            $order->sn = Order::buildSn();
            $order->type = Order::TYPE_REG;
            $order->title = '会员注册';
            $order->price = config('site.reg_price', 198);
            $order->balance = 0;
            $order->online_balance = $order->price;
            $order->status = Order::STATUS_NEW;
            $order->content = [];
            $order->name = $member->name;
            $order->phone = $member->phone;
            $order->rebate = [];
            $order->pay_time = 0;
            $order->cancel_time = 0;
            $order->pay_type = '';
            $order->out_trade_no = '';
            $order->remark = '';
            $order->member_remark = request('remark');
            $order->extensions = [];
            $order->save();

            $repo = new \App\Repo\OrderRepo();
            $repo->payed($order, 'test_' . uniqid());
        }

        return response()->json(['error'=>false]);
    }

    public function update()
    {
        $id = intval(request('id'));
        $member = Member::find($id);
        if ($member == null) {
            return response()->json(['error'=>true, 'message'=>'会员不存在']);
        }

        $old_group_id = $member->group_id;
        $groups = MemberGroup::all();
        $group_ids = $groups->map(function($g){return $g->id;})->toArray();
        $member->group_id = intval(request('group_id'));
        $member->proxy_start_time = strtotime(request('start_date'));
        $member->proxy_end_time = strtotime(request('end_date'));
        $member->probation = false;
        $new_group_id = $member->group_id;
        
        if (!in_array($member->group_id, $group_ids)) {
            return response()->json(['error'=>true, 'message'=>'代理级别不存在']);
        }
        if ($member->proxy_start_time === false || $member->proxy_end_time === false) {
            return response()->json(['error'=>true, 'message'=>'有效期设置错误']);
        }
        if ($member->proxy_start_time >= $member->proxy_end_time) {
            return response()->json(['error'=>true, 'message'=>'有效期设置错误']);
        }

        $member->save();

        $log = new MemberUpgradeLog();
        $log->member_id = $member->id;
        $log->old_group_id = $old_group_id;
        $log->new_group_id = $new_group_id;
        $log->type = MemberUpgradeLog::TYPE_ADMIN;
        $log->userid = $this->user->id;
        $log->save();

        AdminLog::addLog($this->user->id, 'update', 'members', $member, '会员设置');
        session()->flash('message', '设置成功');
        return response()->json(['error'=>false]);
    }

    /**
     * 会员详情
     */
    public function show($id)
    {
        $member = Member::where('id', $id)->with('parent')->withCount('childs')->first();
        if ($member == null) {
            return back()->with('message', '用户不存在');
        }
        
        $values = [
            'member' => $member,
        ];
        $this->setCurrentMenu('member:proxy');
        return view('member.show', $values);
    }

    /**
     * 会员财务记录
     */
    public function bills($id)
    {
        $member = Member::find($id);
        if ($member == null) {
            return back()->with('message', '会员不存在');
        }
        
        $page = intval(request('page', 1));
        $pagesize = 20;
        $export = intval(request('export'));

        $cursor = MemberAccountLog::where('member_id', $member->id)->orderBy('id', 'desc');
        if ($export) {
            return $this->_export_bills($member, $cursor);
        }
        $logs = $cursor->paginate($pagesize);
        $values = [
            'member' => $member,
            'logs' => $logs,
            'start' => ($page - 1) * $pagesize,
        ];
        $this->setCurrentMenu('member:proxy');
        return view('member.bills', $values);
    }

    private function _export_bills($member, $cursor)
    {
        $list = $cursor->get();
        \Excel::create($member->name . '财务日志', function($excel)use($list){
            $excel->sheet('sheet', function($sheet)use($list){
                $sheet->setWidth([
                    'A' => 10,
                    'B' => 10,
                    'C' => 25,
                    'D' => 25,
                    'E' => 50,
                ]);
                $sheet->row(1, ['#', '类型', '金额', '日期', '备注']);
                foreach($list as $idx=>$item) {
                    $sheet->appendRow([
                        $idx + 1,
                        $item->type == MemberAccountLog::TYPE_ADD ? '+' : '-',
                        $item->cash,
                        $item->created_at->format('Y-m-d H:i'),
                        $item->remark,
                    ]);
                }
            });
        })->download('xlsx');
    }

    public function postBills($id)
    {
        $member = Member::find($id);
        if ($member == null) {
            return response()->json(['error'=>true, 'message'=>'会员不存在']);
        }
        
        $type = intval(request('type'));
        $money = floatval(request('money'));
        
        if ($money == 0) {
            return response()->json(['error'=>false]);
        }

        if ($money < 0) {
            $money = -$money;
        }

        if ($type == 1) {
            $member->addCash($money, MemberAccountLog::CATEGORY_SYS, '系统处理充值', [], false);
        } else {
            if ($money > $member->proxy_balance) {
                return response()->json(['error'=>true, 'message'=>'反充金额大于会员现有余额']);
            }
            $member->subCash($money, MemberAccountLog::CATEGORY_SYS, '系统处理反充', [], false);
        }
        
        session()->flash('message', '操作成功');
        return response()->json(['error'=>false]);
    }

    /**
     * 续费分成提现日志
     */
    public function renews($id)
    {
        $member = Member::find($id);
        if ($member == null) {
            return back()->with('message', '会员不存在');
        }
        
        $page = intval(request('page', 1));
        $export = intval(request('export'));
        $pagesize = 20;

        $cursor = MemberRenewLog::where('member_id', $member->id)->with('admin')->orderBy('id', 'desc');
        if ($export) {
            return $this->_export_renew($member, $cursor);
        }
        $logs = $cursor->paginate($pagesize);
        $values = [
            'member' => $member,
            'logs' => $logs,
            'start' => ($page - 1) * $pagesize,
        ];
        $this->setCurrentMenu('member:proxy');
        return view('member.renews', $values);
    }

    private function _export_renew($member, $cursor)
    {
        $list = $cursor->get();
        \Excel::create($member->name . '续费分成提现日志', function($excel)use($list){
            $excel->sheet('sheet', function($sheet)use($list){
                $sheet->setWidth([
                    'A' => 10,
                    'B' => 10,
                    'C' => 25,
                    'D' => 25,
                    'E' => 50,
                ]);
                $sheet->row(1, ['#', '类型', '金额', '时间', '备注']);
                foreach($list as $idx=>$item){
                    $sheet->appendRow([
                        $idx + 1,
                        $item->type == MemberRenewLog::TYPE_ADD ? '+' : '-',
                        $item->money,
                        $item->created_at->format('Y-m-d H:i'),
                        $item->remark,
                    ]);
                }
            });
        })->download('xlsx');
    }

    /**
     * 提取续费分成金额
     */
    public function postRenews($id)
    {
        $member = Member::find($id);
        if ($member == null) {
            return response()->json(['error'=>true, 'message'=>'会员不存在']);
        }
        $money = intval(request('money'));
        if ($money <= 0) {
            return response()->json(['error'=>true, 'message'=>'不能提取小于0元的金额']);
        }
        if ($money > $member->renewMoney) {
            return response()->json(['error'=>true, 'message'=>'不能提取大于可提取金额的金额']);
        }
        
        $member->subRenew($money, $this->user->id, request('remark', ''));
        session()->flash('message', '提取成功');
        return response()->json(['error'=>false]);
    }

    /**
     * 代理升级日志
     */
    public function upgradeLogs($id)
    {
        $member = Member::find($id);
        if ($member == null) {
            return back()->with('message', '会员不存在');
        }

        $logs = MemberUpgradeLog::where('member_id', $member->id)->with('oldGroup', 'newGroup')->orderBy('id', 'desc')->get();
        $values = [
            'member' => $member,
            'logs' => $logs,
        ];
        $this->setCurrentMenu('member:proxy');
        return view('member.upgradelogs', $values);
    }
}
