<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Models\MemberFriend;
use App\Models\FriendTimeline;
use App\Models\Industry;
use App\Models\Article;

/**
 * 会员动态
 */
class TimelineController extends BaseController
{
    public function index()
    {
        $memberid = intval(request('memberid'));
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));
        
        if ($memberid <= 0) $memberid = $this->member->id;

        $friend_ids = MemberFriend::where('member_id', $memberid)->get()->pluck('friend_id')->toArray();
        if (empty($friend_ids)) {   //未关注过其它用户
            return response()->json(['error'=>false, 'list'=>[], 'count'=>0, 'pages'=>1]);
        }

        $cursor = FriendTimeline::whereIn('member_id', $friend_ids);
        $count = $cursor->count();
        $list = $cursor->with('member')->orderBy('id', 'desc')->paginate($pagesize);
        $pages = $list->lastPage();

        $industry_ids = $list->pluck('member.industry')->toArray();
        $industry_ids = explode(',', implode(',', $industry_ids));
        $industries = [];
        if (!empty($industry_ids)) {
            $industries = Industry::whereIn('id', array_unique($industry_ids))->get()->getDictionary();
        }
        
        $model_ids = $list->map(function($line){
            if ($line->model_type == 'article'){
                return $line->model_id;
            }
            return 0;
        })->toArray();
        $articles = [];
        if (count($model_ids) > 0) {
            $articles = Article::whereIn('id', $model_ids)->get()->getDictionary();
        }

        $lines = $list->map(function($line)use($industries, $articles){
            $member = $line->member;
            $_industry = [];
            $industry_ids = explode(',', $member->industry);
            if (!empty($industry_ids)) {
                foreach($industry_ids as $_id) {
                    if (isset($industries[$_id])) {
                        $_industry[] = $industries[$_id]->title;
                    }
                }
            }
            $_article = isset($articles[$line->model_id]) ? $articles[$line->model_id] : null;
            return [
                'member' => [
                    'id' => $member->id,
                    'nickname' => $member->nickname,
                    'name' => $member->name,
                    'avatar' => image_url($member->avatar, null, null, true),
                    'industry' => $_industry,
                ],
                'id' => $_article != null ? $_article->id : 0,
                'type' => $_article != null ? $_article->type : $line->model_type,
                'title' => $_article != null ? $_article->title : '已删除',
                'cover' => image_url($line->cover, null, null, true),
                'video' => $_article != null && $_article->video != '' ? media_url($_article->video) : '',
                'content' => $line->content,
                'created' => $line->created_at->timestamp,
            ];
        })->toArray();
        return response()->json(['error'=>false, 'list'=>$lines, 'count'=>$count, 'pages'=>$pages]);
    }
}
