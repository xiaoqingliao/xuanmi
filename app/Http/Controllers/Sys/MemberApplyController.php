<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Sys;

use App\Models\MemberProxyApply;
use App\Models\AppConstants;
use App\Repo\DivideRepo;
use App\Models\AdminLog;
use App\Models\MemberUpgradeLog;
use App\Models\CompanyFinanceLog;

class MemberApplyController extends BaseController
{
    public function index()
    {
        $page = intval(request('page', 1));
        $pagesize = 20;
        $status = intval(request('status'));
        $export = intval(request('export'));
        if (!in_array($status, [AppConstants::PENDING, AppConstants::ACCEPTED, AppConstants::REJECTED])) {
            $status = AppConstants::PENDING;
        }

        $cursor = MemberProxyApply::query();
        if ($status > 0) {
            $cursor->where('status', $status);
        }
        if ($export) {
            return $this->_export_excel($cursor);
        }
        $applies = $cursor->with('member', 'group', 'admin')->orderBy('id', 'desc')->paginate($pagesize);

        $values = [
            'start' => ($page - 1) * $pagesize,
            'filters' => [
                'status' => $status,
            ],
            'statuses' => [
                AppConstants::PENDING => '待审核申请',
                AppConstants::ACCEPTED => '审核通过的记录',
                AppConstants::REJECTED => '审核未通过的记录',
            ],
            'applies' => $applies,
        ];
        $this->setCurrentMenu('member:upgrade');
        return view('member.applies', $values);
    }
    
    private function _export_excel($cursor) {
        $list = $cursor->with('member', 'group', 'admin')->orderBy('id', 'desc')->get();
        \Excel::create(date('Y-m-d') . '代理申请', function($excel) use($list){
            $excel->sheet('sheet1', function($sheet)use($list){
                $sheet->setWidth([
                    'D' => 30,
                    'F' => 25,
                    'G' => 30,
                    'H' => 30,
                    'I' => 25,
                    'K' => 25,
                    'L' => 40,
                ]);
                $sheet->row(1, ['#', '昵称', '姓名', '公司', '职务', '手机', '申请级别', '申请时间', '申请状态', '审批人', '审核时间', '备注']);
                $statuses = [
                    AppConstants::PENDING => '待审核',
                    AppConstants::ACCEPTED => '已通过',
                    AppConstants::REJECTED => '未通过',
                ];
                foreach($list as $idx=>$item) {
                    $sheet->appendRow([
                        $idx + 1,
                        $item->member->nickname,
                        $item->member->name,
                        $item->member->company,
                        $item->member->duty,
                        $item->member->phone,
                        $item->group->title,
                        $item->created_at->format('Y-m-d H:i'),
                        $statuses[$item->status],
                        $item->admin != null ? $item->admin->name : '',
                        $item->admin != null ? $item->updated_at->format('Y-m-d H:i') : '',
                        $item->remark,
                    ]);
                }
            });
        })->download('xlsx');
    }

    public function accept()
    {
        $id = request('id');
        $money = floatval(request('money'));
        if ($money <= 0) $money = 0;
        $apply = MemberProxyApply::find($id);
        if ($apply != null && $apply->status == AppConstants::PENDING) {
            $start_date = strtotime(request('start_date'));
            $end_date = strtotime(request('end_date'));
            if ($start_date === false || $end_date === false || $end_date <= $start_date) {
                return response()->json(['error'=>true, 'message'=>'请正确选择有效期']);
            }
            \DB::beginTransaction();
            try {
                $member = $apply->member;
                $old_group_id = $member->group_id;
                $member->group_id = $apply->group_id;
                $member->proxy_start_time = $start_date;
                $member->proxy_end_time = $end_date;
                $member->save();
                $new_group_id = $member->group_id;

                $apply->money = $money;
                $apply->status = AppConstants::ACCEPTED;
                $apply->remark = request('remark');
                $apply->userid = $this->user->id;
                $apply->save();

                $log = new MemberUpgradeLog();
                $log->member_id = $member->id;
                $log->old_group_id = $old_group_id;
                $log->new_group_id = $new_group_id;
                $log->userid = $this->user->id;
                $log->type = MemberUpgradeLog::TYPE_APPLY;
                $log->save();

                if ($money > 0) {
                    CompanyFinanceLog::add($member->id, $money, 'MemberProxyApply', '代理升级：' . $member->group->title, $apply->toArray());

                    //分成计算
                    $repo = new DivideRepo();
                    $repo->upgradeDivide($apply);
                }
                session()->flash('message', '审核通过完成');
                \DB::commit();
                AdminLog::addLog($this->user->id, 'update', 'member_proxy_applies', $apply, '代理升级审核通过');
            } catch(\Exception $e) {
                \Log::info('代理升级失败');
                \Log::info($e);
                \DB::rollBack();
                return response()->json(['error'=>true, 'message'=>'审核失败，请联系管理员']);
            }
        }
        return response()->json(['error'=>false]);
    }

    public function reject()
    {
        $id = request('id');
        $apply = MemberProxyApply::find($id);
        if ($apply != null && $apply->status == AppConstants::PENDING) {
            $apply->status = AppConstants::REJECTED;
            $apply->remark = request('remark');
            $apply->userid = $this->user->id;
            $apply->save();

            AdminLog::addLog($this->user->id, 'update', 'member_proxy_applies', $apply, '代理升级审核不通过');
            session()->flash('message', '审核不通过');
        }
        return response()->json(['error'=>false]);
    }
}