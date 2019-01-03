@extends('layouts.base')
@section('header')
@parent
{!! Html::style('assets/sys/css/bootstrap.min.css') !!}
{!! Html::style('assets/sys/css/font-awesome.min.css') !!}
<style>
html,body{position:relative;width:100%;height:100%;background:url({{ asset('assets/sys/img/bg/'. rand(1,41) .'.jpg') }}) no-repeat;background-size:cover;}
.errors{position:absolute;top:10px;left:50%;margin-left:-200px;min-width:365px;max-width:400px;}
.well{margin:0 auto;margin-top:150px;min-width:365px;max-width:400px;
    -webkit-box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    background: rgba(255,255,255, 0.2);}
.well .avatar{
	text-align:center;
	width:100px;height:100px;
	border-radius:50%;overflow:hidden;
	margin:0 auto;
	margin-bottom:30px;
	margin-top:20px;
}
.well .avatar img{max-width:100%;max-height:100%;}
.btn-primary{background:#18bc9c;border-color:#18bc9c;}
.btn-primary:hover{background:#128f76;border-color:#11866f;}
.footer{text-align:center;color:#fff;padding-top:40px;}
</style>
@stop
@section('content')
<div class="container" style="height:100%;">
	@if ($failed)
	<div class="errors">
		<div class="alert alert-danger"><i class="fa fa-warning"></i> 登陆失败，请检查用户名和密码是否正确</div>
	</div>
	@endif
	@if (session('message'))
	<div class="errors">
		<div class="alert alert-danger"><i class="fa fa-warning"></i> {{ session('message') }}</div>
	</div>
	@endif
	<div>
		<div class="well">
			<div class="login-form">
				@if (empty($logo))
				<div class="avatar"><img src="{{ asset('assets/avatar.jpg') }}"></div>
				@else
				<div class="avatar"><img src="{{ image_url($logo) }}"></div>
				@endif
				<form action="" method="post" role="form">
					{!! csrf_field() !!}
					<input name="redirect" type="hidden" value="{{ $redirect }}" />
					<div class="input-group" style="padding-bottom:10px;">
						<div class="input-group-addon">
							<i class="fa fa-user"></i>
						</div>
						<input type="text" name="username" class="form-control input-sm" id="username" placeholder="请输入用户名" value="" />
					</div>
					<div class="input-group" style="padding-bottom:10px;">
						<div class="input-group-addon">
							<i class="fa fa-unlock-alt"></i>
						</div>
						<input type="password" name="password" class="form-control input-sm" id="password" placeholder="请输入密码" />
					</div>
					<div style="margin-top:20px;">
						<button type="submit" class="btn btn-primary btn-lg" style="width:100%;">登陆</button>
					</div>
				</form>
			</div>
		</div>
		<div class="footer">技术支持：<a href="http://www.nb800.cn" target="_blank" style="color:#fff;">宁波泽诚信息科技有限公司</a></div>
	</div>
</div>
@stop
