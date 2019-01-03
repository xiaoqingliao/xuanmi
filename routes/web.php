<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', ['uses'=>'HomeController@login']);
Route::post('login', ['uses'=>'HomeController@postLogin']);

Route::group(['middleware'=>'auth:sys'], function(){
    Route::get('/', ['as'=>'dashboard', 'uses'=>'HomeController@home']);

    Route::match(['get', 'post'], 'ueditor', ['as'=>'ueditor', 'uses'=>'FileController@ueditor']);
    Route::post('upload', ['as'=>'upload', 'uses'=>'FileController@upload']);
    Route::get('images', ['as'=>'images', 'uses'=>'FileController@images']);

    Route::get('password', ['as'=>'password', 'uses'=>'HomeController@password']);
    Route::post('password', ['as'=>'password', 'uses'=>'HomeController@postPassword']);

    Route::post('admin/{id}/disable', ['as'=>'admin.disable', 'uses'=>'AdminController@disable']);
    Route::resource('admin', 'AdminController', ['except'=>['destroy']]);

    Route::get('site/config', ['as'=>'site.config', 'uses'=>'SiteConfigController@config']);
    Route::post('site/config', ['uses'=>'SiteConfigController@postConfig']);
    
    Route::resource('membergroup', 'MemberGroupController', ['except'=>['destroy', 'show']]);
    Route::group(['prefix'=>'member'], function(){
        Route::get('applies', ['as'=>'member.apply', 'uses'=>'MemberApplyController@index']);
        Route::post('applies/accept', ['as'=>'member.apply.accept', 'uses'=>'MemberApplyController@accept']);
        Route::post('applies/reject', ['as'=>'member.apply.reject', 'uses'=>'MemberApplyController@reject']);

        Route::get('withdraws/{id}', ['as'=>'member.withdraw.show', 'uses'=>'MemberWithdrawController@show']);
        Route::get('withdraws', ['as'=>'member.withdraw', 'uses'=>'MemberWithdrawController@index']);
        Route::post('withdraws/accept', ['as'=>'member.withdraw.accept', 'uses'=>'MemberWithdrawController@accept']);
        Route::post('withdraws/reject', ['as'=>'member.withdraw.reject', 'uses'=>'MemberWithdrawController@reject']);
        
        Route::get('normal', ['as'=>'member.normal', 'uses'=>'MemberController@normal']);
        Route::get('proxy', ['as'=>'member.proxy', 'uses'=>'MemberController@proxy']);
        Route::get('ajax', ['as'=>'member.ajax', 'uses'=>'MemberController@searchAjax']);
        Route::post('proxy/update', ['as'=>'member.proxy.update', 'uses'=>'MemberController@update']);
        Route::get('{id}', ['as'=>'member.show', 'uses'=>'MemberController@show']);
        Route::post('{id}/changeparent', ['as'=>'member.changeparent', 'uses'=>'MemberController@changeParent']);
        Route::get('{id}/bills', ['as'=>'member.bills', 'uses'=>'MemberController@bills']);
        Route::post('{id}/bills', ['as'=>'member.bills.post', 'uses'=>'MemberController@postBills']);
        Route::get('{id}/renews', ['as'=>'member.renews', 'uses'=>'MemberController@renews']);
        Route::post('{id}/renews', ['as'=>'member.renewpost', 'uses'=>'MemberController@postRenews']);
        Route::get('{id}/upgrade/logs', ['as'=>'member.upgradelogs', 'uses'=>'MemberController@upgradeLogs']);
        Route::post('{id}/conversion', ['as'=>'member.conversion', 'uses'=>'MemberController@conversion']);
    });

    Route::resource('category', 'CategoryController', ['expect'=>['create', 'edit', 'show']]);
    Route::resource('article', 'ArticleController', ['only'=>['index', 'destroy']]);
    Route::resource('meeting', 'MeetingController', ['only'=>['index', 'destroy']]);
    Route::resource('course', 'CourseController', ['only'=>['index', 'destroy']]);
    Route::resource('industry', 'IndustryController', ['only'=>['index', 'store', 'update', 'destroy']]);
    Route::group(['prefix'=>'company'], function(){
        Route::get('finance', ['as'=>'finance.index', 'uses'=>'CompanyFinanceController@index']);
        Route::post('finance/search', ['as'=>'finance.search', 'uses'=>'CompanyFinanceController@postIndex']);
    });

    Route::group(['prefix'=>'order'], function(){
        Route::get('reg', ['as'=>'order.reg', 'uses'=>'OrderController@reglist']);
        Route::get('renew', ['as'=>'order.renew', 'uses'=>'OrderController@renewlist']);
        Route::get('{id}', ['as'=>'order.show', 'uses'=>'OrderController@show']);
        Route::get('', ['as'=>'order.normal', 'uses'=>'OrderController@index']);
    });

    //Route::resource('article', 'ArticleController', ['except'=>['show']]);

    Route::get('logout', ['as'=>'logout', 'uses'=>'HomeController@logout']);
});