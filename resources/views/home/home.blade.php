@extends('layouts.sys')
@section('main-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-10"><h5>管理中心</h5></div>
            </div>
        </div>
        <div class="panel-body dashboard">
            @if ($page_user->permission('company.finance'))
            <div class="row">
                <div class="col-sm-4">
                    <div class="sm-st">
                        <span class="sm-st-icon" style="background:#F05050;"><i class="iconfont icon-recharge"></i></span>
                        <div class="sm-st-info">
                            <span>￥{{ $finance_stat->income }}</span>
                            总收入金额
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="sm-st">
                        <span class="sm-st-icon" style="background:#7266ba;"><i class="iconfont icon-yiwancheng1"></i></span>
                        <div class="sm-st-info">
                            <span>{{ $finance_stat->expend }}</span>
                            总支出金额
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="sm-st">
                        <span class="sm-st-icon" style="background:#23b7e5;"><i class="iconfont icon-shoukuan1"></i></span>
                        <div class="sm-st-info">
                            <span>{{ $finance_stat->gain }}</span>
                            总盈利金额
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-lg-8 col-sm-8">
                    <div id="echart" style="height:200px;width:100%;"></div>
                </div>
                <div class="col-lg-4 col-sm-4">
                    <div class="card sameheight-item stats">
                        <div class="card-block">
                            <div class="row row-sm stats-container">
                                <div class="col-xs-6 stat-col">
                                    <div class="stat-icon"> <i class="fa fa-users"></i> </div>
                                    <div class="stat">
                                        <div class="value"> {{ $today_reg }} </div>
                                        <div class="name"> 今日注册 </div>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-success" style="width: 25%"></div>
                                    </div>
                                </div>
                                <div class="col-xs-6 stat-col">
                                    <div class="stat-icon"> <i class="fa fa-rocket"></i> </div>
                                    <div class="stat">
                                        <div class="value"> {{ $today_renew }} </div>
                                        <div class="name"> 今日续费 </div>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-success" style="width: 25%"></div>
                                    </div>
                                </div>
                                <div class="col-xs-6  stat-col">
                                    <div class="stat-icon"> <i class="fa fa-line-chart"></i> </div>
                                    <div class="stat">
                                        <div class="value"> {{ $today_proxy }} </div>
                                        <div class="name"> 今日新增代理 </div>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-success" style="width: 25%"></div>
                                    </div>
                                </div>
                                <div class="col-xs-6  stat-col">
                                    <div class="stat-icon"> <i class="fa fa-shopping-cart"></i> </div>
                                    <div class="stat">
                                        <div class="value"> {{ $seven_proxy }} </div>
                                        <div class="name"> 七日新增代理 </div>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-success" style="width: 25%"></div>
                                    </div>
                                </div>
                                <div class="col-xs-6  stat-col">
                                    <div class="stat-icon"> <i class="fa fa-list-alt"></i> </div>
                                    <div class="stat">
                                        <div class="value"> {{ $seven_reg }} </div>
                                        <div class="name"> 七日新增用户 </div>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-success" style="width: 25%"></div>
                                    </div>
                                </div>
                                <div class="col-xs-6 stat-col">
                                    <div class="stat-icon"> <i class="fa fa-dollar"></i> </div>
                                    <div class="stat">
                                        <div class="value"> {{ $seven_renew }} </div>
                                        <div class="name"> 七日新增续费 </div>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-success" style="width: 25%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                @if ($page_user->permission('member.upgrade'))
                <div class="col-xs-6 col-lg-6">
                    <a href="{{ route('sys.member.apply') }}" class="panel" style="display:block;background:#0073b7;color:#fff;">
                        <div class="panel-body">
                            <div class="panel-title">
                                <h5>待处理代理升级</h5>
                            </div>
                            <div class="panel-content">
                                <h1 class="no-margins">{{ $proxy_count }}</h1>
                            </div>
                        </div>
                    </a>
                </div>
                @endif
                @if ($page_user->permission('member.wallet'))
                <div class="col-xs-6 col-lg-6">
                    <a href="{{ route('sys.member.withdraw') }}" class="panel" style="display:block;background:#0073b7;color:#fff;">
                        <div class="panel-body">
                            <div class="panel-title">
                                <h5>待处理提现申请</h5>
                            </div>
                            <div class="panel-content">
                                <h1 class="no-margins">{{ $withdraw_count }}</h1>
                            </div>
                        </div>
                    </a>
                </div>
                @endif
            </div>
            <div class="row">
                @if ($page_user->permission('member.proxy'))
                <div class="col-lg-4">
                    <div class="box box-success">
                        <div class="box-header">
                            <span class="box-title">最新注册用户</span>
                            <span class="pull-right">
                                <a href="{{ route('sys.member.proxy') }}" class="btn btn-box-tool">全部用户</a>
                            </span>
                        </div>
                        <div class="box-body">
                            <table class="table table-hover table-striped">
                                <tbody>
                                @foreach($reg_list as $_reg)
                                <tr>
                                    <td><a href="{{ route('sys.member.show', ['id'=>$_reg->member_id]) }}"><img src="{{ $_reg->member->avatarImage }}" class="avatar30" /> {{ $_reg->member->nickname }}</a></td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                @if ($page_user->permission('member.upgrade'))
                <div class="col-lg-4">
                    <div class="box box-danger">
                        <div class="box-header">
                            <span class="box-title">待处理代理申请</span>
                            <span class="pull-right">
                                <a href="{{ route('sys.member.apply') }}" class="btn btn-box-tool">代理申请</a>
                            </span>
                        </div>
                        <div class="box-body">
                            <table class="table table-hover table-striped">
                                <tbody>
                                @foreach($proxy_list as $_proxy)
                                <tr>
                                    <td><img src="{{ $_proxy->member->avatarImage }}" class="avatar30" /> {{ $_proxy->member->nickname }}</td>
                                    <td style="text-align:right;">申请:{{ $_proxy->group->title }}</td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                @if ($page_user->permission('member.wallet'))
                <div class="col-lg-4">
                    <div class="box box-info">
                        <div class="box-header">
                            <span class="box-title">待处理提现申请</span>
                            <span class="pull-right">
                                <a href="{{ route('sys.member.withdraw') }}" class="btn btn-box-tool">提现申请</a>
                            </span>
                        </div>
                        <div class="box-body">
                            <table class="table table-hover table-striped">
                                <tbody>
                                @foreach($withdraw_list as $_withdraw)
                                <tr>
                                    <td><img src="{{ $_withdraw->member->avatarImage }}" class="avatar30" /> {{ $_withdraw->member->nickname }}</td>
                                    <td style="text-align:right;">提现：{{ $_withdraw->money }}元</td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@stop
@section('script')
{!! Html::script('assets/sys/js/echarts.common.min.js') !!}
{!! Html::script('assets/sys/js/walden.js') !!}
<script>
$(function(){
    var myChart = echarts.init(document.getElementById('echart'), 'walden');
    var option = {
            title: {
                text: ''
            },
            tooltip: {
                trigger:'axis'
            },
            legend: {
                data:['新增会员', '新增代理', '新增续费', '活跃会员']
            },
            xAxis: {
                type:'category',
                boundaryGap:false,
                data: {!! json_encode($chart_options['dates']) !!}
            },
            yAxis: {},
            grid: [{
                top: 20,
                left:40,
                right: 40,
                bottom: 20
            }],
            series: [{
                name: '新增会员',
                type: 'line',
                smooth: true,
                lineStyle: {normal:{width:1.5}},
                areaStyle: {normal:{}},
                data: {!! json_encode($chart_options['members']) !!}
            },{
                name: '新增代理',
                type: 'line',
                smooth: true,
                lineStyle: {normal:{width:1.5}},
                areaStyle: {normal:{}},
                data: {!! json_encode($chart_options['proxy']) !!}
            },{
                name: '新增续费',
                type: 'line',
                smooth: true,
                lineStyle: {normal:{width:1.5}},
                areaStyle: {normal:{}},
                data: {!! json_encode($chart_options['renews']) !!}
            },{
                name: '活跃会员',
                type: 'line',
                smooth: true,
                lineStyle: {normal:{width:1.5}},
                areaStyle: {normal:{}},
                data: {!! json_encode($chart_options['logins']) !!}
            }]
        };
        myChart.setOption(option);
});
</script>
@stop