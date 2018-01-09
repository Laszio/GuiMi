<?php
namespace Admin\Controller;
use Think\Controller;

class OrderController extends Controller 
{

    /**
     * 订单页面
     */
    public function index()
    {
    	$orderModel = M('Order');
    	$orderList = $orderModel->field('id,status,uid,address,getman,code,phone,content,addtime')->order('id desc')->select();
        $arr = [1=>'待支付', '待发货', '待收货', '完成订单'];
        foreach ($orderList as $k => $v) {
            $orderList[$k]['status'] = $arr[$v['status']];
        }
    	$this->assign('orderList',$orderList);
        $this->display();
    }

    /**
     * 订单发货
     * @return [type] [description]
     */
    public function sendGoods() 
    {
        $orderModel = M('Order');
        $order = $orderModel->where('id = '.$_POST['oid'])->save(['status'=>3]);
        if ($order) {
            $this->ajaxReturn('ok');
        } else {
            $this->ajaxReturn(0);
        }
    }

    /**
     * 订单详情页
     */
    public function detail($id) 
    {
        $orderDetailModel = M('OrderDetail');

        $detail = $orderDetailModel->field('id,price,total,num,status,colorpic,size,color,gname')->where('oid = '.$id)->select();
        $arr = [1=>'正常', 2=>'失效'];
        foreach ($detail as $k => $v) {
            $detail[$k]['status'] = $arr[$v['status']];
        }

        $this->assign('detail', $detail);
        $this->display();
    }

    /**
     * 订单详情页
     */
    public function edit($id) 
    {
        $orderModel = M('Order');
        if (IS_POST) {
            if (!$orderModel->create()) {
                exit($orderModel->getError());
            }
            if ($orderModel->save($_POST)) {
                $this->redirect('Order/index');
            } else {
                $this->error('信息错误');
            }
            
            exit;
        }
        $detail = $orderModel->field('id,address,getman,code,phone,content')->where('id = '.$id)->find();
        $this->assign('detail', $detail);
        $this->display();
    }

    /**
     * 展示退货列表
     * @return [type] [description]
     */
    public function comeBack() 
    {
        $ComebackModel = M('Comeback');
        $userModel = M('User');

        $comebackInfo = $ComebackModel->select();
        $isget = [1=>'已收到', '未收到'];
        $reason = [1=>'七天无理由退货', '我不想要了', '退运费', '颜色/尺寸/参数不符', '商品瑕疵', '质量问题', '少件/漏发', '未按约定时间发货', '收到商品时有划痕或破损'];
        $status = [1=>'未审核', '已审核'];
        foreach ($comebackInfo as $k => $v) {
            $comebackInfo[$k]['isget'] = $isget[$v['isget']];
            $comebackInfo[$k]['reason'] = $reason[$v['reason']];
            $comebackInfo[$k]['status'] = $status[$v['status']];
            $username = $userModel->field('username')->where('id = '.$v['uid'])->find();
            $comebackInfo[$k]['uid'] = $username['username'];
        }

        $this->assign('comebackInfo', $comebackInfo);
        $this->display();
    }

    public function comeBackPass() 
    {
        // $this->ajaxReturn(1);exit;
        if (empty($_POST['did'])  || empty($_POST['id'])) {
            echo 4;
            exit;
        }

        $comebackModel = M('Comeback');
        $OrderDetailModel = M('OrderDetail');
        $comebackModel->startTrans();
        if (!$comebackModel->where('id = '.$_POST['id'])->save(['status'=>2]) ) {
            $comebackModel->rollback();
            $this->ajaxReturn(0);
            exit;
            
        }
        if (!$OrderDetailModel->where('id = '.$_POST['did'])->save(['status'=>3])) {
            $comebackModel->rollback();
            $this->ajaxReturn(0);
            exit;
        } else {
            $comebackModel->commit();
            $this->ajaxReturn(1);
        }
        
    }

    /**
     * 计划任务取消订单
     */
    public function clearWaitPay() 
    {
        $orderModel = M('Order');
        $orderDetailModel = M('OrderDetail');
        $GoodsDetailModel = M('GoodsProperty');
        $waitPay = $orderModel->where('status = 1')->select();
        foreach ($waitPay as $k => $v) {
            if ((time() - $v['addtime']) > 60) {
                // 订单失效
                $orderModel->startTrans();
                $detailInfo = $orderDetailModel->where('oid = '.$v['id'])->select();
                foreach ($detailInfo as $key => $value) {
                    $sql = 'update shop_goods_property set store = store + '.$value['num'].' where id='.$value['propid'];
                    $res = $GoodsDetailModel->execute($sql);
                    if (!$res) {
                        $orderModel->rollback();
                        exit;
                    }
                }
                // 最后一步  删除订单
                $result = $orderModel->where('id ='.$v['id'])->delete();

                if (!$result) {
                    $orderModel->rollback();
                    exit;
                }

                $orderModel->commit();
            }        
        }        
    }
}