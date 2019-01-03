<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Models\ApiErrorCode;
use App\Models\Member;
use App\Models\Message;
use App\Models\MessageMember;

/**
 * 消息管理
 */
class MessageController extends BaseController
{
    /**
     * 消息交谈列表
     */
    public function index($memberid)
    {
        $updated = intval(request('updated'));
        $receiver = Member::find($memberid);
        if ($receiver == null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MESSAGE_MEMBER_ERROR]);
        }

        //有更新时间读取新的消息返回
        if ($updated > 0) {
            $cursor = Message::where('sender_id', $memberid)->where('receiver_id', $this->member->id)->where('created_at', '>', date('Y-m-d H:i:s', $updated));
            $msges = $cursor->orderBy('id', 'desc')->get();
            $messages = [];
            foreach($msges as $msg) {
                $messages[] = [
                    'id' => $msg->id,
                    'type' => 'receive',
                    'content' => $msg->content,
                    'created' => $msg->created_at->timestamp,
                ];
            }
            $cursor->update(['read_time'=>time()]);
            return response()->json(['error'=>false, 'list'=>array_reverse($messages), 'updated'=>time()]);
        }

        //优先返回未读取消息
        $unread_cursor = Message::where('sender_id', $memberid)->where('receiver_id', $this->member->id)->where('read_time', 0);
        $unread_msg = $unread_cursor->orderBy('id', 'desc')->get();
        if ($unread_msg->count() > 0) {
            $messages = [];
            foreach($unread_msg as $msg) {
                $messages[] = [
                    'id' => $msg->id,
                    'type' => 'receive',
                    'content' => $msg->content,
                    'created' => $msg->created_at->timestamp,
                ];
            }
            
            $unread_cursor->update(['read_time'=>time()]);
            $visitor = MessageMember::where('member_id', $this->member->id)->where('visit_member_id', $memberid)->first();
            if ($visitor != null) {
                $visitor->unread_count = 0;
                $visitor->save();
            }
            return response()->json(['error'=>false, 'list'=>array_reverse($messages), 'updated'=>time()]);
        }
        
        //没有未读消息则返回最近的5条消息
        $cursor = Message::where(function($q)use($memberid){
            return $q->where('sender_id', $memberid)->where('receiver_id', $this->member->id);
        })->orWhere(function($q)use($memberid){
            return $q->where('sender_id', $this->member->id)->where('receiver_id', $memberid);
        });
        $msges = $cursor->orderBy('id', 'desc')->take(5)->get();
        $msges = $msges->map(function($msg){
            return [
                'id' => $msg->id,
                'type' => $msg->sender_id == $this->member->id ? 'send' : 'receive',
                'content' => $msg->content,
                'created' => $msg->created_at->timestamp,
            ];
        })->toArray();
        return response()->json(['error'=>false, 'list'=>array_reverse($msges), 'updated'=>time()]);
    }

    /**
     * 发送消息
     */
    public function send($memberid)
    {
        $receiver_id = $memberid;
        $content = request('content');

        $receiver = Member::find($receiver_id);
        if ($receiver == null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MESSAGE_MEMBER_ERROR]);
        }
        if ($content == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MESSAGE_CONTENT_ERROR]);
        }

        $msg = new Message();
        $msg->sender_id = $this->member->id;
        $msg->receiver_id = $receiver_id;
        $msg->content = $content;
        $msg->read_time = 0;
        $msg->save();

        $visitor = MessageMember::where('member_id', $this->member->id)->where('visit_member_id', $receiver_id)->first();
        if ($visitor == null) {
            $visitor = new MessageMember();
            $visitor->member_id = $this->member->id;
            $visitor->visit_member_id = $receiver_id;
        }
        $visitor->content = $msg->content;
        $visitor->unread_count = 0;
        $visitor->save();

        $receiver = MessageMember::where('member_id', $receiver_id)->where('visit_member_id', $this->member->id)->first();
        if ($receiver == null) {
            $receiver = new MessageMember();
            $receiver->member_id = $receiver_id;
            $receiver->visit_member_id = $this->member->id;
        }
        $receiver->content = $msg->content;
        $receiver->unread_count++;
        $receiver->save();

        return response()->json(['error'=>false]);
    }

    /**
     * 访客消息列表
     */
    public function visitor()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));
        $key = request('key');

        $cursor = MessageMember::where('member_id', $this->member->id);
        if ($key != '') {
            $cursor->where(function($q)use ($key){
                return $q->whereHas('visitor', function($q)use($key){
                    return $q->where('name', 'like', '%'. $key .'%')->orWhere('nickname', 'like', '%'. $key .'%');
                })->orWhere('content', 'like', '%'. $key .'%');
            });
        }
        $count = $cursor->count();
        $list = $cursor->with('visitor')->orderBy('updated_at', 'desc')->paginate($pagesize);
        $pages = $list->lastPage();

        $visitors = $list->map(function($item){
            $member = $item->visitor;
            return [
                'member' => [
                    'id' => $member->id,
                    'nickname' => $member->nickname,
                    'name' => $member->name,
                    'avatar' => image_url($member->avatar, null, null, true),
                ],
                'content' => $item->content,
                'unread' => $item->unread_count,
                'updated' => $item->updated_at->timestamp,
            ];
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$visitors, 'count'=>$count, 'pages'=>$pages]);
    }
    
    /**
     * 我的未读消息数量
     */
    public function unread()
    {
        $count = MessageMember::where('member_id', $this->member->id)->sum('unread_count');
        return response()->json(['error'=>false, 'count'=>$count]);
    }
}
