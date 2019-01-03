<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<title>{{ isset($page_title) ? $page_title : '' }}</title>
    <meta name="format-detection" content="telephone=no">
    @if (config('site.logo', '') != '')
    <link rel="apple-touch-icon" href="{{ image_url(config('site.logo'), 228, 228) }}" />
    <link rel="apple-touch-icon" sizes="72x72" href="{{ image_url(config('site.logo'), 114, 114) }}" />
    <link rel="apple-touch-icon-precomposed" href="{{ image_url(config('site.logo'), 228, 228) }}" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{ image_url(config('site.logo'), 114, 114) }}" />
    @endif
    @yield('header')
</head>
<body>
	@yield('content')
</body>
</html>
