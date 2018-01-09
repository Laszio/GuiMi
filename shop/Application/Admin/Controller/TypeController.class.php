<?php
namespace Admin\Controller;
use Think\Controller;

class TypeController extends CommonController
{
	/**
	 * [index 显示分页列表]
	 */
	public function index()
	{
		$type = D('Type');
		$list = $type->getTypes();

		$this->assign('list', $list);
		$this->display();
	}

	/**
	 * [add 添加分类]
	 * @return [array] [存储状态和提示信息的数组]
	 */
	public function add()
	{
		if (IS_AJAX) {
			$type = D('Type');

			if (!empty(intval($_POST['id'])) && strlen($_POST['id']) > 0 ) {
				//添加子类
				$data['id'] = I('post.id');
				unset($_POST['id']);
				$info = $type->field('id, path, attr')->where($data)->find();
				if ($info) {
					if ($info['attr'] == 3) $this->ajaxReturn($this->ajaxCommon(0, '最多只能先添加三级分类'));
					$list = $type->create();
					
					if ($list) {
						$list['pid'] = $info['id'];
						$list['path'] = $info['path'].$info['id'].',';
						//统计第几级类
						$num = substr_count($list['path'], ',');
						$list['attr'] = $num;
						$res = $type->add($list);
						if ($res) {
							$this->ajaxReturn($this->ajaxCommon(1, '添加成功'));
						} else {
							$this->ajaxReturn($this->ajaxCommon(0, '添加失败'));
						}
					} else {
						$this->ajaxReturn($this->ajaxCommon(0, $type->getError()));
					}

				} else {
					$this->ajaxReturn($this->ajaxCommon(0, '非法操作'));
				}
			} else {
				//添加顶级分类
				$data = $type->create();
				
				if ($data) {
					$data['attr'] = 1;
					$res = $type->add($data);
					
					if ($res) {
						$this->ajaxReturn($this->ajaxCommon(1, '添加成功'));
					} else {
						$this->ajaxReturn($this->ajaxCommon(0, '添加失败'));
					}
				} else {
					$this->ajaxReturn($this->ajaxCommon(0, $type->getError()));
				}	
			}

		} else if (IS_GET) {

			//get方式进入该页面
			$this->display();
		}
	}

	/**
	 * [ajaxDel AJAX删除一条分类]
	 * @return [array] [存储状态和提示信息的数组]
	 */
	public function ajaxDel()
	{
		if (IS_AJAX) {		
			$id = I('post.id');
			if (empty($id)) $this->ajaxReturn($this->ajaxCommon(0, '非法操作'));	

			$type = D('Type');
			$data['pid'] = $id;
			$son = $type->where($data)->select();

			if ($son) {
				$this->ajaxReturn($this->ajaxCommon(0, '请先删除子类'));
			} else {
				$goods = M('Goods');
				//查该分类下有无已经上架或新添加的商品,并且不能是假删除的数据
				$map['status'] = ['neq', 3];
				$map['delsta'] = 1;
				$map['tid'] = $id;
				$goodsInfo = $goods->where($map)->find();
				if ($goodsInfo) $this->ajaxReturn($this->ajaxCommon(0, '请先下架商品'));
				$res = $type->delete($id);
				
				if ($res) {
					$this->ajaxReturn($this->ajaxCommon(1, '删除成功'));
				} else {
					$this->ajaxReturn($this->ajaxCommon(0, '删除失败'));
				}
				
			}		
		} else {
			return false;
		}
	}

	/**
	 * [ajaxDelMany 批量删除多条]
	 * @return [array] [存储错误状态和提示信息的数组]
	 */
	public function ajaxDelMany()
	{
		if (IS_AJAX) {
			$type = D('Type');
			$checkId = I('post.checkId');
			
			if (!$checkId) {
				$this->ajaxReturn($this->ajaxCommon(0, '您未选择分类'));
			}

			$data['pid'] = ['in', $checkId];
			$list = $type->where($data)->select();

			if ($list) {
				$this->ajaxReturn($this->ajaxCommon(0, '请先删除子类'));
			} else {
				//没有子类
				//是否有商品
				$goods = M('Goods');
				$test['tid'] = ['in', $checkId];
				$goodsInfo = $goods->where($test)->select();
				if ($goodsInfo) $this->ajaxReturn($this->ajaxCommon(0, '请先下架对应的商品'));

				//删除
				$map['id'] = ['in', $checkId];
				$res = $type->where($map)->delete();
				if ($res) {
					$this->ajaxReturn($this->ajaxCommon(1, '删除成功'));
				} else {
					$this->ajaxReturn($this->ajaxCommon(0, '删除失败'));
				}
			}	
		} else {
			return false;
		}
	}

	/**
	 * [edit 编辑分类信息]
	 * @return [array] [存储状态和提示信息]
	 */
	public function edit()
	{
		$type = D('Type');

		if (IS_GET) {
			$id = I('get.id');
			$info = $type->find($id);
			if ($info) {
				$this->assign('info', $info);
				$this->display();
			} else {
				$this->error('非法操作');
			}
		} else if (IS_AJAX) {
			$data = $type->create();
			if ($data) {
				$res = $type->save($data);
				if ($res) {
					$this->ajaxReturn($this->ajaxCommon(1, '修改成功'));
				} else {
					$this->ajaxReturn($this->ajaxCommon(0, '您啥都没改到'));
				}
			} else {
				$this->ajaxReturn($this->ajaxCommon(0, $type->getError()));
			}
		}
	}

	/**
	 * [ajaxStatus ajax修改分类状态]
	 * @return [string] [提示信息]
	 */
	public function ajaxStatus()
	{
		if (IS_AJAX) {

			$id = I('get.id');
			$type = D('Type');
			$goods = M('Goods');
			$info = $type->field('path,status')->find($id);

			if (!$info) {
				$this->ajaxReturn('未找到该分类');
			}
			//匹配所有的子类
			$data['path'] = ['like', $info['path'].$id.',%'];
			//查看是否有子类
			$son = $type->where($data)->select();
			

			if ($son) {
				// dump($info);exit;
				//有子类 
				if ($info['status'] == 1) {
					//判断全部子类的状态是否为禁用
					$sonStatus = array_column($son, 'status');
					if (in_array('1', $sonStatus)) {
						$this->ajaxReturn('请先禁用所有子类');
					}
					$data['status'] = 2;
					$data['id'] = $id;
					$type->save($data);
					$this->ajaxReturn('禁用成功');
				} else if ($info['status'] == 2) {
					//判断全部子类的状态是否正常
					$sonStatus = array_column($son, 'status');
					if (in_array('2', $sonStatus)) {
						$this->ajaxReturn('请先还原所有的子类');
					}
					$data['status'] = 1;
					$data['id'] = $id;
					$type->save($data);
					$this->ajaxReturn('还原成功');

				}
				
			} else {

				//最小的类 无子类
				if ($info['status'] == 1) {
					//查该分类下有无已经上架或新添加的商品,并且不能是假删除的数据
					$map['status'] = ['neq', 3];
					$map['delsta'] = 1;
					$map['tid'] = $id;
					$goodsInfo = $goods->where($map)->select();
					
					if ($goodsInfo) {
						$this->ajaxReturn('请先下架商品');
					} else {
						$data['status'] = 2;
						$data['id'] = $id;
						$type->save($data);
						$this->ajaxReturn('禁用成功');
					}
					

				} else if ($info['status'] == 2) {
					$map['status'] = 1;
					$map['id'] = $id;
					$result = $type->save($map);
					$this->ajaxReturn('还原成功');
					
				}		
			}
			
		} else {
			return false;
		}
	}


	

}
