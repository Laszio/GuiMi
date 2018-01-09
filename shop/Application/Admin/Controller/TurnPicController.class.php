<?php
namespace Admin\Controller;

use Think\Controller;

class TurnPicController extends CommonController 
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
		$arr = M('Turnpic')->field('id,gid,tid,gname,status,img,addtime')
				 ->order('addtime asc')->where('status<>3')->select();
		$status = [1=>'显示','不显示'];
		$path = __ROOT__.'/Uploads';
		foreach ($arr as $k=>$v) {
			$arr[$k]['status'] = $status[$v['status']];
			$arr[$k]['img'] = $path.$v['img'];
		}

		$this->assign('arr',$arr);
		$this->display();
	}

	/**
	 * 添加轮播图
	 * @Author   ryan
	 * @DateTime 2017-11-28
	 * @email    931035553@qq.com
	 */
	public function add()
	{
		if (IS_POST) {
			$turnpic = D('TurnPic');
			$res = $turnpic->addTurnPic();

			if($res){
				success('添加成功','TurnPic/index');
				exit;
			}
		}
		$type = M('type');
		$type = $type->where('attr=3')
				->field('name,id')
				->select();
		$this->assign('type',$type);
		$this->display();
	}

	/**
	 * 删除轮播图
	 * @Author   ryan
	 * @DateTime 2017-11-28
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function del()
	{
		$arr['id'] = I('post.id');

		$res = M('Turnpic')->where($arr)->save(['status'=>3]);
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
		$count = M('Turnpic')->where($map)->count();
		//当前状态
		$arr['id'] = I('post.id');
		$status = M('Turnpic')->field('status')->where($arr)->find();

		if ( $count >= 5 && $status['status'] == 2) {
			$this->ajaxReturn('3');
			exit;
		}

		if ($status['status'] == 1) {
			$res = M('Turnpic')->where($arr)->save(['status'=>2]);
			$this->ajaxReturn('不显示');
	
		} else if ($status['status'] == 2) {
			$res = M('Turnpic')->where($arr)->save(['status'=>1]);
			$this->ajaxReturn('显示');

		}
	}

	public function edit($id)
	{
		$path = '/Uploads/';
		$turnpic = D('TurnPic');
		if (IS_POST) {
			$res = $turnpic->editTurnPic();
			if($res){
				success('修改成功','TurnPic/index');
			}
		}
		//生成type
		$type = M('type');
		$type = $type->where('attr=3')
				->field('name,id')
				->select();
		$arr = $turnpic -> find($id);
		$this->assign('type',$type);
		$this->assign('arr',$arr);
		$this->assign('path',$path);
		$this->display();
	}

	public function goods()
	{
		$goods = M('goods');
		$map['tid'] = I('get.tid');
		$map['status'] = 2;
		$map['delsta'] = 1;
		$good = $goods->where($map)
				->field('gname,id')
				->select();
		
		$this->ajaxReturn($good);
	}
}