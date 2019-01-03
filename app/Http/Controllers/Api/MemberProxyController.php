<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Models\ApiErrorCode;
use App\Models\AppConstants;
use App\Models\MemberGroup;
use App\Models\Member;
use App\Models\MemberProxyApply;

class MemberProxyController extends BaseController
{
    /**
     * 我提交的升级申请
     */
    public function my()
    {
        $apply = MemberProxyApply::where('member_id', $this->member->id)->with('group')->orderBy('id', 'desc')->first();
        if ($apply == null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }
        $apply = [
            'group' => [
                'id' => $apply->group->id,
                'title' => $apply->group->title,
                'icon' => image_url($apply->group->icon, null, null, true),
            ],
            'status' => $apply->status,
            'remark' => $apply->remark,
        ];

        return response()->json(['error'=>false, 'apply'=>$apply]);
    }

    /**
     * 提交升级申请
     */
    public function apply()
    {
        $member = $this->member;
        $group_id = intval(request('groupid'));

        $contract = request('contract');
        $bank = request('bank');

        if ($contract == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::PROXY_APPLY_CONTRACT_ERROR]);
        }
        if ($bank == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::PROXY_APPLY_BANK_ERROR]);
        }

        $contract = image_replace($contract);
        $bank = image_replace($bank);

        $group = MemberGroup::find($group_id);
        if ($group == null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }
        
        if ($group->id <= $member->group_id) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::PROXY_APPLY_GROUP_ERROR]);
        }

        $apply = MemberProxyApply::where('member_id', $member->id)->where('status', AppConstants::PENDING)->orderBy('id', 'desc')->first();
        if ($apply != null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::PROXY_APPLY_EXISTS_ERROR]);
        }
        
        $apply = new MemberProxyApply();
        $apply->member_id = $member->id;
        $apply->group_id = $group->id;
        $apply->status = AppConstants::PENDING;
        $apply->money = $group->money;
        $apply->contract = $contract;
        $apply->bank = $bank;
        $apply->save();

        return response()->json(['error'=>false]);
    }
}