<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Services\ipdb\IPService;
use App\Services\Ali\AliSms;
use App\Models\ApiErrorCode;
use App\Models\AppConstants;
use App\Models\Member;
use App\Models\MemberLoginRecord;
use App\Models\MemberFriend;
use App\Models\MemberGroup;
use App\Models\Banner;
use App\Models\Course;
use App\Models\Meeting;
use App\Models\Article;
use App\Models\Category;
use App\Models\PhoneCode;
use App\Models\VisitRecord;
use App\Models\Industry;
use App\Models\MemberTag;
use App\Models\MemberTagLike;
use App\Models\Order;

class HomeController extends BaseController
{
    /**
     * 主页
     */
    public function index()
    {
        $member_id = intval(request('mchid'));
        if ($member_id > 0) {
            $member = Member::find($member_id);
            if ($member == null) {
                return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
            }
        } else if ($this->member != null && $this->member->group_id > 0) {
            $member = $this->member;
        } else {
            $member = Member::find(config('site.default_mchid', 1));
            if ($member == null) {
                return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
            }
        }
        if ($member->group_id <= 0) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }

        $focused = false;
        if ($this->member != null) {
            $friend = MemberFriend::where('member_id', $this->member->id)->where('friend_id', $member_id)->first();
            $focused = $friend != null;
        }
        $_gps = null;
        $lat = $member->lat;
        $lng = $member->lng;
        if (!empty($lat) && !empty($lng)) {
            $_gps = [
                'lat' => $lat,
                'lng' => $lng,
            ];
        }
        
        $show_content = [];
        $show = $member->getExtensions('show', []);
        foreach($show as $item) {
            if ($item['type'] == 'image') {
                $show_content[] = ['type'=>'image', 'image'=>image_url($item['image'], null, null, true)];
            } else {
                $show_content[] = $item;
            }
        }

        $info = [
            'id' => $member->id,
            'name' => $member->name,
            'avatar' => image_url($member->avatar, null, null, true),
            'phone' => $member->phone,
            'duty' => $member->duty,
            'wechat' => $member->wechat,
            'company' => $member->company,
            'summary' => $member->summary,
            'province' => $member->province,
            'city' => $member->city,
            'area' => $member->area,
            'address' => $member->address,
            'gps' => $_gps,
            'show' => $show_content,
            'group' => [
                'id' => $member->group->id,
                'title' => $member->group->title,
            ],
            'focused' => $focused,
            'expired' => $member->isExpired(),
            'motto' => $member->motto,
            'tags' => $member->tags->map(function($tag){
                return [
                    'id' => $tag->id,
                    'title' => $tag->title,
                    'likes' => $tag->likes,
                ];
            })->toArray(),
        ];

