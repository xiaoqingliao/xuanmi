@extends('layouts.sys')
@section('header')
@parent
{!! Html::style('assets/sys/css/jquery.datetimepicker.css') !!}
@stop
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>平台财务日志</h5></div>
                <div class="col-sm-2">
                    <a href="{{ route('sys.finance.index', array_merge($filters, ['export'=>1])) }}" class="btn btn-success pull-right">导出Excel</a>
                </div>
            </div>
        </div>
        @if (session()->has('message'))
        <div class="panel-body">
            <div class="alert alert-warning">{{ session('message') }}</div>
        </div>
        @endif
        <div class="panel-body">
        <form action="{{ route('sys.finance.search') }}" method="post" class="form-horizontal" id="search-form" role="form">
            {!! method_field('POST') !!}
            {!! csrf_field() !!}
            <div class="form-group">
                <label class="col-sm-2 col-lg-1 control-label">时间</label>
                <div class="col-lg-4 col-sm-6">
                    <div class="input-group">
                        {!! Form::text('start_date', $filters['start_date'], ['class'=>'form-control date', 'autocomplete'=>'off', 'placeholder'=>'开始时间']) !!}
                        <span class="input-group-addon">-</span>
                        {!! Form::text('end_date', $filters['end_date'], ['class'=>'form-control date', 'autocomplete'=>'off', 'placeholder'=>'结束时间']) !!}
                    </div>
                </div>
                <label class="col-sm-2 col-lg-1 control-label">类型</label>
                <div class="col-lg-4 col-sm-4">
                    {!! Form::select('type', $types, $filters['type'], ['class'=>'form-control', 'placeholder'=>'请选择类型']) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-2 col-lg-1"></div>
                <div class="col-sm-3">
                    {!! Form::submit('搜索', ['class'=>'btn btn-sm btn-success']) !!}
                    <a href="{{ route('sys.finance.index') }}" class="btn btn-sm btn-default">取消搜索</a>
                </div>
            </div>
        </form>
        </div>
        <div class="panel-body">
            当前总收入：<a href="{{ route('sys.finance.index', ['type'=>\App\Models\CompanyFinanceLog::TYPE_ADD]) }}" class="label label-info">{{ $stat->income }}元</a>
            总支出：<a href="{{ route('sys.finance.index', ['type'=>\App\Models\CompanyFinanceLog::TYPE_SUB]) }}" class="label label-danger">{{ $stat->expend }}元</a>
            盈利：<label class="label label-warning">{{ $stat->gain }}元</label>
        </div>
        <table class="table table-striped table-hover">
            <colgroup>
                <col width="50" />
            </colgroup>
            <thead>
                <tr>
                    <td>#</td>
                    <td>用户</td>
                    <td>金额</td>
                    <td>备注</td>
                    <td>时间</td>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $idx => $log)
                <tr>
                    <td>{{ $start + $idx + 1 }}</td>
                    <td>
                        @if ($log->member != null)
                        <a href="{{ route('sys.member.show', ['id'=>$log->member_id]) }}"><img src="{{ $log->member->avatarImage }}" class="avatar30" /> {{ $log->member->nickname }}</a>
                        @endif
                    </td>
                    <td><label class="label {{ $log->type == \App\Models\CompanyFinanceLog::TYPE_ADD ? 'label-info' : 'label-danger' }}">{{ $log->type == \App\Models\CompanyFinanceLog::TYPE_ADD ? '+' : '-' }}{{ $log->price }}元</label></td>
                    <td>{{ $log->remark }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="panel-footer">{{ $logs->appends($filters)->render() }}</div>
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