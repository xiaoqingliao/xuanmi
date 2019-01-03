<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Models\ApiErrorCode;
use App\Models\AppConstants;
use App\Models\Course;
use App\Models\Category;
use App\Models\FriendTimeline;

/**
 * 课程接口
 */
class CourseController extends BaseController
{
    /**
     * 课程列表
     */
    public function index()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));
        $member_id = intval(request('mchid'));
        $category_id = intval(request('category_id'));
        if ($member_id <= 0) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEMBER_ERROR]);
        }

        $cursor = Course::where('member_id', $member_id)->where('status', AppConstants::ACCEPTED);
        if ($category_id > 0) {
            $cursor->where('category_id', $category_id);
        }
        $count = $cursor->count();
        $courses = $cursor->orderBy('orderindex', 'desc')->orderBy('id', 'desc')->paginate($pagesize);
        $pages = $courses->lastPage();

        $courses = $courses->map(function($course){
            return [
                'id' => $course->id,
                'title' => $course->title,
                'cover' => image_url($course->cover, null, null, true),
                'price' => $course->price,
                'market_price' => $course->market_price,
                'score' => $course->score,
                'views' => $course->views,
                'buy_number' => $course->buy_number,
                'onsale' => $course->onsale,
            ];
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$courses, 'count'=>$count, 'pages'=>$pages]);
    }

    /**
     * 相关课程目录
     */
    public function catalog($id)
    {
        $course = Course::find($id);
        if ($course == null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }

        $related_course_ids = explode(',', $course->relate_courses);
        $courses = Course::where('member_id', $this->member->id)->whereIn('id', $related_course_ids)->get();
        $courses = $courses->map(function($course){
            return [
                'id' => $course->id,
                'title' => $course->title,
                'cover' => image_url($course->cover, null, null, true),
                'price' => $course->price,
                'market_price' => $course->market_price,
                'score' => $course->score,
                'views' => $course->views,
                'buy_number' => $course->buy_number,
                'onsale' => $course->onsale,
            ];
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$courses]);
    }

    /**
     * 我的课程
     */
    public function my()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));
        $category_id = intval(request('category_id'));
        $key = request('key');

        $cursor = Course::where('member_id', $this->member->id);
        if ($category_id > 0) {
            $cursor->where('category_id', $category_id);
        }
        if ($key != '') {
            $cursor->where('title', 'like', '%'. $key .'%');
        }
        
        $count = $cursor->count();
        $courses = $cursor->orderBy('orderindex', 'desc')->orderBy('id', 'asc')->paginate($pagesize);
        $pages = $courses->lastPage();

        $courses = $courses->map(function($course){
            return [
                'id' => $course->id,
                'title' => $course->title,
                'cover' => image_url($course->cover, null, null, true),
                'price' => $course->price,
                'market_price' => $course->market_price,
                'score' => $course->score,
                'views' => $course->views,
                'buy_number' => $course->buy_number,
                'status' => $course->status,
            ];
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$courses, 'count'=>$count, 'pages'=>$pages]);
    }

    /**
     * 添加课程
     */
    public function store()
    {
        $course = new Course();
        $course->member_id = $this->member->id;
        $course->category_id = intval(request('categoryid'));
        $course->title = request('title');
        $course->cover = request('cover');
        $banners = request('banners', '');
        if (is_array($banners)) $banners = implode(',', $banners);
        $course->banners = $banners;
        $course->video = request('video', '');
        $content = request('content');
        if (is_array($content) == false) $content = json_decode($content, true);
        if (empty($content)) $content = [];
        $course->content = $content;
        $course->price = floatval(request('price'));
        $course->market_price = floatval(request('market_price'));
        $start_time = strtotime(request('start_time'));
        $end_time = strtotime(request('end_time'));
        if ($start_time === false) $start_time = 0;
        if ($end_time === false) $end_time = 0;
        $course->discount_start_time = $start_time;
        $course->discount_end_time = $end_time;
        $course->status = AppConstants::ACCEPTED;
        $course->extensions = request('extensions', []);
        $course->times = intval(request('times'));
        $course->base_clicks = intval(request('base_clicks'));
        $course->relate_courses = request('relate_courses', '');

        if ($course->title == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::COURSE_TITLE_ERROR]);
        }
        if ($course->cover == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::COURSE_COVER_ERROR]);
        }
        if ($course->content == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::COURSE_CONTENT_ERROR]);
        }

        $course->save();

        //发布动态
        $line = new FriendTimeline();
        $line->member_id = $course->member_id;
        $line->model_type = 'course';
        $line->model_id = $course->id;
        $line->title = $course->title;
        $line->cover = $course->cover;
        $line->content = '发布课程';
        $line->save();

        $line->searchWeightIncrement(config('site.course_weight', 2));

        return response()->json(['error'=>false]);
    }

    /**
     * 更新课程
     */
    public function update($id)
    {
        $course = Course::find($id);
        if ($course == null || $course->member_id != $this->member->id) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }

        $course->category_id = intval(request('categoryid'));
        $course->title = request('title');
        $course->cover = request('cover');
        $banners = request('banners', '');
        if (is_array($banners)) $banners = implode(',', $banners);
        $course->banners = $banners;
        $course->video = request('video', '');
        $content = request('content');
        if (is_array($content) == false) $content = json_decode($content, true);
        if (empty($content)) $content = [];
        $course->content = $content;
        $course->price = floatval(request('price'));
        $course->market_price = floatval(request('market_price'));
        $start_time = strtotime(request('start_time'));
        $end_time = strtotime(request('end_time'));
        if ($start_time === false) $start_time = 0;
        if ($end_time === false) $end_time = 0;
        $course->discount_start_time = $start_time;
        $course->discount_end_time = $end_time;
        $course->extensions = request('extensions', []);
        $course->times = intval(request('times'));
        $course->base_clicks = intval(request('base_clicks'));
        $course->relate_courses = request('relate_courses', '');

        if ($course->title == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::COURSE_TITLE_ERROR]);
        }
        if ($course->cover == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::COURSE_COVER_ERROR]);
        }
        if ($course->content == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::COURSE_CONTENT_ERROR]);
        }

        $course->save();
        return response()->json(['error'=>false]);
    }

    /**
     * 删除课程
     */
    public function destroy($id)
    {
        $course = Course::find($id);
        if ($course != null && $course->member_id == $this->member->id) {
            FriendTimeline::where('member_id', $course->member_id)->where('model_type', 'course')->where('model_id', $course->id)->delete();
            $course->delete();
        }
        return response()->json(['error'=>false]);
    }

    /**
     * 课程详情
     */
    public function show($id)
    {
        $course = Course::find($id);
        if ($course == null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }

        return response()->json(['error'=>false, 'course'=>$course->toArrayShow()]);
    }

    /**
     * 更新排序
     */
    public function updateOrder()
    {
        $items = request('items');
        if (!is_array($items)) $items = [];

        foreach($items as $item) {
            if (isset($item['id']) && isset($item['index'])) {
                Course::where('id', intval($item['id']))->where('member_id', $this->member->id)->update(['orderindex'=>intval($item['index'])]);
            }
        }
        return response()->json(['error'=>false]);
    }
}
