<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UploadTrait;
use QrCode;
use App\Models\Article;
use App\Services\VideoModService;

class FileController extends Controller
{
    public function qrcode()
    {
        $url = request('string');
        $size = intval(request('size'));
        $download = intval(request('download'));
        $title = request('title');
        if ($size <= 0) $size = 100;
        
        if ($title == '') $title = md5($url);

        $title .= '_' . $size;

        $qr = QrCode::format('png')->size($size)->encoding('UTF-8')->generate($url);
        
        if ($download) {
            $datetime = mktime(date('H'),date('i'),date('s'),date('n'),date('j'),date('Y') + 1);
            $headers = [
                'Content-Type' => "image/png",
                'Content-Length' => strlen($qr),
                'Content-Disposition' => 'attachment;filename=' . $title . '.png',
                'Access-Control-Allow-Origin' => '*'
            ];
            return response($qr, 200, $headers);
        }

        return $qr;
    }
    
    //图片
    public function imageShow($dir, $path, $size='')
    {
        $path = $dir . '/' . $path;
        $file = upload_file($path);
        if ($file == null){
            return abort(404, 'image not found');
        }

        //图片处理
        if ($size != ''){
            $ext = $file['extension'];
            $arr = explode('_', $size);
            $width = $height = null;
            if (count($arr) == 2){
                $width = intval(substr($arr[0], 1));
                $height = intval(substr($arr[1], 1));
                $path .= '_' . $width . 'x' . $height . '.' . $ext;
            } else if (count($arr) == 1){
                if (substr($arr[0], 0, 1) == 'w'){
                    $width = intval(substr($arr[0], 1));
                    $path .= '_' . $width . '.' . $ext;
                } else {
                    $height = intval(substr($arr[0], 1));
                    $path .= '_' . $height . '.' . $ext;
                }
            } else {
                abort(404, 'file not found');
            }

            $resize_file = upload_file($path);
            if ($resize_file == null){
                $img = \Image::make($file['filepath']);
                $src_width = $img->width();
                $src_height = $img->height();

                if ($width == null && $height != null) {
                    $width = intval($src_width * $height / $src_height);
                    if ($width > $src_width) $width = $src_width;
                } else if ($height == null && $width != null) {
                    $height = intval($src_height * $width / $src_width);
                    if ($height > $src_height) $height = $src_height;
                }
                $min_width = min($src_width, $width);
                $min_height = min($src_height, $height);
                if ($min_width > $min_height) {
                    $img->resize($min_width, null, function($constraint){
                        $constraint->aspectRatio();
                    });
                } else {
                    $img->resize(null, $min_height, function($constraint){
                        $constraint->aspectRatio();
                    });
                }
                $img->crop($min_width, $min_height);
                if ($min_width < $width || $min_height < $height) {
                    $img->resizeCanvas($width, $height);
                }
                
                $img->save(upload_path() . $path);
                $resize_file = upload_file($path);
            }
            $file = $resize_file;
        }

        $filesize = $file['size'];
        $last_modify = $file['lastModified'];
        $mime_type = $file['mimeType'];
        $etag = md5($last_modify);
        $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : '';
        if ($if_none_match == $etag) {
            $datetime = mktime(date('H'),date('i'),date('s'),date('n'),date('j'),date('Y') + 1);
            $headers = [
                'Cache-Control:private, max-age=31536000',  //一年
                'Expires:' . date('D, d M Y H:i:s', $datetime) . ' GMT',
                'ETag:' . $etag,
                'Access-Control-Allow-Origin' => '*',
            ];
            return abort(304, '', $headers);
        }

        $datetime = mktime(date('H'),date('i'),date('s'),date('n'),date('j'),date('Y') + 1);
        $headers = [
            'Cache-Control: private, max-age=31536000',
            'Expires:' . date('D, d M Y H:i:s', $datetime) . ' GMT',
            'ETag:' . $etag,
            'Content-Type' => $file['mimeType'],
            'Content-Length' => $file['size'],
            'Access-Control-Allow-Origin' => '*'
        ];
        return response($file['content'], 200, $headers);
    }
    
    //多媒体文件
    public function media($dir, $path)
    {
        $path = $dir . '/' . $path;
        $file = upload_file($path);
        if ($file == null){
            return abort(404, 'media not found');
        }

        $filesize = $file['size'];
        $last_modify = $file['lastModified'];
        $mime_type = $file['mimeType'];
        $etag = md5($last_modify);
        $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : '';
        if ($if_none_match == $etag) {
            $datetime = mktime(date('H'),date('i'),date('s'),date('n'),date('j'),date('Y') + 1);
            $headers = [
                'Cache-Control:private, max-age=31536000',  //一年
                'Expires:' . date('D, d M Y H:i:s', $datetime) . ' GMT',
                'ETag:' . $etag,
                'Access-Control-Allow-Origin' => '*',
            ];
            return abort(304, '', $headers);
        }

        $datetime = mktime(date('H'),date('i'),date('s'),date('n'),date('j'),date('Y') + 1);
        $headers = [
            'Cache-Control: private, max-age=31536000',
            'Expires:' . date('D, d M Y H:i:s', $datetime) . ' GMT',
            'ETag:' . $etag,
            'Content-Type' => $file['mimeType'],
            'Content-Length' => $file['size'],
            'Access-Control-Allow-Origin' => '*'
        ];
        return response($file['content'], 200, $headers);
    }
    
    //视频截图文件
    public function cover($dir, $path) {
        //没有视频截图，需要临时截图
        if ($dir[0] == '!') {
            $dir = substr($dir, 1);
            //截图
            $videopath = $dir . '/' . $path;
            $videofile = upload_path($videopath);
            
            $videoext = pathinfo($videofile, PATHINFO_EXTENSION);
            $pngfile = str_replace($videoext, 'png', $videofile);
            
            VideoModService::getCover($videofile, $pngfile);
            
            //存储到数据库
            $path = str_replace($videoext, 'png', $videopath);
            $articleId = Article::where('video', $videopath)->value('id');
            Article::where('id', $articleId)->update(['cover'=>$path]);
            
            $p = explode('/', $path);
            $dir = $p[0];
            $path = $p[1];
        }
        //显示图片
        return $this->imageShow($dir, $path);
    }
    
    //文件下载
    public function download($dir, $path)
    {
        $path = $dir . '/' . $path;
        $file = upload_file($path);
        if ($file == null){
            return abort(404, 'file not found');
        }

        $filesize = $file['size'];
        $last_modify = $file['lastModified'];
        $mime_type = $file['mimeType'];
        $etag = md5($last_modify);
        $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : '';
        if ($if_none_match == $etag) {
            $datetime = mktime(date('H'),date('i'),date('s'),date('n'),date('j'),date('Y') + 1);
            $headers = [
                'Cache-Control:private, max-age=31536000',  //一年
                'Expires:' . date('D, d M Y H:i:s', $datetime) . ' GMT',
                'ETag:' . $etag,
                'Access-Control-Allow-Origin' => '*',
            ];
            return abort(304, '', $headers);
        }

        $datetime = mktime(date('H'),date('i'),date('s'),date('n'),date('j'),date('Y') + 1);
        $headers = [
            'Cache-Control: private, max-age=31536000',
            'Expires:' . date('D, d M Y H:i:s', $datetime) . ' GMT',
            'ETag:' . $etag,
            'Content-Type' => $file['mimeType'],
            'Content-Length' => $file['size'],
            'Access-Control-Allow-Origin' => '*'
        ];
        return response($file['content'], 200, $headers);
    }
}
