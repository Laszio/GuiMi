<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends PublicController {

    public function _initialize ()
    {
        // 调用回Public中的_init方法
        $this->HeaderCart();
        if (!session('?userinfo') || session('ip') !== get_client_ip()) {
        	$this->redirect('Login/index');
        } 
        //查询未读信息
		$mp2 = ['unread'=>1,'status'=>1,'uid'=>session('userinfo')['id']]; 
		$unread = '('.M('MessageDetail')->where($mp2)->count().')';
		$this->assign('num',$unread);
    }


}