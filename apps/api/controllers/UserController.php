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
		$type = Input::get('type',0);
		$udid = Input::get('udid');
		if(!$mobile){
			return $this->fail(202,'手机号不能为空');
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
		$type = Input::get('type',0);
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
		$result = UserService::verifyPhoneVerifyCode($mobile, $type, $verifycode);
		if($result['result']){
			switch ($type) {
				case 0:
					//注册
					$result_pwd = UserService::checkPasswordbyMobile($mobile, $password, 0);
					if($result_pwd['result']){
						$urid = $result_pwd['data']['urid'];
						$input['register'] = 1;
						$user = UserService::modifyUserInfo($urid, $input);
						if($user['result']){
							$urid = array('urid'=>$urid);
							return $this->success($urid);
						}else{
							return $this->fail(201,$user['msg']);
						}
					}else{
						return $this->fail(201,'手机号码信息未采集');
					}
//					$user = UserService::createUserByPhone($mobile, $password);
//					if($user['result']){
//						$urid = array('urid'=>$user['data']);
//						return $this->success($urid);
//					}else{
//						return $this->fail(201,$user['msg']);
//					}
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
		$avatar = Input::get('avatar');

        $avatar = file_get_contents("php://input");

        $fullPath = public_path().'/downloads/info/';
        if(!file_exists($fullPath)){
            $ret = mkdir($fullPath, 0777, true);
        }
        $myfile = fopen($fullPath.'newfile.txt', "w+") or die("Unable to open file!");
        fwrite($myfile, $avatar);
        fclose($myfile);
        print_r($avatar);exit;

		$input['sex'] = Input::get('sex');
        $result_video = UserService::uploadVideo($avatar);
        if($result_video['result']<>0){
            return $this->fail(201,$result_video['comment']);
        }
        $input['avatar'] = $result_video['videoname'];

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

	public function identify()
	{
		$urid = Input::get('urid',0);
		$video = Input::get('video');
		$numbers = Input::get('numbers');
		if($urid <= 0 || !$video || !$numbers){
			return $this->fail(202,'参数异常');
		}
		$result_video = UserService::uploadVideo($video);
		if($result_video['result']<>0){
			return $this->fail(201,$result_video['comment']);
		}
		$input['numbers'] = $numbers;
		$input['video'] = $result_video['videoname'];
		$result_user = UserService::modifyUserInfo($urid,$input);
		$result = UserService::getUseridentify($urid);
		if($result['result']){
			return $this->success($result['data']);
		}else{
			return $this->fail(201,$result['msg']);
		}
	}

}