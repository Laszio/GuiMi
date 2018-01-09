<?php
namespace Admin\Controller;
use Think\Controller;

class CommonController extends Controller {
	/**
	 * 会在所有方法执行之前判断是否登录 然后判断是否有权限
	 */
    public function _initialize()
    {
        // 启用redis
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', '6379');

    
        // 判断是否登录
    	if (empty($_SESSION['adminInfo'])) {
    		$this->redirect('Login/index');
    	}
        // 判断账号状态
        $logSta = M('logstatus');
        $map['uid'] = session('adminInfo.id');
        $map['session_id'] = session_id();
        // 查看状态表中是否是本sessionid用户
        $res = $logSta->where($map)->find();
        if (empty($res)) {
            // 在别处被登录
            session('adminInfo', null);
            $this->alert('账号在其他地方被登录了，请修改密码',U('Login/index'));
            exit;
        }
        if ($res['status'] == 2) {
            session('adminInfo', null);
            $logSta->where($map)->delete();
            $this->alert('账号被禁用了，请联系主管',U('Login/index'));
            exit;
        }
        if ($res['status'] == 3) {
            session('adminInfo', null);
            $logSta->where($map)->delete();
            $this->alert('账号信息已修改请重新登录',U('Login/index'));
            exit;
        }

        $changeSta['logtime'] = time();
        $logSta->where($map)->save($changeSta);
        
        //有常量获取当前是哪个控制器和哪个方法
        // echo '当前控制器是：',CONTROLLER_NAME,'<br>';
        $node = CONTROLLER_NAME.'/'.ACTION_NAME;
        // role = 9是超级管理员
        if ($_SESSION['adminInfo']['role'] != '9') {
            if (!in_array_case($node, session('nodeList'))) {
                // echo $this->error('您没有权限',U('Index/welcome'));
                $this->alert('没有权限访问此功能');
                exit;
            }
        }
    }

    /**
     * 防止手贱用户，直接退出并跳404
     */
    public function _empty()
    {
        // $this->jump('非法操作',U('Index/index'));
        $this->alert('功能开发中');
        exit;
    	// session('adminInfo', null);
    	// $this->display('Public/404');
    }
    
    /**
     * 弹窗提示
     * @param  string $str 提示信息
     * @param  string $url 跳转地址
     */
    protected function alert($str, $url = '')
    {
        if (empty($url)) $url = $_SERVER['HTTP_REFERER'];
        echo "<script>alert('$str');
            var index = parent.layer.getFrameIndex(window.name);
                parent.$('.btn-refresh').click();
                parent.layer.close(index);
                window.parent.location.reload();
            </script>";
        exit;
    }

    
    /**
     * [fileUpload 公共的一个文件上传方法]
     * @param  [string] $path [设置文件上传目录]
     * @return [array]       [返回一个具体信息后的数组]
     */
    public function fileUpload($maxSize=3145728, $exts= array('jpg', 'gif', 'png', 'jpeg'))
    {
        $up = new \Think\Upload();// 实例化上传类 
        $up->maxSize   =  $maxSize;// 设置附件上传大小   3M 
        $up->exts      =  $exts;// 设置附件上传类型    
        $up->savePath  =  '/brand/'; // 设置附件上传目录    // 上传文件    
        $up->autoSub   =  false;
        $info = $up->upload();
        if ($info) {
            return $info;
        } else {
            return ['status'=>'error', 'errorMsg'=>$up->getError()];
        }
    }

    /**
     * [ajaxCommon 处理AJAX公共响应信息]
     * @param  [int] $status [响应状态码]
     * @param  [string] $msg    [提示信息]
     * @return [array]         [响应回去的数组]
     */
    public function ajaxCommon($status, $msg)
    {
        return ['status'=>$status, 'msg'=>$msg];
    }
}