@extends('layouts.sys')
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>订单详情</h5></div>
                <div class="col-sm-2">
                </div>
            </div>
        </div>
        @if (session()->has('message'))
        <div class="panel-body">
            <div class="alert alert-warning">{{ session('message') }}</div>
        </div>
        @endif
        <table class="table table-bordered table-hovered table-striped">
            <colgroup>
                <col width="150">
            </colgroup>
            <tr>
                <th class="actived">订单号</th>
                <td>{{ $order->sn }}</td>
            </tr>
            <tr>
                <th class="actived">订单名称</th>
                <td>{{ $order->title }}</td>
            </tr>
            <tr>
                <th class="actived">下单会员</th>
                <td><a href="{{ route('sys.member.show', ['id'=>$order->member_id]) }}"><img src="{{ $order->member->avatarImage }}" class="avatar30" />{{ $order->member->nickname }}</a></td>
            </tr>
            <tr>
                <th class="actived">下单时间</th>
                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <th class="actived">订单费用</th>
                <td><label class="label label-info">{{ $order->price }}元</label></td>
            </tr>
            @if (!empty($order->rebate) && is_array($order->rebate))
            <tr>
                <th class="actived">订单分成</th>
                <td>
                    <table class="table table-bordered">
                        <tr>
                            <td>会员</td>
                            <td>分成比率</td>
                            <td>分成金额</td>
                        </tr>
                        @foreach($order->rebate as $item)
                        <tr>
                            <td><a href="{{ route('sys.member.show', ['id'=>$item['member']['id']]) }}">{{ $item['member']['nickname'] }}</a></td>
                            <td><label class="label label-info">{{ $item['money'] }}元</label></td>
                            <td>
                                @if (isset($item['rebate']))
                                <label class="label label-info">{{ $item['rebate'] }}%</label>
                                @elseif (isset($item['rebatea']))
                                <label class="label label-info">{{ $item['rebatea'] }}%</label>
                                @elseif (isset($item['rebateb']))
                                <label class="label label-info">{{ $item['rebateb'] }}%</label>
                                @elseif (isset($item['rebatea1']))
                                <label class="label label-info">{{ $item['rebatea1'] }}%</label>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
            @endif
            <tr>
                <th class="actived"></th>
                <td><a href="javascript:history.back();" class="btn btn-default">返回</a></td>
            </tr>
        </table>
    </div>
@stop