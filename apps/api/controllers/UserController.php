<?php
use Yxd\Services\ThreadService;
use Illuminate\Support\Facades\Response;
use Yxd\Services\AtmeService;
use Yxd\Services\UserFeedService;
use Yxd\Services\RelationService;
use Illuminate\Support\Facades\Input;
use Youxiduo\User\UserService;
use Yxd\Utility\ImageHelper;

use PHPImageWorkshop\ImageWorkshop;

class UserController extends BaseController
{

	public function login()
	{
		$mobile = Input::get('mobile');
		$password = Input::get('password');
		if(!$mobile){
			return $this->fail(202,'手机号不能为空');
		}
		if(!$password){
			return $this->fail(202,'密码不能为空');
		}
		$result = UserService::checkPasswordbyMobile($mobile, $password);
		if($result['result']){
			$urid = $result['data']['urid'];
			$result = array('urid'=>$urid);
			return $this->success($result);
		}else{
			return $this->fail(201,'用户名密码错误');
		}
	}

	public function smsVerify()
	{
		$mobile = Input::get('mobile');
		$type = Input::get('type');
		$udid = Input::get('udid');
		if(!$mobile){
			return $this->fail(202,'手机号不能为空');
		}
		if(!$type){
			return $this->fail(202,'参数异常');
		}
		$result = UserService::sendPhoneVerifyCode($mobile, $type, $udid);
		if($result['result']){
			return $this->success();
		}else{
			return $this->fail(201,'发送验证码错误');
		}
	}

	/**
	 * 注册
	 */
	public function register()
	{
		$mobile = Input::get('mobile');
		$password = Input::get('password');
		$verifycode = Input::get('verifycode');
		$type = Input::get('type');
		$urid = Input::get('urid',0);
		if(!$mobile){
			return $this->fail(202,'手机号不能为空');
		}
		if(!$password){
			return $this->fail(202,'密码不能为空');
		}
		if(!$verifycode){
			return $this->fail(202,'验证码不能为空');
		}
		if(!$type){
			return $this->fail(202,'参数异常');
		}
		$result = UserService::verifyPhoneVerifyCode($mobile, $type, $verifycode);
		if($result['result']){
			switch ($type) {
				case 0:
					//注册
					$user = UserService::createUserByPhone($mobile, $password);
					if($user['result']){
						$urid = array('urid'=>$user['data']);
						return $this->success($urid);
					}else{
						return $this->fail(201,$user['msg']);
					}
					break;
				case 1:
					//忘记密码
					$result_pwd = UserService::checkPasswordbyMobile($mobile, $password);
					if($result_pwd['result']){
						$user = UserService::modifyUserPwd($mobile, $password);
						if($user['result']){
							$urid = array('urid'=>$user['data']);
							return $this->success($urid);
						}else{
							return $this->fail(201,$user['msg']);
						}
					}else{
						return $this->fail(201,'用户名密码错误');
					}
					break;
				case 2:
					//更换绑定手机
					$result_pwd = UserService::checkPassword($urid, $password);
					if($result_pwd['result']){
						$user = UserService::modifyUserMobile($urid, $password);
						if($user['result']){
							$urid = array('urid'=>$user['data']);
							return $this->success($urid);
						}else{
							return $this->fail(201,$user['msg']);
						}
					}else{
						return $this->fail(201,'用户名密码错误');
					}
					break;
				default:
					return $this->fail(202,'参数异常');
					break;
			}
		}else{
			return $this->fail(201,$result['msg']);
		}

	}

	public function feedback()
	{
		$contact = Input::get('contact');
		$content = Input::get('content');
		$urid = Input::get('urid',0);
		$result = UserService::saveFeedback($urid, $contact, $content);
		if($result['result']){
			return $this->success();
		}else{
			return $this->fail(201,$result['msg']);
		}
	}

	public function info()
	{
		$urid = Input::get('urid',0);
		if($urid <= 0){
			return $this->fail(202,'参数异常');
		}
		$result = UserService::getUserInfo($urid);
		if($result['result']){
			return $this->success($result['data']);
		}else{
			return $this->fail(201,$result['msg']);
		}
	}

	public function edit()
	{
		$urid = Input::get('urid',0);
		$input = array();
		$input['name'] = Input::get('name');
		$input['avatar'] = Input::get('avatar');
		$input['sex'] = Input::get('sex');
		if($urid <= 0){
			return $this->fail(202,'参数异常');
		}
		$result = UserService::modifyUserInfo($urid,$input);
		if($result['result']){
			return $this->success();
		}else{
			return $this->fail(201,$result['msg']);
		}
	}

	public function identification()
	{
		$urid = Input::get('urid',0);
		if($urid <= 0){
			return $this->fail(202,'参数异常');
		}
		$result = UserService::getUseridentify($urid);
		if($result['result']){
			return $this->success($result['data']);
		}else{
			return $this->fail(201,$result['msg']);
		}
	}

	public function identifyrefresh()
	{
		$urid = Input::get('urid',0);
		$input = array();
		$input['identify'] = Input::get('result');
		if($urid <= 0){
			return $this->fail(202,'参数异常');
		}
		$result = UserService::modifyUserInfo($urid,$input);
		if($result['result']){
			return $this->success();
		}else{
			return $this->fail(201,$result['msg']);
		}
	}


}