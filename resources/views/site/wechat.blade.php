@extends('layouts.sys')
@section('header')
    @parent
@stop
@section('main-content')
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-11"><h5>公众号参数设置</h5></div>
                    <div class="col-sm-1">
                    </div>
                </div>
            </div>
            <div class="panel-body">
            @if (session('message'))
            <div class="alert alert-warning">{{ session('message') }}</div>
            @endif
            <form action="" method="post" class="form-horizontal" role="form">
                {!! method_field('POST') !!}
                {!! csrf_field() !!}
                <div class="form-group">
                	<label for="appid" class="col-sm-2 control-label">小程序APPID</label>
                    <div class="col-sm-8">
                        {!! Form::text('xapp_id', $setting->getParam('xapp_id'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="appsecret" class="col-sm-2 control-label">小程序APPSecret</label>
                    <div class="col-sm-8">
                        {!! Form::text('xsecret', $setting->getParam('xsecret'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-8 col-sm-offset-2">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> 保存</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
@stop
@section('script')
<script type="text/javascript">
$(function(){
    $('input.img').imageText({
        multiple:false
    });
});
</script>
@stop
