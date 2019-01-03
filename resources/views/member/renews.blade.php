@extends('layouts.sys')
@section('header')
@parent
{!! Html::style('assets/sys/css/jquery.datetimepicker.css') !!}
@stop
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>代理会员管理 / {{ $member->name }}的续费分成提取日志</h5></div>
                <div class="col-sm-2">
                    <a href="{{ route('sys.member.renews', ['id'=>$member->id, 'export'=>1]) }}" class="btn btn-success pull-right">导出Excel</a>
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
                <col width="50" />
            </colgroup>
            <thead>
                <tr>
                    <td>#</td>
                    <td>备注</td>
                    <td>金额</td>
                    <td>时间</td>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $idx => $log)
                <tr>
                    <td>{{ $start + $idx + 1 }}</td>
                    <td>{{ $log->remark }}{{ $log->type == \App\Models\MemberRenewLog::TYPE_SUB && $log->admin != null ? '/处理人：' . $log->admin->name : '' }}</td>
                    <td><label class="label {{ $log->type == \App\Models\MemberRenewLog::TYPE_ADD ? 'label-info' : 'label-danger' }}">{{ $log->type == \App\Models\MemberRenewLog::TYPE_ADD ? '+' : '-' }}{{ $log->money }}元</label></td>
                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="panel-footer">{{ $logs->render() }}</div>
    </div>
@stop
@section('script')
{!! Html::script('assets/sys/js/jquery.datetimepicker.js') !!}
@stop