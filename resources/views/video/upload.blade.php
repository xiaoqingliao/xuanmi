<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>上传demo</title>
    {!! Html::script('assets/front/js/jquery.min.js') !!}
    {!! Html::script('https://res.wx.qq.com/open/js/jweixin-1.3.2.js') !!}
    {!! Html::script('alioss/lib/aliyun-oss-sdk.js') !!}
    {!! Html::script('alioss/aliyun-upload-sdk.js') !!}
    <style>
    .row{
        padding:5px;
        border-bottom:1px solid #ccc;
    }
    </style>
</head>
<body>
    <input type="file" name="file" id="files" accept="video/*" /><button id="start">开始上传</button>
    <div id="progress"></div>
    <div id="logs"></div>
</body>
</html>
<script>
$(function(){
    var userData = '{"Vod":{}}';
    var credentials = {!! json_encode($credentials, true) !!};
    var logs = $('#logs');

    if (window.wx) {
        logs.append('<div class="row">weixin ready</div>');
    } else {
        logs.append('<div class="row">weixin error</div>');
    }

    var uploader = new AliyunUpload.Vod({
        //partSize: 1048576,
        parallel: 5,
        retryCount: 2,
        retryDuration: 2,
        onUploadstarted: function(uploadInfo){
            console.log(uploadInfo);
            uploader.setSTSToken(uploadInfo, credentials.AccessKeyId, credentials.AccessKeySecret, credentials.SecurityToken);
            $('#progress').text('正在上传，0%');
            logs.append('<div class="row">开始上传:' + JSON.stringify(uploadInfo) + '</div>');
        },
        onUploadSucceed: function(uploadInfo) {
            $('#progress').text('上传成功');
            console.log(uploadInfo + '<br />');
            logs.append('<div class="row">上传成功：' + JSON.stringify(uploadInfo) + '</div>');
            if (windowwx.miniProgram) {
                windowwx.miniProgram.postMessage(uploadInfo);
                logs.append('<div class="row">已通知小程序</div>');
            } else {
                logs.append('<div class="row">小程序通知失败</div>');
            }
        },
        onUploadFailed: function(uploadInfo, code, message) {
            $('#progress').text('上传失败，code:' + code + ' 原因：' + message);
            console.log(uploadInfo);
            logs.append('<div class="row">上传失败, code:' + code + ' 原因：' + message + '</div>');
        },
        onUploadProgress: function(uploadInfo, totalSize, loadedPercent) {
            $('#progress').text('上传进度：' + Math.ceil(loadedPercent * 100) + '%');
            console.log(uploadInfo, totalSize, loadedPercent);
        },
        onUploadTokenExpired: function(uploadInfo) {
            uploader.resumeUploadWithSTSToken(credentials.AccessKeyId, credentials.AccessKeySecret, credentials.SecurityToken, credentials.Expiration);
            logs.append('<div class="row">上传token刷新</div>');
        },
        onUploadEnd: function() {
            //$('#progress').text('上传完成');
            console.log('上传完成', uploadInfo);
            logs.append('<div class="row">上传结束</div>');
            $('#start').removeClass('disabled');
        }
    });
    
    $('#files').change(function(e){
        if (e.target.files.length <= 0) return;
        var file = e.target.files[0];
        file.Title = file.name;
        uploader.addFile(file, null, null, null, userData);
        console.log(file);
        logs.append('<div class="row">文件添加成功:' + JSON.stringify(file) + '</div>');
    });

    $('#start').click(function(){
        if ($(this).hasClass('disabled')) return;
        $(this).addClass('disabled');
        uploader.startUpload();
        logs.append('<div class="row">开始上传</div>');
    });
});
</script>
