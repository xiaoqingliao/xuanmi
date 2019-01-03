@extends('layouts.sys')
@section('header')
    @parent
    {!! Html::style('assets/sys/css/jquery.datetimepicker.css') !!}
@stop
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>用户提现申请管理</h5></div>
                <div class="col-sm-2">
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="col-sm-10">
                <ul class="nav nav-tabs">
                    @foreach($statuses as $key=>$val)
                    <li role="presentation" {!! $filters['status'] == $key ? 'class="active"' : '' !!}><a href="{{ route('sys.member.withdraw', ['status'=>$key]) }}">{{ $val }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-sm-2">
                <a href="{{ route('sys.member.withdraw', array_merge($filters, ['export'=>1])) }}" class="btn btn-success pull-right">导出Excel</a>
            </div>
        </div>
        <div class="panel-body">
        <form action="" method="get" class="form-horizontal" id="search-form" role="form">
            {!! Form::hidden('status', $filters['status']) !!}
            <div class="form-group">
                <div class="col-sm-2 col-lg-1 control-label">
                    提现时间
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="input-group">
                        {!! Form::text('start_date', $filters['start_date'], ['class'=>'form-control date', 'autocomplete'=>'off']) !!}
                        <span class="input-group-addon">-</span>
                        {!! Form::text('end_date', $filters['end_date'], ['class'=>'form-control date', 'autocomplete'=>'off']) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-2 col-lg-1"></div>
                <div class="col-sm-3">
                    {!! Form::submit('搜索', ['class'=>'btn btn-sm btn-success']) !!}
                    <a href="{{ route('sys.member.withdraw') }}" class="btn btn-sm btn-default">取消搜索</a>
                </div>
            </div>
        </form>
        </div>
        @if (session()->has('message'))
        <div class="panel-body">
            <div class="alert alert-warning">{{ session('message') }}</div>
        </div>
        @endif
        <table class="table table-striped table-hover">
            <colgroup>
                @if ($filters['status'] == \App\Models\AppConstants::PENDING)
                <col width="120" />
                @endif
                <col width="50" />
            </colgroup>
            <thead>
                <tr>
                    @if ($filters['status'] == \App\Models\AppConstants::PENDING)
                    <td>操作</td>
                    @endif
                    <td>#</td>
                    <td>昵称</td>
                    <td>头像</td>
                    <td>手机</td>
                    <td>申请提现金额</td>
                    <td>平台手续费</td>
                    <td>实际可提金额</td>
                    <td>申请时间</td>
                    @if ($filters['status'] != \App\Models\AppConstants::PENDING)
                    <td>审核时间</td>
                    <td>审核人</td>
                    <td>审核备注</td>
                    @endif
                    @if ($filters['status'] == \App\Models\AppConstants::SENDED || $filters['status'] == \App\Models\AppConstants::FAILED)
                    <td>提现帐户</td>
                    @if ($filters['status'] == \App\Models\AppConstants::FAILED)
                    <td>结果</td>
                    @endif
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($withdraws as $idx=>$withdraw)
                <tr>
                    @if ($filters['status'] == \App\Models\AppConstants::PENDING)
                    <td>
                        <a href="{{ route('sys.member.withdraw.show', ['id'=>$withdraw->id]) }}" class="btn btn-mini btn-primary">提现处理</a>
                    </td>
                    @endif
                    <td>{{ $start + $idx + 1 }}</td>
                    <td><a href="{{ route('sys.member.show', ['id'=>$withdraw->member_id]) }}">{{ $withdraw->member->nickname }}</a></td>
                    <td>
                        <img src="{{ $withdraw->member->avatarImage }}" class="avatar30" />
                    </td>
                    <td>{{ $withdraw->member->username }}</td>
                    <td><label class="label label-info">{{ $withdraw->money }}元</label></td>
                    <td><label class="label label-info">{{ $withdraw->moneyFee }}元</label></td>
                    <td><label class="label label-info">{{ $withdraw->actualMoney }}元</label></td>
                    <td>{{ $withdraw->created_at->format('Y-m-d H:i') }}</td>
                    @if ($filters['status'] != \App\Models\AppConstants::PENDING)
                    <td>{{ $withdraw->updated_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $withdraw->admin != null ? $withdraw->admin->name : '' }}</td>
                    <td>{{ $withdraw->remark }}</td>
                    @endif
                    @if ($filters['status'] == \App\Models\AppConstants::SENDED || $filters['status'] == \App\Models\AppConstants::FAILED)
                    <td>{{ $withdraw->account }}</td>
                    @if ($filters['status'] == \App\Models\AppConstants::FAILED)
                    <td>{{ $withdraw->logs }}</td>
                    @endif
                    @endif
                </tr>
                @endforeach
            </tbody>
            @if ($filters['status'] == \App\Models\AppConstants::SENDED)
            <tfoot>
                <tr>
                    <td colspan="2">合计：</td>
                    <td></td>
                    <td></td>
                    <td><label class="label label-info">{{ $withdraws->sum('money') }}元</label></td>
                    <td><label class="label label-info">{{ $withdraws->sum('fee') }}元</label></td>
                    <td><label class="label label-info">{{ $withdraws->sum('actual') }}元</label></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2">总计：</td>
                    <td></td>
                    <td></td>
                    <td><label class="label label-info">{{ $total['money'] }}元</label></td>
                    <td><label class="label label-info">{{ $total['fee'] }}元</label></td>
                    <td><label class="label label-info">{{ $total['actual'] }}元</label></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
        <div class="panel-footer">{{ $withdraws->appends($filters)->render() }}</div>
    </div>
@stop
@section('script')
{!! Html::script('assets/sys/js/jquery.datetimepicker.js') !!}
<script>
$(function(){
    $.datetimepicker.setLocale('zh');
    $('.date').datetimepicker({
        format:'Y-m-d',
        timepicker:false
    });
});
</script>
@stop