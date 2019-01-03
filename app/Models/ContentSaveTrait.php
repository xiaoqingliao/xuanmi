<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Models;

trait ContentSaveTrait
{
    /**
     * 保存处理content
     */
    public function save(Array $values=[])
    {
        $content = $this->content;
        if (is_array($content) == false) {
            $content = [];
        }

        $server = $_SERVER['HTTP_HOST'];

        $new_contents = [];
        foreach($content as $item) {
            if (is_string($item)) {
                $new_contents[] = ['type'=>'text', 'content'=>$item];
                continue;
            }
            if (isset($item['type']) == false) continue;
            if ($item['type'] == 'text') {
                $val = $item['content'];
                $tmpArr = explode("\n", $val);
                foreach($tmpArr as $_text) {
                    if (!empty($_text)) {
                        $new_contents[] = [
                            'type' => 'text',
                            'content' => $_text,
                        ];
                    }
                }
            } else if ($item['type'] == 'image') {
                $url = image_replace($item['image']);
                $new_contents[] = ['type'=>'image', 'image'=>$url];
            }
        }
        $this->content = $new_contents;
        $this->cover = image_replace($this->cover);
        parent::save();
    }
}