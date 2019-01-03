@extends('layouts.sys')
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
            <div class="form-horizontal" style="padding:10px;">
                <div class="form-group">
                    <label class="control-label col-sm-2">提现用户</label>
                    <div class="col-sm-9 txt-name"><input type="text" name="name" value="{{ $withdraw->member->name }}" disabled="true" class="form-control" /></div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2">提现后剩余金额</label>
                    <div class="col-sm-9 txt-name">
                        <div class="input-group">
                            <input type="text" name="balance" value="{{ $withdraw->member->proxy_balance }}" disabled="true" class="form-control" />
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2">申请金额</label>
                    <div class="col-sm-9 txt-group">
                        <div class="input-group">
                            <input type="text" name="money" value="{{ $withdraw->money }}" disabled="true" class="form-control" />
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2">手续费</label>
                    <div class="col-sm-9 txt-group">
                        <div class="input-group">
                            <input type="text" name="fee" value="{{ $withdraw->moneyFee }}" disabled="true" class="form-control" />
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2">实际金额</label>
                    <div class="col-sm-9 txt-group">
                        <div class="input-group">
                            <input type="text" name="actual" value="{{ $withdraw->actualMoney }}" disabled="true" class="form-control" />
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2">提现方式</label>
                    <div class="col-sm-9 txt-group">
                        {!! Form::select('type', ['alipay'=>'支付宝', 'bank'=>'银行'], 'alipay', ['class'=>'form-control']) !!}
                        <div class="tip">提现到支付宝由系统自动处理，提现到银行需要手工打款处理</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2">用户支付宝</label>
                    <div class="col-sm-9 txt-group">
                    <input type="text" name="alipay" value="{{ $withdraw->member->alipay }}" disabled="true" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2">用户银行</label>
                    <div class="col-sm-9 txt-group">
                    <input type="text" name="alipay" value="{{ $withdraw->member->bank_name }}/{{ $withdraw->member->bank_no }}/{{ $withdraw->member->bank_contact }}" disabled="true" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2">处理结果</label>
                    <div class="col-sm-9">
                        {!! Form::textArea('remark', '', ['class'=>'form-control', 'style'=>'height:80px;']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-2">
                        <a href="#" class="btn btn-success btn-accept">申请通过</a>
                        <a href="#" class="btn btn-danger btn-reject">申请不通过</a>
                        <a href="javascript:history.back();" class="btn btn-default">返回</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>用户{{ $withdraw->member->name }}收支明细</h5></div>
                <div class="col-sm-2">
                    <a href="{{ route('sys.member.bills', ['id'=>$withdraw->member->id, 'export'=>1]) }}" class="btn btn-success pull-right">导出Excel</a>
                </div>
            </div>
        </div>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>明细</th>
                    <th>金额</th>
                    <th>时间</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $idx=>$log)
                <tr>
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
<script>
$(function(){
    var withdraw_id = {{ $withdraw->id }};
    var token = '{{ csrf_token() }}';
    var loading = new Loading();
    $('.btn-accept').click(function(){
        var data = {
            id: withdraw_id,
            remark: $('textarea[name=remark]').val(),
            type: $('select[name=type]').val(),
            _token: token
        };
        if (data.remark == '') {
            alert('未填写处理结果');
            return;
        }
        loading.show();
        $.ajax({
            url:'{{route('sys.member.withdraw.accept')}}',
            type:'post',
            data:data,
            dataType:'json',
            success:function(json){
                if (json.error) {
                    alert(json.message);
                } else {
                    location.href = '{{ route('sys.member.withdraw') }}';
                }
            },
            complete:function(){
                loading.hide();
            }
        });
    });
    $('.btn-reject').click(function(){
        var data = {
            id: withdraw_id,
            remark:$('textarea[name=remark]').val(),
            _token: token
        };
        if (data.remark == '') {
            alert('未填写处理结果');
            return;
        }
        loading.show();
        $.ajax({
            url:'{{route('sys.member.withdraw.reject')}}',
            type:'post',
            data:data,
            dataType:'json',
            success:function(json){
                if (json.error) {
                    alert(json.message);
                } else {
                    location.href = '{{ route('sys.member.withdraw') }}';
                }
            },
            complete:function(){
                loading.hide();
            }
        });
    });
});
</script>
@stop