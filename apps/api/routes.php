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

//登录√
Route::get('account/login{symbol}',array('before'=>'uri_verify','uses'=>'AccountController@getLogin'));
//注册√
Route::get('account/register{symbol}',array('before'=>'uri_verify','uses'=>'AccountController@getRegister'));
//第三方登录√
Route::get('account/snslogin{symbol}',array('before'=>'uri_verify','uses'=>'AccountController@getSnslogin'));
//第三方注册√
Route::get('account/snsregister{symbol}',array('before'=>'uri_verify','uses'=>'AccountController@getSnsregister'));
//绑定第三方
Route::get('account/bind_sns{symbol}',array('before'=>'uri_verify','uses'=>'AccountController@bindSns'));
//验证邮箱是否被占用
Route::get('account/verify-email{symbol}',array('before'=>'uri_verify','uses'=>'AccountController@getVerifyEmail'));
//验证码
Route::get('account/verifycode{symbol}',array('uses'=>'AccountController@getVerifyCode'));
//Route::get('account/verify',array('before'=>'uri_verify','uses'=>'AccountController@getVerify'));
//用户注销
Route::post('account/logout{symbol}',array('before'=>'uri_verify','uses'=>'AccountController@postLogout'));

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


/*-------------------------------聊天-------------------------------*/
//添加会话用户
Route::get('chat/adduser{symbol}',array('before'=>'uri_verify','uses'=>'ChatController@addUser'));
//会话用户列表
Route::get('chat/users{symbol}',array('before'=>'uri_verify','uses'=>'ChatController@users'));
//聊天记录
Route::get('chat/detail{symbol}',array('before'=>'uri_verify','uses'=>'ChatController@detail'));
//删除聊天记录
Route::get('chat/delete{symbol}',array('before'=>'uri_verify','uses'=>'ChatController@delete'));
//发送聊天内容
Route::post('chat/send{symbol}',array('before'=>'uri_verify','uses'=>'ChatController@sendMessage'));

/*-------------------------------系统消息-------------------------------*/
//系统通知√
Route::get('message/notice{symbol}',array('before'=>'uri_verify','uses'=>'MessageController@notice'));
//消息数√
Route::get('message/number{symbol}',array('before'=>'uri_verify','uses'=>'MessageController@msgNumber'));
//阅读系统消息√
Route::get('message/read{symbol}',array('before'=>'uri_verify','uses'=>'MessageController@read'));
//删除系统消息√
Route::get('message/delete{symbol}',array('before'=>'uri_verify','uses'=>'MessageController@delete'));

/*-------------------------------问答/论坛-------------------------------*/
//论坛首页√
Route::get('forum/home{symbol}',array('before'=>'','uses'=>'ForumController@home'));
//论坛版块帖子列表√
Route::get('forum/topic-list{symbol}',array('before'=>'uri_verify','uses'=>'ForumController@getTopicList'));
//圈友√
Route::get('forum/friends{symbol}',array('before'=>'uri_verify','uses'=>'ForumController@circleFriends'));
//发帖√
Route::post('topic/post-topic{symbol}',array('before'=>'uri_verify','uses'=>'TopicController@postPostTopic'));
//删帖√
Route::get('topic/delete{symbol}',array('before'=>'uri_verify','uses'=>'TopicController@getDelete'));
/*-------------------------------评论-------------------------------*/
//评论√
Route::get('comment/list{symbol}',array('before'=>'uri_verify','uses'=>'CommentController@home'));
//发评论/回复评论√
Route::post('comment/post-comment{symbol}',array('before'=>'uri_verify','uses'=>'CommentController@postComment'));
//删评论√
Route::get('comment/delete-comment{symbol}',array('before'=>'uri_verify','uses'=>'CommentController@deleteComment'));
//设置最佳答案√
Route::get('comment/set-best{symbol}',array('before'=>'uri_verify','uses'=>'CommentController@setBest'));

/*-------------------------------关系-------------------------------*/
//好友
Route::get('relation/friends{symbol}',array('before'=>'uri_verify','uses'=>'RelationController@getFriends'));
//关注√
Route::get('relation/follows{symbol}',array('before'=>'uri_verify','uses'=>'RelationController@getFollows'));
//粉丝√
Route::get('relation/followers{symbol}',array('before'=>'uri_verify','uses'=>'RelationController@getFollowers'));
//添加关注√
Route::get('relation/follow-create{symbol}',array('before'=>'uri_verify','uses'=>'RelationController@getFollowCreate'));
//取消关注√
Route::get('relation/follow-destroy{symbol}',array('before'=>'uri_verify','uses'=>'RelationController@getFollowDestroy'));

