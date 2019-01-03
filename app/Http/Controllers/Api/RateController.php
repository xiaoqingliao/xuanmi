<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Models\ApiErrorCode;
use App\Models\Rate;
use App\Models\Order;

/**
 * 评价接口
 */
class RateController extends BaseController
{
    /**
     * 读取评价列表
     */
    public function index()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));
        $model_type = request('model');
        $model_id = intval(request('id'));

        $cursor = Rate::where('model_type', $model_type)->where('model_id', $model_id);
        $count = $cursor->count();
        $rates = $cursor->orderBy('score', 'desc')->orderBy('id', 'desc')->paginate($pagesize);
        $pages = $rates->lastPage();

        $rates = $rates->map(function($rate){
            return [
                'id' => $rate->id,
                'member' => [
                    'id' => $rate->member->id,
                    'nickname' => $rate->member->nickname,
                    'avatar' => image_url($rate->member->avatar),
                ],
                'score' => $rate->score,
                'content' => $rate->content,
                'created' => $rate->created_at->timestamp,
            ];
        })->toArray();
        return response()->json(['error'=>false, 'list'=>$rates, 'count'=>$count, 'pages'=>$pages]);
    }
    
    /**
     * 发布评价
     */
    public function rate($id)
    {
        $order = Order::find($id);
        if ($order == null || $order->member_id != $this->member->id) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }

        $items = request('items');
        if (!is_array($items)) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::PARAM_ERROR]);
        }

        \DB::beginTransaction();
        try {
            $order->rate_time = $order->freshTimestamp()->timestamp;
            $order->save();

            foreach($items as $item) {
                if (isset($item['type']) && isset($item['id']) && isset($item['score']) && isset($item['content'])) {
                    $rate = new Rate();
                    $rate->member_id = $this->member->id;
                    $rate->model_type = $item['type'];
                    $rate->model_id = intval($item['id']);
                    $rate->score = intval($item['score']);
                    $rate->content = $item['content'];
                    $rate->save();

                    $rate->updateScore();
                }
            }
            \DB::commit();
        } catch(\Exception $e) {
            \DB::rollBack();
            \Log::info($e);
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::RATE_ERROR]);
        }

        return response()->json(['error'=>false]);
    }
}
