@extends('layouts.sys')
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>人员管理 / 查看</h5></div>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-hover table-bordered">
                <colgroup>
                    <col width="120" />
                </colgroup>
                <tbody>
                    <tr>
                        <th class="active">姓名</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th class="active">用户名</th>
                        <td>{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <th class="active">备注</th>
                        <td>{{ $user->getExtension('remark') }}</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td><a href="{{ route('sys.admin.index') }}" class="btn btn-default">返回</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@stop