Route::controller('doc','DocController');
/*-------------------------------首屏-------------------------------*/
//首页√
Config::set('get home',array('3.0.0','3.1.0','3.1.5','3.1.6','3.1.7','3.2.0','3.2.1','3.3.0','3.4.0','3.5.0','3.5.1','3.6.0'));
Config::set('get home/',array('3.0.0','3.1.0','3.1.5','3.1.6','3.1.7','3.2.0','3.2.1','3.3.0','3.4.0','3.5.0','3.5.1','3.6.0'));
Route::get('home{symbol}',array('before'=>'uri_verify','uses'=>'HomeController@index'));

/*-------------------------------资讯-------------------------------*/
//资料大全√
Route::get('article/home{symbol}',array('before'=>'uri_verify','uses'=>'ArticleController@home'));
//文章详情(包括新闻、攻略、评测、新游、主题帖)√
Route::get('article/detail{symbol}',array('before'=>'uri_verify','uses'=>'ArticleController@detail'));
//资讯中心-新闻√
Route::get('article/news{symbol}',array('before'=>'uri_verify','uses'=>'ArticleController@news'));
//资讯中心-攻略合集√
Route::get('article/guide_collect{symbol}',array('before'=>'uri_verify','uses'=>'ArticleController@guide_collect'));
//资讯中心-攻略列表√
Route::get('article/guide_list{symbol}',array('before'=>'uri_verify','uses'=>'ArticleController@guide_list'));
//资讯中心-评测√
Route::get('article/opinion{symbol}',array('before'=>'uri_verify','uses'=>'ArticleController@opinion'));
/*-------------------------------视频-------------------------------*/
//美女视频√
Route::get('video{symbol}',array('before'=>'uri_verify','uses'=>'VideoController@girl'));
//视频详情√
Route::get('video/detail{symbol}',array('before'=>'uri_verify','uses'=>'VideoController@detail'));

/*-------------------------------WCA-----------------------------*/
//wca 多栏目 list 列表
Route::get('wca/list{symbol}',array('uses'=>'WcaController@getGuideLists'));


/*-------------------------------礼包-------------------------------*/
/*
//礼包列表√
Route::get('gift/home',array('before'=>'uri_verify','uses'=>'GiftController@home'));
//搜索礼包√
Route::get('gift/search',array('before'=>'uri_verify','uses'=>'GiftController@search'));
//礼包详情√
Route::get('gift/detail',array('before'=>'uri_verify','uses'=>'GiftController@detail'));
//我的礼包√
Route::get('gift/mygift',array('before'=>'uri_verify','uses'=>'GiftController@myGift'));
//领取礼包√
Route::get('gift/getgift',array('before'=>'uri_verify','uses'=>'GiftController@getGift'));
//我的预定√
Route::get('gift/myreserve',array('before'=>'uri_verify','uses'=>'GiftController@myReserveGift'));
//我的预定-删除√
Route::get('gift/delete-myreserve',array('before'=>'uri_verify','uses'=>'GiftController@removeMyReserveGift'));
//预定礼包√
Route::get('gift/reserve',array('before'=>'uri_verify','uses'=>'GiftController@reserveGift'));
*/


//礼包列表√
Route::get('gift/home{symbol}',array('before'=>'uri_verify','uses'=>'GiftbagController@home'));
//搜索礼包√
Route::get('gift/search{symbol}',array('before'=>'uri_verify','uses'=>'GiftbagController@search'));
//礼包详情√
Route::get('gift/detail{symbol}',array('before'=>'uri_verify','uses'=>'GiftbagController@detail'));
//我的礼包√
Route::get('gift/mygift{symbol}',array('before'=>'uri_verify','uses'=>'GiftbagController@myGift'));
//领取礼包√
Route::get('gift/getgift{symbol}',array('before'=>'uri_verify','uses'=>'GiftbagController@getGift'));
//我的预定√
Route::get('gift/myreserve{symbol}',array('before'=>'uri_verify','uses'=>'GiftbagController@myReserveGift'));
//我的预定-删除√
Route::get('gift/delete-myreserve{symbol}',array('before'=>'uri_verify','uses'=>'GiftbagController@removeMyReserveGift'));
//预定礼包√
Route::get('gift/reserve{symbol}',array('before'=>'uri_verify','uses'=>'GiftbagController@reserveGift'));


