@extends('layouts.sys')
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>行业管理管理</h5></div>
                <div class="col-sm-2">
                    <a href="#" class="btn btn-sm btn-success btn-add pull-right" data-parent=""><i class="fa fa-plus"></i> 添加主分类</a>
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
                @foreach($industries as $industry)
                @if ($industry->parent_id == 0)
                <tr>
                    <td>
                        <a href="#" data-url="{{ route('sys.industry.update', ['id'=>$industry->id]) }}" data-item="{{ json_encode(['title'=>$industry->title, 'parent_id'=>0], JSON_UNESCAPED_UNICODE) }}" class="btn btn-success btn-sm btn-edit">修改</a>
                        <a href="#" data-url="{{ route('sys.industry.destroy', ['id'=>$industry->id, '_token'=>csrf_token()]) }}" onclick="_delete_confirm(this);return false;" class="btn btn-danger btn-sm">删除</a>
                    </td>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $industry->title }} &nbsp;&nbsp;<a href="#" data-parent="{{ json_encode(['id'=>$industry->id, 'title'=>$industry->title]) }}" class="btn btn-sm btn-success btn-add-child"><i class="fa fa-plus"></i> 添加子分类</a></td>
                </tr>
                <?php $idx++?>
                @endif
                    @foreach($industries as $child_industry)
                    @if ($industry->id == $child_industry->parent_id)
                    <tr>
                        <td>
                            <a href="#" data-parent="{{ json_encode(['id'=>$industry->id, 'title'=>$industry->title], JSON_UNESCAPED_UNICODE) }}" data-url="{{ route('sys.industry.update', ['id'=>$child_industry->id]) }}" data-item="{{ json_encode(['title'=>$child_industry->title], JSON_UNESCAPED_UNICODE) }}" class="btn btn-success btn-sm btn-edit">修改</a>
                            <a href="#" data-url="{{ route('sys.industry.destroy', ['id'=>$child_industry->id, '_token'=>csrf_token()]) }}" onclick="_delete_confirm(this);return false;" class="btn btn-danger btn-sm">删除</a>
                        </td>
                        <td>{{ $idx + 1 }}</td>
                        <td>　-　{{ $child_industry->title }}</td>
                    </tr>
                    <?php $idx++?>
                    @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
@stop
@section('script')
<script type="text/html" id="tpl-form">
<div class="form-horizontal" style="padding:20px;">
<div class="form-group">
        <div class="col-sm-3 control-label">父级行业</div>
        <div class="col-sm-9">
            {!! Form::hidden('parent_id', '') !!}
            {!! Form::text('parent_title', '', ['class'=>'form-control', 'disabled'=>'true']) !!}
        </div>
    </div>
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
        show_dialog({}, '{{ route('sys.industry.store') }}', '添加行业', 'POST');
    });
    $('.btn-add-child').click(function(){
        var parent = $(this).data('parent');
        show_dialog({parent:parent}, '{{ route('sys.industry.store') }}', '添加行业', 'POST');
    });
    $('.btn-edit').click(function(){
        var parent = $(this).data('parent');
        var item = $(this).data('item');
        show_dialog({parent:parent, item:item}, $(this).data('url'), '修改行业', 'PUT');
    });

    function show_dialog(data, url, title, method) {
        dialog.show({
            title: title,
            width:500,
            height:150,
            content:$('#tpl-form').html(),
            after:function(){
                if (data.parent) {
                    $('input[name=parent_id]', this.content).val(data.parent.id);
                    $('input[name=parent_title]', this.content).val(data.parent.title);
                } else {
                    $('input[name=parent_id]', this.content).val(0);
                    $('input[name=parent_title]', this.content).val('无');
                }
                $('input[name=title]', this.content).val(data.item.title || '');
            },
            button:[
                {text:'确定', primary:true, click:function(){
                    var _this = this;
                    var data = {
                        title: $('input[name=title]', _this.content).val(),
                        parent_id: $('input[name=parent_id]', _this.content).val(),
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
