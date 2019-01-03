<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Sys;

use App\Models\AdminLog;
use App\Models\Order;

/**
 * 订单管理
 */
class OrderController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->page = intval(request('page', 1));
        $this->pagesize = 20;
        $this->filters = [
            'sn' => request('sn'),
            'start_date' => request('start_date'),
            'end_date' => request('end_date'),
        ];
    }

    /**
     * 注册订单
     */
    public function reglist()
    {
        $export = intval(request('export'));
        $cursor = Order::where('type', Order::TYPE_REG)->where(function($q){
            return $q->where('status', Order::STATUS_PAYED)->orWhere('status', Order::STATUS_FINISHED);
        });
        $cursor->search($this->filters);
        if ($export) {
            return $this->_export_excel('注册订单', $cursor);
        }
        $orders = $cursor->with('member')->orderBy('id', 'desc')->paginate($this->pagesize);
        
        $values = [
            'orders' => $orders,
            'filters' => $this->filters,
            'start' => ($this->page - 1) * $this->pagesize,
        ];
        $this->setCurrentMenu('order:reg');
        return view('order.reg', $values);
    }

    /**
     * 续费订单
     */
    public function renewlist()
    {
        $export = intval(request('export'));
        $cursor = Order::where('type', Order::TYPE_RENEW)->where(function($q){
            return $q->where('status', Order::STATUS_PAYED)->orWhere('status', Order::STATUS_FINISHED);
        });;
        $cursor->search($this->filters);
        if ($export) {
            return $this->_export_excel('续费订单', $cursor);
        }
        $orders = $cursor->with('member')->orderBy('id', 'desc')->paginate($this->pagesize);
        
        $values = [
            'orders' => $orders,
            'filters' => $this->filters,
            'start' => ($this->page - 1) * $this->pagesize,
        ];
        $this->setCurrentMenu('order:renew');
        return view('order.renew', $values);
    }

    /**
     * 会议/课程订单
     */
    public function index()
    {
        $cursor = Order::where('type', Order::TYPE_OTHER);
        $cursor->search($this->filters);
        $orders = $cursor->with('member', 'merchant')->orderBy('id', 'desc')->paginate($this->pagesize);

        $values = [
            'orders' => $orders,
            'filters' => $this->filters,
            'start' => ($this->page - 1) * $this->pagesize,
        ];
        $this->setCurrentMenu('order:index');
        return view('order.index', $values);
    }

    /**
     * 订单详情
     */
    public function show($id)
    {
        $order = Order::find($id);
        if ($order == null) {
            return back()->with('message', '订单不存在');
        }

        $values = [
            'order' => $order,
            'texts' => [
                'reg' => '注册',
                'renew' => '续费',
                'other' => '会议/课程',
            ],
        ];
        $this->setCurrentMenu('order:' . $order->typeKey);
        return view('order.show', $values);
    }

    private function _export_excel($title, $cursor)
    {
        $list = $cursor->with('member')->orderBy('id', 'desc')->get();
        \Excel::create(date('Y-m-d') . $title, function($excel)use($list){
            $excel->sheet('sheet', function($sheet)use($list){
                $sheet->setWidth([
                    'A' => 10,
                    'B' => 25,
                    'C' => 25,
                    'D' => 20,
                    'E' => 30,
                    'F' => 20,
                    'G' => 20,
                    'H' => 20,
                ]);
                $sheet->row(1, ['#', '订单号', '标题', '昵称', '姓名', '公司', '手机', '时间', '金额']);
                foreach($list as $idx=>$item) {
                    $sheet->appendRow([
                        $idx + 1,
                        $item->sn . ' ',
                        $item->title,
                        $item->member->nickname,
                        $item->member->name,
                        $item->member->company,
                        $item->member->phone,
                        $item->created_at->format('Y-m-d H:i'),
                        $item->price,
                    ]);
                }
            });
        })->download('xlsx');
    }
}
