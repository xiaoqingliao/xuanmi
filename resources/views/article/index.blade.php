@extends('layouts.sys')
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>{{ $title }}管理</h5></div>
                <div class="col-sm-2">
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
                <col />
                <col width="100" />
            </colgroup>
            <thead>
                <tr>
                    <td>操作</td>
                    <td>#</td>
                    <td>标题</td>
                    <td>添加时间</td>
                </tr>
            </thead>
            <tbody>
                @foreach($articles as $idx=>$article)
                <tr>
                    <td>
                        <a href="#" data-url="{{ route('sys.article.destroy', ['id'=>$article->id, 'type'=>$article->type, '_token'=>csrf_token()]) }}" onclick="_delete_confirm(this);return false;" class="btn btn-danger btn-sm">删除</a>
                    </td>
                    <td>{{ $start + $idx + 1 }}</td>
                    <td>{{ $article->title }}</td>
                    <td>{{ $article->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="panel-footer">{!! $articles->appends($filters)->render() !!}</div>
    </div>
@stop
@section('script')
<script>
$(function(){
});
</script>
@stop