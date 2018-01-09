<?php
namespace Home\Controller;
use Think\Controller;

class CartController extends PublicController
{
	/**
	 * 购物车的页面
	 */
    public function index(){
        // 判断是否登录
        if (empty(session('userinfo'))) {
            // 未登录
            $this->redis = new \Redis();
            $this->redis->connect('127.0.0.1', '6379');
            if(empty(cookie('Cart_id'))) {
                // 没有唯一表示一定找不到对应的购物车所以为空
                $this->display('Cart/cart-empty');
                exit;
            }
            $key = cookie('Cart_id');
            $cartIds = 'cart:ids:set:'.$key;
            $res = $this->redis->exists($cartIds);
            if (!$res) {
                // 购物车不存在 为空
                $this->display('Cart/cart-empty');
                exit;
            } else {
                // 存在获取所有propid
                $propidList = $this->redis->zRevRange($cartIds, 0, -1);
                $CartInfo = [];
                // 遍历查询这个用户的所有购物车商品 $CartInfo[]
                foreach ($propidList as $k => $propid) {
                    // 凭借购物车商品的key
                    $Rediskey = 'cart:'.$key.':'.$propid;
                    // 循环存储在数组中
                    $CartInfo[] = $this->redis->hGetAll($Rediskey);
                }
            }
        } else {
            // 已登录 获取uid
            $uid = session('userinfo.id');
            // 查询购物车表中所有uid的数据
            $cart = M('cart');
            $CartInfo = $cart->where("uid='%d'",$uid)->select();
            if(empty($CartInfo)) {
                // 没有唯一表示一定找不到对应的购物车所以为空
                $this->display('Cart/cart-empty');
                exit;
            }

        }
        $this->assign('CartInfo',$CartInfo);
        $this->display();
    	
    }

