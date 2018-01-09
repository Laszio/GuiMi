<?php
namespace Admin\Controller;
use Think\Controller;

class IndexController extends CommonController 
{
    public function index()
    {
    	$nodeNameList = join('， ',session('nodeNameList'));
    	$this->assign('nodeNameList',$nodeNameList);
        $this->display();
    }

    public function welcome()
    {
    	// 查询拥有职务
    	$admin_role = M('admin_role');
    	$roleIds = $admin_role->where("aid='%d'",session('adminInfo.id'))->getField('rid', true);
    	$role = M('role');
        $roleNameList = $role->where(['id'=>['in', $roleIds]])->getField('role_name', true);
    	// 节点名表
    	$nodeNameList = join('， ',session('nodeNameList'));
    	// 职务表
    	$roleNameList = join('， ',$roleNameList);
    	// 统计数据
    	$adminCount = M('admin')->count();
    	$this->assign('adminCount',$adminCount);
    	$GoodsCount = M('Goods')->count();
    	$this->assign('GoodsCount',$GoodsCount);
    	$userCount = M('user')->count();
    	$this->assign('userCount',$userCount);
    	$adminCount = M('admin')->count();
        // 分配数据
    	$this->assign('adminCount',$adminCount);
    	$this->assign('nodeNameList',$nodeNameList);
    	$this->assign('roleNameList',$roleNameList);
    	$this->display();
    }

    /**
     * 修改自己的资料
     * @param  int $id 对应id
     */
    public function edit()
    {
        $id = session('adminInfo.id');
        if (IS_POST) {
            $admin = D('Admin');
            $res = $admin->create();
            if (!($res)) {
                $msg = $admin->getError();
                $data['msg'] = $msg;
                $data['status'] = 0;
                $this->ajaxReturn($data);
                exit;
            }
            $res2 = $admin->save($res);
            if ($res2) {
                if (!empty($res['password'])) {
                    // 有修改密码
                   $data['msg'] = '修改成功,请重新登录';
                   $data['status'] = 3;
                   $logSta = M('logstatus');
                   $save['status'] = 3;
                   $logSta->where("uid='%d'",$id)->save($save);
                } else {
                    // 没修改密码
                    $data['msg'] = '修改成功';
                    $data['status'] = 1;
                }
            } else {
                // 修改失败
                $data['msg'] = '没有改到，请稍后重试';
                $data['status'] = 0;
            }
            $this->ajaxReturn($data);
        } else {
            $admin = M('Admin');
            $res = $admin->find($id);
            // 查询对应用户并遍历去表单
            $this->assign('info',$res);
            $this->display();
        }
    }

}