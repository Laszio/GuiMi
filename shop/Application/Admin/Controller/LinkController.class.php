<?php
namespace Admin\Controller;

use Think\Controller;

class LinkController extends CommonController 
{
	/**
	 * 轮播图
	 * @Author   ryan
	 * @DateTime 2017-11-21
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function index ()
	{
		$arr = M('Link')->field('id,toUrl,toUrlname,des,status,img,addtime')
				 ->order('addtime asc')->where('status<>3')->select();
		$status = [1=>'显示','不显示'];
		$path = '/Uploads';
		foreach ($arr as $k=>$v) {
			$arr[$k]['status'] = $status[$v['status']];
			$arr[$k]['img'] = $path.$v['img'];
		}
		$this->assign('arr',$arr);
		$this->display();
	}

	/**
	 * 添加友链
	 * @Author   ryan
	 * @DateTime 2017-11-28
	 * @email    931035553@qq.com
	 */
	public function add()
	{
		if (IS_POST) {
			$link = D('link');

			$res = $link->addLink();
			if($res){
				success('添加成功','Link/index');
			} else {
				error('添加失败');
			}
			exit;
		}
		$this->display();
	}

	/**
	 * 删除友链
	 * @Author   ryan
	 * @DateTime 2017-11-28
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function del()
	{
		$arr['id'] = I('post.id');
		$res = M('Link')->where($arr)->save(['status'=>3]);
		$this->ajaxReturn($res);
	}

	/**
	 * 修改状态
	 * @Author   ryan
	 * @DateTime 2017-11-28
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function status()
	{

		//判断显示的轮播图是否超过5个
		$map['id'] = ['neq',I('post.id')]; 
		$map['status'] = 1; 
		$count = M('Link')->where($map)->count();
		//状态信息
		$arr['id'] = I('post.id');
		$status = M('Link')->field('status')->where($arr)->find();
		if ( $count >= 5 && $status==2 ) {
			$this->ajaxReturn('3');
			exit;
		}


		if ($status['status'] == 1) {
			$res = M('Link')->where($arr)->save(['status'=>2]);
			$this->ajaxReturn('不显示');
	
		} else if ($status['status'] == 2) {
			$res = M('Link')->where($arr)->save(['status'=>1]);
			$this->ajaxReturn('显示');

		}
	}

	/**
	 * 编辑数据
	 * @Author   ryan
	 * @DateTime 2017-11-28
	 * @email    931035553@qq.com
	 * @param    [type]           $id [description]
	 * @return   [type]               [description]
	 */
	public function edit($id)
	{
		$path = '/Uploads/';
		$link = D('link');
		if (IS_POST) {


			$res = $link->editLink();
			if($res){
				success('修改成功','Link/index');
			} else {
				error('修改失败');
			}
			exit;
		}
		$arr = $link -> find($id);
		$this->assign('arr',$arr);
		$this->assign('path',$path);
		$this->display();
	}
}