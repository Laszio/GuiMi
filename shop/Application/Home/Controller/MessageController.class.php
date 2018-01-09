<?php
namespace Home\Controller;

use Think\Controller;

class MessageController extends CommonController
{

	/**
	 * 展示信息
	 * @Author   ryan
	 * @DateTime 2017-12-01
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function index() 
	{	//默认查询所有
		if (I('id') == 3 || empty(I('id'))) $map = [];
		//查询未读
		else $map['unread'] = I('id');
		//未删除
		$map['status'] = 1;
		//所有数据
		$map['uid'] = session('userinfo')['id'];
		//查询当前所有
		$count = M('MessageDetail')->where($map)->count();
		//分页
		$Page = new \Think\Page($count,5);
		$Page->setConfig('next','下一页');
		$Page->setConfig('prev','上一页');
		$map1 = M('MessageDetail')->where($map)
									->distinct(true)
									->getField('mid',true);
		//查询数据
		$map1 = ['id'=>['in',$map1]];
		$arr['data'] = M('Message')->where($map1)
									->field('subject,addtime,id')
									->limit($Page->firstRow.','.$Page->listRows)
									->select();
		
		$arr['page'] = $Page->show();
		if (IS_AJAX) {
			$this->ajaxReturn($arr);
		} else {
			$this->assign($arr);
			$this->display();
		}
	}

	/**
	 * 查看信息详情
	 * @Author   ryan
	 * @DateTime 2017-12-01
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function look()
	{
		$map['id'] = I('post.id');

		$arr = M('Message')->field('content,subject')->where($map)->find();

		$map1['mid'] = $map['id'];
		$map1['uid'] = session('userinfo')['id'];
		$res = M('MessageDetail')->where($map1)->save(['unread'=>2]);
		$this->ajaxReturn($arr);
	}

	/**
	 * 删除信息
	 * @Author   ryan
	 * @DateTime 2017-12-01
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function del()
	{

		$map['mid'] = I('post.id');
		$map['uid'] = session('userinfo')['id'];

		$arr = M('MessageDetail')->where($map)->save(['status'=>2]);

		$this->ajaxReturn($arr);
	}

	/**
	 * 删除所有信息
	 * @Author   ryan
	 * @DateTime 2017-12-01
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function clearAll()
	{
		$map1['uid'] = session('userinfo')['id'];
		$detail =  M('MessageDetail');
		$arr =$detail ->where(true,$map)->save(['status'=>2]);
		success('已全部删除','Message/index');
	}
}