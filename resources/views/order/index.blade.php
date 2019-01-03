@extends('layouts.sys')
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>会议/课程订单管理</h5></div>
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
                <col width="120" />
                <col width="50" />
            </colgroup>
            <thead>
                <tr>
                    <td>操作</td>
                    <td>#</td>
                    <td>订单号</td>
                    <td>标题</td>
                    <td>下单时间</td>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $idx=>$order)
                <tr>
                    <td>
                    </td>
                    <td>{{ $start + $idx + 1 }}</td>
                    <td>{{ $order->sn }}</td>
                    <td>{{ $order->title }}</td>
                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="panel-footer">{!! $orders->appends($filters)->render() !!}</div>
    </div>
@stop
@section('script')
<script>
$(function(){
});
</script>
@stop