        $banners = Banner::where('member_id', $member->id)->orderBy('orderindex', 'desc')->orderBy('id', 'desc')->get();
        $banners = $banners->map(function($banner){
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'cover' => image_url($banner->cover, null, null, true),
                'video' => media_url($banner->video),
                'redirect' => $banner->redirect,
            ];
        })->toArray();

        $new_categories = [];
        $categories = Category::where('type', Category::TYPE_ARTICLE)->get();
        foreach($categories as $_category) {
            $articles = Article::where('member_id', $member->id)->where('type', Category::TYPE_ARTICLE)->where('category_id', $_category->id)->where('status', AppConstants::ACCEPTED)
                ->orderBy('orderindex', 'desc')->orderBy('id', 'desc')->take(5)->get();
            $articles = $articles->map(function($article){
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'created' => $article->created_at->timestamp,
                ];
            })->toArray();
            $new_categories[] = [
                'id' => $_category->id,
                'title' => $_category->title,
                'articles' => $articles,
            ];
        }

        $products = Article::where('member_id', $member->id)->where('type', Category::TYPE_PRODUCT)->where('status', AppConstants::ACCEPTED)
            ->orderBy('orderindex', 'desc')->orderBy('id', 'desc')->get();
        $products = $products->map(function($p){
            return [
                'id' => $p->id,
                'title' => $p->title,
                'cover' => image_url($p->cover, null, null, true),
                'origin' => $p->getExtensions('origin', ''),
                'price' => $p->getExtensions('price', ''),
            ];
        })->toArray();

        $companies = Article::where('member_id', $member->id)->where('type', Category::TYPE_COMPANY)->where('status', AppConstants::ACCEPTED)
            ->orderBy('orderindex', 'desc')->orderBy('id', 'desc')->get();
        $companies = $companies->map(function($com){
            return [
                'id' => $com->id,
                'title' => $com->title,
                'cover' => image_url($com->cover, null, null, true),
            ];
        })->toArray();

        $result = [
            'error' => false,
            'info' => $info,
            'banners' => $banners,
            'articles' => $new_categories,
            'products' => $products,
            'companies' => $companies,
        ];
        return response()->json($result);
    }

    /**
     * 添加访客记录
     */
    public function visit()
    {
        $model_type = request('type');
        $model_id = intval(request('id'));
        $title = request('title');
        $ip = request('ip');
        $areas = request('areas');
        $device = request('device');
        $agent = request('agent');

        if (!in_array($model_type, VisitRecord::getTypeKeys())) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::VISIT_TYPE_ERROR]);
        }
        if (empty($title)) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::VISIT_TITLE_ERROR]);
        }

        $record = new VisitRecord();
        $record->member_id = $this->member->id;
        $record->model_type = $model_type;
        $record->model_id = $model_id;
        $record->title = $title;
        $record->ip = $ip;
        $record->areas = $areas;
        $record->device = $device;
        $record->agent = $agent;
        $record->save();

        $record->updateViews();

        return response()->json(['error'=>false]);
    }

    /**
     * 返回推荐商户信息
     */
    public function merchant()
    {
        $member_id = request('id');
        $member = Member::find($member_id);
        if ($member == null || $member->group_id <= 0) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }
        
        $member = [
            'id' => $member->id,
            'name' => $member->name,
            'avatar' => image_url($member->avatar, null, null, true),
            'company' => $member->company,
        ];
        return response()->json(['error'=>false, 'info'=>$member]);
    }

    /**
     * 全局设置信息
     */
    public function setting()
    {
        $values = [
            'trail-days' => config('site.probation', 0),
            'reg_price' => config('site.reg_price', 0),
            'renew_price' => config('site.renew_price', 0),
            'order_rate' => config('site.order_rate', 0),
            'withdraw_money' => config('site.withdraw_money', 0),
            'withdraw_money_fee' => config('site.withdraw_money_fee', 0),
            'withdraw_file' => image_url(config('copyright.withdraw_content'), null, null, true),
            'copyright' => [
                'service_url' => route('api.copyright', ['type'=>'service']),
                'member_url' => route('api.copyright', ['type'=>'member']),
                'private_url' => route('api.copyright', ['type'=>'private']),
            ],
        ];

        return response()->json(['error'=>false, 'setting'=>$values]);
    }
    
    /**
     * 小程序登录
     * @param string code
     * @return Array 
     */
    public function login()
    {
        request()->setTrustedProxies([env('PROXY_IP')]);
        $ip = request()->getClientIp();
        $areas = IPService::find($ip);

        $return_jsons = function($member, $token)use($ip, $areas){
            return [
                'id' => $member->id,
                'access_token' => $token,
                'ip' => $ip,
                'areas' => is_array($areas) ? implode(',', $areas) : $areas,
                'expires_in' => auth('api')->factory()->getTTL() * 120,
                'has_userinfo' => $member->logged,
                'is_merchant' => $member->group_id > 0,
            ];
        };

        if (config('app.debug') && intval(request('mock')) == 1) {  //测试版模拟登录
            $id = intval(request('id'));
            if ($id <= 0) $id=1;
            $member = Member::where('id', $id)->orderBy('id', 'desc')->first();
            if ($member == null) {
                return response()->json(['error'=>true, 'code'=>ApiErrorCode::OPENID_ERROR]);
            }
            $token = auth('api')->login($member);
            
            $json = $return_jsons($member, $token);
            $json['error'] = false;

            return response()->json($json);
        }
        
        $code = request('code');
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='. config('site.mini_appid') .'&secret='. config('site.mini_secret') .'&js_code='. $code .'&grant_type=authorization_code';
        $result = app('remotecatch')->catchJson($url);
        if ($result == null || isset($result['errcode'])) {
            //\Log::info('openid error:' . json_encode($result, JSON_UNESCAPED_UNICODE));
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::OPENID_ERROR]);
        }

        $openid = $result['openid'];
        $member = Member::where('openid', $openid)->first();
        if ($member != null) {
            $token = auth('api')->login($member);
            if ($token) {
                $member->last_login = time();
                $member->last_ip = $ip;
                $member->last_area = implode(',', $areas);
                $member->save();

                $this->member = $member;
                $this->update();
                MemberLoginRecord::create(['member_id'=>$member->id, 'ip'=>$ip, 'areas'=>implode(',',$areas)]);

                $json = $return_jsons($member, $token);
                $json['error'] = false;

                return response()->json($json);
            }
        } else {
            $member = new Member();
            $member->openid = $openid;
            $member->nickname = uniqid() . rand(0, 99);
            $member->username = '';
            $member->name = '';
            $member->avatar = '';
            $member->avatar_source = '';
            $member->gender = 0;
            $member->group_id = 0;
            $member->last_login = time();
            $member->last_ip = $ip;
            $member->last_area = implode(',', $areas);
            $member->parent_id = 0;
            $member->parent_path = '';
            $member->userinfo = [];
            $member->logged = false;
            $member->lat = '';
            $member->lng = '';
            $member->save();

            $token = auth('api')->login($member);
            MemberLoginRecord::create(['member_id'=>$member->id, 'ip'=>$ip, 'areas'=>implode(',',$areas)]);

            $json = $return_jsons($member, $token);
            $json['error'] = false;

            return response()->json($json);
        }

        return response()->json(['error'=>true, 'code'=>ApiErrorCode::OPENID_ERROR]);
    }
    
    /**
     * 更新微信用户信息
     * @param Array userinfo
     */
    public function update()
    {
        $userinfo = request('userinfo');
        if (!is_array($userinfo)) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::PARAM_ERROR]);
        }
        
        $member = $this->member;
        $member->nickname = isset($userinfo['nickName']) ? $userinfo['nickName'] : uniqid().rand(0,99);
        $member->name = isset($userinfo['nickName']) ? $userinfo['nickName'] : '';
        $member->avatar = isset($userinfo['avatarUrl']) ? app('remotecatch')->catchImage($userinfo['avatarUrl']) : '';
        $member->avatar_source = isset($userinfo['avatarUrl']) ? $userinfo['avatarUrl'] : '';
        $member->gender = isset($userinfo['gender']) ? $userinfo['gender'] : 0;
        $member->userinfo = $userinfo;
        $member->logged = true;
        $member->save();
        
        return response()->json(['error'=>false]);
    }

    /**
     * 发送验证码
     */
    public function sendCode()
    {
        $phone = request('phone');
        if ($phone == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::PHONE_EMPTY_ERROR]);
        }
        $member = Member::where('username', $phone)->first();
        if ($member != null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::PHONE_EXISTS_ERROR]);
        }

        $code = new PhoneCode();
        $code->member_id = $this->member->id;
        $code->phone = $phone;
        $code->code = $code->randCode();
        $code->used_time = 0;
        $code->save();

        $sms = new AliSms();
        $sms->sendCode($phone, $code->code);
        return response()->json(['error'=>false]);
    }

    /**
     * 会员手机注册 
     */
    public function register()
    {
        $mchid = intval(request('mchid'));
        $phone = request('phone');
        $code = request('code');
        $industry_id = intval(request('industry_id'));

        if ($code == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::PHONE_CODE_ERROR]);
        }
        $member = Member::where('username', $phone)->first();
        if ($member != null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::PHONE_EXISTS_ERROR]);
        }

        $industry = Industry::where('id', $industry_id)->with('parent')->first();
        if ($industry == null || $industry->parent == null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::PHONE_INDUSTRY_ERROR]);
        }
        
        $code_item = PhoneCode::where('member_id', $this->member->id)->where('phone', $phone)->where('code', $code)->where('used_time', 0)->orderBy('id', 'desc')->first();
        if ($code_item == null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::PHONE_CODE_ERROR]);
        }
        $code_item->used_time = $code_item->freshTimestamp()->timestamp;
        $code_item->save();
        
        \Log::info('用户注册');
        \Log::info('推荐人id:' . $mchid);
        $member = $this->member;
        $member->parent_id = 0;
        $member->parent_path = '';
        $parent = Member::find($mchid);
        if ($parent != null && $parent->group_id > 0 && $member->id != $parent->id) {  //会员注册时需要有上级用户形成下线关系
            $member->parent_path = $parent->parent_path != '' ? $parent->parent_path . ',' . $parent->id : $parent->id;
            $member->parent_id = $mchid;
        } else {
            $member->parent_id = config('site.default_mchid', 1);
            $member->parent_path = $member->parent_id;
        }
        $member->phone = $phone;
        $member->username = $phone;
        $probation = intval(config('site.probation', 3));
        if ($probation > 0) {
            $member->group_id = MemberGroup::NORMAL_GROUP;
            $member->proxy_first_time = strtotime($member->freshTimestamp()->format('Y-m-d 00:00:00'));
            $member->proxy_start_time = $member->proxy_first_time;
            $member->proxy_end_time = $member->proxy_start_time + $probation * 3600 * 24;
        }
        $member->probation = true;
        $member->industry = $industry->parent_id . ',' . $industry->id;
        $member->reg_date = $member->freshTimestamp();
        $member->save();

        //debug模式下更新 注册完成后默认生成一条198的订单
        if (config('app.debug') && env('VIRTUAL_ORDER', false)) {
            $order = new Order();
            $order->member_id = $this->member->id;
            $order->merchant_id = 0;
            $order->sn = Order::buildSn();
            $order->type = Order::TYPE_REG;
            $order->title = '会员注册';
            $order->price = config('site.reg_price', 198);
            $order->balance = 0;
            $order->online_balance = $order->price;
            $order->status = Order::STATUS_NEW;
            $order->content = [];
            $order->name = $this->member->name;
            $order->phone = $this->member->phone;
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

            $member->probation = false;
            $member->save();
        }

        return response()->json(['error'=>false]);
    }

    /**
     * 行业分类
     */
    public function industry()
    {
        $industries = Industry::where('hide', false)->orderBy('parent_id', 'asc')->orderBy('orderindex', 'desc')->orderBy('id', 'asc')->get();
    
        $result = [];
        foreach($industries as $industry) {
            if ($industry->parent_id == 0) {
                $result[$industry->id] = [
                    'id' => $industry->id,
                    'title' => $industry->title,
                    'childs' => [],
                ];
            } else if (isset($result[$industry->parent_id])) {
                $result[$industry->parent_id]['childs'][] = [
                    'id' => $industry->id,
                    'title' => $industry->title,
                ];
            }
        }
        
        return response()->json(['error'=>false, 'list'=>array_values($result)]);
    }

    /**
     * 个性标签点赞同
     */
    public function tagLike()
    {
        $tagid = intval(request('tagid'));
        $tag = MemberTag::find($tagid);
        if ($tag == null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }
        $liked = MemberTagLike::where('member_id', $this->member->id)->where('tag_id', $tag->id)->first();
        if ($liked == null) {
            $liked = new MemberTagLike();
            $liked->member_id = $this->member->id;
            $liked->tag_id = $tag->id;
            $liked->save();

            $tag->likes = MemberTagLike::where('tag_id', $tag->id)->count();
            $tag->save();
        }
        
        return response()->json(['error'=>false, 'count'=>$tag->likes]);
    }
    
    /**
     * 返回地区数据
     */
    public function regions()
    {
        $str = file_get_contents(base_path('resources/city.txt'));

        $regions = json_decode($str, true);

        return response()->json(['error'=>false, 'regions' => $regions]);
    }

    /**
     * 用户协议
     */
    public function copyright()
    {
        $type = request('type');
        if (in_array($type, ['service', 'member', 'private']) == false) {
            $type = 'service';
        }

        $content = config('copyright.' . $type . '_content', '');
        return view('copyright', ['content'=>$content]);
    }

    public function codeSearch()
    {
        if (config('app.debug') == false) return '';
        $phone = request('phone');
        $item = PhoneCode::where('phone', $phone)->where('used_time', 0)->orderBy('id', 'desc')->get();
        $str = '';
        foreach($item as $_item) {
            $str .= $_item['phone'] . "\t" . $_item['code'] . "\n";
        }
        echo '<xmp>';
        print_r($str);
        echo '</xmp>';
    }
}
