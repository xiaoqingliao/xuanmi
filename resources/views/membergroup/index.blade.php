@extends('layouts.sys')
@section('header')
    @parent
    <style>
    .number{
        color:red;
        font-weight:bold;
        padding:0 4px;
    }
    </style>
@stop
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>代理级别管理</h5></div>
                <div class="col-sm-2">
                </div>
            </div>
        </div>
        @if (session()->has('message'))
        <div class="panel-body">
            <div class="alert alert-warning">{{ session('message') }}</div>
        </div>
        @endif
        <table class="table table-striped table-hover">
            <colgroup>
                <col width="80" />
                <col width="50" />
            </colgroup>
            <thead>
                <tr>
                    <td>操作</td>
                    <td>#</td>
                    <td>名称</td>
                    <td>代理价</td>
                    <td>权益分成</td>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $idx=>$group)
                <tr>
                    <td>
                        <a href="{{ route('sys.membergroup.edit', ['id'=>$group->id]) }}" class="btn btn-success btn-sm">修改</a>
                    </td>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $group->title }}</td>
                    <td>{{ $group->price }}</td>
                    <td>
                        @foreach($groups as $_group)
                        <div class="row">
                            {{ $_group->title }}:前<span class="number">{{ $group->getParams($_group->code . '_number', 0) }}</span>位分成<span class="number">{{ $group->getParams($_group->code . '_rebatea', 0) }}%</span>，超过后分成<span class="number">{{ $group->getParams($_group->code . '_rebateb', 0) }}%</span>
                        </div>
                        @endforeach
                        <div class="row">首层代理非直推奖励：每个分成<span class="number">{{ $group->getParams('proxy_reg_award1', 0) }}%</span></div>
                        <div class="row">次层代理非直推奖励：每个分成<span class="number">{{ $group->getParams('proxy_reg_award2', 0) }}%</span></div>
                        <div class="row">下级会员次年续费：<span class="number">{{ $group->getParams('proxy_fee_renew', 0) }}</span>元/个，续费分润<span class="number">{{ $group->getParams('proxy_fee_level', 2) }}</span>层</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop