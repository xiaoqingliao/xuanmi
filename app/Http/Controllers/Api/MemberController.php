<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Services\miniapp\AppService;
use App\Services\miniapp\DituService;
use App\Models\ApiErrorCode;
use App\Models\AppConstants;
use App\Models\Member;
use App\Models\MemberGroup;
use App\Models\MemberFriend;
use App\Models\MemberAccountLog;
use App\Models\VisitRecord;
use App\Models\MemberWithdraw;
use App\Models\Notice;
use App\Models\Industry;
use App\Models\MemberTag;

/**
 * 个人中心接口
 */
class MemberController extends BaseController
{
    /**
     * 会员级别资料
     */
    public function groups()
    {
        $groups = MemberGroup::query()->orderBy('orderindex', 'desc')->orderBy('id', 'asc')->get();
        $groups = $groups->map(function($group){
            return [
                'id' => $group->id,
                'title' => $group->title,
                'icon' => image_url($group->icon, null, null, true),
                'price' => $group->price,
                'copyright' => $group->copyright ?: '',
                'description' => $group->description ?: '',
                'contract' => image_url($group->contract, null, null, true),
            ];
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$groups]);
    }

    /**
     * 个人资料
     */
    public function info()
    {
        $member = $this->member;
        $_group = null;
        $_parent = null;

        if ($member->group != null) {
            $_group = [
                'id' => $member->group->id,
                'title' => $member->group->title,
                'icon' => image_url($member->group->icon, null, null, true),
            ];
        }
        if ($member->parent != null) {
            $_parent = [
                'id' => $member->parent->id,
                'nickname' => $member->parent->nickname,
                'avatar' => image_url($member->parent->avatar),
                'company' => $member->parent->company,
                'phone' => $member->parent->phone,
                'wechat' => $member->parent->wechat,
            ];
        }

        $focus = MemberFriend::where('member_id', $member->id)->count();
        $fans = MemberFriend::where('friend_id', $member->id)->count();
        $views = VisitRecord::where('model_type', VisitRecord::TYPE_MEMBER)->where('model_id', $member->id)->where('created_at', '>=', date('Y-m-d 00:00:00'))->where('created_at', '<=', date('Y-m-d 23:59:59'))->count();
        $notices_count = Notice::where('member_id', $member->id)->where('read_time', 0)->count();

        $info = [
            'id' => $member->id,
            'openid' => $member->openid,
            'nickname' => $member->nickname,
            'name' => $member->name,
            'avatar' => image_url($member->avatar, null, null, true),
            'gender' => $member->gender,
            'group' => $_group,
            'parent' => $_parent,
            'company' => $member->company,
            'duty' => $member->duty,
            'phone' => $member->phone,
            'wechat' => $member->wechat,
            'summary' => $member->summary ?: '',
            'province' => $member->province ?: '',
            'city' => $member->city ?: '',
            'area' => $member->area ?: '',
            'address' => $member->address ?: '',
            'school' => $member->school ?: '',
            'bank' => [
                'name' => $member->bank_name ?: '',
                'no' => $member->bank_no ?: '',
                'contact' => $member->bank_contact ?: ''
            ],
            'alipay' => $member->alipay ?: '',
            'nation' => [
                'province' => $member->nation_province ?: '',
                'city' => $member->nation_city ?: '',
                'area' => $member->nation_area ?: '',
                'address' => $member->nation_address ?: '',
            ],
            'industry' => $member->industryList,
            //'address' => $member->getExtensions('address', ''),
            'gps' => [
                'lat' => $member->lat ?: config('site.lat', '29.868782'),
                'lng' => $member->lng ?: config('site.lng', '121.549644'),
            ],
            'proxy_start_time' => $member->proxy_start_time,
            'proxy_end_time' => $member->proxy_end_time,
            'focus_count' => $focus,
            'fans_count' => $fans,
            'balance' => $member->proxy_balance,
            'views' => $views,
            'cart_count' => 0,   //todo
            'notice_count' => $notices_count,
            'expired' => $member->isExpired(),
            'qrcode' => route('api.promocode', ['id'=>$member->id]),
            'trail' => $member->probation,
            'motto' => $member->motto,
            'tags' => $member->tags->map(function($tag){
                return [
                    'id' => $tag->id,
                    'title' => $tag->title,
                    'likes' => $tag->likes,
                ];
            })->toArray(),
        ];

        return response()->json(['error'=>false, 'userinfo'=>$info]);
    }

