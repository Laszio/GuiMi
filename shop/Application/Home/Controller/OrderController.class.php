<?php
namespace Home\Controller;
use Think\Controller;

class  OrderController extends PublicController 
{
    // 判断是否登陆
    public function _initialize ()
    {
        $this->HeaderCart();
        if (empty(session('userinfo'))) {
            // 未登录 弹窗提示 然后跳去登录页面
            cookie('OrderUrl',$_SERVER['HTTP_REFERER']);
            error('请先登录再购买商品','Login/index');
            exit;
        }
    }
    /**
     * 确认订单页面
     */
    public function index() {
        $data = I('post.');
        // 判断是否是从一键购过来的
        if (!empty($data['mark'])) {
            if ($data['num'] == '0') {
                error('请选择商品数量');
                exit;
            }
            // 放去购物车
            $cart = D('cart');
            $res = $cart->Buynow();
            if ($res['status'] == 1) {
                $checked_cartGood[1]['propid'] = $data['propid'];
                $checked_cartGood[1]['num'] = $data['num'];
            } else {
                error($res['msg']);
            }
        } else {
            // 遍历数据 筛选出游propid的数据
            foreach ($data as $k => &$value) {
                if (!empty($value['propid'])) {
                    $checked_cartGood[$value['propid']] = $value;
                }
            }
        }
        if (empty($checked_cartGood)) {
            error('没有选中任何数据','Cart/index');
            // 弹窗
            exit;
        }
        $uid = session('userinfo.id');
        $map['uid'] = $uid;
        $goods_prop = M('goods_property');
        $goods = M('goods');
        $goodsinfo = [];
        $allTotalPrice = 0;
        foreach ($checked_cartGood as $k => $v) {
            // 商品详情
            $propid = $v['propid'];
            $prop_res = $goods_prop->field("id,gid,store,size,color,colorpic")->where("id='%d'",$v['propid'])->find();
            if (empty($prop_res)) {
                // 弹窗提示
                error('非法操作','Cart/index');
                exit;
            }
            // 商品goods信息
            $goods_res = $goods->field("id,gname,price,discount,delsta,status")->where("id={$prop_res['gid']}")->find();
            if (($goods_res['status'] == 3) || ($goods_res['delsta'] == 2) ) {
                // 弹窗提示
                error('加入失败，商品已下架','Cart/index');
                exit;
            }
            // 判断库存是否足够
            if ($v['num'] > $prop_res['store']) {
                // 弹窗提示
                error('库存不足','Cart/index');
                exit;
            }
            // 需要的数据
            $goodsinfo[$propid]['propid'] = $propid;
            $goodsinfo[$propid]['gid'] = $prop_res['gid'];
            $goodsinfo[$propid]['gname'] = $goods_res['gname'];
            $goodsinfo[$propid]['price'] = $goods_res['price'];
            $goodsinfo[$propid]['discount'] = $goods_res['discount'];
            $goodsinfo[$propid]['num'] = $v['num'];
            $goodsinfo[$propid]['color'] = $prop_res['color'];
            $goodsinfo[$propid]['size'] = $prop_res['size'];
            $goodsinfo[$propid]['colorpic'] = $prop_res['colorpic'];
        }
        // 计算选中的商品的总价格
        foreach ($goodsinfo as $gk => $gv) {
            $allTotalPrice += ($gv['num']*$gv['price']*$gv['discount']/100);
        }
        // var_dump($goodsinfo);
        $address = M('Address');
        $addressList = $address->order('state desc')->where('uid = '.$_SESSION['userinfo']['id'])->select();
        // 商品总价
        $this->assign('allTotalPrice', $allTotalPrice);
        // 选中的商品信息
        $this->assign('goodsList', $goodsinfo);
        $this->assign('addressList', $addressList);
        $this->display();
    }

    /**
     * 下单处理
     */
   public function pay()
   {
        $order = D('Order');
        $goodsModel = M('Goods');
        foreach ($_POST['goods'] as $k => $v) {
            $goodsTid = $goodsModel->field('tid')->where('id = '.$v['gid'])->find();
        }
        if ($goodsTid['tid'] == 109) {
            $oid = $order->hotPaying();
        } else {
            $oid = $order->paying();
        }
        if (!is_numeric($oid)) {
            $this->error($oid);
            exit;
        }
        $this->redirect('Order/doPay?oid='.$oid);
   }

