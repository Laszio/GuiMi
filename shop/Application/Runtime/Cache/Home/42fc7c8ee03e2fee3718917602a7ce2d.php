<?php if (!defined('THINK_PATH')) exit();?>    <html lang="zh-CN">
    <head>
    
        <meta name="renderer" content="webkit|ie-comp|ie-stand">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta charset="utf-8">
        <meta http-equiv="Cache-Control" content="no-siteapp">
        <title><?php echo ($seoInfo['title']); ?></title>
        <meta name="keywords" content="<?php echo ($seoInfo['keywords']); ?>">
        <meta name="description" content="<?php echo ($seoInfo['description']); ?>">
        <link href="/Public/Home/css/iconfont/iconfont.css" rel="stylesheet"/>
        <link href="/Public/Home/css/common.css" rel="stylesheet"/>
        <link href="/Public/Home/css/home.css" rel="stylesheet"/>
        <link href="/Public/Home/css/iconfont/iconfont.css" rel="stylesheet"/>
        <link href="/Public/Home/css/common.css" rel="stylesheet"/>
        <link href="/Public/Home/css/uc.css" rel="stylesheet"/>
        <link href="/Public/Home/css/goods-detail.css" rel="stylesheet"/>
        <!-- 外部样式用于头部 -->
        <link href="/Public/Home/css/topbar.css" rel="stylesheet"/>
        <link href="/Public/Home/css/cart.css" rel="stylesheet"/>
        <!-- 外部样式用于导航栏 -->
        <link href="/Public/Chat/css/home-chat.css" rel="stylesheet"/>
        
    <link rel="stylesheet" href="/Public/Home/css/search-goods.css">


    
 
    </head>
    <body>

        <!--头部--> 
    <div id="M_PC_top_nav">
        <div id="M_PC_top_nav_container">
            <div class="inner"> 
                <ul> 
                    <li class="go-home"> 
                        <a href="<?php echo U('Index/index');?>"> 
                            <em class="home">
                            </em>首页
                        </a> 
                    </li> 
                    <?php if(!empty(session('userinfo'))): ?><li class="drop"> 
                        <a href="<?php echo U('User/index');?>" class="nick"> 
                            <!-- 小头像 -->
                            <?php if(empty(session('userinfo.qq_openid'))): ?><img class="face" src="/Uploads<?php echo session('userinfo.user_img');?>">
                            <?php echo session('userinfo.username');?>
                            <?php else: ?>
                            <img class="face" src="<?php echo session('userinfo.user_img');?>">
                            <?php echo session('userinfo.nikname'); endif; ?>
                            <em class="arrow">
                            </em> 
                        </a> 
                        <ul class="down account"> 
                            <li> 
                                <a href="<?php echo U('User/index');?>" target="_blank" style="min-width: 80px;"> 个人中心 </a> 
                            </li> 
                            <li>
                                <!-- 退出登录 -->
                                <a href="<?php echo U('Login/logout');?>"> 退出 </a>
                            </li> 
                        </ul> 
                    </li> 
                    <li class="drop"> 
                        <a href="<?php echo U('User/favorite');?>" target="_blank"> 
                            <em class="collect">
                            </em>我的收藏 
                        </a> 
                    </li>
                    <?php else: ?>
                    <li> <a href="#"><em class="weixin"></em>微信登录</a> </li>
                    <li> <a href="<?php echo U('QQLogin/qqLogin');?>"> <em class="qq"></em>QQ登录</a> </li>
                    <li> <a href="<?php echo U('Login/index');?>">登录</a> </li>
                    <li> <a href="<?php echo U('Register/index');?>">注册</a> </li><?php endif; ?>  
                    <li class="drop cart-wrapper"> 
                        <a target="_blank" href="<?php echo U('Cart/index');?>" class="my-cart"> 
                            <em class="cart">
                            </em>我的购物车  
                            <!-- 下面是提示购物车件数 空的时候不输出 -->
                            <?php if($total != '0' and $total != ''): ?><span class="cart-num"><?php echo ($total); ?></span><?php endif; ?>
                        </a>  
                        <?php if(CONTROLLER_NAME != Cart): ?><!-- 这里是购物车显示的部分 -->
                        <div class="down"> 
                            <?php if($total != '0' and $total != ''): ?><ul class="cart-goods">
                                <?php if(is_array($top_cartinfo)): foreach($top_cartinfo as $key=>$top_cart): ?><li> 
                                    <a href="<?php echo U('Home/Goods/index/id/'.$top_cart['gid']);?>" target="_blank" style="padding: 0; float: left;"> 
                                        <span class="cart-goods-img" style="background-image: url(/Uploads<?php echo $top_cart['colorpic'];?>);width: 53px;height: 53px;">
                                        </span> 
                                    </a> 
                                    <div class="cart-goods-desc"> 
                                        <p> 
                                            <a href="<?php echo U('Home/Goods/index/id/'.$top_cart['gid']);?>" target="_blank" style="padding: 0; float: left;"> 
                                                <span class="cart-goods-title"><?php echo ($top_cart['gname']); ?></span> 
                                            </a> 
                                            <span class="cart-goods-price" style="line-height: 30px">￥<?php echo ($top_cart['price']); ?> </span> 
                                        </p> 
                                        <p class="cart-goods-info"> 
                                            <span class="cart-goods-title">  
                                                <span>颜色:<?php echo ($top_cart['color']); ?> </span> &nbsp;&nbsp;  
                                                <span>尺码:<?php echo ($top_cart['size']); ?> </span> &nbsp;&nbsp;  
                                                <!-- <span>数量:<?php echo ($top_cart['num']); ?> </span> &nbsp;&nbsp;   -->
                                            </span> 
                                            <span style="color: black" class="del-cart-goods" data-stock="1weod5s">　x<?php echo ($top_cart['num']); ?></span> 
                                            <!-- <em class="del-cart-goods" data-stock="1weod5s"></em> -->
                                        </p> 
                                    </div>
                                </li><?php endforeach; endif; ?>
                            </ul> 
                            <p class="cart-account"> 
                                <span>购物车里还有
                                    <a class="num" href="<?php echo U('cart/index');?>" target="_blank"> <?php echo ($total); ?> </a>件商品
                                </span> 
                                <a class="check-cart" href="<?php echo U('cart/index');?>" target="_blank" data-ptp-cache-id="1.XLXCOb.0.0.gQlG3wL">查看购物车</a>
                            </p> 
                            <?php else: ?>
                                <!-- 购物车空的时候输出 -->
                                <div class="cart-goods shop_cart_info empty_cart" style="width: 180px;">购物车里没有商品！</div><?php endif; ?>
                            
                        </div><?php endif; ?>
                    </li> 
                    <li> 
                        <a href="<?php echo U('Order/allOrder');?>" target="_blank" data-ptp-cache-id="1.zfrD1b.0.0.B6rzP"> 
                            <em class="order">
                            </em>我的订单
                        </a>
                    </li> 
                        <li class="drop"> 
                            <a target="_blank">帮助中心
                            </a> 
                        </li> 
                        <li>
                            <a href="<?php echo U('Message/index');?>"  class="last">邮件信息<span id='email'><?=strtolower(CONTROLLER_NAME)=='message'?'':$num?></span></a>
                        </li> 
                    </ul> 
                </div>
            </div>
        </div>
    <!--头部结束-->

    
    
            <div class="header-wrap">
            <div class="header wrapper">
                <a href="<?php echo U('Index/index');?>" class="logo">
                    <!-- <img src="/Public/Home/img/logo3.png" alt="" /> -->
                    <img src="/Uploads<?php echo ($seoInfo['logopic']); ?>" style='width: 200px;height: 70px;' alt="" />
                </a>
            <!-- 讯搜 -->
            <div class="header-schbox">
                <div class="inner clearfix">
                <form action="<?php echo U('List/index');?>" method="get">
                    <div class="search-switch">
                        <i class="arrow"></i>
                        <div class="item">商品</div>
                    </div>
                    <input value="<?php echo str_replace('+', ' ', $_GET['marke']);?>" name="marke" class="search-txt" placeholder="搜流行宝贝">
                    <button class="search-btn"></button>
                    <div class="suggest-box">
                        <div class="item" data-title="上衣 短款 短袖">上衣 短款 短袖<div class="tags"><span>雪纺</span><span>蕾丝</span><span>一字领</span></div></div>
                    </div>
                </form>
                </div>
                <div class="hot-words">
                    <?php
 Vendor('XunSearch.lib.XS'); $xs= new \XS('goods'); $search = $xs->search; $hostwords = $search->getHotQuery(); foreach ($hostwords as $k=>$v) { ?>
                        <a href="/Home/List/index/marke/<?=$k?>.html"><?=$k?></a>
                    <?php }?>
                </div>
            </div>
            <!-- 定位结束 -->



                <div class="contact">
                    <div class="item">
                        <span class="ico iconfont">&#xe61b;</span>
                        <span class="tel">400-000-0000</span>
                    </div>
                    <div class="item" style='position:relative'>
                         <span class="ico iconfont" id='online'>&#xe61d;</span><a class="kefu">在线客服</a>
                         <?php if(session("?userinfo")): ?><!-- 在线聊天界面 -->
                         <div  class='chatbox'>
                            <div id="container">
                                <div class="header">
                                    <span style="float: left;">客服</span>
                                    <span style="float: right;" id='closebox'>X </span>
                                </div>
                                <ul class="content">
                                </ul>
                                <div class="footer">
                                    <div id="user_face_icon">
                                        <img src="http://www.xttblog.com/icons/favicon.ico" alt="">
                                    </div>
                                    <input id="text" type="text" placeholder="说点什么吧...">
                                    <span id="btn" style='color:white;font-size:14px;'>发送</span>
                                </div>
                            </div>  
                         </div><?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="nav-box">
            <div class="nav wrapper" style='position:relative;'>
    <div class='my-bar'>
            <div class="slogan" style='text-align:center;font-size:16px;color:white;'>全部分类</div>
        <div class="side-category" style="display: block;position:absolute;top:40px;width:213px;left:0px;opacity:0.9;display:none;z-index:100px;">
            
            <div class="side-category-bd">
            <?php if(is_array($bar)): foreach($bar as $k=>$v): ?><div class="f-item" onmouseenter='getMsg($(this),<?php echo ($k); ?>)'>
                    <div class="f-box">
                        <div class="f-tit"> <a href="<?php echo U('List/index',['tid'=>$k]);?>"><?php echo ($v['name']); ?></a></div>
                        <div class="f-list">
                        <?php if(is_array($v["child"])): foreach($v["child"] as $k1=>$v1): ?><a href="<?php echo U('List/index',['tid'=>$k1]);?>"><?php echo ($v1); ?></a><?php endforeach; endif; ?>
                        </div>
                    </div>
                    <div class="c-box" style=' display:none;'>
                        <div class="hd">分类</div>
                        <div class="bd">
                            <div class="list-wrap">
                            </div>
                        </div>
                  <!--       <div class="ft">
                            <a href=""><img class="ad" src="/Public/Home/uploads/10.jpg" /></a>
                        </div> -->
                    </div>
                </div><?php endforeach; endif; ?>
               
             
            </div>

        </div>

    </div>
                <ul class="nav-ul">
                    <li><a href="<?php echo U('Index/index');?>">首页</a></li>
                    <li><a href="<?php echo U('List/index', ['tid'=>16]);?>">女装馆</a></li>
                    <li><a href="<?php echo U('List/index', ['tid'=>77]);?>">男装城</a></li>
                    <li><a href="">新品首发</a></li>
                </ul>
                <div class="nav-ad"><a class="ad" href=""><img src="/Public/Home/uploads/nav-ad.jpg" alt="" /></a></div>
           
        </div>
        </div>
        <!--头部-->


    
    
    <div class="wrapper router">
        <ul class="router-nav" style="position:relative;">
            <li><a href="">首页</a></li>
            <li><span class="divider"></span></li>
            <?php if (isset($typeName)) { ?>
                <li><span style="border:1px solid pink;padding:5px 20px;color:gray;"><?php echo ($typeName); ?></span></li>
            <?php } else if (isset($_GET['marke'])) {?>
                <?php if (isset($corrected)) { ?>
                    <li><span style="border:1px solid pink;padding:5px 20px;color:gray;"><?php foreach ($corrected as $v) { echo $v.' '; }?></span></li>
                <?php } else if (isset($searchName)) {?>
                     <li><span style="border:1px solid pink;padding:5px 20px;color:gray;"><?php foreach ($searchName as $v) { echo $v.' '; }?></span></li>
                <?php } else {?>
                    <li><span style="border:1px solid pink;padding:5px 20px;color:gray;"><?=str_replace('+', ' ', $_GET['marke'])?></span></li>
                <?php }?>
            <?php } else if (isset($_GET['brandMarke'])) {?>
                <li id="jin-search"><span style="border:1px solid pink;padding:5px 20px;color:gray;">品牌：<?=$brand[0]['brand_name']?></span></li>
                <div style="display:none;position: absolute;top:38px;left:48px;width:400px;z-index:10;border:2px solid pink;"></div>
            <?php }?>
            <?php if(isset($_GET['bid'])): ?><li>>&nbsp;<a href="<?php echo U('index', ['tid'=>I('get.tid'),'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke')]);?>"><span title="<?=$brandName?>" style="border:1px dashed red;padding:5px 20px;color:red;">品牌:<?php echo ($brandName); ?><span style="font-weight: bold;position: relative;left:5px;">x</span></span></a></li><?php endif; ?>
            <?php if(isset($_GET['price'])): ?><li>>&nbsp;<a href="<?php echo U('index', ['tid'=>I('get.tid'),'bid'=>I('get.bid'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>"><span title="<?php echo I('get.price');?>" style="border:1px dashed red;padding:5px 20px;color:red;">价格:<?php echo I('get.price');?><span style="font-weight: bold;position: relative;left:5px;">x</span></span></a></li><?php endif; ?>
            
            <?php if(isset($_GET['season'])): ?><li>>&nbsp;<a href="<?php echo U('index', ['tid'=>I('get.tid'),'bid'=>I('get.bid'), 'price'=>I('get.price'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>"><span title="<?php echo I('get.season');?>" style="border:1px dashed red;padding:5px 20px;color:red;">季节:<?php echo I('get.season');?><span style="font-weight: bold;position: relative;left:5px;">x</span></span></a></li><?php endif; ?>
             <?php if(isset($_GET['color'])): ?><li>>&nbsp;<a href="<?php echo U('index', ['tid'=>I('get.tid'),'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>I('get.season'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>"><span title="<?php echo I('get.color');?>" style="border:1px dashed red;padding:5px 20px;color:red;">颜色:<?php echo I('get.color');?><span style="font-weight: bold;position: relative;left:5px;">x</span></span></a></li><?php endif; ?>
            <?php if(isset($_GET['style'])): ?><li>>&nbsp;<a href="<?php echo U('index', ['tid'=>I('get.tid'),'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>"><span title="<?php echo I('get.style');?>" style="border:1px dashed red;padding:5px 20px;color:red;">风格:<?php echo I('get.style');?><span style="font-weight: bold;position: relative;left:5px;">x</span></span></a></li><?php endif; ?>
            <?php if(isset($_GET['size'])): ?><li>>&nbsp;<a href="<?php echo U('index', ['tid'=>I('get.tid'),'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>"><span title="<?php echo I('get.size');?>" style="border:1px dashed red;padding:5px 20px;color:red;">码数:<?php echo I('get.size');?><span style="font-weight: bold;position: relative;left:5px;">x</span></span></a></li><?php endif; ?>
        </ul>
        <div class="sch-result">
            <span class="tag"></span><span class="fl">共<span class="num"><?php echo count($list);?></span>个相关商品</span>
        </div>
    </div>

    <?php if (isset($corrected)) {?>
        <div style="background-color: lightyellow;border: 2px solid pink" class="wrapper router">
            <h3 style="position: relative;top:-16px;left:10px;">我们为您显示“<span style="color:green">
                <?php foreach ($corrected as $v) { echo $v.' '; }?>
            </span>”相关的商品。仍然搜索：“<a><?=str_replace('+', ' ', $_GET['marke'])?></a>”</h3>
        </div>
    <?php } else if (isset($searchName)) {?>
        <div style="background-color: lightyellow;border: 2px solid pink" class="wrapper router">
            <h3 style="position: relative;top:-16px;left:10px;">我们为您显示“<span style="color:green">
                <?php foreach ($searchName as $v) { echo $v.' '; }?>
            </span>”相关的商品。仍然搜索：“<a><?=str_replace('+', ' ', $_GET['marke'])?></a>”</h3>
        </div>
    <?php }?>
    
    <div class="filter-box">
        <div class="wrapper">
            <?php if (!isset($_GET['bid']) && !isset($_GET['brandMarke'])) {?>
            <div class="sch-prop sch-brand" style="border:2px solid pink;">

                <!-- 品牌定位 -->
                <?php if(empty($brand)): else: ?>
                    <div class="sch-key">品牌：</div>
                    <div class="sch-value clearfix">


                        <?php if(is_array($brand)): foreach($brand as $key=>$v): ?><div class="item">
                                <a href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>$v['id'], 'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke')]);?>" class="inner jin_brand">
                                    <img src="/Uploads/<?php echo ($v['logo']); ?>" alt="" />
                                    <div class="name"><?php echo ($v['brand_name']); ?></div>
                                </a>
                            </div><?php endforeach; endif; ?>
                        
                    </div><?php endif; ?>
                <!-- 定位结束 -->
            </div>
            <?php }?>
                    
            <div class="sch-prop clearfix">
                <div class="sch-key">价格：</div>
                <div class="sch-value clearfix">
                    <a href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>'100-200', 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>">100-200</a>
                    <a href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>'200-300', 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>">200-300</a>
                    <a href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>'300-400', 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>">300-400</a>
                    <a href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>'500-600', 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>">500-600</a>
                    <a href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>'700-800', 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>">700-800</a>
                    <a href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>'大于800', 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>">800以上</a>
                </div>
            </div>
            <div class="sch-prop clearfix">
                <div class="sch-key">季节：</div>
                <div class="sch-value clearfix">
                    <a href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>'2017冬季', 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>">2017冬季 </a>
                    <a href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>'2017秋季', 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>">2017秋季 </a>
                    <a href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>'2017夏季', 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>">2017夏季 </a>
                    <a href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>'2017春季', 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>">2017春季 </a>
                </div>
            </div>

            <?php if (empty($_GET['color'])) {?>
                <div class="sch-prop clearfix">
                    <div class="sch-key">颜色：</div>
                    <div class="sch-value clearfix">
                    <?php if(is_array($color)): foreach($color as $key=>$v): ?><a href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>$v, 'style'=>I('get.style'), 'size'=>I('get.size'),'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>"><?php echo ($v); ?></a><?php endforeach; endif; ?>
                       
                    </div>
                </div>
            <?php }?>

            <?php if (empty($_GET['style'])) {?>
                <div class="sch-prop clearfix">
                    <div class="sch-key">风格：</div>
                    <div class="sch-value clearfix">
                        <?php if(is_array($style)): foreach($style as $key=>$v): ?><a href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>$v, 'size'=>I('get.size'),'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>"><?php echo ($v); ?></a><?php endforeach; endif; ?>
                    </div>
                </div>
            <?php }?>

            <?php if (empty($_GET['size'])) {?>
                <div class="sch-prop clearfix">
                    <div class="sch-key">码数：</div>
                    <div class="sch-value clearfix">
                        <?php if(is_array($size)): foreach($size as $key=>$v): ?><a href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>$v, 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>"><?php echo ($v); ?></a><?php endforeach; endif; ?>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>

    <div class="sg-main wrapper">
        <div class="sg-content">
            <div class="rank-menu">
                <div class="rank">
                    <div class="r-item">
                        <?php if(isset($_GET['buynumdown'])): ?><a class="active" title="点击恢复默认排序" href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'),'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>">销量<span class="sort-arrow desc"></a>
                        <?php else: ?>
                            <a title="点击按销量从高到低排序" href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'buynumdown'=>'buynumdown', 'brandMarke'=>I('get.brandMarke')]);?>">销量<span class="sort-arrow desc"></a><?php endif; ?>
                    </div>
                    <div class="r-item">
                        <?php if(isset($_GET['pricedown'])): ?><a class="active" title="点击恢复默认排序" href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>">价格<span class="sort-arrow desc"></a>
                        <?php else: ?>
                            <a title="点击按价格从高到低排序" href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'pricedown'=>'pricedown', 'brandMarke'=>I('get.brandMarke')]);?>">价格<span class="sort-arrow desc"></a><?php endif; ?>
                    </div>
                    <div class="r-item">
                        <?php if(isset($_GET['clickdown'])): ?><a class="active" title="点击恢复默认排序" href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>">人气<span class="sort-arrow desc"></a>
                        <?php else: ?>
                            <a title="点击按人气从高到低排序" href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'clickdown'=>'clickdown', 'brandMarke'=>I('get.brandMarke')]);?>">人气<span class="sort-arrow desc"></a><?php endif; ?>
                    </div>
                    <div class="r-item">
                        <?php if(isset($_GET['timedown'])): ?><a class="active" title="点击恢复默认排序" href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'brandMarke'=>I('get.brandMarke')]);?>">上架时间<span class="sort-arrow desc"></a>
                        <?php else: ?>
                            <a title="点击按时间从高到低排序" href="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'price'=>I('get.price'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'size'=>I('get.size'), 'marke'=>I('get.marke'), 'timedown'=>'timedown', 'brandMarke'=>I('get.brandMarke')]);?>">上架时间<span class="sort-arrow desc"></a><?php endif; ?>
                    </div>
                    <?php if(isset($_GET['price'])): else: ?>
                        <div class="r-item">
                            <form id="priceForm" action="<?php echo U('index', ['tid'=>I('get.tid'), 'bid'=>I('get.bid'), 'season'=>I('get.season'), 'color'=>I('get.color'), 'style'=>I('get.style'), 'marke'=>I('get.marke'), 'size'=>I('get.size'), 'brandMarke'=>I('get.brandMarke')]);?>" method="get">
                                <div class="sch">
                                    <input value="<?php echo I('get.pricemin');?>" name="pricemin" type="text" class="txtin" placeholder="￥" /><span class="divider">-</span><input value="<?php echo I('get.pricemax');?>" name="pricemax" type="text" placeholder="￥" class="txtin" /><button class="tj ui-btn-theme">确定</button>
                                </div>
                            </form>
                        </div><?php endif; ?>
                </div>
                <div class="help">
                    <label class="check"><input type="checkbox" name="" id="" />仅显示有货商品</label>
                    <span class="info">共<span><?php echo count($list);?>个</span>商品</span>
                    <!-- <div class="r-page">
                        <a class="prev" href="">上一页</a><a class="next" href="">下一页</a>
                    </div> -->
                </div>
            </div>
            <div class="sg-list clearfix" id="ajin">
                
                
                <!-- 定位 -->
                <?php if(is_array($list)): foreach($list as $key=>$v): ?><div class="col col-3">
                        <div class="item">
                            <div class="inner jin-lazyload">
                                <a href="<?php echo U('Goods/index', ['id'=>$v['id']]);?>"><img class="figure" src="/Public/Home/lazygif/lazy3.gif" data-echo="/Uploads/<?php echo ($v['pic1']); ?>" alt="" /></a>
                                <a href=""><div class="name"><?php echo ($v['gname']); ?></div><div class="price">￥<?php echo ($v['price']); ?></div></a>

                                <a class="act" href=""><i class="iconfont icon-cart"></i>加入购物车</a>
                            </div>
                        </div>
                    </div><?php endforeach; endif; ?>
                <!-- 定位结束 -->

            </div>

            <!-- 分页按钮 -->
            <div class="jin-page">
                <?php echo ($btn); ?>
            </div>
            
        </div>


        <div class="sg-aside">
            <div class="sg-aside-tit">
                商品热销
            </div>
            <?php if(empty($host)): else: ?>
                <?php if(is_array($host)): foreach($host as $key=>$v): ?><div class="shop-hot">
                        <div class="item">
                            <a href=""><img class="figure" src="/Public/Home/lazygif/lazy3.gif" data-echo="/Uploads/<?php echo ($v['pic1']); ?>" alt="" /></a>
                            <div class="p-name"><a href=""><?php echo ($v['gname']); ?></a></div>
                            <div class="price">¥<?php echo ($v['price']); ?></div>
                        </div>   
                    </div><?php endforeach; endif; endif; ?>
        </div>
    </div>  


    
         <!--脚部-->
        <div class="fatfooter">
            <div class="wrapper">
                <div class="fatft-service">
                    <div class="item">
                        <a href="">
                            <img src="/Public/Home/img/ico/ft-ser1.png" alt="" class="ico" />
                            <span>品质保障</span>
                        </a>
                    </div>
                    <div class="item">
                        <a href="">
                            <img src="/Public/Home/img/ico/ft-ser2.png" alt="" class="ico" />
                            <span>七天无理由退换货</span>
                        </a>
                    </div>
                    <div class="item">
                        <a href="">
                            <img src="/Public/Home/img/ico/ft-ser3.png" alt="" class="ico" />
                            <span>特色服务体验</span>
                        </a>
                    </div>
                    <div class="item">
                        <a href="">
                            <img src="/Public/Home/img/ico/ft-ser4.png" alt="" class="ico" />
                            <span>帮助中心</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="fatft-links">
                <div class="wrapper">
                    <div class="col-link">
                        <div class="tit">购物指南</div>
                        <div class="link"><a href="">购物流程</a></div>
                        <div class="link"><a href="">账户安全</a></div>
                        <div class="link"><a href="">联系客服</a></div>
                        <div class="link"><a href="">会员介绍</a></div>
                    </div>
                    <div class="col-link">
                        <div class="tit">配送方式</div>
                        <div class="link"><a href="">配送服务查询上</a></div>
                        <div class="link"><a href="">门自提</a></div>
                        <div class="link"><a href="">物流费用标准</a></div>
                    </div>
                    <div class="col-link">
                        <div class="tit">支付方式</div>
                        <div class="link"><a href="">银联支付</a></div>
                        <div class="link"><a href="">支付宝支付</a></div>
                        <div class="link"><a href="">微信支付</a></div>
                    </div>
                    <div class="col-link">
                        <div class="tit">售后服务</div>
                        <div class="link"><a href="">售后政策</a></div>
                        <div class="link"><a href="">价格保护</a></div>
                        <div class="link"><a href="">退单说明</a></div>
                        <div class="link"><a href="">取消订单</a></div>
                    </div>
                    <div class="col-link">
                        <div class="tit">联系我们</div>
                        <div class="link"><a href="">商家入驻</a></div>
                        <div class="link"><a href="">营销服务</a></div>
                        <div class="link"><a href="">关于我们</a></div>
                        <div class="link"><a href="">广告服务</a></div>
                    </div>
                    <div class="col-contact">
                        <div class="phone">400-889-8188</div>
                        <div class="time">周一至周日 8:00-18:00 <br />（仅收市话费）</div>

                    </div>
                </div>
            </div>
            <div style='margin: 0 auto;width:800px;'>
                <?php if(is_array($link)): foreach($link as $k=>$v): ?><a href='<?php echo ($v["toUrl"]); ?>'><img src='<?php echo ($v["img"]); ?>'></a><?php endforeach; endif; ?>
            </div>
        </div>
        <div class="footer">
            <div>安徽XXX网络科技有限公司 版权所有 Copyright © 2016-2018   备案号：皖ICP备123456789</div>
        </div>


        <!--脚部-->
    
       
    </body>
    <script src="/Public/Home/js/jquery.js"></script>
    <link rel="stylesheet" href="/Public/Home/js/slick/slick.css"/>
    <script src="/Public/Home/js/slick/slick.min.js"></script>
    <script src="/Public/Home/js/global.js"></script>

    <script>
        $('.home-full-banner').slick({
            autoplay: true,
            autoplaySpeed: 5000,
            arrows: false,
            dots: true,
            fade: true
          });


        +function () {
     
            a = 0;
            $('.my-bar').on('mouseenter',function () {
            
                //控制导航显示
            if (a == 0)   {   
                $('.side-category').css({'z-index':10000});
                $('.side-category').slideDown(300,function(){
                    a = 1;       
                    b = 0;
                });
            }
            })
        }();

        +function () {
            b = 0
            $('.my-bar').on('mouseleave',function () {
          
                //控制导航显示
                if (b == 0) {
                    $('.side-category').slideUp(300,function(){

                        b = 1;
                        a = 0;
                    });
                }
                
            });
        }();
       
     

         zAction.add({
            'category-toggle':function () {
                if ($(this).hasClass('on')) {
                    $(this).removeClass('on').prev().slideDown(200);
                }
                else {
                    $(this).addClass('on').prev().slideUp(200);
                }
            },

        });
   

    </script>
    <script src="/Public/Home/js/jquery.js"></script>
    <link rel="stylesheet" href="/Public/Home/js/icheck/style.css"/>
    <script src="/Public/Home/js/icheck/icheck.min.js"></script>
    <script src="/Public/Home/js/laydate/laydate.js"></script>
    <script src="/Public/Home/js/global.js"></script>
    <script type="text/javascript" src="/Public/Chat/js/swfobject.js"></script>
    <script type="text/javascript" src="/Public/Chat/js/web_socket.js"></script>
    <script type="text/javascript" src="/Public/Chat/js/jquery.min.js"></script>
    <script type="text/javascript" src="/Public/Chat/js/jquery-sinaEmotion-2.1.0.min.js">
    </script>
    <script type="text/javascript" src="/Public/Chat/js/chat-home.js">
    </script>
    
