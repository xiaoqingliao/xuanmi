@extends('layouts.sys')
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>会员管理 / 查看</h5></div>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-hover table-bordered">
                <colgroup>
                    <col width="120" />
                </colgroup>
                <tbody>
                    @if ($member->logged)
                    <tr>
                        <th>昵称</th>
                        <td>{{ $member->nickname }}</td>
                    </tr>
                    <tr>
                        <th>头像</th>
                        <td><img src="{{ $member->avatarImage }}" class="avatar30" /></td>
                    </tr>
                    @if ($member->parent != null)
                    <tr>
                        <th>上级会员</th>
                        <td><a href="{{ route('sys.member.show', ['id'=>$member->parent_id]) }}">{{ $member->parent->nickname }}</a></td>
                    </tr>
                    @endif
                    <tr>
                        <th>团队人数</th>
                        <td><a href="{{ route('sys.member.proxy', ['parentid'=>$member->id]) }}" class="label label-info">{{ $member->childs_count }}人</a></td>
                    </tr>
                        @if ($member->group_id > 0)
                        <tr>
                            <th>姓名</th>
                            <td>{{ $member->name }}</td>
                        </tr>
                        <tr>
                            <th>公司</th>
                            <td>{{ $member->company }}</td>
                        </tr>
                        <tr>
                            <th>职务</th>
                            <td>{{ $member->duty }}</td>
                        </tr>
                        <tr>
                            <th>手机</th>
                            <td>{{ $member->phone }}</td>
                        </tr>
                        <tr>
                            <th>微信</th>
                            <td>{{ $member->wechat }}</td>
                        </tr>
                        <tr>
                            <th>代理级别</th>
                            <td>{{ $member->group != null ? $member->group->title : '元' }}</td>
                        </tr>
                        <tr>
                            <th>代理账户余额</th>
                            <td><a href="{{ route('sys.member.bills', ['id'=>$member->id]) }}" class="label label-info">{{ $member->proxy_balance }}元</a></td>
                        </tr>
                        <tr>
                            <th>续费分成余额</th>
                            <td><a href="{{ route('sys.member.renews', ['id'=>$member->id]) }}" class="label label-info">{{ $member->renewMoney }}元</a></td>
                        </tr>
                        <tr>
                            <th>代理时间</th>
                            <td>{{ date('Y-m-d', $member->proxy_start_time) }} - {{ date('Y-m-d', $member->proxy_end_time) }}</td>
                        </tr>
                        <tr>
                            <th>简介</th>
                            <td>{!! nl2br($member->summary) !!}</td>
                        </tr>
                        @endif
                    <tr>
                        <th>注册时间</th>
                        <td>{{ $member->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    <tr>
                        <th>最近登录时间</th>
                        <td>{{ date('Y-m-d H:i', $member->last_login) }}</td>
                    </tr>
                    <tr>
                        <th>最近购买时间</th>
                        <td>{{ $member->last_buy > 0 ? date('Y-m-d H:i', $member->last_buy) : '' }}</td>
                    </tr>
                    @else
                    <tr>
                        <th></th>
                        <td>未授权用户信息</td>
                    </tr>
                    @endif
                    <tr>
                        <th></th>
                        <td><a href="javascript:history.back();" class="btn btn-default">返回</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@stop