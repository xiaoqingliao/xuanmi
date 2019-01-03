<?php
namespace App\Http\Controllers;

use App\Models\Resource;

trait UploadTrait{
    //图片上传
    private function image($savetoresource=true) {
        $allow_ext = array('jpg', 'jpeg', 'png', 'gif');
        $allow_mime = array(
            'image/gif',
            'image/jpeg',
            'image/jpg',
            'image/png'
        );

        $result = $this->_upload('file', $allow_ext, $allow_mime);
        $result['url'] = image_url($result['path'], null, null, true);
        $result['preview'] = image_url($result['path'], 150, 150);

        if ($savetoresource && $result['error'] == false) {
            $resource = new Resource();
            $resource->title = $result['original'];
            $resource->path = $result['path'];
            $resource->source_path = $result['path'];
            $resource->size = $result['size'];
            $resource->category = 'image';
            $resource->status = 'normal';
            list($width, $height, $type, $attr) = getimagesize(upload_path() . $result['path']);
            $resource->width = $width;
            $resource->height = $height;
            $resource->mimetype = $result['mime'];
            $resource->tag = '';
            $resource->save();
        }
        return response()->json($result);
    }

    //视频上传
    private function video($savetoresource=true) {
        $allow_ext = array('mp4', 'mov', 'mpeg', 'mpg');
        $allow_mime = array(
            'video/mp4',
            'video/quicktime',
            'video/mpeg'
        );
        $result = $this->_upload('file', $allow_ext);
        if ($savetoresource && $result['error'] == false) {
            $resource = new Resource();
            $resource->title = $result['original'];
            $resource->path = $result['path'];
            $resource->source_path = $result['path'];
            $resource->size = $result['size'];
            $resource->category = 'video';
            $resource->status = 'normal';
            $resource->mimetype = $result['mime'];
            $resource->tag = '';
            $resource->width = 0;
            $resource->height = 0;
            $resource->save();
        }
        return response()->json($result);
    }

    //视频上传
    private function audio($savetoresource=true) {
        $allow_ext = array('mp3', 'wav');
        $allow_mime = array(
            'audio/mp3',
            'audio/wav',
        );
        $result = $this->_upload('file', $allow_ext);
        if ($savetoresource && $result['error'] == false) {
            $resource = new Resource();
            $resource->title = $result['original'];
            $resource->path = $result['path'];
            $resource->source_path = $result['path'];
            $resource->size = $result['size'];
            $resource->category = 'audio';
            $resource->status = 'normal';
            $resource->mimetype = $result['mime'];
            $resource->tag = '';
            $resource->width = 0;
            $resource->height = 0;
            $resource->save();
        }
        return response()->json($result);
    }

    //文件上传
    private function files($savetoresource=true) {
        $allow_ext = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'wps', 'rar', 'zip', '7z', 'cert', 'pem');
        $allow_mime = array(
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/kswps',
            'application/x-rar-compressed',
            'application/zip',
            'application/x-7z-compressed',
            'application/octet-stream',
            'application/*',
        );
        $result = $this->_upload('file', $allow_ext);
        if ($savetoresource && $result['error'] == false) {
            $resource = new Resource();
            $resource->title = $result['original'];
            $resource->path = $result['path'];
            $resource->source_path = $result['path'];
            $resource->size = $result['size'];
            $resource->category = 'file';
            $resource->status = 'normal';
            $resource->mimetype = $result['mime'];
            $resource->tag = '';
            $resource->width = 0;
            $resource->height = 0;
            $resource->save();
        }
        return response()->json($result);
    }

    private function _upload($upload_name, $allow_ext, $allow_mime=array()) {
        $upload_file = request_file($upload_name);
        if ($upload_file == null) {
            return ['error'=>true, 'msg'=>'未选择上传文件'];
        }
        $filename = $upload_file->getClientOriginalName();
        $ext = strtolower($upload_file->getClientOriginalExtension());
        $size = $upload_file->getSize();
        $mime = $upload_file->getMimeType();

        if (!in_array($ext, $allow_ext)) {
            return array('error'=>true, 'msg'=>'非法文件' . $ext. '/' . implode(',', $allow_ext));
        }

        if (count($allow_mime) > 0 && !in_array($mime, $allow_mime) && $ext != 'cert') {
            return array('error'=>true, 'msg'=>'非法文件' . $mime . '/' . implode(',', $allow_mime));
        }

        $new_filename = time() . uniqid() . '.' .$ext;
        $new_path = date('Ymd') . '/' . $new_filename;
        upload_save($upload_file, $new_path);

        return [
            'error'=>false,
            'path'=>$new_path,
            'title' => $new_filename,
            'original' => $filename,
            'url' => download_url($new_path),
            'type' => $ext,
            'size' => $size,
            'mime' => $mime,
        ];
    }
}