    /**
     * 添加商品到购物车
     * @param int  $gid    商品id
     * @param int  $propid 套餐id
     * @param string  $size   套餐属性
     * @param string  $color  套餐属性
     * @param integer $num    加入数量
     * @param string  $gname  $gname
     * @param int  $price  单价
     */
    public function addCart($gid,$propid,$size,$color,$num = 1,$gname,$price)
    {
        // 数据判断
        if ($num == 0) {
            $this->ajaxReturn('请选择商品数量');
            exit;
        }
        // 先对数据做检测
        $Goods = M('goods');
        $goodsInfo = $Goods->where("id='%d'",$gid)->find();
        // 判断商品状态是否下架
        if (($goodsInfo['status'] == 3) || ($goodsInfo['delsta'] == 2) ) {
            $this->ajaxReturn('加入失败，商品已下架');
            exit;
        }
        // 查询详情表
        $goods_prop = M('goods_property');
        $map['gid'] = $gid;
        $map['color'] = $color;
        $map['size'] = $size;
        $GoodsDetails = $goods_prop->where($map)->find();
        // 找不到这个商品
        if (!$GoodsDetails) {
            $this->ajaxReturn('加入失败，库存不足');
            exit;
        }
        // 查询gid是否和详情中的gid对应
        if ($GoodsDetails['gid'] < $gid) {
            $this->ajaxReturn('非法操作');
            exit;
        }
        // 判断商品库存是否充足
        if ($GoodsDetails['store'] < $num || $GoodsDetails['store'] == 0) {
            $this->ajaxReturn('加入失败，库存不足');
            exit;
        }
        
        $cartInfo['gid'] = $gid;//gid
        $cartInfo['propid'] = $propid;//商品详情id - 唯一标识
        $cartInfo['gname'] = $goodsInfo['gname'];//商品名
        $cartInfo['price'] = $goodsInfo['price'];//商品售价
        $cartInfo['status'] = $goodsInfo['status'];//商品状态
        $cartInfo['delsta'] = $goodsInfo['delsta'];//是否已经删除
        $cartInfo['discount'] = $goodsInfo['discount'];//商品折扣
        $cartInfo['color'] = $GoodsDetails['color'];//商品颜色
        $cartInfo['colorpic'] = $GoodsDetails['colorpic'];//对应的图片
        $cartInfo['size'] = $GoodsDetails['size'];//商品大小
        $cartInfo['store'] = $GoodsDetails['store'];//商品库存
        $cartInfo['num'] = $num;//添加的数量

    	// 判断是否登录 未登录就把Redis的唯一外键Key写入cookie保存7天 登录就写入数据库
        if(empty(session('userinfo'))) 
        {
            // 未登录
            $this->redis = new \Redis();
            $this->redis->connect('127.0.0.1', '6379');
            // 判断是否有这个id，没有就创建，有就刷新存活时间
            if(empty(cookie('Cart_id')))
            {
                cookie('Cart_id',session_id(),3600 * 24 * 7);
                $key = cookie('Cart_id');
                // 记录该用户购物车中的总件数
                $this->redis->set($key.'cartTotal',0);
             } else {
                $key = cookie('Cart_id');
                cookie('Cart_id',$key,3600 * 24 * 7);
            }

            // Redis 哈希的外键-唯一表示
            $Rediskey = 'cart:'.$key.':'.$propid;
            // 集合存放这个用户购物车的所有id
            $cartIds = 'cart:ids:set:'.$key;
            // 查询这个用户的购物车中是否有这个商品
            $data = $this->redis->exists($Rediskey);
            if(!$data)
            {
                //购物车之前没有对应的商品 就加入
                $this->redis->hmset($Rediskey,$cartInfo); 
                //将商品ID存放集合中,是为了更好将用户的购物车的商品给遍历出来
                $this->redis->zAdd($cartIds, time(), $propid);
            } else {
                 //购物车有对应的商品，只需要添加对应商品的数量
                $originNum = $this->redis->hget($Rediskey, 'num');
                //原来的数量加上用户新加入的数量
                $newNum = $originNum + $num;
                // 这个用户的购物车中数量超过了库存的情况
                if ($newNum > $GoodsDetails['store']) {
                    // 把数量设置成最大值（库存）
                    $this->redis->hset($Rediskey, 'num', $GoodsDetails['store']);
                    $this->ajaxReturn('超过了商品上限,最多可以购买'.$GoodsDetails['store'].'件');
                    exit;
                }
                // 重新设置info中的num
                $this->redis->hset($Rediskey, 'num', $newNum);
            }
            // 该用户购物车中的总件数
            $oldSum = $this->redis->get($key.'cartTotal');
            //原来的数量加上用户新加入的数量
            $newSum = $oldSum + $num;
            $this->redis->set($key.'cartTotal', $newSum);
            // 更新集合中的排序
            $this->redis->zAdd($cartIds, time(), $propid);
            $this->ajaxReturn('加入成功');
        } else {
            // 获取uid
            $uid = session('userinfo.id');
            // 查询购物车表中所有uid的数据
            $cart = M('cart');
            $cartInfo['uid'] = $uid;
            $map['uid'] = $uid;
            $map['propid'] = $propid;
            $check = $cart->where($map)->find();
            // 判断该用户购物车中是否已有该商品
            if ($check) {
                $oldnum = $check['num'];
                $newnum = $oldnum + $num;
                // 总数超过库存的情况
                if ($newnum > $check['store']) {
                    $newnum = $check['store'];
                }
                $save['num'] = $newnum;
                $res = $cart->where($map)->save($save);
            } else {
                // 没有就写入
                $save['num'] = $cartInfo['num'];
                $res = $cart = $cart->add($cartInfo);   
            }
            if ($res) {
                $this->ajaxReturn('加入成功');
            }
        }
    }

