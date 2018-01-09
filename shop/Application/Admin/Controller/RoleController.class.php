<?php
namespace Admin\Controller;
use Think\Controller;

class RoleController extends CommonController 
{
    /**
     * 显示职位列表
     */
    public function index()
    {
        // 查数据列表
        $role = M('role');
		$count = $role->count();
    	$res = $role->select();
    	$this->assign('list',$res);
    	$this->assign('count',$count);
        $this->display();
    }

    /**
     * 新建role职位
     */
	public function addRole()
	{
        if (IS_POST) {
            // 检测数据是否为空
            if (empty(I('post.role_name')) || empty(I('post.node'))) {
                $data['status'] = 0;
                $data['info'] = '请填写正确的信息';
                $this->ajaxReturn($data);
            }
            // 写正则判断
            
            // 链接数据库
            $roles = M('role');
            // 检测是否存在同名的职位
            $roles->startTrans();
            // $where['role_name'] = ':role_name';
            // $rename = $roles->where($where)->bind(':role_name',I('post.role_name'))->find();
            $rename = $roles->where("role_name='%d'",I('post.role_name'))->find();
            if ($rename) {
                // 状态码 0 为失败
                $data['status'] = 0;
                $data['info'] = '该职位已存在';
                $this->ajaxReturn($data);
            }
            // 预备存入role表的数据
            $ins['role_name'] = I('post.role_name');
            $ins['text'] = I('post.text');
            // 写入
            // 这里需要用事务处理
            $ins_role = $roles->add($ins);
            // 如果写入成功就操作中间表
            if ($ins_role) {
                // 链接库
                $role_node = M('role_node');
                // 查回上次插入的id
                // $role_id = $ins_role; //和下面一行相同效果 不过听说add方法 5.2版本被删除了
                $role_id = $roles->where("role_name='{$ins['role_name']}'")->select();
                // $info[] 存放插入role_node表的数据
                $info['rid'] = $role_id['0']['id'];
                // 统计数据长度
                $len = count(I('post.node'));
                // 循环插入数据
                for ($i=0; $i < $len; $i++) 
                {
                    $info['nid'] = $_POST['node']["{$i}"];
                    $res2 = $role_node->add($info);
                    if (!($res2)) {
                        $data['status'] = 0;
                        $data['info'] = '数据异常';
                        // 插入失败时候清空刚刚插入的数据
                        // $roles->where("id='{$info['rid']}'")->delete();
                        // $role_node->where("rid='{$info['rid']}'")->delete();
                        // 回滚
                        $this->ajaxReturn($data);
                        exit;
                    }
                }
                $data['status'] = 1;
                $data['info'] = '添加成功';
                // 提交
                $roles->commit();
                $this->ajaxReturn($data);
            }
        }
        $nodes = M('node');
        // 查所有权限组
        $father = $nodes->where('pid=0')->select();
        // 查所有节点
        $all_nodes = $nodes->field('id,pid,node_name')->select();
        // 分配数据
        $this->assign('father',$father);
        $this->assign('allnodes',$all_nodes);
        $this->display();
	}

    /**
     * ajax删除职位
     * @param  int $id id
     */
    public function delRole($id)
    {
        $admin_role = M('admin_role');
        $where['rid'] = ':rid';
        $res1 = $admin_role->where($where)->bind(':rid',$id)->find();
        if ($res1) {
            $data['status'] = 0;
            $data['msg'] = '存在使用该职位的用户,无法删除';
            $this->ajaxReturn($data);
            exit;
        } else {
            $roles = M('role');
            $res = $roles->delete($id);
            if ($res) {
                $role_node = M('role_node');
                $where['rid'] = ':rid';
                $role_node->where($where)->bind(':rid',$id)->delete();
                $data['status'] = 1;
                $data['msg'] = '删除成功';
                $this->ajaxReturn($data);
                exit;
            }
        }
    }

