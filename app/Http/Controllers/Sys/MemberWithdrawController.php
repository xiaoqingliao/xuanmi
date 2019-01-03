<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Sys;

use App\Models\AppConstants;
use App\Models\Member;
use App\Models\MemberWithdraw;
use App\Models\AdminLog;
use App\Models\MemberAccountLog;
use App\Models\CompanyFinanceLog;

class MemberWithdrawController extends BaseController 
{
    public function __construct()
    {
        parent::__construct();
        $this->setCurrentMenu('company:wallet');
    }
    
    public function index()
    {
        $page = intval(request('page', 1));
        $pagesize = 20;
        $status = intval(request('status'));
        $date_field = 'created_at';
        $start_date = request('start_date');
        $end_date = request('end_date');
        $export = intval(request('export'));
        if (in_array($status, [AppConstants::PENDING, AppConstants::ACCEPTED, AppConstants::REJECTED, AppConstants::SENDED, AppConstants::FAILED]) == false) {
            $status = AppConstants::PENDING;
        }
        $cursor = MemberWithdraw::query();
        if ($status > 0) {
            $cursor->where('status', $status);
        }
        if ($start_date != '') {
            $cursor->where($date_field, '>=', $start_date . ' 00:00:00');
        }
        if ($end_date != '') {
            $cursor->where($date_field, '<=', $end_date . ' 23:59:59');
        }
        if ($export) {
            return $this->_export_excel($cursor);
        }
        $total_money = $cursor->sum('money');
        $total_fee = $cursor->sum('fee');
        $total_actual = $cursor->sum('actual');
        $withdraws = $cursor->with('member', 'admin')->orderBy('id', 'desc')->paginate($pagesize);
        $values = [
            'start' => ($page - 1) * $pagesize,
            'filters' => [
                'status' => $status,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ],
            'total' => [
                'money' => $total_money,
                'fee' => $total_fee,
                'actual' => $total_actual,
            ],
            'statuses' => [
                AppConstants::PENDING => '待审核申请',
                AppConstants::ACCEPTED => '审核通过的申请',
                AppConstants::REJECTED => '未审核通过的申请',
                AppConstants::SENDED => '已发放',
                AppConstants::FAILED => '发放失败',
            ],
            'withdraws' => $withdraws,
        ];
        return view('member.withdraw', $values);
    }

    private function _export_excel($cursor)
    {
        $list = $cursor->with('member', 'admin')->orderBy('id', 'desc')->get();
        \Excel::create(date('Y-m-d') . '提现申请', function($excel)use($list){
            $excel->sheet('sheet', function($sheet)use($list){
                $sheet->setWidth([
                    'A' => 10,
                    'D' => 30,
                    'F' => 25,
                    'G' => 25,
                    'H' => 25,
                    'I' => 25,
                    'J' => 25,
                    'K' => 25,
                    'L' => 25,
                    'N' => 40,
                    'O' => 40,
                    'P' => 40,
                ]);
                $sheet->row(1, ['#', '昵称', '姓名', '公司', '职务', '手机', '申请提现金额', '平台手续费', '实际提现金额', '状态', '申请时间', '审核人', '审核时间', '备注', '提现账户', '结果']);
                foreach($list as $idx=>$item) {
                    $sheet->appendRow([
                        $idx + 1,
                        $item->member->nickname,
                        $item->member->name,
                        $item->member->company,
                        $item->member->duty,
                        $item->member->phone,
                        $item->money,
                        $item->moneyFee,
                        $item->actualMoney,
                        AppConstants::getStatusText($item->status),
                        $item->created_at->format('Y-m-d H:i'),
                        $item->admin != null ? $item->admin->name : '',
                        $item->admin != null ? $item->updated_at->format('Y-m-d H:i') : '',
                        $item->remark,
                        $item->account,
                        $item->logs,
                    ]);
                }
            });
        })->download('xlsx');
    }
    
    public function show($id)
    {
        $page = intval(request('page', 1));
        $pagesize = 20;
        $withdraw = MemberWithdraw::where('id', $id)->with('member')->first();
        if ($withdraw == null) {
            return back()->with('message', '提现申请不存在');
        }
        
        $logs = MemberAccountLog::where('member_id', $withdraw->member_id)->orderBy('id', 'desc')->paginate($pagesize);
        $values = [
            'withdraw' => $withdraw,
            'logs' => $logs,
            'start' => ($page - 1) * $pagesize,
        ];
        return view('member.withdraw_show', $values);
    }

    public function accept()
    {
        $id = intval(request('id'));
        $item = MemberWithdraw::find($id);
        if ($item != null && $item->status == AppConstants::PENDING) {
            $item->status = AppConstants::ACCEPTED;
            $item->userid = $this->user->id;
            $item->remark = request('remark');
            $item->fee = $item->moneyFee;
            $item->actual = $item->actualMoney;
            $item->balance = $item->member->proxy_balance;
            $item->type = request('type');
            if ($item->type == 'bank') {    //如果是银行则默认打款完成
                $item->status = AppConstants::SENDED;
                $item->account = $item->member->bank_name . '/' . $item->member->bank_no . '/' . $item->member->bank_contact;
            }
            $item->save();

            CompanyFinanceLog::sub($item->member_id, $item->money, 'MemberWithDraw', '用户提现', $item);
            CompanyFinanceLog::add($item->member_id, $item->moneyFee, 'MemberWithDraw', '用户提现手续费', $item);

            session()->flash('message', '提现已处理完成');
            AdminLog::addLog($this->user->id, 'update', 'member_withdraws', $item, '提现已处理完成');
            return response()->json(['error'=>false]);
        }
        return response()->json(['error'=>true, 'message'=>'提现请求不存在或已处理']);
    }

    public function reject()
    {
        $id = intval(request('id'));
        $item = MemberWithdraw::find($id);
        if ($item != null && $item->status == AppConstants::PENDING) {
            $item->status = AppConstants::REJECTED;
            $item->userid = $this->user->id;
            $item->remark = request('remark');
            $item->save();

            $member = $item->member;
            $member->addCash($item->money, MemberAccountLog::CATEGORY_WITHDRAW, '提现审核被拒绝');

            $item->fee = $item->moneyFee;
            $item->actual = $item->actualMoney;
            $item->balance = $member->proxy_balance;
            $item->save();

            session()->flash('message', '提现请求已被拒绝');
            AdminLog::addLog($this->user->id, 'update', 'member_withdraws', $item, '提现请求已被拒绝');
            return response()->json(['error'=>false]);
        }
        return response()->json(['error'=>true, 'message'=>'提现请求不存在或已处理']);
    }
}