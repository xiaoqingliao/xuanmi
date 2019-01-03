@extends('layouts.sys')
@section('header')
@parent
@stop
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>人员管理 / 添加</h5></div>
            </div>
        </div>
        <div class="panel-body">
            @foreach($errors->all('<div class="alert alert-danger">:message</div>') as $message)
            {!! $message !!}
            @endforeach
            <form action="{{ route('sys.admin.store') }}" method="post" class="form-horizontal" role="form" id="admin-form">
                {!! method_field('POST') !!}
                {!! csrf_field() !!}
                <div class="form-group">
                    <label class="col-sm-3 control-label">姓名</label>
                    <div class="col-sm-6">
                        {!! Form::text('name', $user->name, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">用户名</label>
                    <div class="col-sm-6">
                        {!! Form::text('username', $user->username, ['class'=>'form-control', 'autocomplete'=>'false']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">密码</label>
                    <div class="col-sm-6">
                        {!! Form::password('password', ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">管理权限</label>
                    <div class="col-sm-6">
                        @foreach($permissions as $key=>$items)
                        <div class="panel">
                            <div class="panel-heading">
                                <label><input type="checkbox" class="checkall" /> {{ $items['title'] }} </label>
                            </div>
                            <div class="panel-body">
                            @foreach($items['items'] as $_key=>$_val)
                            <label style="margin-right:5px;">{!! Form::checkbox('permissions[]', $key . '.' . $_key, in_array($key . '.' . $_key, $user->permissions)) !!} {{ $_val }}</label>
                            @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">备注</label>
                    <div class="col-sm-6">
                        {!! Form::textArea('extensions[remark]', $user->getExtension('remark', ''), ['class'=>'form-control', 'style'=>'height:50px;']) !!}
                    </div>
                </div>
                <div class="col-sm-6 col-sm-offset-3">
                    {!! Form::submit('保存', ['class'=>'btn btn-success']) !!}
                    <a href="{{ route('sys.admin.index') }}" class="btn btn-default">返回</a>
                </div>
            </form>
        </div>
    </div>
@stop
@section('script')
<script>
$(function(){
    var token = '{{ csrf_token() }}';
    $('input.checkall').change(function(){
        var row = $(this).closest('.panel');
        $('.panel-body input:checkbox', row).prop('checked', $(this).prop('checked'));
    });
});
</script>
@stop