<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Http\Controllers\Sys;

use App\Models\AdminLog;
use App\Models\Course;

/**
 * 课程管理
 */
class CourseController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->setCurrentMenu('course:list');
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
        $cursor = Course::query();
        if ($filters['key'] != '' && in_array($filters['search_field'], ['title', 'content'])) {
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
            $cursor->orderBy('times', 'asc')->orderBy('id', 'desc');
            break;
            case 'time_desc':
            $cursor->orderBy('times', 'desc')->orderBy('id', 'desc');
            break;
            default:
            $cursor->orderBy('id', 'desc');
            break;
        }
        $courses = $cursor->with('member', 'category')->paginate($pagesize);

        $values = [
            'courses' => $courses,
            'filters' => $filters,
            'start' => ($page - 1) * $pagesize,
        ];
        AdminLog::addLog($this->user->id, 'list', 'courses', null, '查看课程列表');
        return view('course.index', $values);
    }

    public function destroy($id)
    {
        $course = Course::find($id);
        if ($course != null) {
            $course->delete();

            AdminLog::addLog($this->user->id, 'delete', 'courses', $course);
            session()->flash('message', '删除成功');
        }
        return response()->json(['error'=>false]);
    }
}
