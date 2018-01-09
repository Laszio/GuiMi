<?php
namespace Home\Controller;
use Think\Controller;

class UserController extends CommonController 
{
    /**
     * 个人中心用户首页
     */
    public function index()
    {
        $user = M('User');
        $detail = M('UserDetail');
        $orderModel = M('Order');
        $userInfo = $user->field('id,username,phone')->where('id='.$_SESSION['userinfo']['id'])->find();
        $waitOrder = $orderModel->where('uid = '.$_SESSION['userinfo']['id'].' and status = 1')->count('id');
        $waitSendOrder = $orderModel->where('uid = '.$_SESSION['userinfo']['id'].' and status = 2')->count('id');
        $waitGetOrder = $orderModel->where('uid = '.$_SESSION['userinfo']['id'].' and status = 3')->count('id');
        $waitEvaluteOrder = $orderModel->where('uid = '.$_SESSION['userinfo']['id'].' and status = 4')->count('id');
        $detailInfo = $detail->field('id,user_img')->where('uid='.$_SESSION['userinfo']['id'])->find();
        $this->assign('waitOrder', $waitOrder);
        $this->assign('waitSendOrder', $waitSendOrder);
        $this->assign('waitGetOrder', $waitGetOrder);
        $this->assign('waitEvaluteOrder', $waitEvaluteOrder);
        $this->assign('detailInfo', $detailInfo);
        $this->assign('userInfo', $userInfo);
        $this->display();
    }

    /**
     * 展示用户信息页面
     */
    public function userInfo() 
    {
        $user = M('User');
        $detail = M('UserDetail');
        $userInfo = $user->field('id,email,username,phone')->where('id='.$_SESSION['userinfo']['id'])->find();
        $detailInfo = $detail->field('id,birthday,sex,address,address2,user_img')->where('uid='.$_SESSION['userinfo']['id'])->find();

        $this->assign('detailInfo', $detailInfo);
        $this->assign('userInfo', $userInfo);
        $this->display();
    }

    /**
     * 修改邮箱
     */
    public function editEmail() 
    {
        if (IS_POST) {
            $userModel = D('User');
            $res = $userModel->checkEmailInfo();    
            if ($res === true) {
                $this->redirect('User/userInfo');
            } else {
                $this->error($res);
            }
            
            exit;
        }
        $this->display();
    }

    /**
     * 做前台用户个人信息修改以及展示页面
     * @return [type] [description]
     */
    public function editInfo($id) 
    {
        $detail = D('UserDetail');
        if (IS_POST) {

            $key =  $detail->where('uid='.$_POST['uid'])->find(); //查看数据库有没有该用户信息

            if (!$detail->create()){     // 如果创建失败 表示验证没有通过 输出错误提示信息
                error($detail->getError());
                exit;
            }
            $data = $detail->addInfo();
            if ($key) { //有该用户信息， 进行修改
                if ($detail->where('uid='.$_POST['uid'])->save($data)) {
                    $this->success('修改成功', U('User/index'));
                } else {
                    $this->success('没有任何修改或修改失败');
                }
            } else { //没有该用户信息， 进行添加

                if ($detail->add($data)) {
                    $this->success('添加成功', U('User/index'));
                } else {
                    $this->success('添加失败');
                }
            }
            exit;
        }

        $areas = M('Areas');
        $user = M('User');
        $userInfo = $user->field('id,username,phone')->where('id='.$_SESSION['userinfo']['id'])->find();
        $list = $areas->where("`parent_id` = 1")->select(); 
        $info = $detail->field('id,uid,user_img,sex,birthday,address2,real_name')->where('uid='.$_GET['id'])->find(); 
        if ($info) {
            $this->assign('info', $info);
        }
        $this->assign('list', $list);
        $this->assign('userInfo', $userInfo);
        $this->display();
    }

    /**
     * 展示安全信息页面
     */
    public function safe() 
    {
        // $sj
        $userModel = M('User');
        $userEmail = $userModel->field('email')->where('id = '.$_SESSION['userinfo']['id'])->find();
        $userEmail = $userEmail['email'];
        $this->assign('userEmail',$userEmail);
        $this->display();
    }


    /**
     * 展示通过原密码修改密码页面 以及验证原密码
     */
    public function checkPwd() 
    {
        if (IS_POST) {
            if (empty($_POST['oldPwd']) && empty($_POST['code'])) {
                $this->error('请填写信息~');
            } else {
                $verify = new \Think\Verify();
                if ($verify->check($_POST['code'])) {
                    $user = M('User');
                    $pwd = $user->field('pass')->where('id='.$_SESSION['userinfo']['id'])->find(); 
                    if (password_verify($_POST['oldPwd'], $pwd['pass'])) {
                        $this->redirect('User/editPwd');
                        exit;
                    }
                    $this->error('密码错误~');
                } else {
                    $this->error('验证码错误~');
                }
            }
            exit;
        }
        $this->display();
    }

