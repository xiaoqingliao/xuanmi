@extends('layouts.sys')
@section('header')
    @parent
@stop
@section('main-content')
<?php
    Form::macro('editor', function($name, $value, $options){
        $options['name'] = $name;
        $options['type'] = 'text/plain';
        $options = $this->html->attributes($options);
        $value = $this->getValueAttribute($name, $value);
        return $this->toHtmlString('<script '. $options .'>'. $value .'</script>');
    });
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-11"><h5>服务协议设置</h5></div>
                    <div class="col-sm-1">
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#service" aria-controls="home" role="tab" data-toggle="tab">服务协议</a></li>
                    <li role="presentation"><a href="#member" aria-controls="home" role="tab" data-toggle="tab">会员协议</a></li>
                    <li role="presentation"><a href="#private" aria-controls="home" role="tab" data-toggle="tab">隐私协议</a></li>
                    <li role="presentation"><a href="#withdraw" aria-controls="home" role="tab" data-toggle="tab">提现协议</a></li>
                </ul>
            @if (session('message'))
            <div class="alert alert-warning">{{ session('message') }}</div>
            @endif
            <form action="" method="post" class="form-horizontal" role="form">
                {!! method_field('POST') !!}
                {!! csrf_field() !!}
                {!! Form::hidden('category', $category) !!}
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="service">
                    {!! Form::editor('service_content', $setting->getParam('service_content'), ['id'=>'editor1', 'style'=>'height:500px;']) !!}
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="member">
                    {!! Form::editor('member_content', $setting->getParam('member_content'), ['id'=>'editor2', 'style'=>'height:500px;']) !!}
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="private">
                    {!! Form::editor('private_content', $setting->getParam('private_content'), ['id'=>'editor3', 'style'=>'height:500px;']) !!}
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="withdraw">
                        {!! Form::text('withdraw_content', $setting->getParam('withdraw_content'), ['class'=>'form-control img']) !!}
                    </div>
                </div>
                <div class="col-sm-8 col-sm-offset-2" style="padding-top:20px;">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> 保存</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
@stop
@section('script')
{!! Html::script('assets/sys/js/bootstrap.min.js') !!}
<script type="text/javascript" src="{{ asset('assets/sys/ueditor/ueditor.config.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/sys/ueditor/ueditor.all.js') }}"></script>
<script type="text/javascript">
$(function(){
    var ue1 = UE.getEditor('editor1');
    var ue2 = UE.getEditor('editor2');
    var ue3 = UE.getEditor('editor3');
    $('input.img').imageText({multiple:false});
});
</script>
@stop
