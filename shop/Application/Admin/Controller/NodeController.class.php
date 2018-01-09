<?php
namespace Admin\Controller;
use Think\Controller;

class NodeController extends CommonController 
{
    /**
     * 显示Node模块的首页
     */
    public function index()
    {
        $nodes = M('node');
        $res = $nodes->where('pid != 0')->order('pid desc')->select();
        $res1 = $nodes->where('pid = 0')->getField('id,node_name',true);
        // 统计数据
        $count = $nodes->where('pid != 0')->count();
        $this->assign('group_name',$res1);
    	$this->assign('list',$res);
        $this->assign('count',$count);
        $this->display();

    }

    /**
     * 显示Node的属组列表的首页
     */
    public function group()
    {
        $nodes = M('node');
        $res = $nodes->where('pid = 0')->select();
        $count = $nodes->where('pid = 0')->count();
        $this->assign('list',$res);
        $this->assign('count',$count);
        $this->display();
    }

    /**
     * 添加节点
     */
    public function addNode()
    {
    	if (IS_POST) {
    		if(empty(I('post.node_name')) || empty(I('post.node')))
    		{
                $data['info'] = '非法操作';
                $data['status'] = 0;
                $this->ajaxReturn($data);
                exit;
    		}
            // 正则判断
            // 连接数据库
    		$nodes = M('node');
            // 预备存入数据库的数据
    		$data['node'] = I('post.node');
    		$data['node_name'] = I('post.node_name');
            $data['pid'] = I('post.pid');
            $data['path'] = '0,'.I('post.pid');
            $data['text'] = I('post.text');
    		// 查看数据库是否已经有这个控制器
    		$arr = $nodes->where("node='%d'",$data['node'])->find();
    		if (!empty($arr)) {
    			$data['info'] = "该节点已经存在, 权限名为：{$arr['node_name']}";
                $data['status'] = 0;
                $data['sql'] = $nodes->_sql();
                $this->ajaxReturn($data);
                exit;
    		}
    		// 写入数据库
    		$res = $nodes->add($data);
			if ($res) {
    			$data['info'] = '添加成功';
                $data['status'] = 1;
                $this->ajaxReturn($data);
                exit;
    		} else {
    			$data['info'] = '非法操作';
                $data['status'] = 0;
                $this->ajaxReturn($data);
                exit;
    		}
    	} else {
            // 查询权限组
            $group = M('node');
            $res = $group->where('pid=0')->select();
            $this->assign('group',$res);
	    	$this->display();
    	}
    }

    /**
     * 新增权限组
     */
    public function addGroup()
    {
        if (IS_POST) {
            if(empty($_POST['node_name']))
            {
                $data['info'] = '非法操作';
                $data['status'] = 0;
                $this->ajaxReturn($data);
                exit;
            }
            $nodes = M('node');
            $data['node_name'] = $_POST['node_name'];
            $data['text'] = $_POST['text'];
            $data['pid'] = 0;
            $data['path'] = '0,';
            $data['node'] = $_POST['node'];
            // 查看数据库是否已经有这个组名
            $arr = $nodes->where("node_name='%d'",$data['node_name'])->find();
            if (!empty($arr)) {
                $data['info'] = "该节点已经存在, 权限名为：{$arr['node_name']}";
                $data['status'] = 0;
                $data['sql'] = $nodes->_sql();
                $this->ajaxReturn($data);
                exit;
            }
            // 写入数据库
            $res = $nodes->add($data);
            if ($res) {
                $data['info'] = '添加成功';
                $data['status'] = 1;
                $this->ajaxReturn($data);
                exit;
            } else {
                $data['info'] = '非法操作';
                $data['status'] = 0;
                $this->ajaxReturn($data);
                exit;
            }
        } else {
            $this->display();
        }
    }

    /**
     * ajax删除Node节点
     * @param  int $id Node节点id   （权限和权限组公用一个方法）
     * @return string     删除的意思信息
     */
    public function delNode($id)
    {
        $nodes = M('node');
        $role_node = M('role_node');
        $role_node_res = $role_node->where("nid='%d'",$id)->find();
        if (!($role_node_res)) {
            $res = $nodes->delete($id);
            if ($res) {
                $data['msg'] = '删除成功';
                $data['status'] = '1';
                $this->ajaxReturn($data);
            } else {
                $data['msg'] = '删除失败';
                $data['status'] = '0';
                $this->ajaxReturn($data);
            }
        } else {
            $data['msg'] = '删除失败，有角色拥有此权限，请先删除对应角色';
                $data['status'] = '0';
                $this->ajaxReturn($data);
        }
    }

    /**
     * 修改权限组备注信息
     * @param  int $id role_id
     */
    public function editNodeText($id)
    {
        if (IS_POST) {
            // 处理数据
            $nodeinfo['text'] = I('post.text');
            $nodeinfo['node_name'] = I('post.node_name');
            $node_id = I('post.id');
            // 修改node表
            $node = M('node');
            $node_res = $node->where("id='%d'",$node_id)->save($nodeinfo);
            $data['status'] = 1;
            $data['info'] = '修改成功';
            // 提交
            $this->ajaxReturn($data);
        } else {
            $node = M('node');
            $res = $node->field('id,text,node_name')->find($id);
            // 分配数据
            $this->assign('info',$res);
            $this->display();
        }
    }
}