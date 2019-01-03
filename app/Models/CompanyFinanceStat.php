<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Models;

/**
 * 公司财务基础统计
 */
class CompanyFinanceStat
{
    private $items = [];
    private $file = '';

    public function __construct()
    {
        $this->items = [
            'income' => 0,  //总收入
            'expend' => 0,  //总支出
            'time' => 0,    //最近统计时间
        ];
        $this->file = base_path('storage/app/stat.json');

        $this->initFromFile();
    }

    public function __get($key)
    {
        if ($key == 'gain') {
            return $this->items['income'] - $this->items['expend'];
        }
        return isset($this->items[$key]) ? $this->items[$key] : 0;
    }

    private function initFromFile()
    {
        $file = $this->file;
        if (file_exists($file) == false) {
            $this->calcs(0);
        } else {
            $this->items = json_decode(file_get_contents($file), true);
            $this->calcs($this->items['time']);
        }
    }

    /**
     * 统计数据
     */
    private function calcs()
    {
        $start_time = $this->items['time'];
        $end_time = time();

        $income = CompanyFinanceLog::income($start_time, $end_time);
        $expend = CompanyFinanceLog::expend($start_time, $end_time);
        
        $items = $this->items;
        $items['income'] += $income;
        $items['expend'] += $expend;
        $items['time'] = $end_time;
        $this->items = $items;

        file_put_contents($this->file, json_encode($this->items, JSON_UNESCAPED_UNICODE));
    }
}