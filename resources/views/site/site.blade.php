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
                    <div class="col-sm-11"><h5>网站参数设置</h5></div>
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
                	<label for="ghid" class="col-sm-2 control-label">联系电话</label>
                    <div class="col-sm-8">
                        {!! Form::text('phone', $setting->getParam('phone'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="ghid" class="col-sm-2 control-label">公司LOGO</label>
                    <div class="col-sm-8">
                        {!! Form::text('logo', $setting->getParam('logo'), ['class'=>'form-control img']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="appid" class="col-sm-2 control-label text-warning">小程序APPID(慎改)</label>
                    <div class="col-sm-8">
                        {!! Form::text('mini_appid', $setting->getParam('mini_appid'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="appsecret" class="col-sm-2 control-label text-warning">小程序APPSecret(慎改)</label>
                    <div class="col-sm-8">
                        {!! Form::text('mini_secret', $setting->getParam('mini_secret'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="appsecret" class="col-sm-2 control-label text-warning">支付商户号(慎改)</label>
                    <div class="col-sm-8">
                        {!! Form::text('pay_mchid', $setting->getParam('pay_mchid'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="appsecret" class="col-sm-2 control-label text-warning">支付商户密钥(慎改)</label>
                    <div class="col-sm-8">
                        {!! Form::text('pay_secret', $setting->getParam('pay_secret'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="appsecret" class="col-sm-2 control-label text-warning">支付宝APPID(慎改)</label>
                    <div class="col-sm-8">
                        {!! Form::text('alipay_appid', $setting->getParam('alipay_appid'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="appsecret" class="col-sm-2 control-label text-warning">支付宝商户私钥(慎改)</label>
                    <div class="col-sm-8">
                        {!! Form::text('alipay_privatekey', $setting->getParam('alipay_privatekey'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="appsecret" class="col-sm-2 control-label text-warning">支付宝公钥(慎改)</label>
                    <div class="col-sm-8">
                        {!! Form::text('alipay_publickey', $setting->getParam('alipay_publickey'), ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                	<label for="appsecret" class="col-sm-2 control-label">试用期时长</label>
                    <div class="col-sm-5">
                        <div class="input-group">
                            {!! Form::text('probation', $setting->getParam('probation', 3), ['class'=>'form-control']) !!}
                            <span class="input-group-addon">天</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                	<label for="appsecret" class="col-sm-2 control-label">会员注册价格</label>
                    <div class="col-sm-5">
                        <div class="input-group">
                            {!! Form::text('reg_price', $setting->getParam('reg_price', 0), ['class'=>'form-control']) !!}
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                	<label for="appsecret" class="col-sm-2 control-label">会员续费价格</label>
                    <div class="col-sm-5">
                        <div class="input-group">
                            {!! Form::text('renew_price', $setting->getParam('renew_price', 0), ['class'=>'form-control']) !!}
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                	<label for="appsecret" class="col-sm-2 control-label">购物平台抽成比率</label>
                    <div class="col-sm-5">
                        <div class="input-group">
                            {!! Form::text('order_rate', $setting->getParam('order_rate', 0), ['class'=>'form-control']) !!}
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                	<label for="appsecret" class="col-sm-2 control-label">提现最低金额</label>
                    <div class="col-sm-5">
                        <div class="input-group">
                            {!! Form::text('withdraw_money', $setting->getParam('withdraw_money', 0), ['class'=>'form-control']) !!}
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                	<label for="appsecret" class="col-sm-2 control-label">提现手续费</label>
                    <div class="col-sm-5">
                        <div class="input-group">
                            {!! Form::text('withdraw_money_fee', $setting->getParam('withdraw_money_fee', 0), ['class'=>'form-control']) !!}
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                	<label for="appsecret" class="col-sm-2 control-label">默认首页展示用户id</label>
                    <div class="col-sm-5">
                        {!! Form::text('default_mchid', $setting->getParam('default_mchid', 1), ['class'=>'form-control']) !!}
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
