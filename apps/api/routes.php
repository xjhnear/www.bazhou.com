<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
/*-------------------------------认证-------------------------------*/

Route::pattern('symbol', '[\/]?');

/*-------------------------------系统-----------------------------*/
//配置√
Route::any('app/upgrade{symbol}',array('before'=>'uri_verify','uses'=>'AppController@upGrade'));

/*-------------------------------用户-------------------------------*/
//获取用户信息√
Route::get('user/info{symbol}',array('before'=>'uri_verify','uses'=>'UserController@getInfo'));
//头像
Route::get('user/avatar/{uid}{symbol}',array('uses'=>'UserController@getAvatar'));
//用户游币
Route::get('user/money{symbol}',array('before'=>'uri_verify','uses'=>'UserController@getMoney'));
//完善资料√
Route::post('user/edit{symbol}',array('before'=>'uri_verify','uses'=>'UserController@postEdit'));
//我的动态
Route::get('user/feeds{symbol}',array('before'=>'uri_verify','uses'=>'UserController@feeds'));
//@我
Route::get('user/atme{symbol}',array('before'=>'uri_verify','uses'=>'UserController@atme'));

Route::get('user/query',array('uses'=>'UserController@query'));


/*
App::missing(function($exception){
	return Response::json(array('result'=>array(),'errorCode'=>11211,'errorMessage'=>'Page Is Not Exists!!'));
});
*/
/*
App::error(function($exception){
    return Response::json(array('result'=>array(),'errorCode'=>11211,'errorMessage'=>'Server Error!!'));
});
*/

