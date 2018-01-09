<?php
namespace Admin\Controller;

use Think\Controller;

class MessageController extends CommonController 
{

	/**
	 * 展示站内信
	 * @Author   ryan
	 * @DateTime 2017-11-28
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	function index ()
	{
		$map['addresser_id'] = session('adminInfo')['id'];
		$arr = M('Message')->where($map)->select();
		$this->assign('arr',$arr);
		$this->display();
	}
	
	/**
	 * 发送信息
	 * @Author   ryan
	 * @DateTime 2017-11-28
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	function sendMessage ()
	{
		if (IS_POST) {

			D('Message')->sendMessage();
			success('发送成功','Message/index');
			exit;
			
		}
		$this->display();
		
	}
}