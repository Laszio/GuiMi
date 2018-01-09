<?php
namespace Home\Controller;
use Think\Controller;

class EmptyController extends Controller {

    /**
     * 空控制器
     */
    public function index() 
    {
        $this->redirect('Index/index');
    }

    /**
     * 空方法
     */
    public function _empty()
    {
        $this->redirect('Index/index');
    }
}