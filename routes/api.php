<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::post('upload', ['as'=>'upload', 'uses'=>'FileController@upload']);

//Route::get('resource', ['uses'=>'ResourceController@index']);
//Route::resource('articles', 'ArticleController', ['except'=>['create', 'edit']]);

Route::get('home', ['uses'=>'HomeController@index']);
Route::get('doc', ['uses'=>'FileController@doc']);
Route::get('code/search', ['uses'=>'HomeController@codeSearch']);
Route::post('order/test', ['uses'=>'OrderController@payTest']);

Route::post('test/login', ['uses'=>'TestController@login']);
Route::post('wechat/login', ['uses'=>'HomeController@login']);
Route::get('groups', ['uses'=>'MemberController@groups']);
Route::get('regions', ['uses'=>'HomeController@regions']);
Route::get('merchant', ['uses'=>'HomeController@merchant']);
Route::get('industry', ['uses'=>'HomeController@industry']);

Route::get('setting', ['uses'=>'HomeController@setting']);
Route::post('upload', ['uses'=>'FileController@upload']);
Route::get('copyright', ['as'=>'api.copyright', 'uses'=>'HomeController@copyright']);
//日志
Route::get('log', ['uses'=>'FileController@log']);
Route::get('demo', ['uses'=>'DemoController@index']);
Route::get('maptest', ['uses'=>'DemoController@map']);

Route::group(['middleware'=>'api.auth'], function(){
    Route::get('video/upload', ['uses'=>'VideoController@upload']);

    Route::post('wechat/update', ['uses'=>'HomeController@update']);
    Route::post('proxy/focus', ['uses'=>'FriendController@postFocus']);
    Route::post('tag/like', ['uses'=>'HomeController@tagLike']);
    
    Route::post('phone/code', ['uses'=>'HomeController@sendCode']);
    Route::post('member/register', ['uses'=>'HomeController@register']);
    Route::post('order/proxy/new', ['uses'=>'OrderController@proxy_register']);

    Route::post('visit', ['uses'=>'HomeController@visit']);
});

Route::group(['prefix'=>'friend', 'middleware'=>'api.auth'], function(){
    Route::get('timeline', ['uses'=>'TimelineController@index']);
    Route::post('search', ['uses'=>'FriendController@recommend']);
});

Route::group(['prefix'=>'message', 'middleware'=>'api.auth'], function(){
    Route::get('{id}', ['uses'=>'MessageController@index']);
    Route::post('{id}', ['uses'=>'MessageController@send']);
    Route::get('visitor', ['uses'=>'MessageController@visitor']);
    Route::get('unread', ['uses'=>'MessageController@unread']);
});

Route::group(['prefix'=>'meeting'], function(){
    Route::get('/', ['uses'=>'MeetingController@index']);
    Route::get('{id}', ['uses'=>'MeetingController@show']);
    Route::get('category', ['uses'=>'MeetingController@category']);
});

Route::group(['prefix'=>'course'], function(){
    Route::get('/', ['uses'=>'CourseController@index']);
    Route::get('{id}', ['uses'=>'CourseController@show']);
    Route::get('{id}/catalog', ['uses'=>'CourseController@catalog']);
});

Route::group(['prefix'=>'article'], function(){
    Route::get('/', ['uses'=>'ArticleController@index']);
    Route::get('{id}', ['uses'=>'ArticleController@show']);
    Route::get('category', ['uses'=>'ArticleController@category']);
});

Route::group(['prefix'=>'order', 'middleware'=>'api.auth'], function(){
    Route::get('/', ['uses'=>'OrderController@index']);
    Route::post('/', ['uses'=>'OrderController@store']);
    Route::delete('{id}', ['uses'=>'OrderController@cancel']);
    Route::get('{id}', ['uses'=>'OrderController@show']);
    Route::post('{id}/pay', ['uses'=>'OrderController@pay']);
    //Route::post('{id}/prepare', ['uses'=>'OrderController@prepare']);
    
    Route::post('{id}/rate', ['uses'=>'RateController@rate']);
});

Route::group(['prefix'=>'cart', 'middleware'=>'api.auth'], function(){
    Route::get('/', ['uses'=>'CartController@index']);
    Route::post('add', ['uses'=>'CartController@add']);
    Route::post('{id}/update', ['uses'=>'CartController@update']);
    Route::delete('remove', ['uses'=>'CartController@remove']);
});

Route::get('banners', ['uses'=>'BannerController@index']);
Route::get('rates', ['uses'=>'RateController@index']);
Route::get('promocode/{id}.jpg', ['as'=>'api.promocode', 'uses'=>'MemberController@promocode']);

Route::group(['prefix'=>'member', 'middleware'=>'api.auth'], function(){
    Route::get('info', ['uses'=>'MemberController@info']);
    Route::post('info', ['uses'=>'MemberController@updateInfo']);
    Route::get('show', ['uses'=>'MemberController@getShow']);
    Route::post('show', ['uses'=>'MemberController@updateShow']);
    
    Route::get('group', ['uses'=>'MemberController@group']);
    Route::get('standing', ['uses'=>'MemberController@standing']);
    Route::get('bills', ['uses'=>'MemberController@bills']);
    Route::post('withdraw', ['uses'=>'MemberController@withdraw']);
    Route::get('visit', ['uses'=>'MemberController@visits']);

    Route::get('focus', ['uses'=>'FriendController@focus']);
    Route::get('fans', ['uses'=>'FriendController@fans']);
    
    Route::get('proxy/apply', ['uses'=>'MemberProxyController@my']);
    Route::post('proxy/apply', ['uses'=>'MemberProxyController@apply']);

    Route::group(['prefix'=>'meeting'], function(){
        Route::get('/', ['uses'=>'MeetingController@my']);
        Route::post('/', ['uses'=>'MeetingController@store']);
        Route::post('{id}', ['uses'=>'MeetingController@update']);
        Route::delete('{id}', ['uses'=>'MeetingController@destroy']);
        Route::post('order', ['uses'=>'MeetingController@updateOrder']);
    });
    
    Route::group(['prefix'=>'course'], function(){
        Route::get('/', ['uses'=>'CourseController@my']);
        Route::post('/', ['uses'=>'CourseController@store']);
        Route::post('{id}', ['uses'=>'CourseController@update']);
        Route::delete('{id}', ['uses'=>'CourseController@destroy']);
        Route::post('order', ['uses'=>'CourseController@updateOrder']);
    });

    Route::group(['prefix'=>'article'], function(){
        Route::get('/', ['uses'=>'ArticleController@my']);
        Route::post('/', ['uses'=>'ArticleController@store']);
        Route::post('{id}', ['uses'=>'ArticleController@update']);
        Route::delete('{id}', ['uses'=>'ArticleController@destroy']);
        Route::post('order', ['uses'=>'ArticleController@updateOrder']);
    });
    
    Route::group(['prefix'=>'banner'], function(){
        Route::get('/', ['uses'=>'BannerController@my']);
        Route::post('/', ['uses'=>'BannerController@store']);
        Route::post('{id}', ['uses'=>'BannerController@update']);
        Route::delete('{id}', ['uses'=>'BannerController@destroy']);
        Route::post('order', ['uses'=>'BannerController@updateOrder']);
    });

    Route::group(['prefix'=>'notice'], function(){
        Route::get('/', ['uses'=>'NoticeController@index']);
        Route::delete('{id}', ['uses'=>'NoticeController@destroy']);
        Route::delete('empty', ['uses'=>'NoticeController@empty']);
    });
});
