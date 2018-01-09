<?php
namespace Admin\Controller;
use Think\Controller;

class SystemController extends CommonController 
{
    public function index()
    {
    	$seoModel = M('Seo');
    	$seoInfo = $seoModel->field('id,title,keywords,description,logopic,addtime')->where('id = 1')->find();
    	$this->assign('seoInfo', $seoInfo);
        $this->display();

    }

    public function edit()
    {
		$seoModel = M('Seo');
    	if (IS_POST) {
    		if (empty($_POST['title'])) {
    			unset($_POST['title']);
    		}
    		if (empty($_POST['keywords'])) {
    			unset($_POST['keywords']);
    		}
    		if (empty($_POST['description'])) {
    			unset($_POST['description']);
    		}
    		$data = $_POST;
    		if ($_FILES['logopic']['error'] != 4) {
			    $upload = new \Think\Upload();// 实例化上传类    
			    $upload->maxSize   =     3145728 ;// 设置附件上传大小    
			    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
			    $upload->savePath  =      '/SEO/'; // 设置附件上传目录    // 上传文件     
                $upload->autoSub   =  false;
			    $info   =   $upload->uploadOne($_FILES['logopic']);    
			    if(!$info) {// 上传错误提示错误信息
			    }else{// 上传成功
			    	$data['logopic'] = $info['savepath'].$info['savename'];
			    }
    		}
    		$res = $seoModel->where('id = 1')->save($data);
    		if ($res) {
    			$this->success('修改成功',U('System/index'));
    		} else {
    			$this->success('错误');
    		}
    		exit;
    	}
    	$seoInfo = $seoModel->field('id,title,keywords,description,logopic,addtime')->where('id = 1')->find();
    	$this->assign('seoInfo', $seoInfo);
        $this->display();

    }

    public function systemLog()
    {
    	$this->display();
    }

}