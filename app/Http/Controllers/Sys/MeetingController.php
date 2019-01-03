<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Http\Controllers\Sys;

use App\Models\AdminLog;
use App\Models\Meeting;

/**
 * 会议管理
 */
class MeetingController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->setCurrentMenu('meeting:list');
    }

    public function index()
    {
        $page = intval(request('page', 1));
        $pagesize = 20;

        $filters = [
            'search_field' => request('search_field'),
            'key' => request('key'),
            'order' => request('order'),
        ];
        $cursor = Meeting::query();
        if ($filters['key'] != '' && in_array($filters['search_field'], ['title', 'content', 'address'])) {
            $cursor->where($filters['search_field'], 'like', '%'. $filters['key'] .'%');
        }
        switch($filters['order']) {
            case 'price_asc':
            $cursor->orderBy('price', 'asc')->orderBy('id', 'desc');
            break;
            case 'price_desc':
            $cursor->orderBy('price', 'desc')->orderBy('id', 'desc');
            break;
            case 'time_asc':
            $cursor->orderBy('start_time', 'asc')->orderBy('id', 'desc');
            break;
            case 'time_desc':
            $cursor->orderBy('start_time', 'desc')->orderBy('id', 'desc');
            break;
            default:
            $cursor->orderBy('id', 'desc');
            break;
        }
        $meetings = $cursor->with('member', 'category')->paginate($pagesize);

        $values = [
            'meetings' => $meetings,
            'filters' => $filters,
            'start' => ($page - 1) * $pagesize,
        ];
        AdminLog::addLog($this->user->id, 'list', 'meetings', null, '查看会议列表');
        return view('meeting.index', $values);
    }

    public function destroy($id)
    {
        $meeting = Meeting::find($id);
        if ($meeting == null) {
            $meeting->delete();

            AdminLog::addLog($this->user->id, 'delete', 'meetings', $meeting);
            session()->flash('message', '删除成功');
        }
        return response()->json(['error'=>false]);
    }
}