   /**
    * 做支付页面
    */
   public function doPay($oid) 
   {
        $orderModel = M('Order');
        $OrderDetailModel = M('OrderDetail');
        $order = $orderModel->field('id,address,getman,phone')->where('id = '.$oid)->find();
        $detailInfo = $OrderDetailModel->field('total,gname,color,size,num')->where('oid = '.$oid)->select();

        foreach ($detailInfo as  $v) {
            $total += $v['total'];
        }
        $this->assign('totalMoney', $total);
        $this->assign('detailInfo', $detailInfo);
        $this->assign('order', $order);
        $this->display();
   }

   /**
    * 成功支付页面
    * @param  int $oid 订单id
    */
   public function okPay($oid) 
   {
        $orderModel = M('Order');
        $data['status'] = 2;
        
        if (!$orderModel->where('id = '.$oid)->save($data)) {
            $this->redirect('User/index');
            exit;
        }
        $OrderDetailModel = M('OrderDetail');
        $order = $orderModel->field('id,address,getman,phone')->where('id = '.$oid)->find();
        $detailInfo = $OrderDetailModel->field('total,gname,color,size,num')->where('oid = '.$oid)->select();

        foreach ($detailInfo as  $v) {
            $total += $v['total'];
        }
        $this->assign('totalMoney', $total);
        $this->assign('detailInfo', $detailInfo);
        $this->assign('order', $order);
        $this->display();
   }

    /**
    * 全部订单状态页面
    */
   public function allOrder() 
   {
        $this->CountStaOrders();
        $waitOrderModel = D('Order');
        $waitOrderNum = $waitOrderModel->getOrderInfoNum(0);
        $Page       = new \Think\Page($waitOrderNum,3);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        $wait = $waitOrderModel->field('id,status,uid,address,getman,code,phone,content,addtime')->where('uid = '.$_SESSION['userinfo']['id'])->limit($Page->firstRow.','.$Page->listRows)->order('addtime desc')->select();
        $waitInfo = $waitOrderModel->getOrderInfo($wait);
        $this->assign('waitInfo', $waitInfo);
        $this->assign('show', $show);
        $this->assign('waitOrderNum', $waitOrderNum);
        $this->display();
   }

   /**
    * 待支付页面
    */
   public function waitPay() 
   {
        $this->CountStaOrders();
        $waitOrderModel = D('Order');
        $waitOrderNum = $waitOrderModel->getOrderInfoNum(1);
        $Page       = new \Think\Page($waitOrderNum,3);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        $wait = $waitOrderModel->field('id,status,uid,address,getman,code,phone,content,addtime')->where('status = 1 and uid = '.$_SESSION['userinfo']['id'])->limit($Page->firstRow.','.$Page->listRows)->order('addtime desc')->select();
        $waitInfo = $waitOrderModel->getOrderInfo($wait);
        $this->assign('waitInfo', $waitInfo);
        $this->assign('show', $show);
        $this->assign('waitOrderNum', $waitOrderNum);
        $this->display();
   }

   /**
    * 代发货页面
    */
   public function waitSend() 
   {
        $this->CountStaOrders();
        $waitOrderModel = D('Order');
        $waitOrderNum = $waitOrderModel->getOrderInfoNum(2);
        $Page       = new \Think\Page($waitOrderNum,3);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        $wait = $waitOrderModel->field('id,status,uid,address,getman,code,phone,content,addtime')->where('status = 2 and uid = '.$_SESSION['userinfo']['id'])->limit($Page->firstRow.','.$Page->listRows)->order('addtime desc')->select();
        $waitInfo = $waitOrderModel->getOrderInfo($wait);

        $this->assign('waitInfo', $waitInfo);
        $this->assign('show', $show);
        $this->assign('waitOrderNum', $waitOrderNum);
        $this->display();
   }

  /**
    * 代发货页面
    */
   public function waitGet() 
   {
        $this->CountStaOrders();
        $waitOrderModel = D('Order');
        $waitOrderNum = $waitOrderModel->getOrderInfoNum(3);
        $Page       = new \Think\Page($waitOrderNum,3);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        $wait = $waitOrderModel->field('id,status,uid,address,getman,code,phone,content,addtime')->where('status = 3 and uid = '.$_SESSION['userinfo']['id'])->limit($Page->firstRow.','.$Page->listRows)->order('addtime desc')->select();
        $waitInfo = $waitOrderModel->getOrderInfo($wait);
        $this->assign('waitInfo', $waitInfo);
        $this->assign('show', $show);
        $this->assign('waitOrderNum', $waitOrderNum);
        $this->display();
   }