    /**
     * 修改职务权限
     * @param  int $id role_id
     */
    public function editRole($id)
    {
        if (IS_POST) {
            // 处理数据
            $roleinfo['text'] = I('post.text');
            $role_id = I('post.id');
            // 修改role表
            $role = M('role');
            $role->startTrans();
            $role_res = $role->where("id='%d'",$role_id)->save($roleinfo);
            // 处理中间表
            $role_node = M('role_node');
            // 删除中间表中原本的权限
            $role_node->where("rid='%d'",$role_id)->delete();
            // 是否有选择权限
            if (!empty(I('post.node'))) {
                // 重新插入新数据
            $len = count(I('post.node'));
                // 循环插入数据
            $info['rid'] = $role_id;
                for ($i=0; $i < $len; $i++) 
                {
                    $info['nid'] = $_POST['node']["{$i}"];
                    $res2 = $role_node->add($info);
                    if (!($res2)) {
                        $data['status'] = 0;
                        $data['info'] = '数据异常';
                        $this->ajaxReturn($data);
                        exit;
                    }
                }
            }
                $data['status'] = 1;
                $data['info'] = '添加成功';
                // 提交
                $role->commit();
                $this->ajaxReturn($data);
        } else {
            // 先判断是否有管理使用此职务
            $admin_role = M('admin_role');
            $where['rid'] = ':rid';
            $res1 = $admin_role->where($where)->bind(':rid',$id)->find();
            if ($res1) {
                $this->assign('using','null');
            }
            // 查询对应职位并遍历去表单
            $role = M('role');
            $res = $role->find($id);
            // 查中间表看他原本拥有什么权限
            $role_node = M('role_node');
            $nodelist = $role_node->where("rid={$id}")->getField('nid', true);
            $nodes = M('node');
            // 查所有权限组
            $father = $nodes->where('pid=0')->select();
            // 查所有节点
            $all_nodes = $nodes->field('id,pid,node_name')->select();
            // 分配数据
            $this->assign('father',$father);
            $this->assign('nodelist',$nodelist);
            $this->assign('allnodes',$all_nodes);
            $this->assign('info',$res);
            $this->display();
                
        }
    }

    /**
     * 修改职务备注信息
     * @param  int $id role_id
     */
    public function editText($id)
    {
        if (IS_POST) {
            // 处理数据
            $roleinfo['text'] = I('post.text');
            $role_id = I('post.id');
            // 修改role表
            $role = M('role');
            $role_res = $role->where("id='%d'",$role_id)->save($roleinfo);
            $data['status'] = 1;
            $data['info'] = '修改成功';
            // 提交
            $this->ajaxReturn($data);
        } else {
            $role = M('role');
            $res = $role->field('id,text,role_name')->find($id);
            // 分配数据
            $this->assign('info',$res);
            $this->display();
        }
    }

    /**
     * 管理职务中的管理员
     * @param  int $id 对应Role的id
     */
    public function editRoleAdmin($id)
    {
        if (IS_POST) {
            // 处理数据
            $role_id = I('post.id');

            // 处理中间表
            $admin_role = M('admin_role');
            // 删除中间表中原本的权限
            $admin_role->where("rid='%d'",$role_id)->delete();
            // 是否有选择权限
            if (!empty(I('post.admin'))) {
                // 重新插入新数据
            $len = count(I('post.admin'));
                // 循环插入数据
            $info['rid'] = $role_id;
                for ($i=0; $i < $len; $i++) 
                {
                    $info['aid'] = $_POST['admin']["{$i}"];
                    $res2 = $admin_role->add($info);
                    if (!($res2)) {
                        $data['status'] = 0;
                        $data['info'] = '数据异常';
                        $this->ajaxReturn($data);
                        exit;
                    }
                }
            }
            $data['status'] = 1;
            $data['info'] = '修改成功';
            // 提交
            $this->ajaxReturn($data);
        } else {
            // 获取这个职务的信息
            $role = M('role');
            $res = $role->field('id,text,role_name')->find($id);
            // 显示所有的管理员
            $admin = M('admin');
            $adminList = $admin->field('id,username')->select();
            // 查看中间表
            $admin_role = M('admin_role');
            $ownList = $admin_role->where("rid={$id}")->getField('aid', true);
            // 分配数据
            $this->assign('ownList',$ownList);
            $this->assign('adminList',$adminList);
            $this->assign('info',$res);
            $this->display();
        }
    }
}