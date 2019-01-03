<?php
if (!function_exists('request_file')) {
    function request_file($key)
    {
        return app('request')->file($key);
    }
}

if (! function_exists('upload_save')) {
    function upload_save($file, $new_name)
    {
        $disk = \Storage::disk('public');
        $pathinfo = pathinfo($new_name);
        $dir = $pathinfo['dirname'];
        if ($dir != '' && ! $disk->exists($dir)){
            $disk->makeDirectory($dir, 0777, true);
        }
        $file_content = '';
        if ($file instanceof SplFileInfo){
            $filepath = $file->getPathname();
            $file_content = file_get_contents($filepath);
        } else {
            $file_content = $file;
        }
        
        if ($file_content == '') return '';

        $disk->put($new_name, $file_content);
    }
}

if (! function_exists('upload_path')) {
    function upload_path($file='')
    {
        if ($file == '') {
            return storage_path('app/public') . '/';
        }
        
        return storage_path('app/public') . '/' . $file;
    }
}

if (! function_exists('upload_file')) {
    function upload_file($path)
    {
        $disk = \Storage::disk('public');
        if (! $disk->exists($path)){
            return null;
        }

        return [
            'extension' => pathinfo($path, PATHINFO_EXTENSION),
            'mimeType' => $disk->mimeType($path),
            'content' => $disk->get($path),
            'lastModified' => $disk->lastModified($path),
            'size' => $disk->size($path),
            'filepath' => upload_path() . $path,
        ];
    }
}

/**
 * 将用户提交的图片路径转换成本地相对路径
 */
if (! function_exists('image_replace')) {
    function image_replace($url)
    {
        if (empty($url)) return '';
        $server = $_SERVER['HTTP_HOST'];
        $url = str_replace(['http://' . $server . '/image/', 'https://' . $server . '/image/'], ['', ''], $url);
        return $url;
    }
}

/**
 * 将用户提交的图片路径转换成本地相对路径
 */
if (! function_exists('media_replace')) {
    function media_replace($url)
    {
        if (empty($url)) return '';
        $server = $_SERVER['HTTP_HOST'];
        $url = str_replace(['http://' . $server . '/download/', 'https://' . $server . '/download/'], ['', ''], $url);
        $url = str_replace(['http://' . $server . '/media/', 'https://' . $server . '/media/'], ['', ''], $url);
        return $url;
    }
}

if (! function_exists('image_url')) {
    function image_url($path, $width=null, $height=null, $full=false)
    {
        $path = trim($path);
        if (empty($path))
            return asset('assets/front/img/tp.png');
            
        if (starts_with($path, 'http'))
            return $path;

        $url = '/image/' . $path;
        if ($width != null && $height != null){
            $url .= "@w" . $width . '_h' . $height;
        } else if ($width != null){
            $url .= "@w" . $width;
        } else if ($height != null){
            $url .= '@h' . $height;
        }

        if ($full) {
            $server = request()->server();
            return ($server['SERVER_PORT'] == 80 ? 'http://' : 'https://') . $server['HTTP_HOST'] . $url;
        }

        return $url;
    }
}

//如果没有截图，则传入!视频path
if (! function_exists('cover_url')) {
    function cover_url($path, $width=null, $height=null, $full=false)
    {
        $path = trim($path);
        if (empty($path))
            return asset('assets/front/img/tp.png');
            
            if (starts_with($path, 'http') || starts_with($path, '!http'))
                return $path;
                
                $url = '/cover/' . $path;
                if ($width != null && $height != null){
                    $url .= "@w" . $width . '_h' . $height;
                } else if ($width != null){
                    $url .= "@w" . $width;
                } else if ($height != null){
                    $url .= '@h' . $height;
                }
                
                if ($full) {
                    $server = request()->server();
                    return ($server['SERVER_PORT'] == 80 ? 'http://' : 'https://') . $server['HTTP_HOST'] . $url;
                }
                
                return $url;
    }
}

if (! function_exists('download_url')) {
    function download_url($path) {
        if (starts_with($path, 'http') || $path == '') {
            return $path;
        }
        $server = request()->server();
        return ($server['SERVER_PORT'] == 80 ? 'http://' : 'https://') . $server['HTTP_HOST'] . '/download/' . $path;
        
        return '/download/' . $path;
    }
}

if (! function_exists('media_url')) {
    function media_url($path) {
        if (starts_with($path, 'http') || $path == '') {
            return $path;
        }
        $server = request()->server();
        return ($server['SERVER_PORT'] == 80 ? 'http://' : 'https://') . $server['HTTP_HOST'] . '/download/' . $path;
        //return '/download/' . $path;
    }
}

if (! function_exists('content_show')) {
    function content_show($contents) {
        $new_contents = [];
        foreach($contents as $item) {
            if ($item['type'] == 'image') {
                $item['image'] = image_url($item['image'], null, null, true);
            }
            $new_contents[] = $item;
        }
        return $new_contents;
    }
}

if (! function_exists('html_content')) {
    function html_content($content)
    {
        $content = preg_replace_callback('{<img(?<img>.*?)>}xm', function($d){
            return '<p style="text-align:center"><img '. $d['img'] .'></p>';
        }, $content);
        return $content;
    }
}
