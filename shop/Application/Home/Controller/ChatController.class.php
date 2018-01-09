<?php
namespace Home\Controller;

use Think\Controller;


class ChatController extends Controller
{
	/**
	 * 首页展示
	 * @Author   ryan
	 * @DateTime 2017-12-06
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	function index()
	{
		
		if (session('?userinfo')) {
			$this->display();
		} else {
			error('请登录','Login/index');
			exit;
		}

	}

	/**
	 * 获取服务人员id
	 * @Author   ryan
	 * @DateTime 2017-12-06
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	function getCustomerService($uid)
	{
		//绑定后台客服人员的aid
		$redis = new \Redis();
		$redis->connect('localhost','6379');
		//判断是否已绑定aid
		$cus_service = $redis->keys($uid);
		if ( $cus_service ) {
			//获取aid
			$aid = $redis->get($uid);
		} else {
			//绑定aid
			//选择客服(依据客服数量进行划分)
			//1.从数据库查询
			$service_arr = M('AdminRole')->where('rid=7')->distinct(true)->getField('aid');
			//得到$aid
			$id = trim(strrchr($uid,'_'),'_');
			$num = count($service_arr);
			//求余
			$key = $id % $num;
			$aid = 'cus_'.$service_arr[$key];
			//设置缓存
			$redis->set($uid,$aid);
			//写入文件
			$arr[$uid]['uid'] = $id;
			$arr[$uid]['cid'] = $service_arr[$key];
			$arr[$uid]['cus'] = $aid;
			$arr[$uid]['time'] = time();
			//文件地址
			$filePath = './Public/Chat/Chatinglog/user_cus';
			$ago = file_get_contents($filePath);
			if ($ago) {
				$ago = json_decode($ago,true);
				$arr = array_merge($ago,$arr);

			}
			$str = json_encode($arr);

			file_put_contents($filePath,$str);
		}
		return $aid;
	}

	/**
	 * 绑定uid
	 * @Author   ryan
	 * @DateTime 2017-12-06
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	function bindUid()
	{
		if (!session('?userinfo')) {
			$this->error('需先登录',U('Login/index'));
			exit;
		}
		$client_id = I('post.id');
		$uid = session('userinfo.id');
		$uid = 'user_'.$uid;
		import("Vendor.GatewayClient.Gateway",'',".php");
		\GatewayClient\Gateway::$registerAddress = '127.0.0.1:1238';
		\GatewayClient\Gateway::bindUid($client_id, $uid);

		//返回数据
		//绑定aid
		$this->getCustomerService($uid);
		//返回初始化数据
		$data = $this->getInitMsg($uid);
		$this->ajaxReturn($data);
	}

	/**
	 * 获取init数据
	 * @Author   ryan
	 * @DateTime 2017-11-27
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	function getInitMsg ($uid)
	{
		$redis = new \Redis();
		$redis -> connect('localhost',6379);
		//获取数据
		$arr['msg'] = $redis->hGetAll($uid.'_msg');
		$arr['uid'] = $uid;
		$arr['unread'] = $redis->get('unread_'.$uid );
		$filePath = './Public/Chat/Chatinglog/'.$uid;
		//获取数据
		if ( count($arr['msg']) <= 20 ) {

			$temp = $arr;
			$str = file_get_contents($filePath);
			if ($str) {
				$store = json_decode($str,true);
				$step = 22 -count($temp);
				$arr['msg'] = array_merge($arr['msg'],array_slice($store,-$step));
			}
		}
		return $arr;
	}

	/**
	 * 聊天
	 * @Author   ryan
	 * @DateTime 2017-12-06
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	function chating()
	{
		if (!session('?userinfo')) {
			$this->error('需先登录',U('Login/index'));
			exit;
		}
		//获取aid
		$uid = session('userinfo.id');
		$uid = 'user_'.$uid;
		$aid = $this->getCustomerService($uid);
		
		$msg = I('post.msg');
		import("Vendor.GatewayClient.Gateway",'',".php");
		\GatewayClient\Gateway::$registerAddress = '127.0.0.1:1238';

		//设置未读信息
		$redis = new \Redis();
		$redis -> connect('localhost',6379);
		

		if ( !$redis->hExists('unread_'.$aid,$uid) ) {

			$redis->hset('unread_'.$aid,$uid,1);	
			$unread = 1;			
		} else {
			$unread = $redis->hIncrBy('unread_'.$aid,$uid,1);

		}
	
		$arr = json_encode(['type'=>'msg','msg'=>$msg,'uid'=>$uid,'unread'=>$unread]);
		
		\GatewayClient\Gateway::sendToUid($aid,$arr);
		$this->storeMsg($uid,$msg);
	}

	/**
	 * 存储信息
	 * @Author   ryan
	 * @DateTime 2017-12-06
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	function storeMsg ($uid,$msg)
	{

		$filePath = '/Public/Chat/Chatinglog/'.$uid;
		$time = time();
		$redis = new \Redis();
		$redis -> connect('localhost',6379);
		$redis -> hset($uid.'_msg',$time,'user-@@-'.$msg);
		$count = $redis -> hlen($uid.'_msg');

		//每隔20条往文件写
		if ($count >= 20 ) { 
			$arr = json_encode($redis -> getAll($uid.'_msg'));

			file_put_contents($filePath,$arr,FILE_APPEND);
			$reids -> del($uid.'_msg');
		}		

	}


	/**
	 * 清除未读标记
	 * @Author   ryan
	 * @DateTime 2017-11-27
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	function clearUnread ()
	{	
		if (!session('?userinfo')) {
			$this->error('需先登录',U('Login/index'));
			exit;
		}
		$redis = new \Redis();
		$redis -> connect('localhost',6379);
		$uid = 'unread_'.I('post.uid');
		dump($uid);
		$redis->set($uid,0);	
	}

}