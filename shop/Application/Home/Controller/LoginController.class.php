<?php
namespace Home\Controller;

use Think\Controller;

class LoginController extends Controller 
{

    public $btn; //是否显示验证码
    /**
     * 在登录前判断是否需要显示验证码
     * @Author   ryan
     * @DateTime 2017-11-20
     * @email    931035553@qq.com
     * @return   [type]           [description]
     */
    public function _before_index ()
    {
        //判断是否为同一个ip且登录且错误超过五次
        $this->redis = new \Redis();
        $this->redis -> connect('localhost',6379);
        //获取ip
        $this ->ip = get_client_ip();
        //获取参数  
        $this->count = count($this->redis->keys($this->ip.'.*'));
        //超过两次，添加验证码
        if ($this->count >= 2) {
            $this->btn = '1';
        } else {
            $this->btn = '0';
        }
    }
    /**
     * 登录验证
     * @Author   ryan
     * @DateTime 2017-11-16
     * @email    931035553@qq.con
     */
    public function index ()
    {       
        //判断是否为其他登录模式
        //定义登录方式
        $patterns = ['qq','email'];
        if (in_array_case($action,$patterns)) {
            //接收登录方式
            $login = D('Login');
            $action = I('get.action');
            $res = $login->$action();
            //判断登录是否成功
            if ($res === true)  {
                $this->transportCartData(); //转移购物车数据
                success('登录成功','Index/index');
            }
            else {
                 error($res,$login->url);
            }
            exit;
        }

        //判断是否为普通登录模式
        if (IS_POST) {
            $login = D('Login');
            $res = $login->index();
            //判断登录是否成功
            if ($res === true)  {
                $this->transportCartData();//转移购物车数据
                if (!empty(cookie('OrderUrl'))) {
                    $Url = cookie('OrderUrl');
                    cookie('OrderUrl',null);
                    success_goBack('登录成功',$Url);
                }
                success('登录成功','Index/index');
            }
            else error($res);
            exit;
        }

        //如果都不是则为显示状态
        $this->assign('btn',$this->btn);
        $this->display();
    }   


    /**
     * 创建验证码
     * @Author   ryan
     * @DateTime 2017-11-16
     * @email    931035553@qq.com
     * @return   [type]           [description]
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
     * 登出
     * @Author   ryan
     * @DateTime 2017-11-20
     * @email    931035553@qq.com
     * @return   [type]           [description]
     */
    public function logout()
    {
        session('userinfo',null);
        session('ip',null);
        cookie('PHPSESSID',null);
        $this->redirect('Index/index');
    }


    /**
     * 迁移购物车数据至mysql
     * @Author   ryan
     * @DateTime 2017-11-28
     * @email    931035553@qq.com
     * @return   [type]           [description]
     */
    public function transportCartData()
    {
        // 查看是否有Redis的购物车信息
            if (!empty(cookie('Cart_id'))) {
                $this->redis = new \Redis();
                $this->redis->connect('127.0.0.1', '6379');
                // 不为空就存在
                $key = cookie('Cart_id');
                // 集合key
                $cartIds = 'cart:ids:set:'.$key;
                // 商品Info key
                $Rediskey = 'cart:'.$key.':'.$propid;
                $res = $this->redis->zRange($cartIds, 0, -1, true);
                if (!empty($res)) {
                    // 不为空
                    $Cart = M('cart');
                    $propidList = $this->redis->zRevRange($cartIds, 0, -1);
                    $CartInfo = [];
                    // 遍历查询这个用户的所有购物车商品 $CartInfo[]
                    foreach ($propidList as $k => $propid) {
                        $Rediskey = 'cart:'.$key.':'.$propid;
                        $CartInfo = $this->redis->hGetAll($Rediskey);
                        $CartInfo['uid'] = session('userinfo.id');

                        $map['propid'] = $CartInfo['propid'];
                        $map['uid'] = $CartInfo['uid'];
                        // 查看购物车中是否有这件商品
                        $old_cart = $Cart->where($map)->find();
                        echo $Cart->_sql().'<br>';
                        if ($old_cart) {
                            $oldnum = $old_cart['num'];
                            $new_data['num'] = $CartInfo['num'] + $oldnum;
                            var_dump($new_data);
                            // 如果数量超过库存就改成最大值（库存数）
                            if ($new_data['num'] > $old_cart['store']) {
                                $new_data['num'] = $old_cart['store'];
                            }
                            // 修改购物车中的商品数量
                            $Cart->where($map)->save($new_data);
                            echo $Cart->_sql();
                        } else {
                            // 写入数据库
                            $Cart->add($CartInfo);
                        }
                        // 上面是把Redis中的数据复写到mysql中
                        // 下面是把Redis中的数据抹去
                        $this->redis->del($Rediskey);
                        $this->redis->del($cartIds);
                        $this->redis->del($key.'cartTotal');
                    }
                }
            }
    }
}