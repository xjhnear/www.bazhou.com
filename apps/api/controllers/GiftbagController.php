<?php
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Yxd\Modules\Activity\GiftbagService;
use Yxd\Services\UserService;
use Yxd\Services\Cms\GameService;
use Yxd\Models\Passport;

class GiftbagController extends BaseController
{	
/**
	 * 礼包首页
	 */
	public function home()
	{
		$page = Input::get('pageIndex',1);
		$pagesize = Input::get('pageSize',20);
		$uid = Input::get('uid',0);
		$gid = Input::get('gid',0);
		$out = array();
		if($uid){
		    $mygift = GiftbagService::getMyCardNoList($uid);
		}else{
			$mygift = null;
		}
		$result = GiftbagService::getList($gid,$page,$pagesize);
		$gids = array();
		foreach($result['result'] as $index=>$row){
			$gids[] = $row['game_id'];
		}
		
		$hots = array();
		if($page==1 && $gid==0){
			$hots = GiftbagService::getHotList();
			foreach($hots as $row)
			{
				$gids[] = $row['game_id'];
			}
		}
		$total = count($hots) + (int)$result['total'];
		$games = GameService::getGamesByIds($gids);
		$giftbags = array_merge($hots,$result['result']);
		foreach($giftbags as $index=>$row){
			$gift = array();
			$gift['gfid'] = $row['id'];
			$ico = isset($games[$row['game_id']]) ? $games[$row['game_id']]['ico'] : '';
			$ico = $this->replaceFreeIcon($ico,$row);
			$gift['url'] = $this->joinImgUrl($ico);
			$gift['gname'] = isset($games[$row['game_id']]) ? $games[$row['game_id']]['shortgname'] : '';
			$gift['title'] = $row['title'];
			$gift['date'] = date('Y-m-d',$row['ctime']);
			$gift['adddate'] = date('Y-m-d',$row['ctime']);
			$gift['starttime'] = date('Y-m-d H:i:s',$row['starttime']);
			$gift['endtime'] = date('Y-m-d H:i:s',$row['endtime']);
			$gift['ishot'] = $row['is_hot'];
			$gift['istop'] = $row['is_top'];
			$gift['cardcount'] = $row['total_num'];
			$gift['lastcount'] = $row['last_num'];
			$ishas = false;
			$number = '';
			if( $row['is_not_limit']==0 && $row['limit_count']==0 && $mygift && is_array($mygift)){
				$mygift_ids = array_keys($mygift);
				$ishas = in_array($row['id'],$mygift_ids);
				if($ishas){
					$number = $mygift[$row['id']];
				}
			}
			$gift['ishas'] = (int)$ishas;
			$gift['numbers'] = $number;
			$out[] = $gift;
		}
		return $this->success(array('result'=>$out,'totalCount'=>$total));
	}
	
	/**
	 * 搜索礼包
	 */
	public function search()
	{
		$page = Input::get('pageIndex',1);
		$pagesize = Input::get('pageSize',20);
		$uid = Input::get('uid',0);
		$keyword = Input::get('keyword');
		if(empty($keyword)){
			return $this->home();
		}
		$out = array();
		if($uid){
		    $mygift = GiftbagService::getMyCardNoList($uid);
		}else{
			$mygift = null;
		}
		$result = GiftbagService::search($keyword,$page,$pagesize);
		
		$gids = array();
		foreach($result['result'] as $index=>$row){
			$gids[] = $row['game_id'];
		}
		$games = GameService::getGamesByIds($gids);
		
		
	    foreach($result['result'] as $index=>$row){
			$gift = array();
			$gift['gfid'] = $row['id'];
			$ico = isset($games[$row['game_id']]) ? $games[$row['game_id']]['ico'] : '';
			$ico = $this->replaceFreeIcon($ico,$row);
			$gift['url'] = $this->joinImgUrl($ico);
			$gift['gname'] = $games[$row['game_id']]['shortgname'];
			$gift['title'] = $row['title'];
			$gift['date'] = date('Y-m-d',$row['ctime']);
			$gift['adddate'] = date('Y-m-d',$row['ctime']);
			$gift['starttime'] = date('Y-m-d H:i:s',$row['starttime']);
			$gift['endtime'] = date('Y-m-d H:i:s',$row['endtime']);
			$gift['ishot'] = $row['is_hot'];
			$gift['istop'] = $row['is_top'];
			$gift['cardcount'] = $row['total_num'];
			$gift['lastcount'] = $row['last_num'];
			$ishas = false;
			$number = '';
			if($row['is_not_limit']==0 && $row['limit_count']==0 && $mygift && is_array($mygift)){
				$mygift_ids = array_keys($mygift);
				$ishas = in_array($row['id'],$mygift_ids);
				if($ishas){
					$number = $mygift[$row['id']];
				}
			}
			$gift['ishas'] = (int)$ishas;
			$gift['numbers'] = $number;
			$out[] = $gift;
		}
		return $this->success(array('result'=>$out,'totalCount'=>$result['total']));
	}
	