/*-------------------------------游戏-------------------------------*/
//热门游戏列表√
Route::get('game/hotgame{symbol}',array('before'=>'uri_verify','uses'=>'GameController@hotgame'));
//最新更新列表√
Route::get('game/lastupdate{symbol}',array('before'=>'uri_verify','uses'=>'GameController@lastupdate'));
//经典必玩√
Route::get('game/mustplay{symbol}',array('before'=>'uri_verify','uses'=>'GameController@mustplay'));
//特色专题√
Route::get('game/collect{symbol}',array('before'=>'uri_verify','uses'=>'GameController@collect'));
//特色专题-详情√
Route::get('game/collect_detail{symbol}',array('before'=>'uri_verify','uses'=>'GameController@collect_detail'));
//评测表√
Route::get('game/test_table{symbol}',array('before'=>'uri_verify','uses'=>'GameController@test_table'));
//信息介绍√
Route::get('game/info{symbol}',array('before'=>'uri_verify','uses'=>'GameController@info'));
//新游预告√
Route::get('game/newgame{symbol}',array('before'=>'uri_verify','uses'=>'GameController@newgame'));
//搜索提示√
Route::get('game/searchtip{symbol}',array('before'=>'uri_verify','uses'=>'GameController@searchtip'));
//搜索结果√
Route::get('game/search{symbol}',array('before'=>'uri_verify','uses'=>'GameController@getSearch'));
//猜你喜欢
Route::get('game/guess{symbol}',array('before'=>'uri_verify','uses'=>'GameController@guess'));
//玩家推荐应用
Route::get('game/recommend{symbol}',array('before'=>'uri_verify','uses'=>'GameController@recommend'));
//星座
Route::get('game/discovery{symbol}',array('uses'=>'GameController@discovery'));
Route::get('game/tags{symbol}',array('uses'=>'GameController@tags'));
Route::get('game/relation{symbol}',array('uses'=>'GameController@relation'));
//游戏下载奖励
Route::get('game/download-money{symbol}',array('before'=>'uri_verify','uses'=>'GameController@downloadMoney'));
//游戏下载统计
Route::get('game/download{symbol}',array('before'=>'uri_verify','uses'=>'GameController@download'));

//远征队
Route::get('game/expedition{symbol}',array('before'=>'uri_verify','uses'=>'GameController@expedition'));


/*--------------------------------排行-------------------------------*/
//Tags
Route::get('rank/tags{symbol}',array('before'=>'uri_verify','uses'=>'RankController@tags'));
//类型
Route::get('rank/types{symbol}',array('before'=>'uri_verify','uses'=>'RankController@types'));
//排行
Route::get('rank/list{symbol}',array('before'=>'uri_verify','uses'=>'RankController@chart'));

/*--------------------------------广场-------------------------------*/
//
Route::get('plaza/home{symbol}',array('before'=>'uri_verify','uses'=>'PlazaController@home'));

/*--------------------------------圈子-------------------------------*/
//游戏圈类型√
Route::get('circle/types{symbol}',array('before'=>'uri_verify','uses'=>'CircleController@types'));
//游戏圈游戏√
Route::get('circle/games{symbol}',array('before'=>'uri_verify','uses'=>'CircleController@games'));
//游戏圈主页√
Config::set('get circle/home',array('3.0.0','3.1.0','3.1.5','3.1.6','3.1.7','3.2.0','3.2.1','3.3.0','3.4.0','3.5.0','3.5.1','3.6.0'));
Route::get('circle/home{symbol}',array('before'=>'uri_verify','uses'=>'CircleController@home'));
//schemesurl
Route::get('circle/schemesurl{symbol}',array('before'=>'uri_verify','uses'=>'GameController@schemesurl'));
//匹配
Route::post('circle/matching{symbol}',array('before'=>'uri_verify','uses'=>'CircleController@matching'));
//添加√
Route::get('circle/addgame{symbol}',array('before'=>'uri_verify','uses'=>'CircleController@addgame'));
//删除√
Route::get('circle/removegame{symbol}',array('before'=>'uri_verify','uses'=>'CircleController@removegame'));
//置顶√
Route::get('circle/gametostick{symbol}',array('before'=>'uri_verify','uses'=>'CircleController@gametostick'));
//我的游戏圈√
Route::get('circle/mygamecircle{symbol}',array('before'=>'uri_verify','uses'=>'CircleController@mygamecircle'));
//圈子动态√
Route::get('circle/feeds{symbol}',array('before'=>'uri_verify','uses'=>'CircleController@feeds'));

