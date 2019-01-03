<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Models\ApiErrorCode;
use App\Models\Notice;

/**
 * 通知管理
 */
class NoticeController extends BaseController
{
    /**
     * 通知列表
     */
    public function index()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));

        $cursor = Notice::where('member_id', $this->member->id);
        $count = $cursor->count();
        $notices = $cursor->orderBy('id', 'desc')->paginate($pagesize);
        $pages = $notices->lastPage();

        $notices = $notices->map(function($notice){
            return [
                'id' => $notice->id,
                'title' => $notice->title,
                'content' => $notice->content,
                'readed' => $notice->read_time > 0,
                'created' => $notice->created_at->timestamp,
            ];
        })->toArray();

        $cursor->where('read_time', 0)->update(['read_time'=>time()]);

        return response()->json(['error'=>false, 'list'=>$notices, 'count'=>$count, 'pages'=>$pages]);
    }

    /**
     * 删除通知
     */
    public function destroy($id)
    {
        $notice = Notice::find($id);
        if ($notice != null && $notice->member_id == $this->member->id) {
            $notice->delete();
        }
        return response()->json(['error'=>false]);
    }

    /**
     * 清除通知
     */
    public function empty()
    {
        Notice::where('member_id', $this->member->id)->delete();
        return response()->json(['error'=>false]);
    }
}
