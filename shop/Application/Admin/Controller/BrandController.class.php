<?php
namespace Admin\Controller;
use Think\Controller;

class BrandController extends CommonController
{

	/**
	 * [index 显示品牌列表页]
	 */
	public function index()
	{
		$brand = D('Brand');
		$list = $brand->order('update_time desc')->getAll();
		$this->assign('list', $list);
		$this->display();

		
	}

	/**
	 * [add 执行AJAX添加品牌和显示添加页面的方法]
	 * @return [array] [存储错误状态和提示信息的数组]
	 */
	public function add()
	{
		if (IS_GET) {

			$type = D('Type');
			$data['status'] = 1;
			$types = $type->where($data)->getTypes();
			
			//如果不是重定向回来的 就删除缓存的图片
			if ($_SERVER['HTTP_REFERER'] != "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}") {
				$this->redis->del('img');
			}
			
			$img = $this->redis->get('img');

			$this->assign('types', $types);
			$this->assign('img', $img);
			$this->display();

		} else if (IS_AJAX) {
			$brand = D('Brand');
			//先用变量存储是个好习惯
			$pic = $this->redis->get('img');
			if (empty($pic)) {
				$this->ajaxReturn($this->ajaxCommon(0, '请选择图片上传'));
			}
			$data = $brand->create();

			if ($data) {
				$data['logo'] = $pic;
				$lastId = $brand->add($data);
				if ($lastId) {
					$this->ajaxReturn($this->ajaxCommon(1,'添加成功'));
				} else {
					$this->ajaxReturn($this->ajaxCommon(0, '添加失败'));
				}
			} else {
				$this->ajaxReturn($this->ajaxCommon(0, $brand->getError()));
			}
		}

	}

	/**
	 * [addPreview 添加品牌图片缓存]
	 */
	public function addPreview()
	{
		if (IS_POST) {
			$info = $this->fileUpload();
			if (!$info['status'] == 'error') {
				$filename = $info['logo']['savepath'].$info['logo']['savename'];
				//获取文件路径 存进redis
				$this->redis->set("img", $filename);
				//重定向回去
				$this->redirect('add');
			} else {
				$this->error($info['errorMsg']);
				exit;
			}		
		} else {
			return false;
		}
	}

	/**
	 * [edit 编辑品牌]
	 * @return [array] [存储错误状态和提示信息的数组]
	 */
	public function edit()
	{

		if (IS_GET) {
			$id = I('get.id');
			if (empty($id)) $this->error('非法操作');
			$brand = D('Brand');
			$type = D('Type');
			$list = $brand->find($id);

			//如果不是重定向回来的 就删除缓存的图片
			if ($_SERVER['HTTP_REFERER'] != "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}") {
				$this->redis->del('editImg');
			}
			$editImg = $this->redis->get('editImg');

			$data['status'] = 1;
			$types = $type->where($data)->getTypes();

			$this->assign('editImg', $editImg);
			$this->assign('types', $types);
			$this->assign('list', $list);
			$this->display();
		} else if (IS_AJAX) {
			$brand = D('Brand');
			$editImg = $this->redis->get('editImg');
			
			$data = $brand->create();

			if ($data) {
				if ($editImg) $data['logo'] = $editImg;

				$res = $brand->save($data);
				if ($res) {
					$this->ajaxReturn($this->ajaxCommon(1, '修改成功'));
				} else {
					$this->ajaxReturn($this->ajaxCommon(0, '你啥都没改到'));
				}

			} else {
				$this->ajaxReturn($this->ajaxCommon(0, $brand->getError()));
			}
		}
	}

	/**
	 * [addPreview 编辑时存储品牌图片缓存]
	 */
	public function editPreview()
	{
		if (IS_POST) {
			$info = $this->fileUpload();
			if (!$info['status'] == 'error') {
				$filename = $info['logo']['savepath'].$info['logo']['savename'];
				//获取文件路径 存进redis
				$this->redis->set("editImg", $filename);
				//重定向回去, 必须绑定ID
				$this->redirect('edit', ['id'=>I('get.id')]);
			} else {
				$this->error($info['errorMsg']);
				exit;
			}		
		} else {
			$this->error('非法操作');
		}
	}

	/**
	 * [ajaxDel AJAX删除单条]
	 * @return [array] [存储错误状态和提示信息的数组]
	 */
	public function ajaxDel()
	{
		if (IS_AJAX) {
			$id = I('post.id');
			if (empty($id)) $this->ajaxReturn($this->ajaxCommon(0, '非法操作'));
			$brand = D('Brand');
		
			$res = $brand->delete($id);
			if ($res) {
				$this->ajaxReturn($this->ajaxCommon(1, '删除成功'));
			} else {
				$this->ajaxReturn($this->ajaxCommon(0, '删除失败'));
			}
		} else {
			$this->error('非法操作');
		}
	}

	/**
	 * [ajaxDelMany AJAX批量删除多条]
	 * @return [array] [存储错误状态和提示信息]
	 */
	public function ajaxDelMany()
	{
		if (IS_AJAX) {
			$checkId = I('post.checkId');
			if (empty($checkId)) $this->ajaxReturn($this->ajaxCommon(0, '尚未勾选品牌'));
			$brand = D('Brand');
			$data['id'] = ['in', $checkId];
			
			$res = $brand->where($data)->delete();
			if ($res) {
				$this->ajaxReturn($this->ajaxCommon(1, '删除成功'));
			} else {
				$this->ajaxReturn($this->ajaxCommon(0, '删除失败'));
			}

		} else {
			$this->error('非法操作');
		}
	}

	/**
	 * [ajaxStatus AJAX修改品牌状态]
	 */
	public function ajaxStatus()
	{
		if (IS_AJAX) {
			$brand = D('Brand');
			$goods = D('Goods');
			$status = $brand->where('id ='.I('get.id'))->getField('status');
			if ($status == 1) {
				//判断该品牌下是否有，已上架或新添加的商品，并且不是能假删除的数据
				$map['bid'] = I('get.id');
				$map['status'] = ['neq', 3];
				$map['delsta'] = 1;
				$goodsInfo = $goods->where($map)->select();
				
				if ($goodsInfo) {
					//该品牌下有
					$this->ajaxReturn($this->ajaxCommon(0, '请先下架该品牌下的商品'));
				} else {
					//该品牌下无，可下架
					$data['id'] = I('get.id');
					$data['status'] = 2;
					$res = $brand->save($data);
					if ($res) {
						$this->ajaxReturn($this->ajaxCommon(1, I('get.id')));
					} else {
						$this->ajaxReturn($this->ajaxCommon(0, '修改失败'));
					}
					
				}

			} else if ($status == 2) {
				$data['id'] = I('get.id');
				$data['status'] = 1;
				$res = $brand->save($data);
				if ($res) {
					$this->ajaxReturn($this->ajaxCommon(2, I('get.id')));
				} else {
					$this->ajaxReturn($this->ajaxCommon(3, '修改失败'));
				}
			}
		} else {
			$this->error('非法操作');
		}
	}
}

