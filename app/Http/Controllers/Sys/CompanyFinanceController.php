<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Http\Controllers\Sys;

use App\Models\CompanyFinanceLog;
use App\Models\CompanyFinanceStat;

/**
 * 公司财务日志
 */
class CompanyFinanceController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->setCurrentMenu('company:finance');
    }
    
    public function index()
    {
        $page = intval(request('page', 1));
        $pagesize = 20;
        $filters = [
            'start_date' => request('start_date'),
            'end_date' => request('end_date'),
            'type' => intval(request('type')),
        ];
        $export = intval(request('export'));

        $cursor = CompanyFinanceLog::query();
        if ($filters['start_date'] != '') {
            $cursor->where('created_at', '>=', $filters['start_date']);
        }
        if ($filters['end_date'] != '') {
            $cursor->where('created_at', '<=', $filters['end_date']);
        }
        if ($filters['type'] > 0) {
            $cursor->where('type', $filters['type']);
        }
        if ($export) {
            return $this->_export_excel($cursor);
        }
        $logs = $cursor->with('member')->orderBy('id', 'desc')->paginate($pagesize);

        $values = [
            'logs' => $logs,
            'filters' => $filters,
            'types' => [
                CompanyFinanceLog::TYPE_ADD => '收入',
                CompanyFinanceLog::TYPE_SUB => '支出',
            ],
            'start' => ($page - 1) * $pagesize,
            'stat' => new CompanyFinanceStat(),
        ];
        return view('finance.index', $values);
    }

    public function postIndex()
    {
        $data = request()->all();
        unset($data['_token']);
        unset($data['_method']);

        return redirect()->route('sys.finance.index', $data);
    }

    private function _export_excel($cursor)
    {
        $list = $cursor->with('member')->orderBy('id', 'desc')->get();
        \Excel::create(date('Y-m-d') . '公司财务日志', function($excel)use($list){
            $excel->sheet('sheet', function($sheet)use($list){
                $sheet->setWidth([
                    'A' => 10,
                    'B' => 20,
                    'C' => 20,
                    'D' => 30,
                    'E' => 20,
                    'F' => 10,
                    'G' => 20,
                    'H' => 40,
                    'I' => 20,
                ]);
                $sheet->row(1, ['#', '昵称', '姓名', '公司', '电话', '类型', '金额', '备注', '时间']);
                foreach($list as $idx=>$item){
                    $sheet->appendRow([
                        $idx + 1,
                        $item->member->nickname,
                        $item->member->name,
                        $item->member->company,
                        $item->member->phone,
                        $item->type == CompanyFinanceLog::TYPE_ADD ? '+' : '-',
                        $item->price,
                        $item->remark,
                        $item->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });
        })->download('xlsx');
    }
}
