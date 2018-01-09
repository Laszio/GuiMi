<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends CommonController {
	/**
	 * 加载管理员列表
	 */
    public function index()
    {
        $admin = D('Admin');
        // 统计一共有多少条数据
        if (session('adminInfo.role') != 9) {
        	$count = $admin->where('role != 9')->count();
	        // 处理数据
	        $res = $admin->where('role != 9')->order('addtime asc')->doAdminIndex();
        } else {
	        $count = $admin->count();
	        // 处理数据
	        $res = $admin->order('addtime asc')->doAdminIndex();
	    }
        // 分配数据
        $this->assign('count',$count);
        $this->assign('list',$res);
        $this->display();
    }

    /**
     * 添加管理员
     */
    public function addAdmin()
    {
    	// 判断是否是POST过来
    	if(IS_POST)
    	{
            // 自动验证
            $admin = D('Admin');
            $res = $admin->create();
            if (!($res)) {
                $msg = $admin->getError();
                $data['msg'] = $msg;
                $data['status'] = 0;
                $this->ajaxReturn($data);
                exit;
            }
            // 预备数据
            // $res['addtime'] = time();
            $res['status'] = 1;
            // add返回的是上次插入的id
            $res_id = $admin->add($res);
            // 判断是否写入成功
            if ($res_id) {
                $admin_role = M('admin_role');
                // 获取上次插入的id
                $admin_id = $res_id;
                // $info[] 存放插入admin_role表的数据
                $info['aid'] = $admin_id;
                // 统计接收到的角色id有多少个
                if (!empty(I('post.role_id'))) {
                    $len = count(I('post.role_id'));
                    // 循环插入数据去中间表
                    for ($i=0; $i < $len; $i++) 
                    {
                        $info['admin_name'] = I('post.username');
                        $info['rid'] = $_POST['role_id']["{$i}"];
                        $res2 = $admin_role->add($info);
                        if (!($res2)) {
                            $data['status'] = 0;
                            $data['msg'] = '数据异常';
                            // 相当于事务回滚 删除之前插入的数据
                            $admin->where("id={$res_id}")->delete();
                            $admin_role->where("id={$res_id}")->delete();
                            $this->ajaxReturn($data);
                        }
                    }
                }
                $data['status'] = 1;
                $data['msg'] = '添加成功';
                $this->ajaxReturn($data);
                exit;
            } else {
                $data['msg'] = '异常，请稍后再试';
                $data['status'] = 0;
                $this->ajaxReturn($data);
                exit;
            }
    	} else {
            // 加载表单
            // 添加中的role列表
            $role = M('Role');
            $roles = $role->select();
            $this->assign('rolelist',$roles);
	    	$this->display();
	    	exit;
    	}
    }

    /**
     * ajax修改用户状态
     * @param  int $id 用户的id
     */
    public function chStatus($id)
    {
        if ($id == session('adminInfo.id')) {
            $data['status'] = 3;
            $data['msg'] = '无法禁用自己';
            $this->ajaxReturn($data);
            exit;
        }
        $admin = M('Admin');
        $info = $admin->find($id);
        if ($info['role'] == '9') {
            $data['status'] = 3;
            $data['msg'] = '无法禁用超级管理员';
            $this->ajaxReturn($data);
            exit;
        }
        // $data 用于修改 Admin 表数据
        if ($info['status'] == 1) {
            $data['status'] = 2;
            $return['msg'] = '禁用';
            $return['status'] = 2;
        } else {
            $data['status'] = 1;
            $return['msg'] = '正常';
            $return['status'] = 1;
        }
        
        $logSta = M('logstatus');
        // 判断这个用户是否有登录 有就通过修改status强迫他下线
        $logMsg = $logSta->where("uid='%d'",$id)->find();
        if ($logMsg) {
            $chaLogSta['status'] = $data['status'];
            $logSta->where("uid='%d'",$id)->save($chaLogSta);
        }
        // 操作Admin表中的status
        $data['id'] = $id;
        $res = $admin->save($data);
        if ($res) {
            $this->ajaxReturn($return);
        } else {
            $this->error('失败');
        }
    }

    /**
     * 删除管理员
     * @param  int $id 对应id
     */
    public function del($id)
    {
        if ($id == session('adminInfo.id')) {
            $data['status'] = 2;
            $data['msg'] = '无法干掉自己';
            $this->ajaxReturn($data);
            exit;
        }
        $admin = M('Admin');
        $admin_role = M('admin_role');
        // 直接删除（做一个事务回滚）
        $res_admin = $admin->where("id='%d'",$id)->find();
        if ($res_admin['role'] == '9') {
            $data['status'] = 2;
            $data['msg'] = '无法删除超级管理员';
            $this->ajaxReturn($data);
            exit;
        }
        // 判断用户是否在线 在线无法删除
        $logSta = M('logstatus');
        $logStaMsg = $logSta->where("uid='%d'",$id)->find();
        if (!empty($logStaMsg['session_id'])) {
            $data['status'] = 2;
            $data['msg'] = '账号在线,无法删除';
            $this->ajaxReturn($data);
            exit;
        }
        // 开始删除
        $res2 = $admin->delete($id);
        if ($res2) {
            // 删除成功
            // 删除中间表的数据
            $res1 = $admin_role->where("aid={$id}")->delete();
            $data['status'] = 1;
            $data['msg'] = '删除成功';
            $this->ajaxReturn($data);
            exit;
        } else {
            $data['status'] = 2;
                $data['msg'] = '删除失败';
                $this->ajaxReturn($data);
                exit;
        }

    }

    /**
     * 修改管理员资料
     * @param  int $id 对应id
     */
    public function edit($id)
    {

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

    /**
     * 修改管理员权限
     * @param  int $id 对应id
     */
    public function editRole($id)
    {
        if (IS_POST) {
            $admin = M('Admin');
            $saveAdmin['text'] = I('post.text');
            $id = I('post.id');
            $res1 = $admin->where("id='%d'",$id)->save($saveAdmin);
            // 处理中间表
            $admin_role = M('admin_role');
            $admin_role->where("aid='%d'",$id)->delete();
            // 重新插入
            $info['aid'] = $id;
            if (!empty(I('post.role_id'))) {
                $len = count(I('post.role_id'));
                // 循环插入数据去中间表
                for ($i=0; $i < $len; $i++) 
                {
                    $info['admin_name'] = I('post.username');
                    $info['rid'] = $_POST['role_id']["{$i}"];
                    $res2 = $admin_role->add($info);
                    if (!($res2)) {
                        $data['status'] = 0;
                        $data['msg'] = '数据异常';
                        // 相当于事务回滚 删除之前插入的数据
                        $admin->where("id={$res_id}")->delete();
                        $admin_role->where("id={$res_id}")->delete();
                        $this->ajaxReturn($data);
                    }
                }
            }
            $data['msg'] = '修改成功';
            $data['status'] = 3;
            // 就该登录状态表
            $logSta = M('logstatus');
            $save['status'] = 3;
            $logSta->where("uid='%d'",$id)->save($save);
            $this->ajaxReturn($data);
        } else {
            $admin = M('Admin');
            $res = $admin->field('id,username,text')->find($id);
            $admin_role = M('admin_role');
            $ownRole = $admin_role->where("aid='%d'",$id)->getField('rid',true);
            // 添加中的role列表
            $role = M('Role');
            $roles = $role->select();
            $this->assign('ownRole',$ownRole);
            $this->assign('rolelist',$roles);
            // 查询对应用户并遍历去表单
            $this->assign('info',$res);
            $this->display();
        }
    }

}