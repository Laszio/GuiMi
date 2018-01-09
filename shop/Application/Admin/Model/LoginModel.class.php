<?php
namespace Admin\Model;
use Think\Model;

class LoginModel extends Model
{
	protected $_validate = [
		array('username','require','用户名不能为空！'), //在新增的时候验证name字段是否为空
		array('password','require','密码不能为空！'), //在新增的时候验证name字段是否为空
	];
	protected $_auto = [
		['password','password_hash',1,'function',[PASSWORD_DEFAULT]],
	];
	
}