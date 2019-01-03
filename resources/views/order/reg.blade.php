@extends('layouts.sys')
@section('header')
@parent
{!! Html::style('assets/sys/css/jquery.datetimepicker.css') !!}
@stop
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>注册订单管理</h5></div>
                <div class="col-sm-2">
                    <a href="{{ route('sys.order.reg', array_merge($filters, ['export'=>1])) }}" class="btn btn-success pull-right">导出Excel</a>
                </div>
            </div>
        </div>
        @if (session()->has('message'))
        <div class="panel-body">
            <div class="alert alert-warning">{{ session('message') }}</div>
        </div>
        @endif
        <div class="panel-body">
        <form action="{{ route('sys.order.reg') }}" method="get" class="form-horizontal" id="search-form" role="form">
            <div class="form-group">
                <label class="col-sm-2 col-lg-1 control-label">时间</label>
                <div class="col-lg-4 col-sm-6">
                    <div class="input-group">
                        {!! Form::text('start_date', $filters['start_date'], ['class'=>'form-control date', 'autocomplete'=>'off', 'placeholder'=>'开始时间']) !!}
                        <span class="input-group-addon">-</span>
                        {!! Form::text('end_date', $filters['end_date'], ['class'=>'form-control date', 'autocomplete'=>'off', 'placeholder'=>'结束时间']) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-2 col-lg-1"></div>
                <div class="col-sm-3">
                    {!! Form::submit('搜索', ['class'=>'btn btn-sm btn-success']) !!}
                    <a href="{{ route('sys.order.reg') }}" class="btn btn-sm btn-default">取消搜索</a>
                </div>
            </div>
        </form>
        </div>
        <table class="table table-striped table-hover">
            <colgroup>
                <col width="50" />
            </colgroup>
            <thead>
                <tr>
                    <td>#</td>
                    <td>订单号</td>
                    <td>标题</td>
                    <td>会员信息</td>
                    <td>注册时间</td>
                    <td>注册费用</td>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $idx=>$order)
                <tr>
                    <td>{{ $start + $idx + 1 }}</td>
                    <td><a href="{{ route('sys.order.show', ['id'=>$order->id]) }}">{{ $order->sn }}</a></td>
                    <td>{{ $order->title }}</td>
                    <td><a href="{{ route('sys.member.show', ['id'=>$order->member_id]) }}"><img src="{{ $order->member->avatarImage }}" class="avatar30" /> {{ $order->member->nickname }}</a></td>
                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    <td><label class="label label-info">{{ $order->price }}元</label></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="panel-footer">{!! $orders->appends($filters)->render() !!}</div>
    </div>
@stop
@section('script')
{!! Html::script('assets/sys/js/jquery.datetimepicker.js') !!}
<script>
$(function(){
    $.datetimepicker.setLocale('zh');
    $('input.date').datetimepicker({
        timepicker:false,
        format:'Y-m-d'
    });
});
</script>
@stop