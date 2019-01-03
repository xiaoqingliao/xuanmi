@extends('layouts.sys')
@section('main-content')
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>修改密码</h5></div>
            </div>
        </div>
        <div class="panel-body">
            @if (session()->has('message'))
                <div class="alert alert-warning">{{ session('message') }}</div>
            @endif
            <form action="" method="post" class="form-horizontal" role="form" id="pwd-form">
                {!! method_field('POST') !!}
                {!! csrf_field() !!}
                <div class="form-group">
                    <label class="col-sm-3 control-label">旧密码</label>
                    <div class="col-sm-6">
                        {!! Form::password('old_pwd', ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">新密码</label>
                    <div class="col-sm-6">
                        {!! Form::password('pwd', ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">确认密码</label>
                    <div class="col-sm-6">
                        {!! Form::password('retry_pwd', ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-6 col-sm-offset-3">
                    {!! Form::submit('保存', ['class'=>'btn btn-success']) !!}
                </div>
            </form>
        </div>
    </div>
</div>
@stop
@section('script')
<script>
$(function(){
    $('#pwd-form').submit(function(){
        var pwd = $('input[name=pwd]').val();
        var rpwd = $('input[name=retry_pwd]').val();

        if (pwd == '' || pwd != rpwd) {
            alert('新密码不一致');
            return false;
        }
        return true;
    });
});
</script>
@stop