	/**
	 * 礼包详情
	 */
	public function detail()
	{
		$gift_id = Input::get('gfid');
		$uid = Input::get('uid',0);
		$password = Input::get('password');
		$idfa = Input::get('idfa');
		
		if(!$gift_id){
			return $this->fail(11211,'礼包不存在');
		}
		$check_version = true;
	    //if($uid == 5542314 || $uid == 100240 || $uid == 100001){
		$version = Input::get('version');
		$vers = array('3.4.0','3.5.0','3.5.1','3.6.0');
		if(!in_array($version,$vers)){
			$check_version = false;
		}
		
		if(in_array($version,$vers) && $uid>0 && $password && $this->checkUserStatus($uid, $password)==false){
			$check_version = false;
		}
		//}
		$gift = GiftbagService::getDetail($gift_id,$uid);
		if($gift){
			$out = array();
			$out['gfid']   = $gift['id'];			
			$out['title']  = trim($gift['title']);
			$out['gid']    = $gift['game_id'] ? $gift['game_id']  : 0;
			$out['gname']  = trim($gift['game']['shortgname']) ? trim($gift['game']['shortgname']) : $gift['game']['gname'];
			$out['url']    = self::joinImgUrl($gift['game']['ico']);
			//$out['url'] = self::joinImgUrl($this->replaceFreeIcon($gift['is_charge'],$gift['game']['ico'],$gift['listpic']));
			$out['starttime'] = date('Y-m-d H:i:s',$gift['starttime']);
			$out['endtime'] = date('Y-m-d H:i:s',$gift['endtime']);
			$out['ishas']  = $gift['ishas'];
			if($this->checkUserStatus($uid, $password)==true){
				$out['number'] = $gift['cardno'] ? : '';
			}else{
			    $out['number'] = '';
			}
			$btnshow = 1;
			if($uid){
				if($gift['is_appoint']){//授权礼包
					$btnshow = GiftbagService::isGiftbagAppointUser($gift_id, $uid);
					$check_version = true;
				}else{
					$btnshow = GiftbagService::isGetGiftbagByAppleIdentify($gift_id, $uid) ? 0 : 1;
					//非收费版则不显示领取按钮	
					if($gift['is_charge'] && !in_array($version,array('3.6.0'))){
						$btnshow = 0;
					}				
				}
			}else{
				if($gift['is_charge'] && !in_array($version,array('3.6.0'))){
					$btnshow = 0;
				}
			}
			if($gift['ishas']) $btnshow = 1;			
			$out['btnshow'] = $check_version ? $btnshow : 0;
			//无限领取逻辑
			if($gift['is_not_limit']==1) $out['btnshow'] = 1;
			//互斥礼包逻辑
			if($gift['mutex_giftbag_id']>0 && $uid>0){
				$out['btnshow'] = GiftbagService::isGetGiftbag($gift['mutex_giftbag_id'],$uid) ? 0 : 1;
			}

			if($gift['limit_register_time']>0 && $uid){
				$user = UserService::getUserInfo($uid);
				if($user){
					if($user['dateline']<$gift['limit_register_time']){
						$out['btnshow'] = 0;
					}
				}
			}
			//限制领取次数
			if($gift['limit_count']>0 && $uid>0){
			    $out['btnshow'] = GiftbagService::isGetGiftbagLimitByUID($gift['limit_count'],$gift_id,$uid) ? 0 : 1;
			}

			$out['cardcount']	=	$gift['total_num'];
			$out['lastcount']	=	$gift['last_num'];
			$out['needTourCurrency'] = isset($gift['condition']['score']) ? $gift['condition']['score'] : 0;
			$out['remainTourCurrency'] = $uid ? UserService::getUserRealTimeCredit($uid,'score') : 0;
			$out['company'] = $gift['game']['company'];
			$append = '';
			//收费礼包且当前版本未非收费版则提示升级
			if($gift['is_charge'] && !in_array($version,array('3.6.0'))){
				$append = '<p style="white-space: normal;padding:10px 0px 2px 0px;"><span style="color: rgb(0, 0, 255);font-size:18px;">免费版暂不支持本礼包领取，如有需要请下载付费版本领取本礼包！</span></p>';
				$append .= '<p style="padding:0px 0px 6px 0px; text-align:center;"><span style="color:#0000FF;font-size:24px;">☆</span><a href="https://itunes.apple.com/us/app/you-xi-duo-shou-ji-you-xi/id953018137?l=zh&ls=1&mt=8" target="_blank" style="text-decoration:underline;"><span style="color:#FF6600;font-size:24px;">下载安装</span></a><span style="color:#0000FF;font-size:24px;">☆</span></p>';
			}elseif($check_version==false){//小于3.4.0的版本则提示升级
				$append = '<p style="white-space: normal;padding:10px 0px 2px 0px;"><span style="color: rgb(0, 0, 255);font-size:18px;">游戏多更新啦~！请升级最新版本才能领取礼包~<br>（新版本强化了账号安全体系）</span></p>';
				$append .= '<p style="padding:0px 0px 6px 0px; text-align:center;"><span style="color:#0000FF;font-size:24px;">☆</span><a href="https://itunes.apple.com/us/app/you-xi-duo-shou-ji-you-xi/id953018137?l=zh&ls=1&mt=8" target="_blank" style="text-decoration:underline;"><span style="color:#FF6600;font-size:24px;">下载安装</span></a><span style="color:#0000FF;font-size:24px;">☆</span></p>';
			}			
			$out['body'] = $append . $gift['content'];
			
			return $this->success(array('result'=>$out));			
		}
		return $this->fail(11211,'礼包不存在');
	}
	
