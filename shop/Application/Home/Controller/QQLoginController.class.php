<?php
namespace Home\Controller;
use Think\Controller;

class QQLoginController extends Controller
{
	
	public function _initialize()
	{
		$this->qq = new \Org\Util\QqConnect();
	}

	/**
	 * [qqLogin 引入QQ登录页面]
	 */
	public function qqLogin()
	{
		
		$this->qq->getAuthCode();
	}


	/**
	 * [callback QQ授权登录的回调处理方法]
	 */
	public function callback()
	{

		$qqUserInfo = $this->qq->getUsrInfo();
		if ($qqUserInfo && $qqUserInfo['openid']) {
			//qqUserInfo为真
			$openid = $qqUserInfo['openid'];
			$nikname = $qqUserInfo['nickname'];
			$address = $qqUserInfo['province'].$qqUserInfo['city'];
			$birthday = $qqUserInfo['year'];
			$user_img = $qqUserInfo['figureurl_qq_1'];
			$sex = $qqUserInfo['gender'];
			

			//查数据库
			$user = M('User');
			$data['qq_openid'] = $openid;
			$data['status'] = 1;
			$res = $user->field('id, username, nikname, qq_openid')->where($data)->find();

			if (empty($res)) {
				//第一次用qq登录
				
				//username 用 qq_拼接openid前5位
				$map['username'] = 'qq_'.substr($openid,0, 5);

				//昵称默认使用qq昵称
				$map['nikname'] = $nikname;
				//密码使用openid前8位
				$map['pass'] = substr($openid,0, 8);
				
				$map['qq_openid'] = $openid;
				
				//开启事务				
				M()->startTrans();

				$lastId = M('User')->add($map);
				
				if ($lastId) {

					$list['user_img'] = $user_img;
					$list['address'] = $address;
					if ($sex = '男') {
						$list['sex'] = 1;
					} else if ($sex = '女') {
						$list['sex'] = 2;
					} else {
						$list['sex'] = 3;
					}
					$list['uid'] = $lastId;
					
					$res = M('UserDetail')->add($list);
					
				}
		
				if (!empty($lastId) && !empty($res)) {
					M()->commit();
					
					//查用户数据
					$userInfo = $user->field('id, username, nikname, qq_openid')->where('id ='. $lastId.' and '.'status=1')->find();

					if ($userInfo) {
						$userInfo['user_img'] = $user_img;
						$userInfo['nikname'] = $nikname;

						$_SESSION['userinfo'] = $userInfo;
						$_SESSION['ip'] = get_client_ip();
						
						$this->redirect('Index/index');
					} else {
						error('登录失败');
						exit;
					}


				} else {
					M()->rollback();
					error('异常');
					exit;
				}

			} else {
				//已经用qq登录过
				$res['user_img'] = $user_img;
				$res['nikname'] = $nikname;
				
				$_SESSION['userinfo'] = $res;
				$_SESSION['ip'] = get_client_ip();
				$this->redirect('Index/index');
			}
		} else {
			error('非法操作');
			exit;
		}
	}
}