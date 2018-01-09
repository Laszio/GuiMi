<?php
namespace Home\Controller;

use Think\Controller;

class PublicController extends Controller 
{
    /**
     * header头小购物车数据
     */
    public function _initialize()
    {   

        $this->HeaderCart();
    }

    /**
     * 空操作
     */
    public function _empty(){        
        $this->redirect('Index/index');    
    }
    
    /**
     * 一二级导航栏
     * @Author   ryan
     * @DateTime 2017-11-28
     * @email    931035553@qq.com
     * @return   [type]           [description]
     */
    public function firstBar()
    {

        $arr = D('Public')->index();
        //查询未读信息
        if (session('?userinfo')) {
           //查询未读信息
            $mp2 = ['unread'=>1,'status'=>1,'uid'=>session('userinfo')['id']]; 
            $unread = '('.M('MessageDetail')->where($mp2)->count().')';
            $this->assign('num',$unread);
        }

        $this->assign($arr);
    }

/**
 * 三级导航栏
 * @Author   ryan
 * @DateTime 2017-11-28
 * @email    931035553@qq.com
 * @param    [type]           $id [description]
 * @return   [type]               [description]
 */
    public function bar ($id)
    {
    	$map['path'] = ['like','%,'.$id.',%'];
    	$map['attr'] = [3];
		$arr = M('type')
                ->where($map)
                ->cache(true)
                ->order('id')
                ->getField('id,id,name');

		$this->ajaxReturn($arr);

    }

    public function HeaderCart()
    {
        $seoModel = M('Seo');
        $seoInfo = $seoModel->field('id,title,keywords,description,logopic,addtime')->where('id = 1')->find();
        $this->assign('seoInfo', $seoInfo);
        if (empty(session('userinfo'))) {
            $this->redis = new \Redis();
            $this->redis->connect('127.0.0.1', '6379');
            //未登录
            if(!empty(cookie('Cart_id'))) {
                // 不为空
                $key = cookie('Cart_id');
                $cartIds = 'cart:ids:set:'.$key;
                $res = $this->redis->exists($cartIds);
                if (!$res) {
                    // 就是购物车不存在 为空
                } else {
                    // 存在获取所有propid
                    $propidList = $this->redis->zRevRange($cartIds, 0, -1);
                    $top_cartinfo = [];
                    // 遍历查询这个用户的所有购物车商品 $top_cartinfo[]
                    foreach ($propidList as $k => $propid) {
                        // 凭借购物车商品的key
                        $Rediskey = 'cart:'.$key.':'.$propid;
                        // 循环存储在数组中
                        $top_cartinfo[] = $this->redis->hGetAll($Rediskey);
                    }
                    $total = $this->redis->get($key.'cartTotal');
                }
            }
        } else {
            // 已经登录的情况
            $top_total = M('cart');
            $map['uid'] = session('userinfo.id');
            $top_cartinfo = [];
            // 获取这个用户的购物车总件数
            $total = $top_total->where($map)->Sum("num");
            $uid = session('userinfo.id');
            // 查询购物车表中所有uid的数据
            $top_cartinfo = $top_total->where("uid='%d'",$uid)->select();
        }
        // 分配数据 头部购物车的内容和总数
        $this->assign('top_cartinfo',$top_cartinfo);
        $this->assign('total',$total);
    }
}