    /**
     * ajax加减购物车
     * @param  str  $make   '+'or'-
     * @param  int $propid 对应的商品详情id
     */
    public function cartChaNum($make,$propid = 0,$num = 0)
        {
            $this->redis = new \Redis();
            $this->redis->connect('127.0.0.1', '6379');
            if ($make != '-' && $make != '+' && $make != 'blur') {
                $ajax['status'] = 2;
                $ajax['msg'] = '系统错误，请稍后再试';
                $this->ajaxReturn($ajax);
                exit;    
            }
            // 判断是否登陆
            if (empty(session('userinfo'))) {
                // 未登录
                // 唯一key
                $key = cookie('Cart_id');
                // 集合key
                $cartIds = 'cart:ids:set:'.$key;
                // 商品Info key
                $Rediskey = 'cart:'.$key.':'.$propid;
                // 商品数目 key
                $oldSum = $this->redis->get($key.'cartTotal');
                // 看购物车中有没有这个info
                $data = $this->redis->exists($Rediskey);
                if (!$data) {
                    $ajax['status'] = 2;
                    $ajax['msg'] = '系统错误，请稍后再试';
                    $this->ajaxReturn($ajax); 
                    exit;
                }
                // 获取Redis中对应商品购物车的信息
                $info = $this->redis->hGetAll($Rediskey);
                switch ($make) {
                    case '+':
                        // 修改购物车Info中的Num
                        $info['num'] = $info['num'] + 1;
                        if ($info['num'] > $info['store']) {
                            $ajax['status'] = 2;
                            $ajax['msg'] = '超出库存，该商品库存为：'.$info['store'];
                            $this->ajaxReturn($ajax); 
                            exit;
                        }
                        $res = $this->redis->hmset($Rediskey,$info);
                        // 重新设置总件数
                        $newSum = $oldSum + 1;
                        $this->redis->set($key.'cartTotal', $newSum);
                        $ajax['status'] = 1;
                        $ajax['msg'] = $res;
                        $this->ajaxReturn($ajax);
                        break;
                    
                    case '-':
                        // 修改购物车Info中的Num
                        $info['num'] = $info['num'] - 1;
                        if ($info['num'] == '0') {
                            $ajax['status'] = 2;
                            $ajax['msg'] = '数量不能少于0';
                            $this->ajaxReturn($ajax);
                            exit;
                        }
                        $this->redis->hmset($Rediskey,$info);
                        // 重新设置总件数
                        $newSum = $oldSum - 1;
                        $res = $this->redis->set($key.'cartTotal', $newSum);
                        $ajax['status'] = 1;
                        $ajax['msg'] = $res;
                        $this->ajaxReturn($ajax);
                        break;
                    case 'blur':
                        $oldInfonum = $info['num'];
                        $info['num'] = $num;
                        if ($info['num'] == '0') {
                            $ajax['status'] = 2;
                            $ajax['msg'] = '数量不能少于0';
                            $this->ajaxReturn($ajax);
                            exit;
                        }
                        if ($info['num'] > $info['store']) {
                            $ajax['status'] = 2;
                            $ajax['msg'] = '超出库存，该商品库存为：'.$info['store'];
                            $this->ajaxReturn($ajax); 
                            exit;
                        }
                        $res = $this->redis->hmset($Rediskey,$info);
                        // 重新设置总件数
                        $newSum = $oldSum - $oldInfonum + $num;
                        $this->redis->set($key.'cartTotal', $newSum);
                        $ajax['status'] = 1;
                        $ajax['msg'] = $res;
                        $ajax['top_total'] = $newSum;
                        $this->ajaxReturn($ajax);
                        break;
                    default:
                        break;
                }
            } else {
                // 已登录 
                $uid = session('userinfo.id');
                $map['uid'] = $uid;
                $map['propid'] = $propid;
                $cart = M('cart');
                // 查询购物车
                $cart_res = $cart->where($map)->find();
                // 获取购物车表中的数量
                $oldnum = $cart_res['num'];
                $store = $cart_res['store'];
                if (!($cart_res)) {
                    $ajax['status'] = 2;
                    $ajax['msg'] = '非法操作';
                    $this->ajaxReturn($ajax);
                    exit;
                }
                switch ($make) {
                    case '+':
                        $save['num'] = $oldnum + 1;
                        if ($save['num'] > $store) {
                            $ajax['status'] = 2;
                            $ajax['msg'] = '库存不足，该商品库存仅为：'.$store;
                            $this->ajaxReturn($ajax);
                            exit;
                        }
                        $res = $cart->where($map)->save($save);
                        $ajax['status'] = 1;
                        $ajax['msg'] = $res;
                        $this->ajaxReturn($ajax);
                        break;
                    
                    case '-':
                        $save['num'] = $oldnum - 1;
                        if ($save['num'] < 1) {
                            $ajax['status'] = 3;
                            $ajax['msg'] = '数量不能少于0';
                            $this->ajaxReturn($ajax);
                            exit;
                        }
                        $res = $cart->where($map)->save($save);
                        $ajax['status'] = 1;
                        $ajax['msg'] = $res;
                        $this->ajaxReturn($ajax);
                        break;
                    case 'blur':
                        $save['num'] = $num;
                        if ($save['num'] == '0') {
                            $ajax['status'] = 2;
                            $ajax['msg'] = '数量不能少于0';
                            $this->ajaxReturn($ajax);
                            exit;
                        }
                        if ($save['num'] > $store) {
                            $ajax['status'] = 2;
                            $ajax['msg'] = '超出库存，该商品库存为：'.$store.'件';
                            $this->ajaxReturn($ajax); 
                            exit;
                        }
                        $res = $cart->where($map)->save($save);
                        $ajax['status'] = 1;
                        $ajax['msg'] = $res;;
                        $newSum = $cart->where("uid='%d'",$uid)->Sum("num");
                        $ajax['top_total'] = $newSum;
                        $this->ajaxReturn($ajax);
                        break;
                    default:
                        break;
                }
            }
        }

