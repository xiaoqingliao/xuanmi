@extends('layouts.sys')
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>普通会员管理</h5></div>
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
                    <td>昵称</td>
                    <td>头像</td>
                    <td>注册时间</td>
                    <td>最近登录时间</td>
                    <td>最近购买时间</td>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $idx=>$member)
                <tr>
                    <td>{{ $start + $idx + 1 }}</td>
                    <td><a href="{{ route('sys.member.show', ['id'=>$member->id]) }}">{{ $member->logged ? $member->nickname : '未读取用户信息' }}</a></td>
                    <td>
                        @if ($member->logged)
                        <img src="{{ $member->avatarImage }}" class="avatar30" />
                        @endif
                    </td>
                    <td>{{ $member->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ date('Y-m-d H:i', $member->last_login) }}</td>
                    <td>{{ $member->last_buy > 0 ? date('Y-m-d H:i', $member->last_buy) : 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="panel-footer">{{ $members->appends($filters)->render() }}</div>
    </div>
@stop