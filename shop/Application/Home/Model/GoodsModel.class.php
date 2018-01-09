<?php
namespace Home\Model;
use Think\Model;

class GoodsModel extends Model
{
	
    /**
     * 获取商品信息
     * @return array 处理完数据的商品信息
     */
	public function getInfo() 
	{
        if (!isset($_GET['id'])) {
            return false;
            exit;
        }
        // 先查缓存中有没有该数据
        $goodsInfo = S('goodsInfo:'.$_GET['id']);
        // 缓存中没有数据， 进行数据库查询
        if (empty($goodsInfo)) {
            $fix = C( "DB_PREFIX" );
            $goods = $fix.'goods'; 
            $des = $fix.'goods_des'; 
            $detail = $fix.'goods_property'; 
            $gg = M("Goods");
            $goodsInfo = $gg->field("$goods.id,$goods.status,$goods.tid,$goods.gname,$goods.price,$goods.discount,$goods.pic1,$goods.pic2,$goods.pic3,$goods.pic4,$goods.pic5,$goods.score,$goods.clicknum,$goods.buynum,$des.des,$des.guide,$des.dress1,$des.dress2,$des.dress3,$des.dress4,$des.season,$des.style")
                ->where($goods.'.id='.$_GET['id'])
                ->join("$des on $goods.id = $des.gid")
                ->select();
            $goodsInfo = $goodsInfo[0];
            if (!$goodsInfo) {
                return false;
                exit;
            }
            if ($goodsInfo['status'] != 2) {
                return false;
                exit;
            }
            // 对商品价格进行处理.
            $goodsInfo['nowPrice'] = ($goodsInfo['price'] * $goodsInfo['discount'] ) / 100;
            // 判断是否是热销，点击量高的商品，如果符合条件，写入缓存当中
            if ($goodsInfo['clicknum'] > 100 || $goodsInfo['buynum'] > 100) { //满足条件 写入缓存
                S('goodsInfo:'.$goodsInfo['id'],
                    $goodsInfo,
                    [
                    'type'=>'memcache',
                    'host'=>'127.0.0.1',
                    'port'=>'11211',    
                    'expire'=>  60
                    ]);
            }
        }
        return $goodsInfo;
	}

    /**
     * 获取商品信息
     * @return array 购买量最高的五条商品数据
     */
    public function getBuyInfo() 
    {
         // 先查缓存中有没有该数据
        $gbkey = 'goodsInfo:buy';
        $goodsInfo = S($gbkey);
        if (empty($goodsInfo)) {
            $gg = M("Goods");
            $goodsInfo = $gg->field("id,gname,price,discount,pic1")
                ->where(' delsta = 1 and status = 2')
                ->order('buynum desc')
                ->limit(5)
                ->select();
            foreach ($goodsInfo as $k => $v) {
                $goodsInfo[$k]['nowPrice'] = ($v['price'] * $v['discount'] ) / 100;
            }
            S($gbkey,
                    $goodsInfo,
                    [
                    'type'=>'memcache',
                    'host'=>'127.0.0.1',
                    'port'=>'11211',    
                    'expire'=>  60
                    ]);
        }
        return $goodsInfo;
    }

    /**
     * 获取商品信息
     * @return array 销售量最高的五条数据
     */
    public function getClickInfo() 
    {
        // 先查缓存中有没有该数据
        $gckey = 'goodsInfo:click';
        $goodsInfo = S($gckey);
        if (empty($goodsInfo)) {
            $gg = M("Goods");
            $goodsInfo = $gg->field("id,gname,price,discount,pic1")
                ->where(' delsta = 1 and status = 2')
                ->order('clicknum desc')
                ->limit(6)
                ->select();
            foreach ($goodsInfo as $k => $v) {
                $goodsInfo[$k]['nowPrice'] = ($v['price'] * $v['discount'] ) / 100;
            }
            S($gckey,
                    $goodsInfo,
                    [
                    'type'=>'memcache',
                    'host'=>'127.0.0.1',
                    'port'=>'11211',    
                    'expire'=>  60
                    ]);
        }
        return $goodsInfo;
    }

}