    /**
     * 展示修改密码
     */
    public function editPwd() 
    {
        if (IS_POST) {
            if (empty($_POST['pass']) && empty($_POST['pass2'])) {
                $this->error('信息请填写完整哦~');
                exit;
            } 
            if ($_POST['pass'] !== $_POST['pass2']) {
                $this->error('密码不一致哟~');
                exit;
            }
            $data['pass'] =password_hash($_POST['pass'], PASSWORD_DEFAULT);
            $user = M('User');
            if ($user->where('id='.$_SESSION['userinfo']['id'])->save($data)) {
                $this->redirect('User/finish');
                exit;
            }
            $this->error('修改失败~');
            exit;
        }
        $this->display();
    }

    /**
     * 展示发送邮箱页面
     */
    public function checkEmail() 
    {
        if (IS_POST) {
            if (!I("post.yzm")) {
                $this->error('数据异常， 请重新获取验证码~');
                exit;
            }
            if (empty($_COOKIE['emailYZM'])) {
                $this->error('验证码失效,请重新获取验证码~');
                exit;
            }
            $yzm = $_COOKIE['emailYZM'];
            if ($yzm != $_POST['yzm']) {
                $this->error('验证码错误，请重新提交哦~');
                exit;
            }
            $this->redirect('User/editPwd');
            var_dump($_POST);
            exit;
        }
        $user = M('User');
        $info = $user->field('email')->where('id='.$_SESSION['userinfo']['id'])->find();  
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 展示密码修改成功页面
     */
    public function finish() 
    {
        // 注销当前用户存在的缓存信息，让其重新登录
        $this->display();
    }

    /**
     * ajax三级联动获取地址
     * @return array 地址信息
     */
    public function getArea() 
    {
        // echo $_GET['id'];
        $id = $_GET['id'] + 0;
        $areas = M('Areas');

        $list = $areas->where("`parent_id` = {$id}")->select();
        echo json_encode($list);
    }

    /**
     * 验证码
     */
    public function yzm() 
    {
        $config =    array(
            'fontSize'    =>    16,    // 验证码字体大小    
            'length'      =>    4,     // 验证码位数    
            'useNoise'    =>    false, // 关闭验证码杂点
        );
        $Verify =     new \Think\Verify($config);
        $Verify->entry();
    }

    /**
     * 发送邮箱
     */
    public function sendEmail() 
    {
        vendor('PHPMailer.class', '', '.pop3.php');

        vendor('PHPMailer.classphpmailer');

        $str = 'LZZ'.rand(0000,9999);
        $res = sendMail($_GET['email'], '重要信息,请及时查看', '请在五分钟内输入验证码信息，您的验证码是：'.$str);

        $b = substr($res,-7);
        $a = substr($res,0,5);
        if ($a == 'FALSE') {
            $this->ajaxReturn('1'); 
            exit; 
        } else {
            setcookie("emailYZM", $b, time()+300, '/'); 
            $this->ajaxReturn('ok'); 
        }

    }

    /* kero */
    /**
     * 个人收藏页面
     * @return [type] [description]
     */
    public function favorite()
    {
        $fav = M('favorite');
        $uid = session('userinfo.id');
        // 查询收藏商品的id
        $fav_id = $fav->where("uid='%d'",$uid)->order('addtime desc')->getField('addtime,gid',true);
        $Ids = join(',',$fav_id);
        // 查表
        $goods = M('goods');
        $goodsList = $goods->where(['id'=>['in', $Ids]])->field("gname,id,price,status,delsta,discount,pic1")->select();
        $this->assign('goodsList',$goodsList);
        $this->display();       
    }

    /**
     * 取消收藏
     * @param  int $gid 收藏商品的id
     */
    public function delFav($gid)
    {
        $uid = session('userinfo.id');
        $fav = M('favorite');
        $map['uid'] = $uid;
        $map['gid'] = $gid;
        // $res = $fav->where($map)->delete();
        $res = 1;
        if ($res) {
            $data['status'] = 1;
            $data['msg'] = '删除成功';
        } else {
            $data['status'] = 2;
            $data['msg'] = '数据异常';
        }
        $this->ajaxReturn($data);
    }

    /**
     * ajax完成修改密码注销用户
     * @return [type] [description]
     */
    public function userLogout() 
    {
        session('userinfo',null);
        session('ip',null);;
        $this->ajaxReturn(1);
    }
}