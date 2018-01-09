<?php
namespace Home\Controller;

use Think\Controller;

class RegisterController extends Controller 
{
    /**
     * 登录验证
     * @Author   ryan
     * @DateTime 2017-11-16
     * @email    931035553@qq.con
     */
    public function index ()
    {
        if (IS_POST) {
           
            $register = D('Register');
            $res = $register->create();
            session('verified',null);
            if ($res) {
                $res = $register->add($res);
                $detail = M('user_detail');
                $detailInfo['uid'] = $res;
                $detailInfo['user_img'] = '/user/no-img.jpg';
                $detail->add($detailInfo);
                success('注册成功','Login/index');
                exit;
            }
            error($register->getError());
            exit;
        }
        
    	$this->display();
    }	

    /**
     * 创建验证码
     * @Author   ryan
     * @DateTime 2017-11-16
     * @email    931035553@qq.com
     *  
     */
    public function createVerify ()
    {
        $Verify = new \Think\Verify();
        //ajax验证码验证
        if (IS_AJAX) {
            $parm = I('post.parm');
            $res = $Verify->check($parm);
            if ($res) {
                session('verified',true);
                $this->ajaxReturn(['status'=>true,'msg'=>'ok']);
            }
            
            $this->ajaxReturn(['status'=>false,'msg'=>'验证码错误']);
        }
        
        //生成验证码
        $Verify->fontSize = 32;
        $Verify->length   = 4;
        $Verify->useNoise = false;
        $Verify->expire  = 60;
        $Verify->entry();

    }
    

    /**
     * 发送短信信息
     * @Author   ryan
     * @DateTime 2017-11-16
     * @email    931035553@qq.com
     */
    public function sendPhoneVerify()
    {
        //判断是否符合正则
        $data['phone'] = I('post.phone');
        $regex = '/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(197))\d{8}$/';
        if (!preg_match($regex,$data['phone'])) {
            $this->error('请填写正确的手机号');
        }
        //判断手机号是否已注册
        $user = M('user');
        $arr = $user->where($data)->find();

        if ( $arr && I('post.action') != 'forgot') {
                $this->error('该手机号已注册');
                exit;
            
        } else if (!$arr && I('post.action') == 'forgot') {
             $this->error('该手机号未注册');
            exit;
        }
        
        //判断是否已经点过
        if (session('?phone_time')) $time = session('phone_time');
        if ( time() - $time < 60 && session('?phone_code') ) {
            $this->error('你已经发送过了!再勿在六十秒重复发送');
            exit;
        } 


        //引用用短信控制器的代码
        
        $sms = A('Sms');
        $res = $sms->send_phone(I('post.phone'));
        if ($res) {
            $this->success('发送成功，请及时查收');
            exit;
        } else {
            $this->error('发送失败');
            exit;
        }

    }


    /**
     * 发送邮箱信息
     * @Author   ryan
     * @DateTime 2017-11-16
     * @email    931035553@qq.com
     */
    public function sendEmailVerify ()
    {

        //判断邮箱是否符合正则
        $data['email'] = I('post.email');
        $regex = '/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/';
        if (!preg_match($regex,$data['email'])) {
            $this->error('请填写正确的邮箱');
        }
        //判断手机号是否已注册
        $user = M('user');
        $arr = $user->where($data)->find();

        if ( $arr && I('post.action') != 'forgot') {
                $this->error('该邮箱已注册');
                exit;
            
        } else if (!$arr && I('post.action') == 'forgot') {
             $this->error('该邮箱未注册');
            exit;
        }
        
        //判断是否已经点过
        if (session('?email_time')) $time = session('email_time');
        if ( time() - $time < 60 && session('?email_code') ) {
            $this->error('已发送，请及时查收');
            exit;
        } 
        //生成随机的验证码
        vendor('PHPMailer.class', '', '.pop3.php');
        vendor('PHPMailer.classphpmailer');
        $str = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789'),4,4);
        $res = sendMail($data['email'], '找回密码', '请在五分钟内输入验证码信息，您的验证码是：'.$str);

        if ( !$res ) {
            $this->error('发送失败');
            exit;
        } 
        //设置时间
        session('email_time',time());
        session('email_code',$str);
        session('email',$data['email']);
        $this->success('发送成功，请及时查收');

    }


    /**
     * 验证用户名是否唯一（ajax）
     * @Author   ryan
     * @DateTime 2017-11-18
     * @email    931035553@qq.com
     * @return   [type]           [description]
     */
    public function checkUsername ()
    {
        //判断手机号是否已注册
        $user = M('user');

        $data['username'] = I('post.username');
        if ( $user->where($data)->find() ) {
            $this->error('该用户名已注册');
            exit;
        } 

        $this->success('ok');
    }


    /**
     * 验证手机验证码
     * @Author   ryan
     * @DateTime 2017-11-17
     * @email    931035553@qq.com
     * @return   [type]           [description]
     */
    public function checkPhoneVerify()
    {
        $parm = I('post.parm');
        $time = time() - session('phone_time');
        if ($time > 60 ) {
            $res = ['status'=>false,'msg'=>'您已超时'];
            $this->ajaxReturn($res);
            exit;
        } else if ( session('?phone_code') && session('phone_code') == $parm ){

            $this->ajaxReturn(['status'=>true,'msg'=>'ok']);
            exit;

        }
        $res = ['status'=>false,'msg'=>'验证码错误'];
        $this->ajaxReturn($res);
    }

    /**
     * 验证邮箱验证码
     * @Author   ryan
     * @DateTime 2017-11-17
     * @email    931035553@qq.com
     * @return   [type]           [description]
     */
    public function checkEmailVerify()
    {
        $parm = I('post.parm');
        $time = time() - session('email_time');
        if ($time > 5*60 ) {
            $res = ['status'=>false,'msg'=>'您已超时'];
            $this->ajaxReturn($res);
            exit;
        } else if ( session('?email_code') && session('email_code') == $parm ){

            $this->ajaxReturn(['status'=>true,'msg'=>'ok']);
            exit;

        }
        $res = ['status'=>false,'msg'=>'验证码错误'];
        $this->ajaxReturn($res);
    }

    /**
     * 重置密码
     * @Author   ryan
     * @DateTime 2017-11-18
     * @email    931035553@qq.com
     * 
     */
    public function forgot ()
    {
        // echo 111;
        if (IS_POST) {
           
            $register = D('Register');
            if (I('post.phone')) {
                //手机
                $register->forgetPhone();

            } else if (I('post.email')) {

                //邮箱
                 $register->forgetEmail();

            } else {
                //其他
                error('请填写邮箱或手机号');
                exit;
            }

        }

        $this->display();

    }

    public function reset () 
    {   
        //判断是否有有该属性
        if (!session('?phone') && !session('?email')) {
            $this->redirect('login/logout');
            session('phone',null);
            exit;
        } 
        //提交表单
        if (IS_POST) {
            
            $Register = D('Register');
            $arr = $Register->reset();
            $ope = $arr[0];
            if ($ope == 'success') {
                $ope($arr[1],'Login/index');
            } else {
                $ope($arr[1]);
            }
            exit;
        }
        $this->assign(I('get.'));
        $this->display();
    }
}