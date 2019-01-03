@extends('layouts.sys')
@section('header')
@parent
{!! Html::style('assets/sys/css/jquery.datetimepicker.css') !!}
@stop
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>用户升级申请管理</h5></div>
                <div class="col-sm-2">
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="col-sm-10">
                <ul class="nav nav-tabs">
                    @foreach($statuses as $key=>$val)
                    <li role="presentation" {!! $filters['status'] == $key ? 'class="active"' : '' !!}><a href="{{ route('sys.member.apply', ['status'=>$key]) }}">{{ $val }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-sm-2">
                <a href="{{ route('sys.member.apply', ['export'=>1, 'status'=>$filters['status']]) }}" class="btn btn-success pull-right">导出Excel</a>
            </div>
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
                    <td>申请级别</td>
                    <td>申请时间</td>
                    @if ($filters['status'] != \App\Models\AppConstants::PENDING)
                    <td>审核时间</td>
                    <td>审核人</td>
                    <td>审核备注</td>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($applies as $idx=>$apply)
                <tr data-item="{{ json_encode(['id'=>$apply->id, 'name'=>$apply->member->name, 'start_time'=>date('Y-m-d', $apply->member->proxy_start_time), 'end_time'=>date('Y-m-d', $apply->member->proxy_end_time), 'group'=>$apply->group->title, 'money'=>$apply->money, 'contract'=>image_url($apply->contract), 'bank'=>image_url($apply->bank)]) }}">
                    @if ($filters['status'] == \App\Models\AppConstants::PENDING)
                    <td>
                        <a href="#" class="btn btn-mini btn-primary btn-accept">审核请求</a>
                    </td>
                    @endif
                    <td>{{ $start + $idx + 1 }}</td>
                    <td><a href="{{ route('sys.member.show', ['id'=>$apply->member_id]) }}">{{ $apply->member->nickname }}</a></td>
                    <td>
                        <img src="{{ $apply->member->avatarImage }}" class="avatar30" />
                    </td>
                    <td>{{ $apply->member->username }}</td>
                    <td>{{ $apply->group->title }}</td>
                    <td>{{ $apply->created_at->format('Y-m-d H:i') }}</td>
                    @if ($filters['status'] != \App\Models\AppConstants::PENDING)
                    <td>{{ $apply->updated_at->format('Y-m-d H:I') }}</td>
                    <td>{{ $apply->admin != null ? $apply->admin->name : '' }}</td>
                    <td>{{ $apply->remark }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="panel-footer">{{ $applies->appends($filters)->render() }}</div>
    </div>
@stop
@section('script')
<script type="text/html" id="tpl-form">
<div class="form-horizontal" style="padding:10px;">
    <div class="form-group">
        <label class="control-label col-sm-2">用户</label>
        <div class="col-sm-9 txt-name"><input type="text" name="name" disabled="true" class="form-control" /></div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2">申请级别</label>
        <div class="col-sm-9 txt-group"><input type="text" name="group" disabled="true" class="form-control" /></div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2">上传文件</label>
        <div class="col-sm-9">
            <a href="#" class="lnk-contract" target="_blank">代理合同</a>
            <a href="#" class="lnk-bank" target="_blank">银行底单</a>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2">代理费用</label>
        <div class="col-sm-9 txt-group">
            <div class="input-group">
                <input type="text" name="money" class="form-control" />
                <span class="input-group-addon">元(按此价格计算分成)</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2">有效期</label>
        <div class="col-sm-9">
            <div class="input-group">
                {!! Form::text('start_date', '', ['class'=>'form-control date', 'auto-complete'=>'off']) !!}
                <span class="input-group-addon">至</span>
                {!! Form::text('end_date', '', ['class'=>'form-control date', 'auto-complete'=>'off']) !!}
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2">备注</label>
        <div class="col-sm-9">
            {!! Form::textArea('remark', '', ['class'=>'form-control', 'style'=>'height:80px;']) !!}
        </div>
    </div>
</div>
</script>
{!! Html::script('assets/sys/js/jquery.datetimepicker.js') !!}
<script>
$(function(){
    var token = '{{ csrf_token() }}';
    $.datetimepicker.setLocale('zh');
    $('.btn-accept').click(function(){
        var item = $(this).closest('tr').data('item');
        dialog.show({
            title:'申请审核处理结果',
            width:800,
            height:360,
            content:$('#tpl-form').html(),
            after:function(){
                var _this = this
                $('input[name=name]', _this.content).val(item.name);
                $('input[name=group]', _this.content).val(item.group);
                $('input[name=start_date]', _this.content).val(item.start_time);
                $('input[name=end_date]', _this.content).val(item.end_time);
                $('input[name=money]', _this.content).val(item.money);
                $('.lnk-contract', _this.content).attr('href', item.contract)
                $('.lnk-bank', _this.content).attr('href', item.bank);
                $('input.date').datetimepicker({
                    format:'Y-m-d',
                    timepicker:false
                });
            },
            button:[
                {text:'通过', primary:true, click:function(){
                    var _this = this;
                    var data = {
                        id: item.id,
                        start_date: $('input[name=start_date]', _this.content).val(),
                        end_date: $('input[name=end_date]', _this.content).val(),
                        remark: $('textarea[name=remark]', _this.content).val(),
                        money: parseInt($('input[name=money]', _this.content).val()),
                        _token: token
                    };
                    if (data.money < 0 || isNaN(data.money)) {
                        alert('请填写代理费用，关系用户分成计算');
                        return;
                    }
                    if (data.start_date == '' || data.end_date == '') {
                        alert('请选择有效期');
                        return false;
                    }
                    _this.showLoading();
                    $.ajax({
                        url:'{{route('sys.member.apply.accept')}}',
                        type:'post',
                        data:data,
                        dataType:'json',
                        success:function(json){
                            if (json.error) {
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
                {text:'不通过', cls:'btn-danger', click:function(){
                    var _this = this;
                    var data = {
                        id: item.id,
                        remark:$('textarea[name=remark]', _this.content).val(),
                        _token: token
                    };
                    if (data.remark == '') {
                        alert('审核不通过请填写备注');
                        return;
                    }
                    _this.showLoading();
                    $.ajax({
                        url:'{{route('sys.member.apply.reject')}}',
                        type:'post',
                        data:data,
                        dataType:'json',
                        success:function(json){
                            if (json.error) {
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