<script src="/Public/Home/js/echo.js"></script>
<!-- jin的脚本 -->
<script>
    //懒加载
    Echo.init({
        offset: 100, //距离可视区域100像素提前加载
        throttle: 1000   //延迟多少毫秒加载
    });

    $('.jin-page a, .jin-page span').unwrap('<div></div>').wrap('<li></li>').parent().parent().wrapInner('<ul class="pagination"></ul>');
    $('.jin-page').find('.current').css({'color':'white', 'backgroundColor':'red'});
    
    //品牌的hover事件
    var timer = null;
    $('#jin-search').mouseover(function() {
        clearTimeout(timer);
        $(this).next().css('display', 'block');

        //缓存有。拿缓存。没有就发送ajax
        if ($('#jin-search').next().attr('cache')) {
             $('#jin-search').next().html($('#jin-search').next().attr('cache'));
        } else {
            $.ajax({
                url: '/Home/List/ajaxFindBrand',
                type: 'get',
                async: true,
                success: function(res) {
                    var div = '';
                    for (var k in res) {
                        div += '<a href="/Home/List/index/brandMarke/'+res[k].id+'.html"><li style="float:left;margin:0;padding:0;"><img style="width:100px;heihgt:50px;" src="/Uploads/'+res[k].logo+'"></li></a>';
                    }
                    $('#jin-search').next().html(div);
                    //存进一个属性的缓存
                    $('#jin-search').next().attr('cache', div);
                }
            });        
        }
    });

    //设置一下缓存时间10秒；
    setInterval(function() {
        $('#jin-search').next().attr('cache', '');
    }, 10000);

    $("#jin-search").mouseout(function() {
        timer = setInterval(function() {
            $("#jin-search").next().css('display', 'none');
        }, 100);
    });
    $('#jin-search').next().mouseover(function() {
        clearTimeout(timer);
        $(this).css('display', 'block');

    });
    $('#jin-search').next().mouseout(function() {
        clearTimeout(timer);
        $(this).css('display', 'none');

    });

</script>


<script>
    
+function () {
    var dftLine=5,
    dftH=50; //这里品牌的比普通的一行要高，所以用品牌的做标准行高
    //
    $('.filter-box .sch-value').each(function(index, el) {
        var h=$(this).height();
        if (h>dftH) {
            $('<a class="prop-toggle"  data-action="prop-toggle" href="javascript:;">查看更多<i></i></a>').insertAfter($(this));
            $(this).addClass('slideup');
        }
        console.log(123);
    });
    $('.filter-box .sch-prop').each(function(index, el) {
        index+=1;
        if (index==dftLine) {
            $('.filter-box').append('<span class="filter-toggle" data-action="filter-toggle"><span class="tohide">收起<i></i></span><span class="toshow">更多<i></i></span></span>');
        }
        if (index>dftLine) {
            $(this).addClass('hide');
        }

    });
    zAction.add({
        'filter-toggle':function () {
            $(this).toggleClass('on').parent().toggleClass('on');
        },
        'prop-toggle':function () {
            $(this).toggleClass('on').prev('.sch-value').toggleClass('slideup')
        }
    });

}();



</script>

    </html>