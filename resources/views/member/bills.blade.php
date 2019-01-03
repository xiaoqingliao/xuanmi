@extends('layouts.sys')
@section('header')
@parent
{!! Html::style('assets/sys/css/jquery.datetimepicker.css') !!}
@stop
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>代理会员管理 / {{ $member->name }}的财务日志(<label class="label label-info">当前余额：{{ $member->proxy_balance }}元</label>)</h5></div>
                <div class="col-sm-2">
                    <a href="{{ route('sys.member.bills', ['id'=>$member->id, 'export'=>1]) }}" style="margin-left:15px;" class="btn btn-success pull-right">导出Excel</a>
                    <a href="#" class="btn btn-success btn-dlg pull-right">充值反充</a>
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
                    <td>{{ $log->remark }}</td>
                    <td><label class="label {{ $log->type == \App\Models\MemberAccountLog::TYPE_ADD ? 'label-info' : 'label-danger' }}">{{ $log->type == \App\Models\MemberAccountLog::TYPE_ADD ? '+' : '-' }}{{ $log->cash }}元</label></td>
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
<script type="text/html" id="tpl-dlg">
<div class="form-horizontal" style="padding:20px;">
    <div class="form-group">
        <label class="col-sm-3 control-label">操作类型</label>
        <div class="col-sm-8">
            {!! Form::select('type', ['1'=>'充值', '2'=>'反充'], 1, ['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">操作金额</label>
        <div class="col-sm-8">
            {!! Form::text('money', '', ['class'=>'form-control']) !!}
        </div>
    </div>
</div>
</script>
<script>
$(function(){
    var token = '{{ csrf_token() }}';
    var max_money = {{ $member->proxy_balance }};
    $('.btn-dlg').click(function(){
        dialog.show({
            title: '充值反充',
            width:500,
            height:150,
            content: $('#tpl-dlg').html(),
            button:[
                {text:'确定', primary:true, click:function(){
                    var _this = this;
                    var data = {
                        type: $('select[name=type]', _this.content).val(),
                        money: parseFloat($('input[name=money]', _this.content).val()),
                        _token: token
                    };
                    if (isNaN(data.money) || data.money <= 0) {
                        alert('请正确填写金额');
                        return;
                    }
                    if (data.type == 2 && data.money > max_money) {
                        alert('反充不能大于用户余额');
                        return;
                    }
                    _this.showLoading();
                    $.ajax({
                        url:'{{ route('sys.member.bills.post', ['id'=>$member->id]) }}',
                        type:'post',
                        data:data,
                        dataType:'json',
                        success:function(json){
                            if (json.error){
                                alert(json.message);
                            } else {
                                location.reload();
                            }
                        },
                        complete:function(){
                            _this.hideLoading();
                        }
                    });
                }},
                {text:'取消', click:function(){
                    this.hide();
                }}
            ]
        });
    });
});
</script>
@stop