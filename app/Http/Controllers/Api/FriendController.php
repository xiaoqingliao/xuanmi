<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Models\ApiErrorCode;
use App\Models\Member;
use App\Models\MemberFriend;

class FriendController extends BaseController
{
    /**
     * 我关注的用户
     */
    public function focus()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));

        $cursor = MemberFriend::where('member_id', $this->member->id);
        $count = $cursor->count();
        $members = $cursor->whereHas('friend', function($q){
            return $q->where('username', '<>', '');
        })->with('friend')->orderBy('id', 'desc')->paginate($pagesize);
        $pages = $members->lastPage();

        $members = $members->map(function($m){
            $member = $m->friend;
            return [
                'id' => $member->id,
                'nickname' => $member->nickname,
                'name' => $member->name,
                'avatar' => image_url($member->avatar, null, null, true),
                'phone' => $member->phone,
                'company' => $member->company,
                'expired' => $member->isExpired(),
            ];
        })->toArray();
        return response()->json(['error'=>false, 'list'=>$members, 'count'=>$count, 'pages'=>$pages]);
    }

    /**
     * 关注/取消关注用户
     */
    public function postFocus()
    {
        $friend_id = intval(request('mchid'));
        if ($friend_id == $this->member->id) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::PARAM_ERROR]);
        }
        $friend = Member::find($friend_id);
        if ($friend == null || $friend->group_id <= 0) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }
        
        $focus = MemberFriend::where('member_id', $this->member->id)->where('friend_id', $friend_id)->first();
        if ($focus == null) {
            $focus = new MemberFriend();
            $focus->member_id = $this->member->id;
            $focus->friend_id = $friend_id;
            $focus->save();
        } else {
            $focus->delete();
        }
        return response()->json(['error'=>false]);
    }
    
    /**
     * 关注我的用户
     */
    public function fans()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));

        $cursor = MemberFriend::where('friend_id', $this->member->id);
        $count = $cursor->count();
        $members = $cursor->whereHas('member', function($q){
            return $q->where('username', '<>', '');
        })->with('member')->orderBy('id', 'desc')->paginate($pagesize);
        $pages = $members->lastPage();

        $members = $members->map(function($m){
            $member = $m->member;
            return [
                'id' => $member->id,
                'nickname' => $member->nickname,
                'name' => $member->name,
                'avatar' => image_url($member->avatar, null, null, true),
                'phone' => $member->phone,
                'company' => $member->company,
                'expired' => $member->isExpired(),
            ];
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$members, 'count'=>$count, 'pages'=>$pages]);
    }

    /**
     * 推荐关注列表
     */
    public function recommend()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));
        $type = request('type');

        if (!in_array($type, ['recommend', 'industry', 'gps', 'school', 'nation'])){
            $type = 'recommend';
        }
        $key = request('key');

        $cursor = Member::where('group_id', '>', '0');
        $friend_ids = MemberFriend::where('member_id', $this->member->id)->get()->map(function($item){
            return $item->friend_id;
        })->toArray();
        if (count($friend_ids) > 0) {
            $cursor->whereNotIn('id', $friend_ids);
        }
        switch($type) {
            case 'industry':
            $industry_id = intval(request('industryid'));
            if ($industry_id > 0) {
                $cursor->whereRaw('find_in_set('. $industry_id .', industry)');
            } else {
                $cursor->where('industry', $this->member->industry);
            }
            $cursor->orderBy('search_weight', 'desc')->orderBy('id', 'desc');
            break;
            case 'gps':
            $lat = request('lat');
            $lng = request('lng');
            if ($lat == '' || $lng == '') {
                return response()->json(['error'=>false, 'list'=>[], 'count'=>0, 'pages'=>1]);
            }
            $cursor->select(\DB::raw('*, SQRT(POW('. $lat .' - lat, 2) + POW('. $lng .' - lng, 2)) as distance'));
            $cursor->orderBy('distance', 'asc')->orderBy('search_weight', 'desc')->orderBy('id', 'desc');
            break;
            case 'school':
            $cursor->where('school', 'like', '%'. $this->member->school .'%');
            $cursor->orderBy('search_weight', 'desc')->orderBy('id', 'desc');
            break;
            case 'nation':
            $cursor->where('nation_province', $this->member->nation_province)->where('nation_city', $this->member->nation_city)->where('nation_area', $this->member->nation_area);
            $cursor->orderBy('search_weight', 'desc')->orderBy('id', 'desc');
            break;
            default:
            $cursor->orderBy('search_weight', 'desc')->orderBy('id', 'desc');
            break;
        }
        
        if ($key != '') {
            $cursor->where(function($q)use($key){
                return $q->where('nickname', 'like', '%'. $key .'%')->orWhere('name', 'like', '%'. $key .'%');
            });
        }
        $count = $cursor->count();
        $members = $cursor->paginate($pagesize);
        $pages = $members->lastPage();

        $member_ids = $members->pluck('id')->toArray();
        $friend_ids = [];
        if (!empty($member_ids)) {
            $friend_ids = MemberFriend::where('member_id', $this->member->id)->whereIn('friend_id', $member_ids)->get()->pluck('friend_id')->toArray();
        }
        
        $members = $members->map(function($member)use($friend_ids){
            return [
                'id' => $member->id,
                'nickname' => $member->nickname,
                'name' => $member->name,
                'avatar' => image_url($member->avatar, null, null, true),
                'company' => $member->company,
                'focused' => in_array($member->id, $friend_ids),
            ];
        })->toArray();
        return response()->json(['error'=>false, 'list'=>$members, 'count'=>$count, 'pages'=>$pages]);
    }
}
