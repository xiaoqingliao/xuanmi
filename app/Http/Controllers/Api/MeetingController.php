<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Models\ApiErrorCode;
use App\Models\AppConstants;
use App\Models\Category;
use App\Models\Meeting;
use App\Models\MeetingSku;
use App\Models\MeetingSponsor;
use App\Models\FriendTimeline;

/**
 * 会议接口
 */
class MeetingController extends BaseController
{
    /**
     * 会议列表
     */
    public function index()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));
        $member_id = intval(request('mchid'));
        $category_id = intval(request('categoryid'));

        if ($member_id <= 0) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEMBER_ERROR]);
        }

        $cursor = Meeting::where('member_id', $member_id)->where('status', AppConstants::ACCEPTED);
        if ($category_id > 0) {
            $cursor->where('category_id', $category_id);
        }
        $count = $cursor->count();
        $meetings = $cursor->orderBy('orderindex', 'desc')->orderBy('id', 'desc')->paginate($pagesize);
        $pages = $meetings->lastPage();

        $meetings = $meetings->map(function($meeting){
            $_gps = null;
            if (!empty($meeting->lat) && !empty($meeting->lng)) {
                $_gps = [
                    'lat' => $meeting->lat,
                    'lng' => $meeting->lng,
                ];
            }
            return [
                'id' => $meeting->id,
                'title' => $meeting->title,
                'cover' => image_url($meeting->cover, null, null, true),
                'start_time' => $meeting->start_time->timestamp,
                'end_time' => $meeting->end_time->timestamp,
                'province' => $meeting->province,
                'city' => $meeting->city,
                'area' => $meeting->area,
                'address' => $meeting->address,
                'gps' => $_gps,
                'price' => $meeting->price,
                'buy_number' => $meeting->buy_number,
                'views' => $meeting->views,
            ];
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$meetings, 'count'=>$count, 'pages'=>$pages]);
    }

    /**
     * 会议分类
     */
    public function category()
    {
        $categories = Category::where('type', Category::TYPE_MEETING)->orderBy('orderindex', 'desc')->orderBy('id', 'asc')->get();
        $categories = $categories->map(function($category){
            return [
                'id' => $category->id,
                'title' => $category->title,
            ];
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$categories]);
    }

    /**
     * 我的会议
     */
    public function my()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));
        $category_id = intval(request('categoryid'));

        $cursor = Meeting::where('member_id', $this->member->id);
        if ($category_id > 0) {
            $cursor->where('category_id', $category_id);
        }
        $count = $cursor->count();
        $meetings = $cursor->orderBy('orderindex', 'desc')->orderBy('id', 'desc')->paginate($pagesize);
        $pages = $meetings->lastPage();

        $meetings = $meetings->map(function($meeting){
            return [
                'id' => $meeting->id,
                'title' => $meeting->title,
                'cover' => $meeting->cover,
                'time' => $meeting->start_time->timestamp,
                'province' => $meeting->province,
                'city' => $meeting->city,
                'area' => $meeting->area,
                'address' => $meeting->address,
                'price' => $meeting->price,
                'buy_number' => $meeting->buy_number,
                'orderindex' => $meeting->orderindex,
                'status' => $meeting->status,
            ];
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$meetings, 'count'=>$count, 'pages'=>$pages]);
    }

    /**
     * 添加会议
     */
    public function store()
    {
        $meeting = new Meeting();
        $meeting->member_id = $this->member->id;
        $meeting->category_id = intval(request('categoryid'));
        $meeting->title = request('title');
        $meeting->cover = request('cover');
        $banners = request('banners', '');
        if (is_array($banners)) $banners = implode(',', $banners);
        $meeting->banners = $banners;
        $meeting->video = request('video', '');
        $meeting->content = request('content');
        $start_time = strtotime(request('start_time'));
        $end_time = strtotime(request('end_time'));
        if ($start_time === false) $start_time = 0;
        if ($end_time === false) $end_time = 0;

        $meeting->province = request('province');
        $meeting->city = request('city');
        $meeting->area = request('area', '');
        $meeting->address = request('address');
        $meeting->lat = request('lat', '');
        $meeting->lng = request('lng', '');
        $meeting->status = AppConstants::ACCEPTED;
        $meeting->extensions = request('extensions', []);
        $meeting->base_clicks = intval(request('base_clicks'));

        $skus = request('skus');
        $sku_items = [];
        foreach($skus as $idx=>$_item) {
            if (!isset($_item['title']) || $_item['title'] == '' || !isset($_item['price'])) continue;
            $_sku_item = new MeetingSku();
            $_sku_item->title = $_item['title'];
            $_sku_item->price = floatval($_item['price']);
            $_sku_item->market_price = floatval($_item['price']);
            $sku_items[] = $_sku_item;
            if ($idx == 0) {
                $meeting->price = $_sku_item->price;
            }
        }
        
        $sponor_ids = request('sponor_ids');
        if (is_array($sponor_ids)) $sponor_ids = implode(',', $sponor_ids);
        $meeting->sponor_ids = $sponor_ids;
        
        if ($meeting->title == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_TITLE_ERROR]);
        }
        if ($meeting->cover == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_COVER_ERROR]);
        }
        if ($meeting->content == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_CONTENT_ERROR]);
        }
        if ($start_time <= 0 || $end_time <= 0) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_TIME_ERROR]);
        }
        if ($meeting->province == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_PROVINCE_ERROR]);
        }
        if ($meeting->city == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_CITY_ERROR]);
        }
        if ($meeting->address == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_ADDRESS_ERROR]);
        }
        if (count($sku_items) <= 0) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_SKU_ERROR]);
        }
        $meeting->start_time = date('Y-m-d H:i:s', $start_time);
        $meeting->end_time = date('Y-m-d H:i:s', $end_time);

        \DB::beginTransaction();
        try {
            $meeting->save();
            foreach($sku_items as $_item){
                $_item->meeting_id = $meeting->id;
                $_item->save();
            }
            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();
            \Log::info($e);
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_SAVE_ERROR]);
        }

        //发布动态
        $line = new FriendTimeline();
        $line->member_id = $meeting->member_id;
        $line->model_type = 'meeting';
        $line->model_id = $meeting->id;
        $line->title = $meeting->title;
        $line->cover = $meeting->cover;
        $line->content = '发布会议';
        $line->save();

        $this->member->searchWeightIncrement(config('site.meeting_weight', 2));

        return response()->json(['error'=>false]);
    }

    /**
     * 更新会议
     */
    public function update($id)
    {
        $meeting = Meeting::find($id);
        if ($meeting == null || $meeting->member_id != $this->member->id) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }

        $meeting->category_id = intval(request('categoryid'));
        $meeting->title = request('title');
        $meeting->cover = request('cover');
        $banners = request('banners', '');
        if (is_array($banners)) $banners = implode(',', $banners);
        $meeting->banners = $banners;
        $meeting->video = request('video', '');
        $meeting->content = request('content');
        $start_time = strtotime(request('start_time'));
        $end_time = strtotime(request('end_time'));
        if ($start_time === false) $start_time = 0;
        if ($end_time === false) $end_time = 0;

        $meeting->province = request('province');
        $meeting->city = request('city');
        $meeting->area = request('area', '');
        $meeting->address = request('address');
        $meeting->lat = request('lat', '');
        $meeting->lng = request('lng', '');
        $meeting->status = AppConstants::ACCEPTED;
        $meeting->extensions = request('extensions', []);
        $meeting->base_clicks = intval(request('base_clicks'));

        $old_sku_items = [];
        $_items = MeetingSku::where('meeting_id', $meeting->id)->get();
        foreach($_items as $_item) {
            $old_sku_items[$_item->id] = $_item;
        }
        $skus = request('skus');
        $sku_items = [];
        foreach($skus as $idx=>$_item) {
            if (!isset($_item['title']) || $_item['title'] == '' || !isset($_item['price'])) continue;

            if (isset($_item['id']) && isset($old_sku_items[$_item['id']])) {
                $_sku_item = $old_sku_items[$_item['id']];
                unset($old_sku_items[$_item['id']]);
            } else {
                $_sku_item = new MeetingSku();
            }
            $_sku_item->title = $_item['title'];
            $_sku_item->price = floatval($_item['price']);
            $_sku_item->market_price = floatval($_item['price']);
            $sku_items[] = $_sku_item;
            if ($idx == 0) {
                $meeting->price = $_sku_item->price;
            }
        }

        $sponor_ids = request('sponor_ids');
        if (is_array($sponor_ids)) $sponor_ids = implode(',', $sponor_ids);
        $meeting->sponor_ids = $sponor_ids;
        
        if ($meeting->title == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_TITLE_ERROR]);
        }
        if ($meeting->cover == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_COVER_ERROR]);
        }
        if ($meeting->content == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_CONTENT_ERROR]);
        }
        if ($start_time <= 0 || $end_time <= 0) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_TIME_ERROR]);
        }
        if ($meeting->province == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_PROVINCE_ERROR]);
        }
        if ($meeting->city == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_CITY_ERROR]);
        }
        if ($meeting->address == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_ADDRESS_ERROR]);
        }
        if (count($sku_items) <= 0) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_SKU_ERROR]);
        }
        $meeting->start_time = date('Y-m-d H:i:s', $start_time);
        $meeting->end_time = date('Y-m-d H:i:s', $end_time);

        \DB::beginTransaction();
        try {
            $meeting->save();
            foreach($sku_items as $_item){
                $_item->meeting_id = $meeting->id;
                $_item->save();
            }
            if (count($old_sku_items) > 0) {
                $old_sku_ids = array_keys($old_sku_items);
                MeetingSku::whereIn('id', $old_sku_ids)->delete();
            }
            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();
            \Log::info($e);
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEETING_SAVE_ERROR]);
        }

        return response()->json(['error'=>false]);
    }

    /**
     * 删除会议
     */
    public function destroy($id)
    {
        $meeting = Meeting::find($id);
        if ($meeting != null && $meeting->member_id == $this->member->id) {
            FriendTimeline::where('member_id', $meeting->member_id)->where('model_type', 'meeting')->where('model_id', $meeting->id)->delete();
            $meeting->delete();
        }
        return response()->json(['error'=>false]);
    }

    /**
     * 会议详情
     */
    public function show($id)
    {
        $meeting = Meeting::find($id);
        if ($meeting == null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }

        return response()->json(['error'=>false, 'meeting'=>$meeting->toArrayShow()]);
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
                Meeting::where('id', intval($item['id']))->where('member_id', $this->member->id)->update(['orderindex'=>intval($item['index'])]);
            }
        }
        
        return response()->json(['error'=>false]);
    }
}
