@extends('layouts.sys')
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>代理会员管理 / {{ $member->name }}的代理变更日志</h5></div>
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
                <col width="50" />
            </colgroup>
            <thead>
                <tr>
                    <td>#</td>
                    <td>原级别</td>
                    <td>新级别</td>
                    <td>升级时间</td>
                    <td>处理人员</td>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $idx => $log)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $log->oldGroup->title }}</td>
                    <td>{{ $log->newGroup->title }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $log->admin != null ? $log->admin->name : '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop