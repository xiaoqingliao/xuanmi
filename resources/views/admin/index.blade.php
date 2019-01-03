@extends('layouts.sys')
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>人员管理</h5></div>
                <div class="col-sm-2">
                    <a href="{{ route('sys.admin.create') }}" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加人员</a>
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
                <col width="150" />
                <col width="50" />
            </colgroup>
            <thead>
                <tr>
                    <td>操作</td>
                    <td>#</td>
                    <td>姓名</td>
                    <td>用户名</td>
                    <td>状态</td>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $idx=>$user)
                <tr>
                    <td>
                        @if ($user->username != 'admin')
                        <a href="{{ route('sys.admin.edit', ['id'=>$user->id]) }}" class="btn btn-success btn-sm">修改</a>
                        @if ($user->disabled)
                        <a href="#" data-url="{{ route('sys.admin.disable', ['id'=>$user->id]) }}" class="btn btn-danger btn-sm btn-disable">启用</a>
                        @else
                        <a href="#" data-url="{{ route('sys.admin.disable', ['id'=>$user->id]) }}" class="btn btn-danger btn-sm btn-disable">禁用</a>
                        @endif
                        @endif
                    </td>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>
                        {{ $user->disabled ? '禁用' : '正常' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop
@section('script')
<script>
$(function(){
    var token = '{{ csrf_token() }}';
    $('.btn-disable').click(function(){
        var text = $(this).text();
        var url = $(this).data('url');
        dialog.confirm({
            content:'确定要'+ text +'吗？',
            button:[
                {text:'确定', primary:true, click:function(){
                    this.showLoading();
                    $.ajax({
                        url:url,
                        type:'post',
                        data:{_token:token},
                        dataType:'json',
                        success:function(){
                            location.reload();
                        }
                    });
                }},
                {text:'取消', click:function(){this.hide();}}
            ]
        });
        return false;
    });
});
</script>
@stop