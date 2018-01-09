<?php

namespace Home\Model;
use Think\Model;

class UserDetailModel extends Model
{
	public $_validate = [
		['sex', '/^(1|2)$/', '无效的性别'],
		['birthday', '/^(\d{4}-\d{2}-\d{2})$/', '日期XXXX-XX-XX格式错误哦~', 2],
		['address2', '/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、下划线、中文组成的地址'],
		['real_name', '/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、下划线、中文组成的真实名字'],
	];

	public function index() 
	{
		
	}

	public function addInfo()	 
	{
	    $areas = M('Areas');
        $data['uid'] = $_POST['uid'];
        if (!empty($_POST['address2'])) {
            $data['address2'] = $_POST['address2'];
        }

        $sf = $areas->field('area_name')->where("`id` = ".I('post.sf'))->find();
        $city = $areas->field('area_name')->where("`id` = ".I('post.city'))->find();
        $area = $areas->field('area_name')->where("`id` = ".I('post.area'))->find();
        if (!empty($sf)) {
            if (!empty($city)) {
    	        if (!empty($area)) $adress = $sf['area_name'].$city['area_name'].$area['area_name'];
                    $data['address'] = $adress;
    	        } 
        }

        if (!empty($_POST['birthday'])) $data['birthday'] = $_POST['birthday'];
        if (!empty($_POST['real_name'])) $data['real_name'] = $_POST['real_name'];
        if (!empty($_POST['adress'])) $data['adress2'] = $_POST['adress'];
        if (!empty($_POST['sex'])) $data['sex'] = $_POST['sex'];
        if ($_FILES['user_img']['error'] !== 4) {
            $upload = new \Think\Upload();// 实例化上传类    
            $upload->maxSize   =     3145728 ;// 设置附件上传大小    
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
            $upload->savePath  =      '/UserInfo'.$data['uid'].'/'; // 设置附件上传目录    // 上传文件  
            $upload->autoSub   =  false;   
            $info   =   $upload->uploadOne($_FILES['user_img']);    
            if(!$info) {// 上传错误提示错误信息
                return $upload->getError();
            } else {// 上传成功        
            	$data['user_img'] = $info['savepath'].$info['savename'];    
            }
        }
        return $data;


	}
}