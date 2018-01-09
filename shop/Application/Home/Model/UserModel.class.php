<?php

namespace Home\Model;
use Think\Model;

class UserModel extends Model
{
	public $_validate = [
		['sex', '/^(1|2)$/', '无效的性别'],
		['birthday', '/^(\d{4}-\d{2}-\d{2})$/', '日期XXXX-XX-XX格式错误哦~', 2],
		['adress', '/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、下划线、中文组成的地址'],
		['real_name', '/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、下划线、中文组成的真实名字'],
	];

	public function index() 
	{
		
	}

	public function addInfo()	 
	{
		echo 1;
		$areas = M('Areas');
        $data['uid'] = $_POST['uid'];

        $sf = $areas->field('area_name')->where("`id` = ".I('post.sf'))->find();
        $city = $areas->field('area_name')->where("`id` = ".I('post.city'))->find();
        $area = $areas->field('area_name')->where("`id` = ".I('post.area'))->find();
        if (!empty($sf)) {
        	if (!empty($city)) {
	        	if (!empty($area)) $adress = $sf['area_name'].$city['area_name'].$area['area_name'];
		        $data['adress'] = $adress;
        	} 
        }

        if (!empty($_POST['birthday'])) $data['birthday'] = $_POST['birthday'];
        if (!empty($_POST['adress'])) $data['adress2'] = $_POST['adress'];
        if (!empty($_POST['sex'])) $data['sex'] = $_POST['sex'];

        var_dump($data);



        $upload = new \Think\Upload();// 实例化上传类    
        $upload->maxSize   =     3145728 ;// 设置附件上传大小    
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
        $upload->savePath  =      '/UserInfo'.$data['uid'].'/'; // 设置附件上传目录    // 上传文件  
        $upload->autoSub   =  false;   
        $info   =   $upload->uploadOne();    
        if(!$info) {// 上传错误提示错误信息
	    	echo $upload->getError();
        	return $upload->getError();
        }else{// 上传成功        
        	$data['real_img'] = $info['savePath'].$info['savename'];    
        }
        return $data;
	}

    /**
     * 验证修改邮箱
     * @return bool 验证信息结果
     */
    public function checkEmailInfo() 
    {
        if (empty($_POST['yzm']) || empty($_POST['email'])) {
                return '请完整填写信息哟~';
                exit;
            }
            $regex = '/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/';
            if (!preg_match($regex,$_POST['email'])) {
                return '请填写正确的邮箱';
            }

            if ($_POST['yzm'] != $_COOKIE['emailYZM']) {
                return '验证码错误';
                exit;
            }
            if ($this->where('id = '.$_SESSION['userinfo']['id'])->save(['email'=>$_POST['email']])) {
                setcookie("emailYZM", '', -1, '/'); 
                return true;
                exit;
            } else {
                return false;
            }
    }
}