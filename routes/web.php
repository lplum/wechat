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
Route::any('/wechat/event','EventController@event');
Route::get('/wechat/get_access_token','wechatController@get_access_token');
Route::get('/wechat/get_user_list','wechatController@get_user_list');
Route::get('/wechat/get_user_detail/{openid}','wechatController@get_user_detail');
Route::get('/wechat/login','wechatLoginController@login'); 
Route::get('/wechat/wlogin','wechatLoginController@wechatLogin');
Route::get('/wechat/code','wechatLoginController@code');
Route::get('/wechat/clear_api','wechatController@clear_api');
Route::get('/wechat/addtag','wechatController@tag'); //添加标签页
Route::post('/wechat/doaddtag','wechatController@add_tag'); //添加操作
Route::get('/wechat/taglist','wechatController@tag_list'); //列表
Route::get('/wechat/deltag/{id}','wechatController@del_tag'); //删除操作
Route::get('/wechat/edtag/{id}','wechatController@edit_tag'); //修改操作
Route::post('/wechat/edit_tag','wechatController@do_edit_tag'); //改操作
Route::post('/wechat/usertaglist','wechatController@usertaglist'); //用户打标签
Route::get('/wechat/user_tag','wechatController@user_tag'); //标签粉丝列表
Route::get('/wechat/pushtag','wechatController@push_tag_message'); //推送消息
Route::post('/wechat/do_push','wechatController@do_push_tag_message'); //推送消息
Route::get('/wechat/upload','uploadController@upload'); //上传
Route::post('/wechat/doupload','uploadController@doupload'); //上传操作
Route::get('/wechat/sourcelist','uploadController@source_list');
Route::get('/wechat/uploadlist','uploadController@uploadlist');//素材列表
Route::get('/wechat/download','uploadController@download');//下载素材
Route::get('/wechat/menu_list','menuController@menu_list'); //菜单列表
Route::post('/wechat/create_menu','menuController@create_menu'); //菜单
Route::get('/wechat/load_menu','menuController@load_menu'); //刷新菜单
Route::get('/zhou/login','ZhoukaoController@login'); 
Route::get('/zhou/wlogin','ZhoukaoController@wechatLogin');
Route::get('/zhou/code','ZhoukaoController@code');
Route::group(['middleware' => ['login'],'prefix'=>'/zhou'], function () {
    Route::get('/get_user_list','ZhoukaoController@get_user_list');
    Route::any('/push','ZhoukaoController@push_tag_message'); //推送消息
	Route::post('/do_push','ZhoukaoController@do_push_tag_message'); //推送消息
});
Route::get('/wechat/wechat_list','wechatController@wechat_list');
Route::get('/wechat/create_qrcode','wechatController@create_qrcode');