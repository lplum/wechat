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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/wechat/get_access_token','wechatController@get_access_token');
Route::get('/wechat/get_user_list','wechatController@get_user_list');
Route::get('/wechat/login','wechatLoginController@login'); 
Route::get('/wechat/wlogin','wechatLoginController@wechatLogin');
Route::get('/wechat/code','wechatLoginController@code');
Route::get('/wechat/clear_api','wechatController@clear_api');
Route::get('/wechat/source','wechatController@wechat_source'); //素材管理
Route::get('/wechat/upload','wechatController@upload'); //上传
Route::post('/wechat/do_upload','wechatController@do_upload'); //上传
Route::get('/wechat/addtag','wechatController@tag'); //添加标签页
Route::post('/wechat/doaddtag','wechatController@add_tag'); //添加操作
Route::get('/wechat/taglist','wechatController@tag_list'); //列表
Route::get('/wechat/deltag/{id}','wechatController@del_tag'); //删除操作
Route::get('/wechat/edtag/{id}','wechatController@edit_tag'); //修改操作
Route::post('/wechat/edit_tag','wechatController@do_edit_tag'); //改操作
Route::get('/wechat/tag_openid_list','wechatController@tag_openid_list'); //标签下用户的openid列表
Route::post('/wechat/tag_openid','wechatController@tag_openid'); //为用户打标签
Route::get('/wechat/user_tag_list','wechatController@user_tag_list'); //用户下的标签列表
Route::get('/wechat/push_tag_message','wechatController@push_tag_message'); //推送标签消息
Route::post('/wechat/do_push_tag_message','wechatController@do_push_tag_message'); //执行推送标签消息
