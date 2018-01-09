<?php
namespace Home\Controller;
use Think\Controller;

class  GoodsController extends PublicController 
{
    public function index(){
        $make = S('secondKillTime');
        $this->firstBar();
        $goods = D('Goods');
        $good = $goods->getInfo();
        if (!$good) {
            $this->redirect('Index/index');
            exit;
        }
        // 获取购买量，点击量排序前几的商品信息
        $clickGood = $goods->getClickInfo();
        $buyGood = $goods->getBuyInfo();
        if ($make) {
            $make = $make - time();
        } else {
            $make = 0;
        }

        $pro = D('GoodsProperty');
        $prop = $pro->editPro($good['id']);
        $size = $prop['size'];
        unset($prop['size']);
        // 判断用户是否有收藏该商品
        if (!empty(session('userinfo'))) {
            $fav = M('favorite');
            $uid = session('userinfo.id');
            $map['gid'] = I('get.id');
            $map['uid'] = $uid;
            $res = $fav->where($map)->find();
            // var_dump(session());
            if ($res) {
                // 已收藏
                $fav_status = 1;
            } else {
                // 未收藏
                $fav_status = 2;
            }
        }
        A('History')->goodsHistory(I('get.id'));

        /**查看评价*/
        $comment = A('Comment');//跨控制器调用方法
        if (IS_AJAX) {
            $allComment = $comment->seeComment(I('get.id'));
            $this->ajaxReturn($allComment);

        } else {
            $allComment = $comment->seeComment(I('get.id'));
            $btn = $allComment['btn'];
            unset($allComment['btn']);
            $this->assign('comment', $allComment);
            $this->assign('btn', $btn);
        }

        
        
        $this->assign('favStatus',$fav_status);
        
        $this->assign('make', $make);
        $this->assign('buyGood', $buyGood);
        $this->assign('marke', $marke);
        $this->assign('clickGood', $clickGood);
    	$this->assign('good', $good);
    	$this->assign('prop', $prop);
    	$this->assign('size', $size);
    	$this->display();
    }

    /**
     * ajax获取对应的商品属性的库存
     * @return array 对应商品的库存以及表中的id
     */
    public function getStore() 
    {
        if(empty($_POST['gid']))exit('异常');
        $pro = M('GoodsProperty');
        $str = 'gid = '.$_POST['gid'].' and color = "'.$_POST['color'].'" and size = "'.$_POST['size'].'"';
        $store = $pro->field('id,store')->where($str)->find();
        if (empty($store)) {
            $this->ajaxReturn(0);
        } else {
            $this->ajaxReturn($store);
        }
    }

    public function getDes() 
    {
        // if (!empty($_POST['id'])) {
        //     // 送去首页，待处理
        //     exit;
        // }
        $des = M('GoodsDes');
        $de = $des->field('id,des,season,style,guide')->where('gid = '.$_POST['id'])->find();
        // dump($de);
        if (empty($de)) {
            $this->ajaxReturn(0);
        } else {
            $this->ajaxReturn($de);
        }
    }

    /**
     * 对相同IP再次访问该商品的点击量处理
     */
    public function setClick() 
    {
        $gidKey = "goodId";
        echo '当前的gidKey:'.$gidKey;
        echo '<br>';
        $str = S($gidKey);
        $str = empty($str) ? 0 : $str;
        if ($str != 0) {
                $arr = explode(',', $str);
                array_pop($arr);
                foreach ($arr as $k => $v) {
                    $gidClickKey = "goodId:click:$v";
                    $click = S($gidClickKey);
                    $good = M('Goods');
                    $sql = 'update shop_goods set clicknum = clicknum + '.$click.' where id='.$v;
                    $good->execute($sql);
                    S($gidClickKey, null);
                }
            S($gidKey, null);
        }

    }

    /**
     * 测试添加点击量
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function testClick($id) 
    {
        // 获取当前用户的IP信息，拼接成判断半小时检测是否存在指定时间内再次点击
        $ukey = $id.$_SERVER['SERVER_ADDR'];
        $gidKey = "goodId";
        $gidClickKey = "goodId:click:{$id}";
        echo "当前的ukey:$ukey<br>";
        echo "当前的gkey:$gidKey<br>";
        echo "当前的gidClickkey:$gidClickKey<br>";
        // 查缓存，判断该用户半小时内有么有访问该商品 缓存中有，则半小时内有访问过
        $userClick = S($ukey);

        if (empty($userClick)) { //缓存中没有，把点击量记录到缓存中
            echo '查缓存<br>';
            //设置用户时间内点击不生效
            S($ukey,
                1,
                [
                'type'=>'memcache',
                'host'=>'127.0.0.1',
                'port'=>'11211',    
                'expire'=>  10
            ]);

             // 查看该商品有没有信息在数组里面，没有则添加进去
            $str = S($gidKey);
            $str = empty($str) ? 0 : $str;
            echo '缓存中的id:'.$str;

            if ($str != 0) {
                $arr = explode(',', $str);
                echo '<pre>当前的商品数组';
                    print_r($arr);
                echo '</pre>';
                // exit;
                if (!in_array($_GET['id'], $arr)) {
                    $str .= ','.$_GET['id'];
                    echo '该商品id:'.$str;
                    S($gidKey,
                            $str,
                            [
                            'type'=>'memcache',
                            'host'=>'127.0.0.1',
                            'port'=>'11211',    
                    ]);
                }
            } else {
                S($gidKey,
                            $_GET['id'],
                            [
                            'type'=>'memcache',
                            'host'=>'127.0.0.1',
                            'port'=>'11211',    
                    ]);
            }

            // 判断是否是商品第一次点击，是赋值， 不是 拿出数据进行加1，再存进缓存
            $gclick = S($gidClickKey);
            $inr = empty($gclick) ? 1 : ($gclick + 1);
            echo '当前商品的点击量'.$inr;
            S($gidClickKey,
                $inr,
                [
                'type'=>'memcache',
                'host'=>'127.0.0.1',
                'port'=>'11211',    
            ]);
        }
    }

    /**
     * 添加收藏夹
     * @param int $gid 收藏商品的id
     */
    public function addFav($gid)
    {
        $uid = session('userinfo.id');
        $fav = M('favorite');
        $map['uid'] = $uid;
        $map['gid'] = $gid;
        $res = $fav->where($map)->find();
        if (!empty($res)) {
            // 已存在
            $res = $fav->where($map)->delete();
            $data['status'] = 2;
            $data['msg'] = 'del';
        } else {
            $map['addtime'] = time();
            $res = $fav->add($map);
            $data['status'] = 1;
            $data['msg'] = 'add';
        }
        $this->ajaxReturn($data);
    }
}