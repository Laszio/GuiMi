<?php
namespace Admin\Model;
use Think\Model;

class MessageModel extends Model
{
	/**
	 * 发送消息数据处理
	 * @Author   ryan
	 * @DateTime 2017-11-28
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	function sendMessage ()
	{
		$map['id'] = $this->storeMessage();

		import("Vendor.GatewayClient.Gateway",'',".php");
		\GatewayClient\Gateway::$registerAddress = '127.0.0.1:1238';
		//数据
		// $res = $this->find($map);
		//发送
		$res['type'] = 'email';
		$str = json_encode($res);
		if ( I('post.type') == 2) {

			\GatewayClient\Gateway::sendToAll($str);
		} else {

			$user = 'user_'.'2';
			\GatewayClient\Gateway::sendToUid($user,$str);

		}

	}

	function storeMessage () 
	{
		//判断是否正确传值
		if ( I('post.type') == 2 ) {

			$arr = M('user') ->where('status=1')->getField('id',true);
			//循环添加数据
			$map1['addresser_id'] = session('adminInfo')['id'];
			$map1['addresser'] = session('adminInfo')['username'];
			$map1['consignee'] = 'all';
			$map1['content'] = I('content');
			$map1['subject'] = I('subject');
			//开启事务
			$this -> startTrans();
			$map2['mid'] = $this -> add($map1);

			//循环得到id
			foreach ($arr as $v) {
				
				$map2['uid'] = $v;

				$res = M('MessageDetail') -> add($map2);
				//确认事务
				if ($map2['mid'] && $res) {
					$this->commit();
				} else {
					$this->rollback();
					error('发送失败');
					exit;
				}
			
			}

		} else if ( empty(I('consignee')) ) {

			error('请填写收件人');
			exit;

		} else {
			//是否有该用户名
			$arr['username'] = I('consignee');
			$res = M('user') ->field('username,id') ->where($arr)->find();
			//判断是否有该用户名
			if ( !$res ) {
				error('请填写正确的用户名');
				exit;
			}

			//网数据库添加数据
			$map1 = [];
			$map1['addresser_id'] = session('adminInfo')['id'];
			$map1['addresser'] = session('adminInfo')['username'];
			$map1['consignee'] = I('consignee');
			$map1['content'] = I('content');
			$map1['subject'] = I('subject');
			//开启事务
			$this -> startTrans();
			//写入
			
			$map2['mid'] = $this -> add($map1);
			$map2['uid'] = $res['id'];
			$res = M('MessageDetail') -> add($map2);

			//确认事务
			if ($map2['mid'] && $res) {
				$this->commit();
			} else {
				$this->rollback();
				error('发送失败');
				exit;
			}
		}	
		return $map2['mid'];
	}

}