	/**
	 * 我的礼包
	 */
	public function myGift()
	{
		$uid = Input::get('uid',0);
		$password = Input::get('password');
		$idfa = Input::get('idfa');
		$version = Input::get('version');
		$vers = array('3.4.0','3.5.0','3.5.1');
		$check_version = true;
		
		//if(!in_array($version,$vers) || $this->checkUserStatus($uid, $password)==false){
		//	$check_version = false;
		//}
		$page = Input::get('pageIndex',1);
		$pagesize = Input::get('pageSize',20);
		$result = GiftbagService::getMyGift($uid,$page,$pagesize);
		$out = array();
		$games = $gifts = $gift_ids = $game_ids = array();
		foreach($result['result'] as $row){
			$gift_ids[] = $row['gift_id'];
			$game_ids[] = $row['game_id'];
		}
		
		$_gifts = GiftbagService::getListByIds($gift_ids);
		foreach($_gifts as $row){
			$gifts[$row['id']] = $row;
		}
		
		$games = GameService::getGamesByIds($game_ids);
	    foreach($result['result'] as $key=>$row){
			if(!isset($gifts[$row['gift_id']])) continue;
	    	if(isset($games[$row['game_id']])){
				$gname = $games[$row['game_id']]['shortgname'];
				$ico = $games[$row['game_id']]['ico'];
			}else{
				$gname = '';
				$ico = '';
			}			
			$gift = array();
			$gift['gfid'] = $row['gift_id'];
			$gift['url'] = $this->joinImgUrl($ico);
			$gift['gname'] = $gname;
			$gift['title'] = $gifts[$row['gift_id']]['title'];
			$gift['date'] = date('Y-m-d H:i:s',$gifts[$row['gift_id']]['starttime']);
			$gift['adddate'] = date('Y-m-d H:i:s',$row['addtime']);
			$gift['number'] =  $check_version ? $row['card_no'] : '';
			$out[] = $gift;
		}
		if((int)$result['total']==0){
			return $this->success(array('result'=>array(),'totalCount'=>0));
		}else{
			return $this->success(array('result'=>$out,'totalCount'=>$result['total']));
		}
	}
	
