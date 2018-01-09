<?php
namespace Home\Model;

use Think\Model;

class LoginModel extends Model
{
	private $ip;
	private $redis;
	private $count;
	private $res;
	//指定数据表
	protected $tableName = 'user';
	/**
	 * 用于公共判断
	 * @Author   ryan
	 * @DateTime 2017-11-16
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function _initialize () 
	{
		//判断是否为同一个ip且登录且错误超过五次
		$this->redis = new \Redis();
		$this->redis -> connect('localhost',6379);
		//获取ip
		$this ->ip = get_client_ip();
		//获取参数	
		$this->count = count($this->redis->keys($this->ip.'.*'));
	     //判断验证码是否正确
        if ($this->count >= 2 && session('verified') !== true && $this->count < 5) {
            $this->res = true;
            error('验证码错误');
        	exit;
        }
		//超过五次，直接返回
		if ($this ->count >= 5 ) {
			error('您已被限制登录');
			exit;
		}
			
	}

	/**
	 * 结束写入缓存
	 * @Author   ryan
	 * @DateTime 2017-11-18
	 * @email    931035553@qq.com
	 */
	public function __destruct()	
	{
		//如果错误，则直接往redis内写入数据
		if ($this->res !== true && $this->count < 5) {
			//client_id自增
			$index = $this->redis->incr('client_id');
			//拼接$id
			$id = $this->ip.'.'.$index;
			//添加次数
			$this->redis -> setex($id,1800,'1');
		}
	}

	/**
	 * 登录成功设置
	 * @Author   ryan
	 * @DateTime 2017-11-20
	 * @email    931035553@qq.com
	 */
	public function setSession($pass)
	{

		//若点击自动登录则5天内可自动登录
		
		if (I('?post.auto') && I('post.auto') == 1) 
			{
				session(['name'=>'session_id','expire'=>3600*24*5]);
				cookie('PHPSESSID',session_id(),3600 * 24 * 5);
			}
			

			$map['uid'] = $pass['id'];
			$userdetail = M('user_detail');
			$detail = $userdetail->field('user_img')->where($map)->find();
			$pass = array_merge($pass,$detail);
			session('userinfo',$pass);
			session('ip',get_client_ip());
	}

	/**
	 * 用于普通登录
	 * @Author   ryan
	 * @DateTime 2017-11-16
	 * @email    931035553@qq.com
	 * 
	 */
    public function index ()
    {
 		
    	//判断是否为手机登录  	
    	if (strlen(I('post.character')) == 11 && is_numeric(I('post.character'))) {
    		$data['phone'] = I('post.character');
    		// $data['pass'] = I('post.pass');
    		$pass = $this->where($data)->find();
    		//判断用户名密码是否正确
    		if (!empty($pass['pass']) && password_verify(I('post.pass'),$pass['pass'])) {
    			
    			$this->setSession($pass);

    			return $this->res = true;
    		} 
    		return $this->res = '手机号或密码错误';
    	}

  		//判断是否为邮箱登录
  		$regex = '/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/';
        if ( preg_match($regex,I('post.character')) ) {

           $data['email'] = I('post.character');

           $pass = $this->where($data)->find();

           if (!empty($pass['pass']) && password_verify(I('post.pass'),$pass['pass'])) {
				//设置session
				
				$this->setSession($pass);

				return $this->res = true;
			}

			return $this->res = '邮箱或密码错误';
        }
    	//用户名登录
    	$data['username'] = I('post.character');

		$pass = $this->where($data)->find();
		//判断用户名密码是否正确
		if (!empty($pass['pass']) && password_verify(I('post.pass'),$pass['pass'])) {
			//设置session
			$this->setSession($pass);

			return $this->res = true;
		}
		return $this->res = '用户名或密码错误';
    }	

    /**
     * qq登录
     */
    public function qq ()
    {

    }


    /**
     * 
     */
}