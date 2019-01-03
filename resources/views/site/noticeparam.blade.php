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
                    <div class="col-sm-11"><h5>财务分成回复消息设置</h5></div>
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
                	<label for="name" class="col-sm-2 control-label">用户在线支付</label>
                    <div class="col-sm-8">
                        {!! Form::text('member_online_pay', $setting->getParam('member_online_pay', '用户充值'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="name" class="col-sm-2 control-label">用户注册财务通知</label>
                    <div class="col-sm-8">
                        {!! Form::text('member_register', $setting->getParam('member_register'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="name" class="col-sm-2 control-label">用户续费财务通知</label>
                    <div class="col-sm-8">
                        {!! Form::text('member_renew', $setting->getParam('member_renew'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="name" class="col-sm-2 control-label">用户购物消费财务通知</label>
                    <div class="col-sm-8">
                        {!! Form::text('member_pay', $setting->getParam('member_pay'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="name" class="col-sm-2 control-label">用户销售收入财务通知</label>
                    <div class="col-sm-8">
                        {!! Form::text('member_sale', $setting->getParam('member_sale'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="name" class="col-sm-2 control-label">直推用户全额分成</label>
                    <div class="col-sm-8">
                        {!! Form::text('child_new1', $setting->getParam('child_new1'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="ghid" class="col-sm-2 control-label">直推用户非全额分成</label>
                    <div class="col-sm-8">
                        {!! Form::text('child_new2', $setting->getParam('child_new2'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="ghid" class="col-sm-2 control-label">直推用户首层代理分成</label>
                    <div class="col-sm-8">
                        {!! Form::text('child_new_proxy1', $setting->getParam('child_new_proxy1'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="ghid" class="col-sm-2 control-label">直推用户次层代理分成</label>
                    <div class="col-sm-8">
                        {!! Form::text('child_new_proxy2', $setting->getParam('child_new_proxy2'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="ghid" class="col-sm-2 control-label">下级续费分成</label>
                    <div class="col-sm-8">
                        {!! Form::text('child_renew', $setting->getParam('child_renew'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="ghid" class="col-sm-2 control-label">下级用户升级代理全额分成</label>
                    <div class="col-sm-8">
                        {!! Form::text('child_proxy_upgrade1', $setting->getParam('child_proxy_upgrade1'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="ghid" class="col-sm-2 control-label">下级用户升级代理部分分成</label>
                    <div class="col-sm-8">
                        {!! Form::text('child_proxy_upgrade2', $setting->getParam('child_proxy_upgrade2'), ['class'=>'form-control']) !!}
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
