<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Models\ApiErrorCode;
use App\Models\Banner;

/**
 * 轮播图管理
 */
class BannerController extends BaseController
{
    /**
     * 读取轮播图
     */
    public function index()
    {
        $member_id = intval(request('mchid'));
        $banners = Banner::where('member_id', $member_id)->where('model_type', Banner::TYPE_COMPANY)->orderBy('orderindex', 'desc')->orderBy('id', 'desc')->get();
        $banners = $banners->map(function($banner){
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'cover' => image_url($banner->cover, null, null, true),
                'video' => media_url($banner->video),
                'redirect' => $banner->redirect,
            ];
        })->toArray();
        
        return response()->json(['error'=>false, 'list'=>$banners]);
    }

    /**
     * 我的轮播图
     */
    public function my()
    {
        $banners = Banner::where('member_id', $this->member->id)->where('model_type', Banner::TYPE_COMPANY)->orderBy('orderindex', 'desc')->orderBy('id', 'desc')->get();
        $banners = $banners->map(function($banner){
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'cover' => image_url($banner->cover, null, null, true),
                'video' => media_url($banner->video),
                'redirect' => $banner->redirect,
            ];
        })->toArray();
        return response()->json(['error'=>false, 'list'=>$banners]);
    }

    /**
     * 添加
     */
    public function store()
    {
        $banner = new Banner();
        $banner->member_id = $this->member->id;
        $banner->model_type = Banner::TYPE_COMPANY;
        $banner->model_id = $this->member->id;
        $banner->title = request('title');
        $banner->cover = image_replace(request('cover'));
        $banner->video = image_replace(request('video', ' '));
        $banner->redirect = request('redirect', ' ');
        $banner->orderindex = 0;
        
        if ($banner->title == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::BANNER_TITLE_ERROR]);
        }
        if ($banner->cover == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::BANNER_COVER_ERROR]);
        }
        $banner->save();
        return response()->json(['error'=>false]);
    }

    /**
     * 更新
     */
    public function update($id)
    {
        $banner = Banner::find($id);
        if ($banner == null || $banner->member_id != $this->member->id) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }
        $banner->title = request('title');
        $banner->cover = image_replace(request('cover'));
        $banner->video = image_replace(request('video', ' '));
        $banner->redirect = request('redirect', '');
        
        if ($banner->title == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::BANNER_TITLE_ERROR]);
        }
        if ($banner->cover == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::BANNER_COVER_ERROR]);
        }
        $banner->save();
        return response()->json(['error'=>false]);
    }

    /**
     * 删除
     */
    public function destroy($id)
    {
        $banner = Banner::find($id);
        if ($banner != null && $banner->member_id == $this->member->id) {
            $banner->delete();
        }
        return response()->json(['error'=>false]);
    }

    /**
     * 排序
     */
    public function updateOrder()
    {
        $items = request('items');
        if (!is_array($items)) $items = json_decode($items, true);
        if (empty($items))  $items = [];

        foreach($items as $item) {
            if (isset($item['id']) && isset($item['index'])) {
                Banner::where('id', intval($item['id']))->where('member_id', $this->member->id)->update(['orderindex'=>intval($item['index'])]);
            }
        }
        return response()->json(['error'=>false]);
    }
}
