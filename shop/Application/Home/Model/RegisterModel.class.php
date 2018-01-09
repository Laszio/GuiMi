<?php
namespace Home\Model;

use Think\Model;

class RegisterModel extends Model
{	
	//指定数据表
	protected $tableName = 'user';
	//自动验证
	protected $_validate = [
	     array('username','/^\w{3,6}$/','用户名为3~6个字母数字下划线',1,'regex',1), //判断用户名是否合法   
	     array('username','','用户名已经存在！',1,'unique',1), //在新增的时候验证name字段是否唯一 
	     array('phone','','手机号已经存在！',1,'unique',1), //在新增的时候验证name字段是否唯一
	     array('ph_verify','require','请输入手机验证码！'), //验证码必须
	     array('em_verify','require','请输入邮箱验证码！'), //验证码必须
	     array('verify','require','请输入验证码！'), //验证码必须
	     array('pass','checkPwd','密码输入不正确',1,'callback',1), // 自定义函数验证密码格式
	     array('ph_verify','checkPhoneVerify','手机验证码不正确',0,'callback',3), // 自定义函数验证密码格式
     	 array('em_verify','checkEmailVerify','邮箱验证码不正确',0,'callback',3), // 自定义函数验证密码格式
	     // array('verify','checkVerify','验证码不正确',1,'callback',3), // 自定义函数验证密码格式
	];
	//自动完成
	protected $_auto = [
		['pass','password_hash',1,'function',[PASSWORD_DEFAULT]],
	];

	/**
	 * 判断密码是否正确
	 * @Author   ryan
	 * @DateTime 2017-11-16
	 * @email    931035553@qq.com
	 * @param    [type]           $pass [description]
	 * @return   [type]                 [description]
	 */
	function checkPwd ()
	{
		$pass = trim(I('post.pass'));
		$pass2 = trim(I('post.pass2'));
		if (empty($pass) || empty($pass2)) {
			return false;
		}

		if ($pass !== $pass2) {
			return false;
		}
	}

	/**
	 * 判断验证码是否正确
	 * @Author   ryan
	 * @DateTime 2017-11-16
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function checkVerify ($parm)
	{
		if (!session('?verified')) return false;

	}

	/**
	 * 手机验证
	 * @Author   ryan
	 * @DateTime 2017-11-17
	 * @email    931035553@qq.com
	 * @param    [type]           $parm [description]
	 * @return   [type]                 [description]
	 */
	public function checkPhoneVerify ($parm)
	{
		$time = time() - session('phone_time');
		if ($time > 60 || !session('?phone_code') || session('phone_code') != $parm || session('phone') !== I('post.phone')) {
			return false;
		} 
	}
	/**
	 * 邮箱验证
	 * @Author   ryan
	 * @DateTime 2017-11-17
	 * @email    931035553@qq.com
	 * @param    [type]           $parm [description]
	 * @return   [type]                 [description]
	 */
	public function checkEmailVerify ($parm)
	{
		$time = time() - session('email_time');

		if ($time > 5*60 || !session('?email_code') || session('email_code') != $parm || session('email') !== I('post.email')){
			return false;
		} 
	}

	/**
	 * 重置密码
	 * @Author   ryan
	 * @DateTime 2017-11-22
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function reset()
	{
	    //判断是否非法请求
	    
        if (empty(I('get.id'))) {
            session('phone',null);
        	return (['redirect','login/logout']);
        }
        //判断密码是否为空
        $pass = trim(I('post.pass'));
        $pass2 = trim(I('post.pass2'));
        if ( !$pass || !$pass2 ) {
        	return (['error','密码不能为空格']);
            exit;
        } 
        //判断密码是否一致
        if (I('post.pass') != I('post.pass2')) {
        	return (['error','两次密码不一致']);
        }
        //查询id,phone是否一致
        $data['id'] = I('post.id');
        if (session('?phone') ) 
        	$data['phone'] = session('phone');
    	else
        	$data['email'] = session('email');

        $arr = $this->field('id')->where($data)->find();

        if (!$arr) {
           session('?phone')?session('phone',null) : session('email',null);
        	return (['redirect','login/logout']);

        }

        $data['pass'] = password_hash(I('post.pass'),PASSWORD_DEFAULT);

        $res = $this->save($data);
        if ($res) {
            session('?phone')?session('phone',null) : session('email',null);
        	return (['success','修改成功']);

        } 

        return (['error','修改失败，请重新修改']);
	}


	/**
	 * 忘记密码使用手机
	 * @Author   ryan
	 * @DateTime 2017-11-22
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function forgetPhone()
	{

        //手机号
        $data['phone'] = I('post.phone');
        //判断手机号是否正确
        $reg = '/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(197))\d{8}$/';
        if ( !preg_match($reg,$data['phone']) ) {
            error('手机号错误');
            exit;
        }

        //判断验证码是否正确
        if ($this->checkPhoneVerify($data['phone'])) {
            error('验证码错误');
            exit;
        }
        //判断数据库是否有该手机号
        $arr = $this->field('id,phone')->where($data)->find();
        if (!$arr) {
            error('手机号未注册');
            exit;
        }
        session('verified',null);
        session('phone',$arr['phone']);
        //重定向
        redirect(U('/Home/Register/reset/id/'.$arr['id']));
        exit;

	}

	/**
	 * 忘记密码使用邮箱
	 * @Author   ryan
	 * @DateTime 2017-11-22
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function forgetEmail()
	{

	     $data['email'] = I('post.email');
	     //判断邮箱是否正确
	    $reg = '/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/';
	    if ( !preg_match($reg,$data['email']) ) {
	        error('手机号错误');
	        exit;
	    }
	   //判断验证码是否正确
	    if ($this->checkEmailVerify($data['email'])) {
	        error('验证码错误');
	        exit;
	    }

	     //判断数据库是否有该邮箱
        $arr = $this->field('id,email')->where($data)->find();
        if (!$arr) {
            error('手机号未注册');
            exit;
        }

        session('verified',null);
        session('email',$arr['email']);
        //重定向
        redirect(U('/Home/Register/reset/id/'.$arr['id']));
        exit;
	}
}