/*-------------------------------任务-------------------------------*/
//任务列表√
Route::get('task/list{symbol}',array('before'=>'uri_verify','uses'=>'TaskController@home'));
//可接受的任务数
Route::get('task/number{symbol}',array('before'=>'uri_verify','uses'=>'TaskController@number'));
//签到
Route::get('task/checkin{symbol}',array('before'=>'uri_verify','uses'=>'TaskController@checkin'));
//分享任务
Route::get('task/share{symbol}',array('before'=>'uri_verify','uses'=>'TaskController@share'));
//最近一周连续签到记录
//Route::get('task/checkin_log',array('before'=>'uri_verify','uses'=>'TaskController@checkin_log'));

/*-------------------------------赞-------------------------------*/
//赞√
Route::get('like/dolike{symbol}',array('before'=>'uri_verify','uses'=>'LikeController@dolike'));
//赞列表
Route::get('like/users{symbol}',array('before'=>'uri_verify','uses'=>'LikeController@users'));

/*-------------------------------游币商城-----------------------------*/
/**
 * 商品列表√
 * @param int pageIndex
 * @param int pageSize
 * @param int cate_id 
 * 
 */
Route::get('shop/goods{symbol}',array('before'=>'uri_verify','uses'=>'ShopController@goods'));

/**
 * 商品分类
 * 3.1.0新增接口
 */
Route::get('shop/cates{symbol}',array('before'=>'uri_verify','uses'=>'ShopController@CateList'));

/**
 * 商品详情√
 * @param int atid 活动ID
 * @param int uid 用户UID 
 */
Route::get('shop/goods_detail{symbol}',array('before'=>'uri_verify','uses'=>'ShopController@goods_detail'));
/**
 * 兑换列表
 * @param int atid 商品ID
 * @param int pageIndex
 * @param int pageSize
 *
 */
Route::get('shop/exchange_list{symbol}',array('before'=>'uri_verify','uses'=>'ShopController@exchange_list'));


/**
 * 我的商品√
 * @param int pageIndex
 * @param int pageSize 
 * @param int uid 用户UID 
 */
Route::get('shop/mygoods{symbol}',array('before'=>'uri_verify','uses'=>'ShopController@mygoods'));

/**
 * 兑换商品√
 * @param int atid 商品ID
 * @param int uid 用户UID 
 */
Route::get('shop/exchange{symbol}',array('before'=>'uri_verify','uses'=>'ShopController@exchange'));

/*-------------------------------寻宝箱-----------------------------*/
//寻宝箱首页√
Route::get('hunt/home{symbol}',array('before'=>'uri_verify','uses'=>'HuntController@home'));

/*-------------------------------活动-----------------------------*/
//
/**
 * 活动列表√
 * @param int gid 游戏ID
 * @param int pageIndex
 * @param int pageSize 
 */
Route::get('activity/ask-list{symbol}',array('before'=>'uri_verify','uses'=>'ActivityController@getList'));

/**
 * 问答详情√
 * @param int atid 活动ID
 * @param int uid 用户UID 
 */
Route::get('activity/ask-detail{symbol}',array('before'=>'uri_verify','uses'=>'ActivityController@AskDetail'));
/**
 * 提交回答√
 * @param int uid
 * @param int atid 
 * @param json answer[{'numid':1,'choice':'A'}]
 */
Route::get('activity/commit{symbol}',array('before'=>'uri_verify','uses'=>'ActivityController@doCommit'));
/*-------------------------------系统-----------------------------*/
//配置√
Route::get('app/config{symbol}',array('before'=>'uri_verify','uses'=>'AppController@getConfig'));

