<?php

use Illuminate\Database\Seeder;

use App\Models\Industry;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $industries = [
            [
                'title' => '商务-营销',
                'childs' => ['销售', '房地产', '保险', '金融证券']
            ],
            [
                'title' => '生活-服务',
                'childs' => ['食品/饮料', '零售批发', '餐饮/酒店', '家政/安保']
            ],
            [
                'title' => '技术-制造',
                'childs' => ['技工/工人', '贸易/物流', '机械/设备', '建筑/装修']
            ]
        ];

        foreach($industries as $item) {
            $main = new Industry();
            $main->parent_id = 0;
            $main->title = $item['title'];
            $main->save();

            foreach($item['childs'] as $_item) {
                $child = new Industry();
                $child->parent_id = $main->id;
                $child->title = $_item;
                $child->save();
            }
        }
    }
}
