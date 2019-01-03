@extends('layouts.sys')
@section('header')
@parent
{!! Html::style('assets/sys/css/jquery.datetimepicker.css') !!}
@stop
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>代理会员管理 @if ($parent != null)  / {{ $parent->nickname }}的下级会员 @endif</h5></div>
                <div class="col-sm-2">
                    <a href="{{ route('sys.member.proxy', array_merge($filters, ['export'=>1])) }}" class="btn btn-success pull-right">导出Excel</a>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <ul class="nav nav-tabs">
                <li role="presentation" {!! $filters['probation'] == 0 ? 'class="active"' : '' !!}><a href="{{ route('sys.member.proxy', ['probation'=>0]) }}">正式会员</a></li>
                <li role="presentation" {!! $filters['probation'] == 1 ? 'class="active"' : '' !!}><a href="{{ route('sys.member.proxy', ['probation'=>1]) }}">试用会员</a></li>
            </ul>
        </div>
        @if (session()->has('message'))
        <div class="panel-body">
            <div class="alert alert-warning">{{ session('message') }}</div>
        </div>
        @endif
        <div class="panel-body">
        <form action="{{ route('sys.member.proxy') }}" method="get" class="form-horizontal" id="search-form" role="form">
            {!! method_field('POST') !!}
            {!! csrf_field() !!}
            {!! Form::hidden('parentid', $filters['parentid']) !!}
            {!! Form::hidden('probation', $filters['probation']) !!}
            <div class="form-group">
                <div class="col-sm-2 col-lg-1">
                    {!! Form::select('search_field', $fields, $filters['search_field'], ['class'=>'form-control']) !!}
                </div>
                <div class="col-lg-4 col-sm-6">
                    {!! Form::text('key', $filters['key'], ['class'=>'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-2 col-lg-1"></div>
                <div class="col-sm-3">
                    {!! Form::submit('搜索', ['class'=>'btn btn-sm btn-success']) !!}
                    <a href="{{ route('sys.member.proxy') }}" class="btn btn-sm btn-default">取消搜索</a>
                </div>
            </div>
        </form>
        </div>
        <table class="table table-striped table-hover">
            <colgroup>
                <col width="80" />
                <col width="50" />
                <col width="80" />
            </colgroup>
            <thead>
                <tr>
                    <td>#</td>
                    <td>#</td>
                    <td>会员id</td>
                    <td>上级会员</td>
                    <td>昵称</td>
                    <td>手机</td>
                    <td>代理账户余额</td>
                    <td>续费分成</td>
                    <td>代理级别</td>
                    <td>代理时间</td>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $idx=>$member)
                <tr data-item="{{ json_encode(['id'=>$member->id, 'group_id'=>$member->group_id, 'start_date'=>date('Y-m-d', $member->proxy_start_time), 'end_date'=>date('Y-m-d', $member->proxy_end_time), 'renew_money'=>$member->renewMoney, 'nickname'=>$member->nickname, 'name'=>$member->name]) }}">
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-gear"></i> 操作 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                @if ($member->probation)
                                <li><a href="#" data-url="{{ route('sys.member.conversion', ['id'=>$member->id]) }}" class="btn-conversion">转为正式会员</a></li>
                                @endif
                                <li><a href="#" class="btn-setting">设置代理级别</a></li>
                                <li><a href="{{ route('sys.member.upgradelogs', ['id'=>$member->id]) }}">代理变更日志</a></li>
                                <li><a href="#" data-url="{{ route('sys.member.renewpost', ['id'=>$member->id]) }}" class="btn-renew-sub">提取续费金额</a></li>
                                <li><a href="#" data-url="{{ route('sys.member.changeparent', ['id'=>$member->id]) }}" class="btn-change-parent">更改上级</a></li>
                            </ul>
                        </div>
                    </td>
                    <td>{{ $start + $idx + 1 }}</td>
                    <td>{{ $member->id }}</td>
                    <td>
                        @if ($member->parent != null)
                        <a href="{{ route('sys.member.proxy', ['parentid'=>$member->parent_id]) }}"><img src="{{ $member->parent->avatarImage }}" class="avatar30" /> {{ $member->parent->nickname }}</a>
                        @endif
                    </td>
                    <td><a href="{{ route('sys.member.show', ['id'=>$member->id]) }}"><img src="{{ $member->avatarImage }}" class="avatar30" /> {{ $member->logged ? $member->nickname : '未授权用户信息' }}</a></td>
                    <td>{{ $member->phone }}</td>
                    <td><a href={{ route('sys.member.bills', ['id'=>$member->id]) }} class="label label-info">{{ $member->proxy_balance }}元</a></td>
                    <td><a href="{{ route('sys.member.renews', ['id'=>$member->id]) }}" class="label label-info">{{ $member->renewMoney }}元</a></td>
                    <td>{{ $member->group->title }}</td>
                    <td>{{ date('Y-m-d', $member->proxy_start_time) }} - {{ date('Y-m-d', $member->proxy_end_time) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>合计：</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><label class="label label-info">{{ $members->sum('proxy_balance') }}元</label></td>
                    <td><label class="label label-info">{{ $members->sum('renewMoney') }}元</label></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>总计：</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><label class="label label-info">{{ $total['balance'] }}元</label></td>
                    <td><label class="label label-info">{{ $total['renew'] > 0 ? $total['renew'] : 0 }}元</label></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <div class="panel-footer">{{ $members->appends($filters)->render() }}</div>
    </div>
@stop
@section('script')
{!! Html::script('assets/sys/js/jquery.datetimepicker.js') !!}
<script type="text/html" id="tpl-form">
<div class="form-horizontal" style="padding:10px;">
    <div class="form-group">
        <label class="col-sm-3 control-label">级别</label>
        <div class="col-sm-8">
            {!! Form::select('group_id', $groups->pluck('title', 'id'), 0, ['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">有效期</label>
        <div class="col-sm-8">
            <div class="input-group">
                {!! Form::text('start_date', '', ['class'=>'form-control date', 'autocomplete'=>'off']) !!}
                <span class="input-group-addon">至</span>
                {!! Form::text('end_date', '', ['class'=>'form-control date', 'autocomplete'=>'off']) !!}
            </div>
        </div>
    </div>
</div>
</script>
<script type="text/html" id="tpl-renew-dlg">
<div class="form-horizontal" style="padding:10px;">
    <div class="form-group">
        <label class="col-sm-3 control-label">提取用户</label>
        <div class="col-sm-8">
            {!! Form::text('member', '', ['class'=>'form-control', 'autocomplete'=>'off', 'disabled'=>'true']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">可提取金额</label>
        <div class="col-sm-8">
            <div class="input-group">
                {!! Form::text('renew_money', '', ['class'=>'form-control', 'autocomplete'=>'off', 'disabled'=>'true']) !!}
                <span class="input-group-addon">元</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">提取金额</label>
        <div class="col-sm-8">
            <div class="input-group">
                {!! Form::text('money', '', ['class'=>'form-control', 'autocomplete'=>'off']) !!}
                <span class="input-group-addon">元</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">备注</label>
        <div class="col-sm-8">
            {!! Form::text('remark', '', ['class'=>'form-control', 'autocomplete'=>'off']) !!}
        </div>
    </div>
</div>
</script>
<script type="text/html" id="tpl-list">
<div class="panel">
    <div class="panel-heading form-inline">
        <input type="text" name="key" class="form-control" placeholder="用户昵称/手机号码" />
        <a href="#" class="btn btn-success btn-search">搜索</a>
    </div>
    <div class="panel-body" style="max-height:250px;overflow-y:auto;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th></th>
                    <th>昵称</th>
                    <th>姓名</th>
                    <th>手机</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
</script>
<script type="text/html" id="tpl-conversion">
<div class="form-horizontal" style="padding:10px;">
    <div class="form-group">
        <label class="col-sm-3 control-label">转正用户</label>
        <div class="col-sm-8">
            {!! Form::text('member', '', ['class'=>'form-control', 'autocomplete'=>'off', 'disabled'=>'true']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">是否分润</label>
        <div class="col-sm-8">
            <label>{!! Form::radio('profit', 1, true) !!} 是</label>
            <label>{!! Form::radio('profit', 0, false) !!} 否</label>
        </div>
    </div>
</div>
</script>
<script>
$(function(){
    var token = '{{ csrf_token() }}';
    $.datetimepicker.setLocale('zh');
    $('.btn-setting').click(function(){
        var item = $(this).closest('tr').data('item')
        dialog.show({
            title:'用户设置',
            width:600,
            height:120,
            content:$('#tpl-form').html(),
            after:function(){
                var _this =  this;
                $('select[name=group_id]', _this.content).val(item.group_id);
                $('input[name=start_date]', _this.content).val(item.start_date);
                $('input[name=end_date]', _this.content).val(item.end_date);
                $('input.date', _this.content).datetimepicker({
                    format:'Y-m-d',
                    timepicker:false
                });
            },
            button:[
                {text:'确定', primary:true, click:function(){
                    var _this = this
                    var data = {
                        id: item.id,
                        group_id: $('select[name=group_id]', _this.content).val(),
                        start_date: $('input[name=start_date]', _this.content).val(),
                        end_date: $('input[name=end_date]', _this.content).val(),
                        _token: token
                    };
                    if (data.start_date == '' || data.end_date == '') {
                        alert('有效期设置错误');
                        return;
                    }
                    _this.showLoading();
                    $.ajax({
                        url:'{{ route('sys.member.proxy.update') }}',
                        type: 'post',
                        data: data,
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
                {text:'取消', click:function(){this.hide();}}
            ]
        });
    });
    $('.btn-renew-sub').click(function(){
        var item = $(this).closest('tr').data('item');
        var _url = $(this).data('url');
        dialog.show({
            title: '提取续费分成金额',
            width:500,
            height:220,
            content: $('#tpl-renew-dlg').html(),
            after:function(){
                var _this = this;
                $('input[name=member]', _this.content).val(item.nickname + '/' + item.name);
                $('input[name=renew_money]', _this.content).val(item.renew_money);
                $('input[name=remark]', _this.content).val('');
            },
            button:[
                {text:'确定', primary:true, click:function(){
                    var _this = this;
                    var data = {
                        id: item.id,
                        money: parseInt($('input[name=money]', _this.content).val()),
                        remark: $('input[name=remark]', _this.content).val(),
                        _token: token
                    };
                    if (data.money > item.renew_money) {
                        alert('提取金额不能超过可提取金额');
                        return;
                    }
                    if (data.money <= 0) {
                        alert('不能提取小于0元的金额');
                        return;
                    }
                    _this.showLoading();
                    $.ajax({
                        url:_url,
                        type: 'post',
                        dataType: 'json',
                        data: data,
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
                {text:'取消', click:function(){this.hide();}}
            ]
        });
    });

    $('.btn-change-parent').click(function(){
        var data_url = $(this).data('url');
        dialog.show({
            title: '更改上级会员',
            width:500,
            height:340,
            content:$('#tpl-list').html(),
            after:function(){
                var _this = this;
                $('.btn-search', _this.content).click(function(){
                    search();
                });
                $('input[name=key]', _this.content).keyup(function(e){
                    if (e.keyCode == 13) {
                        search();
                    }
                });
                
                function search() {
                    var key = $('input[name=key]', _this.content).val();
                    _this.showLoading();
                    $.ajax({
                        url: '{{ route('sys.member.ajax') }}',
                        type: 'get',
                        data: {key:key},
                        dataType: 'json',
                        success:function(json){
                            if (json.error) {
                                alert(json.message);
                            } else {
                                load_members(json.list, $('table tbody', _this.content));
                            }
                        },
                        complete:function(){
                            _this.hideLoading();
                        }
                    });
                }
            },
            button:[
                {text:'确定', primary:true, click:function(){
                    var _this = this;
                    var pid = $('input[name=mid]:checked', _this.content).val();
                    _this.showLoading();
                    $.ajax({
                        url:data_url,
                        type:'post',
                        dataType:'json',
                        data:{parentid:pid,_token:token},
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
                {text:'取消', click:function(){this.hide();}}
            ]
        });
    });

    $('.btn-conversion').click(function(){
        var url = $(this).data('url');
        var member = $(this).closest('tr').data('item');
        dialog.show({
            title: '用户转正',
            width:500,
            height:140,
            content: $('#tpl-conversion').html(),
            after:function(){
                $('input[name=member]', this.content).val(member.nickname);
            },
            button:[
                {text:'确定', primary:true, click:function(){
                    var _this = this;
                    var data = {
                        profit: parseInt($('input[name=profit]:checked', _this.content).val()),
                        _token: token,
                    }
                    _this.showLoading();
                    $.ajax({
                        url:url,
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
                {text:'取消', click:function(){this.hide();}}
            ]
        });
    });
    
    function load_members(members, container) {
        container.empty();
        /*var row = $('<tr>'
            + '<td><input type="radio" name="mid" value="0"></td>'
            + '<td colspan="3">设为顶层</td>'
            + '</tr>');
        row.click(function(){
            $('input:radio', this).prop('checked', true);
        });
        container.append(row);*/
        members && $.each(members, function(idx, member){
            var row = $('<tr>'
            + '<td><input type="radio" name="mid" value="'+ member.id +'"></td>'
            + '<td><img src="'+ member.avatar +'" class="avatar30" />'+ member.nickname +'</td>'
            + '<td>'+ member.name +'</td>'
            + '<td>'+ member.phone +'</td>'
            + '</tr>');
            row.click(function(){
                $('input:radio', this).prop('checked', true);
            });
            container.append(row);
        });
    }
});
</script>
@stop