Route::get('app/simple-config{symbol}',array('before'=>'uri_verify','uses'=>'AppController@simpleConfig'));

/**
 * 版本√
 * @param string appname 应用名称
 * @param string version 版本
 */
Route::get('app/check-version{symbol}',array('before'=>'uri_verify','uses'=>'AppController@checkVersion'));

/*-------------------------------举报-----------------------------*/

/**
 * 举报主题√
 * @param int linkid 主题ID
 * @param int uid 举报人UID
 */
Route::get('inform/topic{symbol}',array('before'=>'uri_verify','uses'=>'InformController@topic'));
/**
 * 举报评论√
 * @param int cid 评论ID
 * @param int uid 举报人UID
 * @param int typeID 评论类型 
 */
Route::get('inform/comment{symbol}',array('before'=>'uri_verify','uses'=>'InformController@comment'));


/*-------------------------------分享-----------------------------*/
/**
 * 分享√
 * @param int type 分享类型[0:关于我们][1:游戏][2:新游][3:专题][4:攻略][5:评测][6:新闻][7:视频][8:帖子][9:活动][10:礼包][11:关于我们]
 * @param string shareid 分享资源ID  
 */
Route::get('share/to{symbol}',array('before'=>'uri_verify','uses'=>'ShareController@to'));
Route::get('share/trigger{symbol}',array('before'=>'uri_verify','uses'=>'ShareController@trigger'));


/*-------------------------------广告-----------------------------*/

/**
 * 启动页广告√
 * @param string appname 应用名称
 * @param string version 版本
 * @param int isiphone5 0/1是否是iphone5
 */
Config::set('get adv/launch',array('3.0.0','3.1.0','3.1.5','3.1.6','3.1.7','3.2.0','3.2.1','3.3.0','3.4.0','3.5.0','3.5.1','3.6.0'));
Route::get('adv/launch{symbol}',array('before'=>'uri_verify','uses'=>'AdvController@launch'));

/**
 * 弹窗广告√
 * @param string appname 应用名称
 * @param string version 版本
 * @param null|1 entrance 如果存在则为游戏详情广告否则为首页广告
 *  
 */
Config::set('get adv/detail',array('3.0.0','3.1.0','3.1.5','3.1.6','3.1.7','3.2.0','3.2.1','3.3.0','3.4.0','3.5.0','3.5.1','3.6.0'));
Route::get('adv/detail{symbol}',array('before'=>'uri_verify','uses'=>'AdvController@openwin'));

/**
 * 统计√
 * @param string appname 应用名称
 * @param string version 版本
 * @param string advid
 * @param string mac
 * @param string idfa
 * @param string osversion
 * @param string code
 * @param int linkid
 * @param string location
 * @param string openudid
 * @param string source
 * @param int type
 * @param string os 
 */
Route::get('adv/advstat{symbol}',array('before'=>'uri_verify','uses'=>'AdvController@advstat'));

/**
 * 激活统计√
 * @param string advid
 * @param string mac
 * @param string idfa
 */
Route::get('advcate/activestat{symbol}',array('uses'=>'AdvController@activestat'));
/*-------------------------------临时版-----------------------------*/
//新闻√
Route::get('beta/news{symbol}',array('before'=>'uri_verify','uses'=>'BetaController@newgame'));
//攻略大全√
Route::get('beta/guide{symbol}',array('before'=>'uri_verify','uses'=>'BetaController@guide'));
//攻略合集√
Route::get('beta/guide-list{symbol}',array('before'=>'uri_verify','uses'=>'BetaController@guideList'));

/*-------------------------------小游戏-----------------------------*/
//list 列表
Route::get('xgame/list{symbol}',array('before'=>'uri_verify','uses'=>'XgameController@getlist'));
//游戏详情  article
Route::get('xgame/article{symbol}',array('before'=>'uri_verify','uses'=>'XgameController@article'));

//增加热度
Route::get('xgame/dohot{symbol}',array('before'=>'uri_verify','uses'=>'XgameController@doHot'));
//banner list
Route::get('xgame/bannerlist{symbol}',array('before'=>'uri_verify','uses'=>'XgameController@getBannerList'));

Route::any('xgame/count{symbol}',array('uses'=>'XgameController@anyCount'));

Route::any('callback/weixin{symbol}',function(){
    echo 'success';
});
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

