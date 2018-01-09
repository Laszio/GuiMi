<?php
namespace Admin\Controller;

use Think\Controller;

class ChatController extends CommonController 
{
	/**
	 * 展示所有聊天数据
	 * @Author   ryan
	 * @DateTime 2017-12-06
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function show ()
	{
		//绑定后台客服人员的aid
		$filePath = './Public/Chat/Chatinglog/user_cus';
		$str = file_get_contents($filePath);
		if ($str) $arr = json_decode($str,true);
		else $arr=[];
		//用户名id和客户端id
		$uid = array_column($arr,'uid');
		$cid = array_column($arr,'cid');
		//得到数据
		$user = M('user')->where(['id'=>['in',$uid]])->getField('id,username');
		$admin =  M('admin')->where(['id'=>['in',$cid]])->getField('id,username');
		foreach($arr as $k =>$v) {
			$arr[$k]['uname'] = $user[$v['uid']];
			$arr[$k]['cname'] = $admin[$v['cid']];
			$arr[$k]['time'] = date('Y-m-d',$v['time']);
		
		}
		$this->assign('arr',$arr);

		$this->display();
	}

	/**
	 * 历史记录
	 * @Author   ryan
	 * @DateTime 2017-12-06
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function history ()
	{
		$uid = I('post.uid');
		$filePath = './Public/Chat/Chatinglog/user_cus';
		$str = file_get_contents($filePath);
		//查看历史记录
		
		if ($str) $arr = json_decode($str,true);
		else $this->error('暂无数据');

		if ( $arr[$uid]['cid'] != session('adminInfo')['id'] )
			$this->error('您无权限查看此历史记录');
		//是否有文件消息
		$filePath = './Public/Chat/Chatinglog/'.$uid;
		$str = file_get_contents($filePath);
		if ($str) $arr1 = json_decode($str,true);
		else $arr1 = [];
		//是否有缓存消息
		$redis = new \Redis();
		$redis -> connect('localhost',6379);
		$arr2 = $redis->hGetAll($uid.'_msg');
		
		//是否有缓存信息
		if ( $arr2 ) {
			$arr = array_merge($arr1,$arr2);
		} else {
			$arr = $arr1;
		}

			$this->ajaxReturn($arr);

	}

	/**
	 * [insertChating description]
	 * @Author   ryan
	 * @DateTime 2017-12-06
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function insertChating ()
	{

	}
	/**
	 * 首页
	 * @Author   ryan
	 * @DateTime 2017-12-06
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function index ()
	{
		
		$this->display();
	}

	/**
	 * 获取init数据
	 * @Author   ryan
	 * @DateTime 2017-11-27
	 *5553@qq.com
	 * @return   [type]           [description]
	 */
	function getInitMsg ($aid)
	{
		$redis = new \Redis();
		$redis -> connect('localhost',6379);
		//获取成员
		$aid = 'unread_'.$aid;
		$uids = $redis ->hKeys($aid);
		foreach ($uids as $uid ) {
			//未读信息数
			$arr[$uid]['unread'] = $redis->hget($aid,$uid);
			//获取当前uid的信息
			$arr[$uid]['msg'] = $redis->hGetAll($uid.'_msg');
			$filePath = './Public/Chat/Chatinglog/'.$uid;

			//获取该用户数据
		
			if ( count($arr) <= 20 ) {

				$temp = $arr[$uid]['msg'];
				$str = file_get_contents($filePath);
				if ($str) {
					$store = json_decode($str,true);

					$step = 22 -count($temp);
					$arr[$uid]['msg'] = array_merge($arr[$uid]['msg'],array_slice($store,-$step));
				}
			}
		}
		return $arr;

	}

	/**
	 * 绑定uid
	 * @Author   ryan
	 * @DateTime 2017-11-27
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	function bindUid()
	{
		//客服id
		$client_id = I('post.id');
		// dump($client_id);exit;
		$aid = 'cus_'.session('adminInfo')['id'];
		import("Vendor.GatewayClient.Gateway",'',".php");
		\GatewayClient\Gateway::$registerAddress = '127.0.0.1:1238';
		\GatewayClient\Gateway::bindUid($client_id, $aid);
		$arr = $this->getInitMsg($aid);
		$this->ajaxReturn($arr);
		
	}

	/**
	 * 发送聊天数据给对方
	 * @Author   ryan
	 * @DateTime 2017-11-27
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	function chating()
	{
		//获取客户的$uid
		$uid = I('post.uid');
		$msg = I('post.msg');
		//设置未读信息
		$redis = new \Redis();
		$redis -> connect('localhost',6379);

		if ( !$redis->exists('unread_'.$uid )) {
			$redis->set('unread_'.$uid ,1);
			$unread = 1;				
		} else {
			$unread = $redis->incr('unread_'.$uid );
		}

		$arr = json_encode(['type'=>'msg','msg'=>$msg,'unread'=>$unread]);

		import("Vendor.GatewayClient.Gateway",'',".php");
		\GatewayClient\Gateway::$registerAddress = '127.0.0.1:1238';
		\GatewayClient\Gateway::sendToUid($uid,$arr);

		$this -> storeMsg($uid,$msg);
	
	}

	function storeMsg ($uid,$msg)
	{
		$filePath = './Public/Chat/Chatinglog/'.$uid;
		$redis = new \Redis();
		$redis -> connect('localhost',6379);
		$time = time();
		$redis -> hset($uid.'_msg',$time,'cus-@@-'.$msg);
		$count = $redis -> hlen($uid.'_msg');
		//每隔20条往文件写
		if ($count >= 20 ) { 
			$arr = json_encode($redis -> hGetAll($uid.'_msg'));

			file_put_contents($filePath,$arr,FILE_APPEND);
			$redis -> Del($uid.'_msg');

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
		$redis = new \Redis();
		$redis -> connect('localhost',6379);
		$uid = I('post.uid');
		$aid = $redis->get($uid);
		$redis->hset('unread_'.$aid,$uid,0);	
	}

}