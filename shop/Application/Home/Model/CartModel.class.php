<?php
namespace Home\Model;
use Think\Model;

class CartModel extends Model
{
    /**
     * 处理立即购买在Order控制器的数据处理
     * @return array 订单对应的数据
     */
    public function Buynow() 
    {
        $data = I('post.');
        if ($data['num'] == 0) {
            $info['status'] = '2';
            $info['msg'] = '请选择商品数量';
            return $info;
            exit;
        }
        // 先对数据做检测
        $Goods = M('goods');
        $goodsInfo = $Goods->where("id='%d'",$data['gid'])->find();
        // 判断商品状态是否下架
        if (($goodsInfo['status'] == 3) || ($goodsInfo['delsta'] == 2) ) {
            $info['status'] = '2';
            $info['msg'] = '加入失败，商品已下架';
            return $info;
            exit;
        }
        // 查询详情表
        $goods_prop = M('goods_property');
        $map['gid'] = $data['gid'];
        $map['color'] = $data['color'];
        $map['size'] = $data['size'];
        $GoodsDetails = $goods_prop->where($map)->find();
        // 找不到这个商品
        if (!$GoodsDetails) {
            $info['status'] = '2';
            $info['msg'] = '加入失败，库存不足';
            return $info;
            exit;
        }
        // 查询gid是否和详情中的gid对应
        if ($GoodsDetails['gid'] < $data['gid']) {
            $info['status'] = '2';
            $info['msg'] = '非法操作';
            return $info;
            exit;
        }
        // 判断商品库存是否充足
        if ($GoodsDetails['store'] < $data['num'] || $GoodsDetails['store'] == 0) {
            $info['status'] = '2';
            $info['msg'] = '加入失败，库存不足';
            return $info;
            exit;
        }
        // 预备存入数据库的数据
        $cartInfo['uid'] = session('userinfo.id');
        $cartInfo['gid'] = $data['gid'];//gid
        $cartInfo['propid'] = $data['propid'];//商品详情id - 唯一标识
        $cartInfo['gname'] = $goodsInfo['gname'];//商品名
        $cartInfo['price'] = $goodsInfo['price'];//商品售价
        $cartInfo['status'] = $goodsInfo['status'];//商品状态
        $cartInfo['delsta'] = $goodsInfo['delsta'];//是否已经删除
        $cartInfo['discount'] = $goodsInfo['discount'];//商品折扣
        $cartInfo['color'] = $GoodsDetails['color'];//商品颜色
        $cartInfo['colorpic'] = $GoodsDetails['colorpic'];//对应的图片
        $cartInfo['size'] = $GoodsDetails['size'];//商品大小
        $cartInfo['store'] = $GoodsDetails['store'];//商品库存
        $cartInfo['num'] = $data['num'];//添加的数量
        
        // 获取uid
        $uid = session('userinfo.id');
        // 查询购物车表中所有uid的数据
        $cart = M('cart');
        $cartmap['uid'] = $uid;
        $cartmap['propid'] = $data['propid'];
        // 判断该用户购物车中是否已有该商品
        $check = $cart->where($cartmap)->find();
        if (!empty($check)) {
            $save['num'] = $data['num'];
            $cart->where($cartmap)->save($save);
        } else {
            // 没有就写入
            $cart->add($cartInfo);   
        }
        $info['status'] = 1;
        $info['msg'] = '成功';
        return $info;
    }
	
}