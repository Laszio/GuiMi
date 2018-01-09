<?php
namespace Admin\Model;
use Think\Model;

class AdminModel extends Model
{
		//指定数据表
	protected $tableName = 'admin';
	//自动验证
	protected $_validate = [
		 array('username','require','用户名不能为空！',1,'',3), //验证name字段是否为空
	     array('username','','用户名已经存在！',1,'unique',3), //验证name字段是否唯一 
	     array('password','checkPwd','两次密码输入不一致',2,'callback',3), // 自定义函数验证密码格式
	     array('phone','','手机号已经存在！',1,'unique',1), //在新增的时候验证name字段是否唯一
	     array('phone','/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(197))\d{8}$/','请输入正确的电话号码',1,'regex',3), // 当值不为空的时候判断是否在一个范围内  
	     array('phone','require','手机号不能为空',1,'unique',1),//手机必须写
	     array('Email','require','请输入验证码！'),//邮箱也一定要写
	     array('Email', '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/', '邮箱不合法！', 1, '', 3),
	];//邮箱合法验证

	//自动完成
	protected $_auto = [
		array('password','',2,'ignore'),
		['password','password_hash',3,'function',[PASSWORD_DEFAULT]],
		array('password',null,2,'ignore'),
	];

	/**
	 * 判断密码是否正确
	 * @Author   ryan
	 * @DateTime 2017-11-16
	 * @email    931035553@qq.com
	 * @param    [type]           $password [description]
	 * @return   [type]                 [description]
	 */
	function checkPwd ()
	{
		$password = trim(I('post.password'));
		$password2 = trim(I('post.password2'));
		// 2个密码是否为空
		if (empty($password) || empty($password2)) {
			return false;
		}
		// 验证两次密码是否一致
		if ($password !== $password2) {
			return false;
		}
	}

	/**
	 * 处理管理员列表的数据
	 */
	public function doAdminIndex()
	{
		$res = $this->select();
		// 性别
		$sex = [1=>'男', '女', '保密'];
		// 管理员状态 ：
		$status = ['1' => '已启用', '已禁用','?','?'];
		// 角色
		$role = ['1' => 'God'];
		// 遍历改每一个用户
		foreach ($res as $k=>$v) {
			$res[$k]['sex'] = $sex[$v['sex']];
			// 用['status-msg']来角色新数据 ，status用来比较
			$res[$k]['status-msg'] = $status[$v['status']];
			$res[$k]['role'] = $role[$v['sex']];
			// 登录时间
			if (empty($v['logtime'])) {
				$res[$k]['logtime'] = '暂无登录记录';
				$res[$k]['logip'] = '暂无登录记录';
			} else { 
				$res[$k]['logip'] = $v['logip'];
				$res[$k]['logtime'] = date('Y-m-d H:i',$v['logtime']);
			}
		}
		return $res;
	}

	
}
