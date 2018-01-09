<?php

namespace Home\Model;
use Think\Model;

class OrderModel extends Model
{
    /**
     * 支付页面
     * @return [type] [description]
     */
    public function paying() 
    {
        if (!empty($_POST['text'])) {
            if(!preg_match('/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', $_POST['text'])){
                return '请输入3~18位的字母、数字、下划线或者中文,文字中间不能包含空格哦'; 
                exit;
            }
        }
        $this->startTrans();
        $addressModel = M('Address');
        $addres = $addressModel->field('uid,name,area,address,code,phone')->where('id = '.$_POST['aid'])->find();
        if ($addres['uid'] != $_SESSION['userinfo']['id']) {
            $this->rollback();
            return '无效的地址信息'; 
            exit;
        }

        $OrderData['address'] = $addres['area'].$addres['addres'];
        $OrderData['getman'] = $addres['name'];
        $OrderData['phone'] = $addres['phone'];
        $OrderData['code'] = $addres['code'];
        $OrderData['content'] = $_POST['text'];
        $OrderData['uid'] = $_SESSION['userinfo']['id'];

        $OrderModel = M('Order');
        if (!$lastId = $OrderModel->add($OrderData)) {
            $this->rollback();
            return $OrderModel->_sql(); 
            exit;
        } 
        foreach ($_POST['goods'] as $k => $v) {
            $goods = M('Goods');
            $goodsPro = M('GoodsProperty');
            $good = $goods->field('id,gname,price,discount,status,delsta')->where('id = '.$v['gid'])->find();

            if ($good['delsta'] == 2 || $good['status'] == 3) {
                $this->rollback();
                return "该{$good['delsta']}商品已失效"; 
                exit;
            }
            // 判断传过来的GID  跟属性表中的GID是否一致
            $prop = $goodsPro->field('id,gid,color,size,store')->where('id = '.$v['propid'])->find();
            if ($prop['gid'] != $v['gid']) {
                $this->rollback();
                return "无效商品，请重新购买~"; 
                exit;
            }
            if ($prop['size'] != $v['size']) {
                $this->rollback();
                return "无效商品，请重新购买!"; 
                exit;
            }
            if ($prop['color'] != $v['color']) {
                $this->rollback();
                return "无效商品，请重新购买..."; 
                exit;
            }
            // 验证通过 下单
            $OrderDetailModel = M('OrderDetail');
            $OrderDetailData['gid'] = $v['gid'];
            $OrderDetailData['propid'] = $v['propid'];
            $OrderDetailData['size'] = $v['size'];
            $OrderDetailData['color'] = $v['color'];
            $OrderDetailData['num'] = $v['num'];
            $OrderDetailData['colorpic'] = $v['colorpic'];
            $OrderDetailData['oid'] = $lastId;
            $OrderDetailData['gname'] = $good['gname'];
            $OrderDetailData['price'] = ($good['price'] * $good['discount']) / 100;
            $OrderDetailData['total'] = $OrderDetailData['price'] * $OrderDetailData['num'];

            if (!$OrderDetailModel->add($OrderDetailData)) {
                $this->rollback();
                return $OrderDetailModel->_sql();
                exit;
            }
            $propData['store'] = $prop['store']-$v['num'];
            if (!$goodsPro->where('id = '.$v['propid'])->save($propData)) {
                $this->rollback();
                return $goodsPro->_sql();
                exit;
            }            

            // 删除购物车数据表的数据
            $cartModel = M('Cart');
            $deleRes = $cartModel->where('uid = '.$addres['uid'].' and propid = '.$v['propid'])->delete();
            if (!$deleRes) {
                $this->rollback();
                return $cartModel->_sql();
                exit;
            }
        }
        $this->commit();


        return $lastId;
    }

