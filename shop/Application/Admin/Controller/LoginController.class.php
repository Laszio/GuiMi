<?php
namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller 
{
    /**
     * 登录
     */
    public function index()
    {
        /**
         * 如果是post提交就处理数据
         */
    	if (IS_POST) {
            // 接收数据
    		$map['username'] = I('post.username');
            // 查表
            $admin = M('admin');
            $info = $admin->where($map)->find();
            if (empty($info)) {
                $this->alert('登录失败',U('Login/index'));
                exit;
            }
            if ($info['status'] != 1) {
                $this->alert('账号已禁用',U('Login/index'));
                exit;
            }
            // 获取数据库中的哈希密码
            $pwd = I('post.password');
            $respwd = $info['password'];
            // 判断密码是否正确
            if ( password_verify ( $pwd ,  $respwd )) {
                session('adminInfo', $info);
                // 查看账号登录状态
                $logstatus = M('logstatus');
                $uid = session('adminInfo.id');
                $oldLogMsg = $logstatus->where("uid='%d'",$uid)->find();
                // 判断是否满足限制登录的条件
                if (($oldLogMsg['logsum'] > 5) && ((time() - $oldLogMsg['logtime']) < 600)) {
                    $this->alert('账号已被限制登录，请联系老板');
                    exit;
                }
                // 如果session_id 上有存值 就表示有用户在线上
                if ( (!empty($oldLogMsg['session_id'])) && ((time() - $oldLogMsg['logtime']) < 600) ) {
                    // 账号已经在线
                    $change['session_id'] = '';
                    $oldtime = $oldLogMsg['logtime'];
                    // 5分钟内重复撞下线就会计算次数  超过5就会停止登录10分钟
                    if ((time() - $oldtime) < 300) {
                        $change['logsum'] = $oldLogMsg['logsum'] + 1;
                    }
                    $logstatus->where("uid='%d'",$uid)->save($change);
                    session('adminInfo', null);
                    $this->alert('账号已登录，请重新登录',U('Login/index'));
                    exit;
                }
                // 预备新的登录状态数据
                $logSta['uid'] = $uid;
                $logSta['session_id'] = session_id();
                $logSta['status'] = 1;
                $logSta['logtime'] = time();
                // 表中是否存在该uid的数据
                if ($oldLogMsg) {
                    // 如果距离最后登录时间超过5分钟就重置里面的冲突次数
                    if ((time() - $oldLogMsg['logtime']) > 300) {
                        $logSta['logsum'] = 0;
                    }
                    $logstatus->where("uid='%d'",$uid)->save($logSta);
                } else {
                    $logSta['logsum'] = 0;
                    $logstatus->add($logSta);
                }

                //1.根据用户id获取当前用户的职业id
                $admin_role = M('admin_role');
                $roleIds = $admin_role->where("aid='{$info['id']}'")->getField('rid', true);
                //2.根据职业id查节点id
                $role_node = M('role_node');
                $nodeIds = $role_node->where(['rid'=>['in', $roleIds]])->getField('nid', true);
                //3.根据节点id查出所有节点
                $node = M('node');
                // 权限节点集合
                $arr = $node->where(['id'=>['in', $nodeIds]])->getField('node', true);
                // 权限名字集合
                $arr2 = $node->where(['id'=>['in', $nodeIds]])->getField('node_name', true);
                //追加一个首页的权限
                $arr[] = 'Index/index';
                $arr[] = 'Index/welcome';
                $arr[] = 'Index/edit';
                session('nodeList', $arr);
                session('nodeNameList', $arr2);
                // 修改登录消息
                $data['logtime'] = time();
                $data['logip'] = get_client_ip();
                $data['logsum'] = $info['logsum'] + 1;
                $logtimeres = $admin->where("id='{$info['id']}'")->save($data);
                //跳到首页
                $this->redirect('Index/index');
            } else {
                $this->alert('登录失败',U('Login/index'));
                exit;
            }
    	} else {
            // 显示表单
            $this->display();
        }
    }

    /**
     * 注销登陆过
     */
    public function logout()
    {
        $logstatus = M('logstatus');
        $map['uid'] = session('adminInfo.id');
        $map['session_id'] = session_id();
        $res = $logstatus->where($map)->delete();
        session('adminInfo', null);
        $this->redirect('Login/index');
    }

    /**
     * 弹窗提示
     * @param  string $str 提示信息
     * @param  string $url 跳转地址
     */
    protected function alert($str, $url = '')
    {
        if (empty($url)) $url = $_SERVER['HTTP_REFERER'];
        echo "<script>alert('$str');location.href='$url'</script>";
        exit;
    }
}