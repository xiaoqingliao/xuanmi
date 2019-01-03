<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\UploadTrait;

class FileController extends BaseController
{
    use UploadTrait;

    public function upload()
    {
        $type = request('filetype');
        if ($type == 'video'){
            return $this->video(false);
        } else if ($type == 'audio'){
            return $this->audio(false);
        }

        return $this->image(false);
    }

    public function doc()
    {
        $parse = new \Parsedown();
        $content = file_get_contents(base_path('resources/api.md'));
        $content = str_replace('{server}', $_SERVER['HTTP_HOST'], $content);

        //return $parse->text($content);
        echo '<xmp>';
        print_r($content);
    }
    
    public function log()
    {
        $log = request('file');
        if ($log == '') {
            $log = date('Y-m-d');
        }
        $file = base_path('storage/logs/laravel-' . $log . '.log');
        if (file_exists($file)) {
            echo '<xmp>';
            print_r(file_get_contents($file));
            exit;
        }
        return '日志不存在';
    }
}