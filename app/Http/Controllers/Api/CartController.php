<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Models\ApiErrorCode;
use App\Models\Cart;
use App\Models\Course;
use App\Models\Meeting;
use App\Models\MeetingSku;
use App\Models\Order;
use App\Models\OrderDetail;

/**
 * 购物车
 */
class CartController extends BaseController
{
    /**
     * 我的购物车
     */
    public function index()
    {
        $carts = Cart::where('member_id', $this->member->id)->with('model', 'sku')->orderBy('updated_at', 'desc')->get();
        $meeting_ids = $carts->filter(function($cart){
            return $cart->model_type == 'meeting';
        })->map(function($cart){
            return $cart->model_id;
        })->toArray();
        $skus = [];
        if (!empty($meeting_ids)) {
            $skus = MeetingSku::whereIn('meeting_id', $meeting_ids)->orderBy('id', 'asc')->get();
        }
        $carts = $carts->filter(function($cart){
            if ($cart->model_type == 'meeting') {
                return $cart->model != null && $cart->sku != null;
            }
            return $cart->model != null;
        })->map(function($cart)use($skus){
            $model = $cart->model;
            $item = [
                'id' => $cart->id,
                'title' => $model->title,
                'type' => $cart->model_type,
                'model_id' => $cart->model_id,
                'sku_id' => $cart->sku_id,
                'cover' => image_url($model->cover, null, null, true),
                'skus' => [],
            ];
            if ($cart->model_type == 'meeting') {
                $item['price'] = $cart->sku->price;
                foreach($skus as $sku) {
                    if ($sku->meeting_id == $cart->model_id) {
                        $item['skus'][] = [
                            'id' => $sku->id,
                            'title' => $sku->title,
                            'price' => $sku->price,
                        ];
                    }
                }
            } else if ($model->model_type == 'course') {
                if ($model->onsale) {
                    $item['price'] = $model->price;
                } else {
                    $item['price'] = $model->market_price;
                }
            }
            return $item;
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$carts]);
    }
    
    /**
     * 添加到购物车
     */
    public function add()
    {
        $type = request('type');
        $id = intval(request('id'));
        $skuid = intval(request('skuid'));

        if (in_array($type, ['course', 'meeting']) == false) {
            return resposne()->json(['error'=>true, 'code'=>ApiErrorCode::CART_TYPE_ERROR]);
        }

        $detail = OrderDetail::where('member_id', $this->member->id)->where('model_type', $type)->where('model_id', $id)->where('sku_id', $skuid)->with('order')->first();
        if ($detail != null && $detail->order->status != Order::STATUS_CANCELED) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::CART_BUYED_ERROR]);
        }

        $model = null;
        $sku = null;
        if ($type == 'course') {
            $model = Course::find($id);
        } else if ($type == 'meeting') {
            $model = Meeting::find($id);
            $sku = MeetingSku::find($skuid);
        }
        if ($model == null || $model->member_id == $this->member->id) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::CART_MODEL_ERROR]);
        }
        if ($type == 'meeting' && ($sku == null || $sku->meeting_id != $model->id)) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::CART_SKU_ERROR]);
        }

        $cart = Cart::where('member_id', $this->member->id)->where('model_type', $type)->where('model_id', $id)->where('sku_id', $skuid)->first();
        if ($cart == null) {
            $cart = new Cart();
            $cart->member_id = $this->member->id;
            $cart->model_type = $type;
            $cart->model_id = $id;
            $cart->sku_id = $skuid;
            $cart->number = 1;
            $cart->save();
        }

        return response()->json(['error'=>false]);
    }

    /**
     * 更新购物车
     */
    public function update($id)
    {
        $sku_id = intval(request('skuid'));

        $cart = Cart::where('member_id', $this->member->id)->where('id', $id)->first();
        if ($cart == null || $cart->model_type != 'meeting') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }

        $sku = MeetingSku::find($sku_id);
        if ($sku == null || $sku->meeting_id != $cart->model_id) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::CART_SKU_ERROR]);
        }

        $cart->sku_id = $sku->id;
        $cart->save();

        return response()->json(['error'=>false]);
    }
    
    /**
     * 删除
     */
    public function remove()
    {
        $ids = request('ids');
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        if (!is_array($ids)) $ids = [];
        
        Cart::whereIn('id', $ids)->where('member_id', $this->member->id)->delete();
        return response()->json(['error'=>false]);
    }
}