    /**
     * 抢购商品订单流程
     * @return [type] [description]
     */
    public function hotPaying() 
    {
        // 进行输入内容非法字符  地址 库存 属性验证
        if (!empty($_POST['text'])) {
            if(!preg_match('/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', $_POST['text'])){ 
                return '请输入3~18位的字母、数字、下划线或者中文,文字中间不能包含空格哦';
                exit;
            }
        }
        $addressModel = M('Address');
        $addres = $addressModel->field('uid,name,area,address,code,phone')->where('id = '.$_POST['aid'])->find();
        if ($addres['uid'] != $_SESSION['userinfo']['id']) {
            $this->rollback();
            return "无效的地址信息";
            exit;
        }
        $goodsInfo = $_POST['goods'][0];

        $goods = M('Goods');
        $goodsPro = M('GoodsProperty');
        $good = $goods->field('id,gname,price,discount,status,delsta')->where('id = '.$goodsInfo['gid'])->find();

        if ($good['delsta'] == 2 || $good['status'] == 3) {
            $this->rollback();
            return "该{$good['delsta']}商品已失效";
            exit;
        }
        // 判断传过来的GID  跟属性表中的GID是否一致
        $prop = $goodsPro->field('id,gid,color,size,store')->where('id = '.$goodsInfo['propid'])->find();
        if ($prop['gid'] != $goodsInfo['gid']) {
            $this->rollback();
            return "无效商品，请重新购买~";
            exit;
        }

        if ($prop['size'] != $goodsInfo['size']) {
            $this->rollback();
            return "无效商品，请重新购买!";
            exit;
        }
        if ($prop['color'] != $goodsInfo['color']) {
            $this->rollback();
            return "无效商品，请重新购买...";
            exit;
        }
        if ($prop['store'] < $goodsInfo['num']) {
            $this->rollback();
            return "库存不足";
            exit;
        }

        $orderList = $_POST;
        $orderList['uid'] = $_SESSION['userinfo']['id'];
        //验证结束，放进队列
        $redis = new \Redis();
        $redis->connect('127.0.0.1', '6379');
        $key = 'order:'.$_POST['goods'][0]['gid'];
        $postArray = json_encode($orderList);
        $redis->lpush($key,$postArray);
        $length = $redis->lSize($key);
        while ($length) {
            $redisInfo = $redis->rpop($key);
            $redisInfo = json_decode($redisInfo,true);
            $redisGoodsInfo = $redisInfo['goods'][0];

            $this->startTrans();
            $addressModel = M('Address');
            $addres = $addressModel->field('uid,name,area,address,code,phone')->where('id = '.$redisInfo['aid'])->find();
            $OrderData['address'] = $addres['area'].$addres['addres'];
            $OrderData['getman'] = $addres['name'];
            $OrderData['phone'] = $addres['phone'];
            $OrderData['code'] = $addres['code'];
            $OrderData['content'] = $_POST['text'];
            $OrderData['uid'] = $_SESSION['userinfo']['id'];
            $OrderModel = M('Order');
            if (!$lastId = $OrderModel->add($OrderData)) {
                $this->rollback();
                return $OrderModel->_sql();
                exit;
            }

            $goods = M('Goods');
            $goodsPro = M('GoodsProperty');
            $good = $goods->field('id,gname,score,price,discount,status,delsta')->where('id = '.$redisGoodsInfo['gid'])->find();
            if ($good['delsta'] == 2 || $good['status'] == 3) {
                $this->rollback();
                return "该{$good['delsta']}商品已失效";
                exit;
            }
            // 判断传过来的GID  跟属性表中的GID是否一致
            $prop = $goodsPro->field('id,gid,color,size,store')->where('id = '.$redisGoodsInfo['propid'])->find();

            if ($prop['gid'] != $redisGoodsInfo['gid']) {
                $this->rollback();
                return "无效商品，请重新购买~";
                exit;
            }
            if ($prop['size'] != $redisGoodsInfo['size']) {
                $this->rollback();
                return "无效商品，请重新购买!";
                exit;
            }
            if ($prop['color'] != $redisGoodsInfo['color']) {
                $this->rollback();
                return "无效商品，请重新购买...";
                exit;
            }
            if ($prop['store'] < $redisGoodsInfo['num']) {
                $this->rollback();
                return "库存不足";
                exit;
            }

            // 验证通过 下单
            $OrderDetailModel = M('OrderDetail');

            $OrderDetailData['gid'] = $redisGoodsInfo['gid'];
            $OrderDetailData['propid'] = $redisGoodsInfo['propid'];
            $OrderDetailData['size'] = $redisGoodsInfo['size'];
            $OrderDetailData['color'] = $redisGoodsInfo['color'];
            $OrderDetailData['num'] = $redisGoodsInfo['num'];
            $OrderDetailData['colorpic'] = $redisGoodsInfo['colorpic'];
            $OrderDetailData['oid'] = $lastId;
            $OrderDetailData['gname'] = $good['gname'];
            $OrderDetailData['price'] = ($good['price'] * $good['discount']) / 100;
            $OrderDetailData['total'] = $OrderDetailData['price'] * $OrderDetailData['num'];

            if (!$OrderDetailModel->add($OrderDetailData)) {
                $this->rollback();
                return '系统繁忙，订单添加失败,请稍后尝试xq~';
                exit;
            }
            $propData['store'] = $prop['store']-$redisGoodsInfo['num'];
            if (!$goodsPro->where('id = '.$redisGoodsInfo['propid'])->save($propData)) {
                $this->rollback();
                return $goodsPro->_sql();
                exit;
            }            

            // 删除购物车数据表的数据
            $cartModel = M('Cart');
            $deleRes = $cartModel->where('uid = '.$addres['uid'].' and propid = '.$redisGoodsInfo['propid'])->delete();
            if (!$deleRes) {
                $this->rollback();
                return '系统繁忙，订单添加失败,请稍后尝试delcart~';
                exit;
            }

            $userModel = M('User');
            $sql = "update shop_user SET score=score+{$good['score']} WHERE ( id = {$redisInfo['uid']})";
            $userRes = $userModel->execute($sql);
            if (!$userRes) {
                $this->rollback();
                return '系统繁忙，订单添加失败,请稍后尝试uscore~';
                exit;
            }

            $this->commit();
            if ($redisInfo['uid'] == $_SESSION['userinfo']['id']) {
                return $lastId;
                break;
            } else {
                $length = $redis->lSize($key);
            }

        }

    }