    /**
     * 个人资料更新
     */
    public function updateInfo()
    {
        $member = $this->member;
        $member->name = request('name');
        $member->company = request('company');
        $member->gender = intval(request('gender'));
        $member->duty = request('duty');
        $member->phone = request('phone');
        $member->wechat = request('wechat');
        $member->summary = request('summary');
        $member->province = request('province');
        $member->city = request('city');
        $member->area = request('area');
        $member->address = request('address');
        //$lat = request('lat', '');
        //$lng = request('lng', '');
        $lat = '';
        $lng = '';
        $member->school = request('school');
        $member->nation_province = request('nation_province');
        $member->nation_city = request('nation_city');
        $member->nation_area = request('nation_area');
        $member->nation_address = request('nation_address');
        $member->bank_name = request('bank_name');
        $member->bank_no = request('bank_no');
        $member->bank_contact = request('bank_contact');
        $member->alipay = request('alipay');
        $member->motto = request('motto');

        if ($member->name == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::INFO_NAME_ERROR]);
        }
        if ($member->phone == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::INFO_PHONE_ERROR]);
        }
        $industry_id = intval(request('industry_id'));
        $industry = Industry::where('id', $industry_id)->with('parent')->first();
        if ($industry == null || $industry->parent == null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::INFO_INDUSTRY_ERROR]);
        }
        $member->industry = $industry->parent_id . ',' . $industry->id;

        if ($lat == '' && $lng == '') {
            $ditu = new DituService();
            $address = $member->address;
            $r = $ditu->reverse($address);
            if ($r != null) {
                $lat = $r['lat'];
                $lng = $r['lng'];
            }
        }
        $member->lat = $lat;
        $member->lng = $lng;

        $member->save();

        //更新用户标签
        $tags = request('tags');
        if (!is_array($tags)) {
            $tags = json_decode($tags, true);
            if (empty($tags)) $tags = [];
        }
        $old_tags = MemberTag::where('member_id', $member->id)->get();
        $exists_tag_ids = [];
        foreach($tags as $tag_item) {
            if (isset($tag_item['id']) && isset($tag_item['title']) && $tag_item['title'] != '') {
                if (intval($tag_item['id']) > 0) {
                    MemberTag::where('member_id', $member->id)->where('id', intval($tag_item['id']))->update(['title'=>$tag_item['title']]);
                    $exists_tag_ids[] = intval($tag_item['id']);
                } else {
                    $_tag = new MemberTag();
                    $_tag->member_id = $member->id;
                    $_tag->title = $tag_item['title'];
                    $_tag->likes = 0;
                    $_tag->save();
                    $exists_tag_ids[] = $_tag->id;
                }
            }
        }
        foreach($old_tags as $_tag) {
            if (in_array($_tag->id, $exists_tag_ids) == false) {
                $_tag->delete();
            }
        }

        return response()->json(['error'=>false]);
    }

    /**
     * 个人推广二维码
     */
    public function promocode($id)
    {
        $member = Member::find($id);
        if ($member == null) {
            abort(404);
        }
        //$promo_path = $member->getExtensions('promo_miniapp', '');
        $promo_path = '';
        if ($promo_path == '') {
            $bg = \Image::canvas(750, 1123);
            if ($member->avatar != '') {
                $avatar = \Image::make(upload_path($member->avatar));
                $avatar->resize(175, 175);
                $bg->insert($avatar, 'top-left', 289, 845);
            } else {
                $avatar = \Image::make(base_path('public/assets/avatar.jpg'));
                $avatar->resize(175, 175);
                $bg->insert($avatar, 'top-left', 289, 845);
            }
            $miniapp = new AppService();
            try {
                $qr = \Image::make($miniapp->getImage('/pages/index1?mchid=' . $member->id, 390));
                $bg->insert($qr, 'top-left', 179, 374);
            } catch(\Exception $e) {
                \Log::info('获取小程序码错误');
                \Log::info($e);
            }

            $codebg = \Image::make(base_path('resources/assets/codebg.png'));
            $bg->insert($codebg, 'top-left', 0, 0);

            if ($member->nickname != '') {
                $nickname = $member->nickname;
                $fontfile = base_path('/resources/assets/msyh.ttc');
                $text_size = imagettfbbox(28, 0, $fontfile, $nickname);
                $width = $text_size[2] - $text_size[0];
                $left = (750 - $width) / 2 + $width / 2;
                
                $bg->text($nickname, $left, 1075, function($font)use($fontfile){
                    $font->file($fontfile);
                    $font->size(28);
                    $font->color('#333333');
                    $font->align('center');
                });
            }

            $bgpath = date('Ymd') . '/' . time() . uniqid() . '.jpg';
            upload_save($bg->stream('jpg', 60), $bgpath);
            $member->setExtensions('promo_miniapp', $bgpath);
            $member->save();
            $promo_path = $bgpath;
        }
        
        $codebg = \Image::make(upload_path($promo_path));
        $datetime = mktime(date('H'),date('i'),date('s'),date('n'),date('j'),date('Y') + 1);
        $headers = [
            'Content-Type' => 'image/jpg',
        ];
        return response($codebg->stream('jpg', 60), 200, $headers);
    }

    /**
     * 读取展示一切内容
     */
    public function getShow()
    {
        $member = $this->member;
        $show = $member->getExtensions('show', []);
        $new_show = [];
        foreach($show as $item) {
            if ($item['type'] == 'image') {
                $new_show[] = ['type'=>'image', 'image'=>image_url($item['image'], null, null, true)];
            } else {
                $new_show[] = $item;
            }
        }

        return response()->json(['error'=>false, 'content'=>$new_show]);
    }

    /**
     * 更新展示一切
     */
    public function updateShow()
    {
        $member = $this->member;

        $show = request('content');
        if (is_string($show)) {
            $show = ['type'=>'text', 'content'=>$show];
        }
        $new_show = [];
        $server = $_SERVER['HTTP_HOST'];
        foreach($show as $item) {
            if (is_string($item)) {
                $new_show[] = ['type'=>'text', 'content'=>$item];
            } else if (isset($item['type']) && $item['type'] == 'image' && isset($item['image'])) {
                $url = str_replace(['http://' . $server . '/image/', 'https://' . $server . '/image/'], ['', ''], $item['image']);
                $new_show[] = ['type'=>'image', 'image'=>$url];
            } else if (isset($item['type']) && $item['type'] == 'text' && isset($item['content'])) {
                $val = $item['content'];
                $tmpArr = explode("\n", $val);
                foreach($tmpArr as $_text) {
                    if (!empty($_text)) {
                        $new_show[] = [
                            'type' => 'text',
                            'content' => $_text,
                        ];
                    }
                }
            }
        }
        $member->setExtensions('show', $new_show);
        $member->save();

        return response()->json(['error'=>false]);
    }

    /**
     * 我的团队
     */
    public function group()
    {
        $parentid = intval(request('parentid'));
        if ($parentid <= 0) $parentid = $this->member->id;
        
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));

        $cursor = Member::where('parent_id', $parentid)->where('group_id', '>', 0);
        $count = $cursor->count();
        $members = $cursor->with('group')->withCount('childs')->orderBy('id', 'asc')->paginate($pagesize);
        $pages = $members->lastPage();

        //统计三层用户数量
        $childs_count = Member::where('group_id', '>', 0)->whereRaw('find_in_set(\''. $parentid .'\', substring_index(parent_path, \',\', -3))')->count();

        $members = $members->map(function($member){
            return [
                'id' => $member->id,
                'nickname' => $member->nickname,
                'name' => $member->name,
                'avatar' => image_url($member->avatar, null, null, true),
                'group' => $member->group->title,
                'childs' => $member->childs_count,
            ];
        })->toArray();
        
        return response()->json(['error'=>false, 'list'=>$members, 'count'=>$count, 'childs'=>$childs_count, 'pages'=>$pages]);
    }

    /**
     * 我的访客记录
     */
    public function visits()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));

        $cursor = VisitRecord::where('model_type', VisitRecord::TYPE_MEMBER)->where('model_id', $this->member->id);
        $count = $cursor->count();
        $records = $cursor->whereHas('member', function($q){
            return $q->where('username', '<>', '');
        })->with('member')->orderBy('id', 'desc')->paginate($pagesize);
        $pages = $records->lastPage();

        $records = $records->map(function($r){
            $m = $r->member;
            return [
                'id' => $r->id,
                'member' => [
                    'id' => $m->id,
                    'nickname' => $m->logged ? $m->nickname : '未注册会员',
                    'name' => $m->name,
                    'phone' => $m->phone,
                    'company' => $m->company,
                    'avatar' => image_url($m->avatar, null, null, true),
                    'expired' => $m->isExpired(),
                ],
                'created' => $r->created_at->timestamp,
            ];
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$records, 'count'=>$count, 'pages'=>$pages]);
    }
    
    /**
     * 我的战绩(发展的下线用户提成记录)
     */
    public function standing()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));

        $cursor = MemberAccountLog::where('member_id', $this->member->id)->whereIn('category', [MemberAccountLog::CATEGORY_NEW, MemberAccountLog::CATEGORY_GROUP_UPDATE, MemberAccountLog::CATEGORY_RENEW, MemberAccountLog::CATEGORY_CHILD_NEW, MemberAccountLog::CATEGORY_CHILD_RENEW]);
        $total = $cursor->sum('cash');
        $count = $cursor->count();
        $logs = $cursor->orderBy('id', 'desc')->paginate($pagesize);
        $pages = $logs->lastPage();

        $logs = $logs->map(function($log){
            return [
                'id' => $log->id,
                'icon' => image_url('', null, null, true),
                'title' => $log->remark,
                'money' => $log->cash,
                'created' => $log->created_at->timestamp,
            ];
        })->toArray();
        return response()->json(['error'=>false, 'total'=>$total, 'list'=>$logs, 'count'=>$count, 'pages'=>$pages]);
    }
    
    /**
     * 我的财务记录(分成记录，余额消费记录，余额提现记录)
     */
    public function bills()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));

        $cursor = MemberAccountLog::where('member_id', $this->member->id);
        $count = $cursor->count();
        $logs = $cursor->orderBy('id', 'desc')->paginate($pagesize);
        $pages = $logs->lastPage();

        $logs = $logs->map(function($log){
            return [
                'id' => $log->id,
                'icon' => image_url('', null, null, true),
                'title' => $log->remark,
                'type' => $log->typeKey,
                'money' => $log->cash,
                'created' => $log->created_at->timestamp,
            ];
        })->toArray();
        return response()->json(['error'=>false, 'balance'=>$this->member->proxy_balance, 'list'=>$logs, 'count'=>$count, 'pages'=>$pages]);
    }
    
    /**
     * 提现申请
     */
    public function withdraw()
    {
        $money = floatval(request('money'));
        if ($money <= 0 || $money > $this->member->proxy_balance || $money < config('site.withdraw_money', 0)) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::WITHDRAW_MONEY_ERROR]);
        }

        \DB::beginTransaction();
        try {
            $member = $this->member;
            $member->subCash($money, MemberAccountLog::CATEGORY_WITHDRAW, '用户提现');

            $withdraw = new MemberWithdraw();
            $withdraw->member_id = $member->id;
            $withdraw->money = $money;
            $withdraw->status = AppConstants::PENDING;
            $withdraw->save();

            \DB::commit();
        } catch(\Exception $e) {
            \Log::info($e);
            \DB::rollBack();
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::WITHDRAW_UNKNOW_ERROR]);
        }
        return response()->json(['error'=>false]);
    }
}
