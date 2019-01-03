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
                    <div class="col-sm-11"><h5>公司信息设置</h5></div>
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
                {!! Form::hidden('category', $category) !!}
                <div class="form-group">
                	<label for="name" class="col-sm-2 control-label">公司名称</label>
                    <div class="col-sm-8">
                        {!! Form::text('title', $setting->getParam('title'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="name" class="col-sm-2 control-label">简介</label>
                    <div class="col-sm-8">
                        {!! Form::text('summary', $setting->getParam('summary'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="name" class="col-sm-2 control-label">公司品牌</label>
                    <div class="col-sm-8">
                        {!! Form::text('brand', $setting->getParam('brand'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="name" class="col-sm-2 control-label">网址</label>
                    <div class="col-sm-8">
                        {!! Form::text('website', $setting->getParam('website'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="ghid" class="col-sm-2 control-label">联系电话</label>
                    <div class="col-sm-8">
                        {!! Form::text('phone', $setting->getParam('phone'), ['class'=>'form-control']) !!}
                        <div class="alert alert-success" style="margin-bottom:0;padding:5px;">多个电话间请用逗号(,)分隔开</div>
                    </div>
                </div>
                <div class="form-group">
                	<label for="ghid" class="col-sm-2 control-label">微信号</label>
                    <div class="col-sm-8">
                        {!! Form::text('wechat', $setting->getParam('wechat'), ['class'=>'form-control']) !!}
                        <div class="alert alert-success" style="margin-bottom:0;padding:5px;">多个微信呈间请用逗号(,)分隔开</div>
                    </div>
                </div>
                <div class="form-group">
                	<label for="ghid" class="col-sm-2 control-label">公司LOGO</label>
                    <div class="col-sm-8">
                        {!! Form::text('logo', $setting->getParam('logo'), ['class'=>'form-control img']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="ghid" class="col-sm-2 control-label">公司地址</label>
                    <div class="col-sm-8">
                        {!! Form::text('address', $setting->getParam('address'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="ghid" class="col-sm-2 control-label">公司坐标</label>
                    <div class="col-sm-8">
                        {!! Form::hidden('lng', $setting->getParam('lng')) !!}
                        {!! Form::hidden('lat', $setting->getParam('lat')) !!}
                        <div class="lnglat">
                        @if ($setting->getParam('lng') != '' && $setting->getParam('lat') != '')
                        当前坐标：{{ $setting->getParam('lng') }},{{ $setting->getParam('lat') }}
                        @endif
                        </div>
                        <a href="#" class="btn btn-lnglat btn-success">选择坐标</a>
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

    $('.btn-lnglat').click(function(){
        var lat = $('input[name=lat]').val();
        var lng = $('input[name=lng]').val();
        var addr = $('input[name=address]').val();
        seajs.use('map', function(){
            dialog.pickup({
                address: addr,
                lat: lat,
                lng: lng,
                callback:function(new_lat, new_lng){
                    $('input[name=lat]').val(new_lat);
                    $('input[name=lng]').val(new_lng);
                    $('.lnglat').html('当前坐标：' + new_lng + ',' + new_lat);
                }
            });
        });
    });
});
</script>
@stop
