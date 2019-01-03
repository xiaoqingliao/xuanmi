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

class TestController extends BaseController
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
     * 小程序登录
     * @param string code
     * @return Array 
     */
    public function login()
    {
        var_dump(666);die();
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

}