    /**
    * 代发货页面
    */
   public function waitEvaluate() 
   {
        $this->CountStaOrders();
        $waitOrderModel = D('Order');
        $waitOrderNum = $waitOrderModel->getOrderInfoNum(4);
        $Page       = new \Think\Page($waitOrderNum,3);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        $wait = $waitOrderModel->field('id,status,uid,address,getman,code,phone,content,addtime')->where('status = 4 and uid = '.$_SESSION['userinfo']['id'])->limit($Page->firstRow.','.$Page->listRows)->select();
        $waitInfo = $waitOrderModel->getOrderInfo($wait);
        $this->assign('waitInfo', $waitInfo);
        $this->assign('show', $show);
        $this->assign('waitOrderNum', $waitOrderNum);
        $this->display();
   }

   /**
    * 个人主页的订单状态的头部数量
    */
   public function CountStaOrders()
   {
        $waitOrderModel1 = D('Order');
        $waitOrderNum0 = $waitOrderModel1->getOrderInfoNum(0);
        $this->assign('waitOrderNum0', $waitOrderNum0);
        $waitOrderNum1 = $waitOrderModel1->getOrderInfoNum(1);
        $this->assign('waitOrderNum1', $waitOrderNum1);
        $waitOrderNum2 = $waitOrderModel1->getOrderInfoNum(2);
        $this->assign('waitOrderNum2', $waitOrderNum2);
        $waitOrderNum3 = $waitOrderModel1->getOrderInfoNum(3);
        $this->assign('waitOrderNum3', $waitOrderNum3);
        $waitOrderNum4 = $waitOrderModel1->getOrderInfoNum(4);
        $this->assign('waitOrderNum4', $waitOrderNum4);
   }

   /**
    * ajax确认收货
    */
    public function confirmOrder() 
    {
        if (empty($_POST['id'])) {
           $this->redirect(U('Login/logout'));
           exit;
        }
        $orderModel = M('Order');
        if ($orderModel->where('id = '.$_POST['id'])->save(['status'=>4])) {
            $this->ajaxReturn('ok');
        } else {
            $this->ajaxReturn(0);
        }
    }

    /**
     * 订单详情表，订单多商品的情况可以进来,可评价，可申请退货
     */
    public function orderDetail() 
    {
        $orderDetailModel = M('OrderDetail');
        // 要做验证
        $orderModel = M('Order');
        $orderInfo = $orderModel->field('id,uid,status,addtime')->where('id = '.$_GET['oid'])->find();
        // echo '<pre>';
        //     print_r($orderInfo);
        // echo '</pre>';
        if ($orderInfo['uid'] != $_SESSION['userinfo']['id']) {
            //送去404页面
            echo 'gg';
            exit;
        }
        $orderDetailInfo = $orderDetailModel->where('oid = '.$_GET['oid'])->select();

        $this->assign('orderInfo', $orderInfo);
        $this->assign('detailInfo', $orderDetailInfo);
        $this->display();
    }

    public function comeBack() 
    {
        if (IS_POST) {

            if (count($_FILES['pic']['name']) > 3) {
                $this->error('图片数量最多三张哦~');
            }
            $orderModel = D('Order');
            if ($orderModel->makeComeBack()) {
                $this->redirect('Order/comeBackInfo');
            } else {
                $this->error('申请信息异常，请稍后尝试');
            }

            exit;
        }
        $orderDetailModel = M('OrderDetail');
        // 要做验证
        $orderModel = M('Order');
        $orderInfo = $orderModel->field('id,uid,status,addtime')->where('id = '.$_GET['oid'])->find();
        if ($orderInfo['uid'] != $_SESSION['userinfo']['id']) {
            //送去404页面
            echo 'gg';
            exit;
        }
        if (($orderInfo['status'] != 2) && ($orderInfo['status'] != 3)) {
            echo 'gg1';
            exit;
        }
        $orderDetailInfo = $orderDetailModel->where('id = '.$_GET['id'])->find();
        $this->assign('orderInfo', $orderInfo);
        $this->assign('detailInfo', $orderDetailInfo);
        $this->display();
    }

    public function comeBackInfo() 
    {
        $comebackModel = M('Comeback');
        $orderDetailModel = M('OrderDetail');
        $comebackInfo = $comebackModel->field('id,did,money,status,addtime')->where('uid = '.$_SESSION['userinfo']['id'])->select();
        $arr = [1=>'未审核', '已审核'];
        foreach ($comebackInfo as $k => $v) {
            $comebackInfo[$k]['status'] = $arr[$v['status']];
            $order = $orderDetailModel->field('gname,color,size,colorpic,total,oid')->where('id = '.$v['did'])->find();
            $dataInfo[] = array_merge($comebackInfo[$k], $order);

        }


        $this->assign('dataInfo', $dataInfo);
        $this->display();
    }
}