        /**
         * ajax删除购物车
         * @param  int $propid 商品详情id
         */
        public function cartDel($propid)
        {
            $this->redis = new \Redis();
            $this->redis->connect('127.0.0.1', '6379');
            if (empty(session('userinfo'))) {
                // 未登录
                // 唯一key
                $key = cookie('Cart_id');
                // 集合key
                $cartIds = 'cart:ids:set:'.$key;
                // 商品Info key
                $Rediskey = 'cart:'.$key.':'.$propid;
                // 购物车total数
                $oldSum = $this->redis->get($key.'cartTotal');
                $info = $this->redis->hGetAll($Rediskey);
                // 获取这个购物车的件数
                $num = $info['num'];
                // 删除这个购物车商品
                var_dump($Rediskey);
                $res = $this->redis->del($Rediskey);
                // 如果删除失败就提示
                if (!$res) {
                    $data['status'] = 2;
                    $data['msg'] = '数据异常';
                    $this->ajaxReturn($data);
                }
                // 处理总数-减少总件数
                $newSum = $oldSum - $num;
                $res = $this->redis->set($key.'cartTotal', $newSum);
                // 处理集合中的id
                $res2 = $this->redis->zDelete($cartIds,$propid);
                $data['status'] = 1;
                $data['msg'] = 'ok';
                $this->ajaxReturn($data);
            } else {
                // 登陆后
                $uid = session('userinfo.id');
                $map['uid'] = $uid;
                $map['propid'] = $propid;
                $cart = M('cart');
                // 删除对应个购物车
                $res = $cart->where($map)->delete();
                if ($res) {
                    $data['status'] = 1;
                    $this->ajaxReturn($data);
                    exit;
                }
                $data['status'] = 2;
                $this->ajaxReturn($data);
                exit;
            }
        }
        
        /**
         * 登录时对购物车缓存的操作
         * @return [type] [description]
         */
        public function xxxooologin()
        {
            // 通过了登录验证之后
            // 写入用户信息
            $_SESSION['userinfo']['id'] = 2;
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
                        var_dump($CartInfo);
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
            /*结束*/
        }
    }

      