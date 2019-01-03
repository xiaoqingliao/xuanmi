<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
* 文件上传，图片上传
*/
namespace App\Http\Controllers\Sys;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UploadTrait;
use App\Models\Resource;
use GuzzleHttp\Client as GuzzleClient;

class FileController extends Controller
{
    use UploadTrait;
    //图片列表
    public function images()
    {
        $key = request('key');
        $cursor = Resource::where('category', 'image')->where('status', 'normal');
        if ($key != '') {
            $cursor->where('title', 'like', '%'. $key .'%');
        }
        $images = $cursor->orderBy('id', 'desc')->paginate(16);
        $data = [];
        foreach($images as $image){
            $data[] = [
                'id' => $image->id,
                'title' => $image->title,
                'path' => $image->path,
                'url' => image_url($image->path),
                'preview' => image_url($image->path, 150, 150),
            ];
        }
        return response()->json(['error'=>false, 'list'=>$data, 'totalpage'=>$images->lastPage()]);
    }

    //ueditor设置
    public function ueditor()
    {
        $action = request('action');
        if ($action == 'config') {
            $value = [ 
                'imageActionName' => 'uploadimage',
                'imageFieldName' => 'file',
                'imageMaxSize' => 2048000,
                'imageAllowFiles' => ['.jpg', '.png', '.gif', '.jpeg'],
                'imageCompressEnable' => true,
                'imageCompressBorder' => 1600,
                'imageInsertAlign' => 'none',
                'imageUrlPrefix' => '',
                'imagePathFormat' => '',
                'catchRemoteImageEnable' => false,
                'catcherLocalDomain' => [],
                'catcherActionName' => 'catchimage',
                'catcherFieldName' => 'file',
                'catcherUrlPrefix' => '',
            ];
            return \Response::json($value);
        } else if ($action == 'uploadimage') {
            $result = $this->image();
            $result = $result->getData(true);
            $value = [
                'state' => 'SUCCESS',
                'url' => $result['url'],
                'title' => $result['original'],
                'original' => $result['original'],
                'type' => $result['mime'],
                'size' => $result['size'],
            ];
            return \Response::json($value);
        } else if ($action == 'catchimage') {
            $files = request('file');
            $list = [];
            $folder = date('Ymd');
            $client = new GuzzleClient();
            foreach($files as $_url) {
                if (empty($_url)) continue;
                $res = $client->get($_url);
                $file_content = '';
                if ($res->getStatusCode() == 200) {
                    $file_content = $res->getBody();
                }
                $_info = pathinfo($_url);
                $ext = 'png';
                if (isset($_info['extension']) && $_info['extension'] != '') {
                    $ext = $_info['extension'];
                }
                if (!empty($file_content)) {
                    $new_filename = time() . uniqid() . '.' . $ext;
                    $new_path = $folder . '/' . $new_filename;
                    upload_save($file_content, $new_path);
                    $list[] = [
                        'state' => 'SUCCESS',
                        'url' => image_url($new_path),
                        'title' => '',
                        'original' => '',
                        'source' => $_url,
                    ];
                } else {
                    $list[] = [
                        'state' => 'SUCCESS',
                        'url' => assets('content/img/remoteerror.jpg'),
                        'title' => '',
                        'original' => '',
                        'source' => $_url,
                    ];
                }
            }
            $value = [
                'state' => 'SUCCESS',
                'list' => $list,
            ];
            return response()->json($value);
        }
    }

    //文件上传
    public function upload()
    {
        $type = request('filetype');
        if ($type == 'image'){
            return $this->image();
        } else if ($type == 'video'){
            return $this->video();
        } else if ($type == 'audio'){
            return $this->audio();
        } else {
            return $this->files();
        }
    }
}
