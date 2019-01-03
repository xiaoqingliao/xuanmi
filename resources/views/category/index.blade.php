@extends('layouts.sys')
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>{{ $type_title }}管理</h5></div>
                <div class="col-sm-2">
                    <a href="#" class="btn btn-sm btn-success btn-add pull-right"><i class="fa fa-plus"></i> 添加</a>
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
                <col width="120" />
                <col width="50" />
            </colgroup>
            <thead>
                <tr>
                    <td>操作</td>
                    <td>#</td>
                    <td>名称</td>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $idx=>$category)
                <tr>
                    <td>
                        <a href="#" data-url="{{ route('sys.category.update', ['id'=>$category->id, 'type'=>$category->type]) }}" data-item="{{ json_encode(['title'=>$category->title], JSON_UNESCAPED_UNICODE) }}" class="btn btn-success btn-sm btn-edit">修改</a>
                        <a href="#" data-url="{{ route('sys.category.destroy', ['id'=>$category->id, 'type'=>$category->type, '_token'=>csrf_token()]) }}" onclick="_delete_confirm(this);return false;" class="btn btn-danger btn-sm">删除</a>
                    </td>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $category->title }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop
@section('script')
<script type="text/html" id="tpl-form">
<div class="form-horizontal" style="padding:20px;">
    <div class="form-group">
        <div class="col-sm-3 control-label">名称：</div>
        <div class="col-sm-9">
            {!! Form::text('title', '', ['class'=>'form-control']) !!}
        </div>
    </div>
</div>
</script>
<script>
$(function(){
    var token = '{{ csrf_token() }}';
    $('.btn-add').click(function(){
        show_dialog({}, '{{ route('sys.category.store', ['type'=>$current_type]) }}', '添加类型', 'POST');
    });
    $('.btn-edit').click(function(){
        show_dialog($(this).data('item'), $(this).data('url'), '修改类型', 'PUT');
    });

    function show_dialog(data, url, title, method) {
        dialog.show({
            title: title,
            width:500,
            height:100,
            content:$('#tpl-form').html(),
            after:function(){
                $('input[name=title]', this.content).val(data.title || '');
            },
            button:[
                {text:'确定', primary:true, click:function(){
                    var _this = this;
                    var data = {
                        title: $('input[name=title]', _this.content).val(),
                        _token: token
                    };
                    console.log(data);
                    if (data.title == '') {
                        alert('未填写类型名称');
                        return;
                    }
                    _this.showLoading();
                    $.ajax({
                        url: url,
                        type: method,
                        data: data,
                        dataType:'json',
                        success:function(json) {
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
    }
});
</script>
@stop
