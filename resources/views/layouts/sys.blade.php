@extends('layouts.base')
@section('header')
{!! Html::style('assets/sys/css/bootstrap.min.css') !!}
{!! Html::style('assets/sys/css/font-awesome.min.css') !!}
{!! Html::style('assets/sys/css/base.css') !!}
{!! Html::style('assets/sys/css/home.css?2') !!}
{!! Html::style('//at.alicdn.com/t/font_438592_juh8c5sgbwhr529.css') !!}
<style type="text/css">
.wrapper{padding:20px;}
</style>
<script type="text/javascript">
var config = {
    upload_url:'{{ route('sys.upload') }}',
    image_url:'{{ route('sys.images') }}',
    map_key: '{{ config('app.map_key') }}'
};
</script>
@stop
@section('content')
<div class="wrapper" id="wrapper-container">
    <div class="sidebar">
        <div class="nav-header">
            <div class="header-title">{{ $page_title }}</div>
        </div>
        <div class="scroll-wrapper">
            <ul class="nav">
                <li class="nav-title"><i class="fa fa-cog"></i><span class="text">平台管理</span></li>
                @if ($page_user->has('member.'))
                <li class="nav-items{{ starts_with($page_menu, 'member:') ? ' active' : '' }}">
                    <a href="#" title="会员管理"><i class="fa fa-user"></i><span class="text">会员管理</span><span class="pull-right fa fa-angle-left"></span></a>
                    <ul class="nav nav-childs">
                        @if ($page_user->permission('member.proxy'))
                        <li>
                            <a class="{{ $page_menu == 'member:proxy' ? 'active' : '' }}" href="{{ route('sys.member.proxy') }}" title="代理会员管理"><span class="text">代理会员管理</span></a>
                        </li>
                        @endif
                        @if ($page_user->permission('member.group'))
                        <li>
                            <a class="{{ $page_menu == 'member:group' ? 'active' : '' }}" href="{{ route('sys.membergroup.index') }}" title="代理级别管理"><span class="text">代理级别管理</span></a>
                        </li>
                        @endif
                        @if ($page_user->permission('member.upgrade'))
                        <li>
                            <a class="{{ $page_menu == 'member:upgrade' ? 'active' : '' }}" href="{{ route('sys.member.apply') }}" title="代理升级申请"><span class="text">代理升级申请</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($page_user->has('meeting.'))
                <li class="nav-items{{ starts_with($page_menu, 'meeting:') ? ' active' : '' }}">
                    <a href="#" title="会议管理"><i class="fa fa-group"></i><span class="text">会议管理</span><span class="pull-right fa fa-angle-left"></span></a>
                    <ul class="nav nav-childs">
                        @if ($page_user->permission('meeting.category'))
                        <li>
                            <a class="{{ $page_menu == 'meeting:category' ? 'active' : '' }}" href="{{ route('sys.category.index', ['type'=>\App\Models\Category::TYPE_MEETING]) }}" title="会议类型管理"><span class="text">会议类型管理</span></a>
                        </li>
                        @endif
                        @if ($page_user->permission('meeting.list'))
                        <li>
                            <a class="{{ $page_menu == 'meeting:list' ? 'active' : '' }}" href="{{ route('sys.meeting.index') }}" title="会议管理"><span class="text">会议管理</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($page_user->has('course.'))
                <li class="nav-items{{ starts_with($page_menu, 'course:') ? ' active' : '' }}">
                    <a href="#" title="课程管理"><i class="fa fa-book"></i><span class="text">课程管理</span><span class="pull-right fa fa-angle-left"></span></a>
                    <ul class="nav nav-childs">
                        @if ($page_user->permission('course.category') && false)
                        <li>
                            <a class="{{ $page_menu == 'course:category' ? 'active' : '' }}" href="{{ route('sys.category.index', ['type'=>\App\Models\Category::TYPE_COURSE]) }}" title="课程类型管理"><span class="text">课程类型管理</span></a>
                        </li>
                        @endif
                        @if ($page_user->permission('course.list'))
                        <li>
                            <a class="{{ $page_menu == 'course:list' ? 'active' : '' }}" href="{{ route('sys.course.index') }}" title="课程管理"><span class="text">课程管理</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($page_user->has('order.'))
                <li class="nav-items{{ starts_with($page_menu, 'order:') ? ' active' : '' }}">
                    <a href="#" title="订单管理"><i class="fa fa-money"></i><span class="text">订单管理</span><span class="pull-right fa fa-angle-left"></span></a>
                    <ul class="nav nav-childs">
                        @if ($page_user->permission('order.reg'))
                        <li>
                            <a class="{{ $page_menu == 'order:reg' ? 'active' : '' }}" href="{{ route('sys.order.reg') }}" title="代理注册订单"><span class="text">代理注册订单</span></a>
                        </li>
                        <li>
                            <a class="{{ $page_menu == 'order:renew' ? 'active' : '' }}" href="{{ route('sys.order.renew') }}" title="代理续费订单"><span class="text">代理续费订单</span></a>
                        </li>
                        @endif
                        @if ($page_user->permission('order.normal'))
                        <li>
                            <a class="{{ $page_menu == 'order:index' ? 'active' : '' }}" href="{{ route('sys.order.normal') }}" title="会议订单"><span class="text">会议/课程订单</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($page_user->has('company.'))
                <li class="nav-items{{ starts_with($page_menu, 'company:') ? ' active' : '' }}">
                    <a href="#" title="公司财务管理"><i class="fa fa-money"></i><span class="text">公司财务管理</span><span class="pull-right fa fa-angle-left"></span></a>
                    <ul class="nav nav-childs">
                        @if ($page_user->permission('company.finance'))
                        <li>
                            <a class="{{ $page_menu == 'company:finance' ? 'active' : '' }}" href="{{ route('sys.finance.index') }}" title="财务日志"><span class="text">财务日志</span></a>
                        </li>
                        @endif
                        @if ($page_user->permission('company.wallet'))
                        <li>
                            <a class="{{ $page_menu == 'company:wallet' ? 'active' : '' }}" href="{{ route('sys.member.withdraw') }}" title="提现申请管理"><span class="text">提现申请管理</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (false)
                @if ($page_user->has('article.'))
                <li class="nav-items{{ starts_with($page_menu, 'article:') ? ' active' : '' }}">
                    <a href="#" title="内容管理"><i class="fa fa-list"></i><span class="text">文章管理</span><span class="pull-right fa fa-angle-left"></span></a>
                    <ul class="nav nav-childs">
                        @if ($page_user->permission('article.category'))
                        <li>
                            <a class="{{ $page_menu == 'article:category' ? 'active' : '' }}" href="{{ route('sys.category.index', ['type'=>\App\Models\Category::TYPE_ARTICLE]) }}">类型管理</a>
                        </li>
                        @endif
                        @if ($page_user->permission('article.list'))
                        <li>
                            <a class="{{ $page_menu == 'article:list' ? 'active' : '' }}" href="{{ route('sys.article.index', ['type'=>\App\Models\Category::TYPE_ARTICLE]) }}">文章管理</a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($page_user->permission('product.list'))
                <li class="nav-items">
                    <a href="{{ route('sys.article.index', ['type'=>\App\Models\Category::TYPE_PRODUCT]) }}"><i class="fa fa-list"></i><span class="text">产品展示管理</span></a>
                </li>
                @endif
                @if ($page_user->permission('company.list'))
                <li class="nav-items">
                    <a href="{{ route('sys.article.index', ['type'=>\App\Models\Category::TYPE_COMPANY]) }}"><i class="fa fa-list"></i><span class="text">企业展示管理</span></a>
                </li>
                @endif
                @endif
                
                <li class="nav-title"><i class="fa fa-cogs"></i><span class="text">系统设置</span></li>
                @if ($page_user->permission('sys.user'))
                <li class="nav-items{{ $page_menu == 'sys:user' ? ' active' : '' }}">
                    <a href="{{ route('sys.admin.index') }}" title="用户管理"><i class="fa fa-user"></i><span class="text">用户管理</span></a>
                </li>
                @endif
                @if ($page_user->permission('sys.params'))
                <li class="nav-items{{ $page_menu == 'sys:param:site' ? ' active' : '' }}">
                    <a href="{{ route('sys.site.config', ['category'=>'site']) }}" class="参数设置"><i class="fa fa-bars"></i><span class="text">参数设置</span></a>
                </li>
                @endif
                @if ($page_user->permission('sys.noticeparam'))
                <li class="nav-items{{ $page_menu == 'sys:param:noticeparam' ? ' active' : '' }}">
                    <a href="{{ route('sys.site.config', ['category'=>'noticeparam']) }}" class="财务分成回复消息设置"><i class="fa fa-bars"></i><span class="text">财务分成回复消息设置</span></a>
                </li>
                @endif
                @if ($page_user->permission('sys.copyright'))
                <li class="nav-items{{ $page_menu == 'sys:param:copyright' ? ' active' : '' }}">
                    <a href="{{ route('sys.site.config', ['category'=>'copyright']) }}" class="服务协议设置"><i class="fa fa-bars"></i><span class="text">服务协议设置</span></a>
                </li>
                @endif
                @if ($page_user->permission('sys.industry'))
                <li class="nav-items{{ $page_menu == 'sys:industry' ? ' active' : '' }}">
                    <a href="{{ route('sys.industry.index') }}" class="行业管理"><i class="fa fa-bars"></i><span class="text">行业管理</span></a>
                </li>
                @endif
                <li class="nav-items{{ $page_menu == 'sys:password' ? ' active' : '' }}">
                    <a class="{{ $page_menu == 'sys:password' ? 'active' : '' }}" href="{{ route('sys.password') }}" title="密码修改"><i class="fa fa-key"></i><span class="text"> 密码修改</span></a>
                </li>
                <li class="nav-items">
                    <a href="{{ route('sys.logout') }}"><i class="fa fa-sign-out"></i><span class="text"> 退出</span></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="page-wrapper">
        <div class="row wrapper-header">
            <div class="global-menus pull-left">
                <ul class="nav navbar-nav">
                    <li><a href="#" id="lnk-toggle"><i class="fa fa-list"></i></a></li>
                    <li class="active"><a href="{{ route('sys.dashboard') }}">首页</a></li>
                </ul>
            </div>
            <div class="infobar pull-right">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset('assets/avatar.jpg') }}" />
                            <span class="hidden-xs">{{ $page_user->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('sys.password') }}"><i class="fa fa-key"></i> 修改密码</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('sys.logout') }}"><i class="fa fa-power-off"></i></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="wrapper-content" style="padding:20px;">
            <div class="container-fluid">
            @yield('main-content')
            </div>
        </div>
    </div>
{!! Html::script('assets/sys/js/jquery.min.js') !!}
{!! Html::script('assets/sys/js/bootstrap.min.js') !!}
{!! Html::script('assets/sys/js/jquery.cookie.js') !!}
<script type="text/javascript">
seajs.config({
    alias: {},
    paths: {
        'css': '{{ asset('assets/sys/css') }}'
    }
});
$(function(){
    if ($.cookie('sys.menuhide') && $.cookie('sys.menuhide') == 1) {
        $('#wrapper-container').addClass('toggle');
    }
    $('.sidebar .nav-items>a').click(function(){
        $('.sidebar .nav-items').removeClass('active');
        $(this).parent().addClass('active');
    });
    $('.sidebar .nav-items').hover(function(){
        $(this).addClass('hover');
    }, function(){
        
        $(this).removeClass('hover');
    });
    $('#lnk-toggle').click(function(){
        if ($('#wrapper-container').hasClass('toggle')) {
            $('#wrapper-container').removeClass('toggle');
            $.cookie('sys.menuhide', '0', { expires: 365, path: '/' });
        } else {
            $('#wrapper-container').addClass('toggle');
            $.cookie('sys.menuhide', '1', { expires: 365, path: '/' });
        }
    });
    console.log('used ' + {{ microtime(true) - $page_start }});
});
</script>
    @yield('script')
</div>
@stop