    /**
	 * 领取礼包
	 */
	public function getGift()
	{
		$uid = Input::get('uid');
		$gift_id = Input::get('gfid');
		$password = Input::get('password');
		$idfa = Input::get('idfa');		
		
		if(!$uid || !$gift_id){
			return $this->fail(11211,'参数错误');
		}
		
		//if($uid == 5542314 || $uid == 100240 || $uid == 100001){
		$version = Input::get('version');
		$vers = array('3.4.0','3.5.0','3.5.1','3.6.0');
		$gift = GiftbagService::getDetail($gift_id,0);
		if($gift && $gift['is_appoint']==0){//授权礼包默认通过
			if(!in_array($version,$vers)){
				return $this->fail(11211,'您的应用版本过低,请升级新版本');
			}
			
			if(in_array($version,$vers) && $uid && $password && $this->checkUserStatus($uid, $password)==false){
				return $this->fail(11211,'安全验证失败,请重新登录');
			}
			
			if(($uid == 100240 || $uid==100013) && $gift['is_charge'] && !in_array($version,array('3.6.0'))){
				return $this->fail(11211,'您的应用版本过低,请升级新版本');
			}
		}
		//}
		
		$card = GiftbagService::doMyGift($gift_id, $uid);
		if($card==-4){
			return $this->fail(11211,'该礼包仅限新用户领取');
		}elseif($card==-2){
			return $this->fail(11211,'该礼包为活动专属礼包，只有参加活动的用户才能领取哦，如有问题请在“意见反馈”中及时和客服联系，谢谢！');
		}elseif($card==-1){
			return $this->fail(11211,'礼包不存在');
		}elseif($card===0){
			return $this->fail(11211,'礼包已经被领完');
		}elseif($card===1){
			return $this->fail(11211,'礼包领取失败');
		}elseif($card===2){
		    return $this->fail(11211,'游币不足');
		}elseif($card===-3){
		    return $this->fail(11211,'您的账号使用的设备今天已经领取过礼包');
		}elseif($card===-5){
		    return $this->fail(11211,'您的账号无法再领取更多该礼包');
		}else{
			return $this->success(array('result'=>$card));
		}
	}
	
	/**
	 * 我的预定
	 */
	public function myReserveGift()
	{
		$uid = Input::get('uid');
	    $page = Input::get('pageIndex',1);
		$pagesize = Input::get('pageSize',20);
		$result = GiftbagService::myReserve($uid,$page,$pagesize);		
		$out = array();
		foreach($result['result'] as $row){
			$reserve = array();			
			$reserve['gid'] = $row['game_id'];
			$reserve['url'] = self::joinImgUrl($row['game']['ico']);
			$reserve['gname'] = $row['game']['shortgname'];
			$reserve['bookdate'] = date('Y-m-d H:i:s',$row['addtime']);			
			$reserve['gfid'] = $row['gift_id'] ? : '';
			$out[] = $reserve;
		}
		
	    return $this->success(array('result'=>$out,'totalCount'=>$result['total']));
	}
	
	protected function checkUserStatus($uid,$password)
	{		
		if(!$password) return false;		
		$status = Passport::verifyLocalLogin($uid, $password,'uid');
		if($status===-1 || $status===null){
			return false;
		}
		return true;
	}
	
	protected function replaceFreeIcon($free_icon,$gift)
	{
		$uid = Input::get('uid',0);
		$version = Input::get('version');
		if(($uid == 100240 || $uid==100013) && $gift){
			$icon = $free_icon;
			if($gift['is_appoint'] && $gift['appoint_icon']){
				$icon = $gift['appoint_icon'];
			}
			if(!in_array($version,array('3.6.0')) && $gift['is_charge'] && $gift['charge_icon']){
				$icon = $gift['charge_icon'];
			}
			return $icon;
		}
		return $free_icon;		
	}
	
	/**
	 * 我的预定-删除
	 */
	public function removeMyReserveGift()
	{
		$game_id = Input::get('gid');
		$uid = Input::get('uid');
		GiftbagService::removeMyReserve($game_id, $uid);
		return $this->success(array('result'=>null));
	}
		
	/**
	 * 预定礼包
	 */
	public function reserveGift()
	{
		$uid = Input::get('uid',0);
		$game_id = Input::get('gid');
		$result = GiftbagService::doMyReserve($game_id, $uid);
		if($result>0){
			return $this->success(array('result'=>array()));
		}elseif($result===-1){
			return $this->fail('11211','该游戏礼包已经预定');
		}else{
			return $this->fail('11211','礼包预定失败');
		}
	}	
}
