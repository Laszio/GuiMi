<?php
namespace Admin\Controller;
use Think\Controller;

class UserController extends CommonController 
{
	/**
	 * [index 展示会员列表]
	 */
    public function index()
    {
    	$user = D('User');
    	$list = $user->order('addtime desc')->getAll();
    	$this->assign('list', $list);
        $this->display();
    }

    /**
     * [show 展示个人信息弹层]
     */
    public function show()
    {
    	$id = I('get.id');
    	if (empty($id)) $this->error('非法操作');
    	$user = D('User');
    	$info = $user->field('shop_user.id,username,phone,email,score,addtime,money,sex,address')->join("LEFT JOIN __USER_DETAIL__ on __USER__.id = __USER_DETAIL__.uid")->where('shop_user.id='.$id.'')->find();
    	$this->assign('info', $info);
    	$this->display();
    }

    /**
     * [ajaxStatus ajax修改用户状态]
     */
   	public function ajaxStatus()
	{
		if (IS_AJAX) {
			$user = D('User');
			$status = $user->where('id ='.I('get.id'))->getField('status');
			if ($status == 1) {
				$data['id'] = I('get.id');
				$data['status'] = 2;
				$res = $user->save($data);
				if ($res) {
					$this->ajaxReturn($this->ajaxCommon(1, I('get.id')));
				} else {
					$this->ajaxReturn($this->ajaxCommon(0, '修改失败'));
				}
			} else if ($status == 2) {
				$data['id'] = I('get.id');
				$data['status'] = 1;
				$res = $user->save($data);
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
