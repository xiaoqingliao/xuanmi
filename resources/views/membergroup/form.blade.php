@extends('layouts.sys')
@section('header')
@parent
@stop
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>代理级别管理 / {{ $group->id > 0 ? '修改' : '添加' }}</h5></div>
            </div>
        </div>
        <div class="panel-body">
            @foreach($errors->all('<div class="alert alert-danger">:message</div>') as $message)
            {!! $message !!}
            @endforeach
            <form action="{{ $group->id > 0 ? route('sys.membergroup.update', ['id'=>$group->id]) : route('sys.membergroup.store') }}" method="post" class="form-horizontal" role="form" id="admin-form">
                {!! method_field($group->id > 0 ? 'PUT' : 'POST') !!}
                {!! csrf_field() !!}
                <div class="form-group">
                    <label class="col-sm-3 control-label">名称</label>
                    <div class="col-sm-6">
                        {!! Form::text('title', $group->title, ['class'=>'form-control']) !!}
                    </div>
                </div>
                @if ($group->id <= 0)
                <div class="form-group">
                    <label class="col-sm-3 control-label">唯一标识码</label>
                    <div class="col-sm-6">
                        {!! Form::text('code', $group->code, ['class'=>'form-control', 'autocomplete'=>'false']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">可发展同级代理</label>
                    <div class="col-sm-6">
                        <label>{!! Form::radio('same', 1, $group->same) !!} 是</label>
                        <label>{!! Form::radio('same', 0, $group->same == false) !!} 否</label>
                    </div>
                </div>
                @endif
                <div class="form-group">
                    <label class="col-sm-3 control-label">图标</label>
                    <div class="col-sm-6">
                        {!! Form::text('icon', $group->icon, ['class'=>'form-control img']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">代理合同</label>
                    <div class="col-sm-6">
                        {!! Form::text('contract', $group->contract, ['class'=>'form-control img']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">代理价格(前台显示)</label>
                    <div class="col-sm-6">
                        {!! Form::text('price', $group->price, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">代理价格(计算分成用)</label>
                    <div class="col-sm-6">
                        <div class="input-group">
                            {!! Form::text('money', $group->money, ['class'=>'form-control']) !!}
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">分销设置</label>
                    <div class="col-sm-9" style="max-width:600px;">
                        <table class="table table-striped table-hover">
                            <colgroup>
                                <col width="200" />
                            </colgroup>
                            @foreach($groups as $_group)
                            <tr>
                                <td><label class="control-label">{{ $_group->title }}</label></td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon">前</span>
                                        {!! Form::text('params['. $_group->code .'_number]', $group->getParams($_group->code . '_number', 0), ['class'=>'form-control', 'style'=>"min-width:80px;"]) !!}
                                        <span class="input-group-addon">位分成</span>
                                        {!! Form::text('params['. $_group->code .'_rebatea]', $group->getParams($_group->code . '_rebatea', 0), ['class'=>'form-control', 'style'=>'min-width:80px;']) !!}
                                        <span class="input-group-addon">%</span>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon">超过后分成</span>
                                        {!! Form::text('params['. $_group->code .'_rebateb]', $group->getParams($_group->code . '_rebateb', 0), ['class'=>'form-control', 'style'=>'min-width:80px;']) !!}
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <td><label class="control-label">首层代理非直推奖励</label></td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon">分成</span>
                                        {!! Form::text('params[proxy_reg_award1]', $group->getParams('proxy_reg_award1', 0), ['class'=>'form-control', 'style'=>'min-width:80px;']) !!}
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">次层代理非直推奖励</label></td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon">分成</span>
                                        {!! Form::text('params[proxy_reg_award2]', $group->getParams('proxy_reg_award2', 0), ['class'=>'form-control', 'style'=>'min-width:80px;']) !!}
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">下级会员次年续费</label></td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon">分成</span>
                                        {!! Form::text('params[proxy_fee_renew]', $group->getParams('proxy_fee_renew', 0), ['class'=>'form-control', 'style'=>'min-width:80px;']) !!}
                                        <span class="input-group-addon">元/个</span>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon">分润层级</span>
                                        {!! Form::text('params[proxy_fee_level]', $group->getParams('proxy_fee_level', 2), ['class'=>'form-control', 'style'=>'min-width:80px;']) !!}
                                        <span class="input-group-addon">层</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">申请说明</label>
                    <div class="col-sm-6">
                        {!! Form::textArea('copyright', $group->copyright, ['class'=>'form-control', 'style'=>'height:200px;']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">付款信息</label>
                    <div class="col-sm-6">
                        {!! Form::textArea('description', $group->description, ['class'=>'form-control', 'style'=>'height:200px;']) !!}
                    </div>
                </div>
                <div class="col-sm-6 col-sm-offset-3">
                    {!! Form::submit('保存', ['class'=>'btn btn-success']) !!}
                    <a href="{{ route('sys.membergroup.index') }}" class="btn btn-default">返回</a>
                </div>
            </form>
        </div>
    </div>
@stop
@section('script')
<script>
$(function(){
    $('input.img').imageText({});
});
</script>
@stop