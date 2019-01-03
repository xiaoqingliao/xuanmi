<?php

Route::pattern('id', '\d+');
Route::pattern('dir', '\d{8}');
Route::pattern('dir2', '\!?\d{8}');
Route::pattern('path', '[a-zA-Z0-9]+\.(jpg|jpeg|png|bmp|mp3|mp4|gif|pdf|doc|xls)');
Route::get('image/{dir}/{path}', ['as'=>'image.show', 'uses'=>'FileController@imageShow']);
Route::get('image/{dir}/{path}@{size}', ['as'=>'image.show.size', 'uses'=>'FileController@imageShow']);
Route::get('media/{dir}/{path}', ['as'=>'file.media', 'uses'=>'FileController@media']);
Route::get('download/{dir}/{path}', ['as'=>'file.media', 'uses'=>'FileController@media']);

Route::any('weixin/pay/nofity', ['as'=>'weixin.pay.notify', 'uses'=>'PayController@notify']);
Route::get('cover/{dir2}/{path}', ['as'=>'file.cover','uses'=>'FileController@cover']);