    /**
     * 获取订单状态信息
     * @param  int $status 订单状态
     * @return array          查询结果
     */
    public function getOrderInfo($wait) 
    {
        $OrderDetailModel = M('OrderDetail');
        foreach ($wait as $k => $v) {
            $info = $OrderDetailModel->field('id,gid,propid,oid,num,price,total,colorpic,gname,size,color,status,addtime')->where('oid = '.$v['id'])->select();
            foreach ($info as $key => $val) {
                $waitInfo[$k]['goodsInfo'][$key]['num'] = $val['num'];
                $waitInfo[$k]['goodsInfo'][$key]['price'] = $val['price'];
                $waitInfo[$k]['goodsInfo'][$key]['total'] = $val['total'];
                $waitInfo[$k]['goodsInfo'][$key]['colorpic'] = $val['colorpic'];
                $waitInfo[$k]['goodsInfo'][$key]['color'] = $val['color'];
                $waitInfo[$k]['goodsInfo'][$key]['size'] = $val['size'];
                $waitInfo[$k]['goodsInfo'][$key]['status'] = $val['status'];
                $waitInfo[$k]['gname'] = $val['gname'];
                $waitInfo[$k]['total'] += $val['num'] * $val['price'];
                $waitInfo[$k]['addtime'] = $v['addtime'];
                $waitInfo[$k]['id'] = $v['id'];
                $waitInfo[$k]['status'] = $v['status'];
            }
        }

        return $waitInfo;
    }

    /**
     * 查询订单状态数量
     * @param  integer $status 订单状态
     * @return array          订单条数
     */
    public function getOrderInfoNum($status = 1) 
    {
        if ($status) {
            return $this->where('uid = '.$_SESSION['userinfo']['id'].' and status = '.$status)->count('id');
        } else {
            return $this->where('uid = '.$_SESSION['userinfo']['id'])->count('id');
        }
    }

    /**
     * 处理退货申请表单
     * @return bool 处理结果 
     */
    public function makeComeBack() 
    {
        echo '<pre>';
            print_r($_POST);
        echo '</pre>';
        dump($_FILES);
        if (empty($_POST['description'])) {
            unset($_POST['description']);
        }
        if (empty($_POST['did']) || empty($_POST['isget']) || empty($_POST['reason'])) {
            return false;
        }
        // echo 1111;exit;

        if (!preg_match('/^[1|2]{1}$/', $_POST['isget'])) {
            return false;
        }
        if (!preg_match('/^[1-9]{1}$/', $_POST['reason'])) {
            return false;
        }
        if (!preg_match('/^[0-9]+(.[0-9]{1,2})$/u', $_POST['money'])) {
            return false;
        }
        if (!preg_match('/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', $_POST['reason'])) {
            return false;
        }
        if ($_FILES['pic']['error'][0] != 4) {
            $upload = new \Think\Upload();// 实例化上传类    
            $upload->maxSize   =     3145728 ;// 设置附件上传大小    
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
            $upload->savePath  =      '/comeBack/'; // 设置附件上传目录    // 上传文件
            $up->autoSub   =  false;
            $info   =   $upload->upload();    
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());   
                exit;
                return false; 
            }
        }

        $data = $_POST;
        $data['uid'] = $_SESSION['userinfo']['id'];

        $marke = 1;
        foreach ($info as $k => $v) {
            $data['pic'.$marke] = $v['savepath'].$v['savename'];
            $marke++;
        }

        $orderDetailModel = M('OrderDetail');
        $comebackModel = M('Comeback');
        $orderDetailModel->startTrans();

        if (!$comebackModel->add($data)) {
            $orderDetailModel->rollback();
            return false;
        }

        if (!$orderDetailModel->where('id = '.$_POST['did'])->save(['status'=>2])) {
            $orderDetailModel->rollback();
            return false;
        }
        $orderDetailModel->commit();
        return true;
    }

    
}   