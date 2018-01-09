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


    
    

    <div class="wrapper">
        <div class="detail-top clearfix">
            <div class="detail-goods">
                <div class="detail-show">
                    <div class="origin-show">
                        <div class="zoomup"></div>
                        <img class="big-pic" src="/Uploads<?php echo ($good['pic1']); ?>" alt="<?php echo ($good['name']); ?>" />
                    </div>
                    <div class="thumb-show">
                        <span class="item"><img class="s-pic" src="/Uploads<?php echo ($good['pic1']); ?>" bsrc="/Uploads<?php echo ($good['pic1']); ?>" /></span>
                        <span class="item"><img class="s-pic" src="/Uploads<?php echo ($good['pic2']); ?>" bsrc="/Uploads<?php echo ($good['pic2']); ?>" /></span>
                        <span class="item"><img class="s-pic" src="/Uploads<?php echo ($good['pic3']); ?>" bsrc="/Uploads<?php echo ($good['pic3']); ?>" /></span>
                        <span class="item"><img class="s-pic" src="/Uploads<?php echo ($good['pic4']); ?>" bsrc="/Uploads<?php echo ($good['pic4']); ?>" /></span>
                        <span class="item"><img class="s-pic" src="/Uploads<?php echo ($good['pic5']); ?>" bsrc="/Uploads<?php echo ($good['pic5']); ?>" /></span>
                    </div>
                    <div class="zoom-show">
                        <img src="" alt="" />
                    </div>
                </div>
                <div class="detail-info">
                <form action="<?php echo U('Order/index');?>" method="post">
                    <input type="hidden" name="mark" value="1">
                    <input type="hidden" name="gid" id="lz-gid" value="<?php echo ($good['id']); ?>">
                    <input type="hidden" name="propid" id="lz-propid" value="">
                    <input type="hidden" name="size" id="lz-size" value="">
                    <input type="hidden" name="color" id="lz-color" value="">
                    <input type="hidden" name="num" id="lz-num" value="1">
                    <div class="item-title" id="lz-gname"><?php echo ($good['gname']); ?></div>
                    <div class="item-price">
                        <span class="now" id="lz-price">￥<?php echo ($good['nowPrice']); ?></span><span class="dft">￥<?php echo ($good['price']); ?></span>
                    </div>
                    <ul class="item-data clearfix">
                        <li class="col-4">销量<span class="txt-theme ml10"><?php echo ($good['buynum']); ?>件</span></li>
                        <li class="col-4">好评率<span class="txt-theme ml10">99%</span></li>
                        <li class="col-4">收藏<span class="txt-theme ml10">228人</span></li>
                    </ul>
                    <div class="sku-info">
                        <div class="prop">
                            <div class="dt">颜色：</div>
                            <div class="dd" id="lzz-div-color">
                                <ul class="chose-img">
                                    <?php if(is_array($prop)): foreach($prop as $key=>$v): ?><li>
                                            <a onclick="getStore(this)" val="<?php echo ($v['color']); ?>" href="javascript:;">
                                            <img style="width: 44px;height: 44px"  src="/Uploads<?php echo ($v['colorpic']); ?>" alt="<?php echo ($v['color']); ?>" />
                                            </a>
                                        </li><?php endforeach; endif; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="prop">
                            <div class="dt">尺寸：</div>
                            <div class="dd" id="lzz-div-size">
                                <ul class="chose-common">
                                <?php if(is_array($size)): foreach($size as $key=>$v): ?><li>
                                        <a onclick="getStore(this, '<?php echo ($v); ?>')" val="<?php echo ($v); ?>" href="javascript:;"><?php echo ($v); ?></a>
                                    </li><?php endforeach; endif; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="prop">
                            <div class="dt">数量：</div>
                            <div class="dd">
                                <div class="mod-numbox chose-num" id="data-max" data-max="10">
                                    <span class="count-minus" id="lzz-dec"></span>
                                    <input  value="" id="max-num" min="1"  />
                                    <span class="count-plus" id="lzz-inr"></span>
                                </div>
                                <span id="max-store"></span>
                                <div id="store" class="stock">(库存1件)</div>
                            </div>
                        </div>
                    </div>
                    <div class="item-action">
                    <?php if($good['tid'] == 109): if($make != 0): ?><a href="javascript:;" val="<?php echo ($make); ?>" class="buy-now lz-time"></a>
                        <?php else: ?> 
                            <button  class="buy-now">立即购买</button><?php endif; ?>
                    <?php else: ?>
                        <button href="cart.html"  class="buy-now">立即购买</button>
                        <a href="javascript:void(0);" id="add-cart" class="add-cart">加入购物车</a><?php endif; ?>
                    </div>
                    <div class="item-extend">
                        <?php if(!empty(session('userinfo'))): if($favStatus == 1): ?><a href="javascript:void(0);" class="fav" id="add-fav">已收藏</a>
                            <?php else: ?>
                            <a href="javascript:void(0);" class="fav" id="add-fav">
                                <i class="iconfont icon-star"></i>收藏</a><?php endif; endif; ?>

                        <!-- <a href="" class="share"><i class="iconfont icon-fenxiang"></i>分享</a> -->
                    </div>
                </div>
            </form>
            </div>
            <div class="detail-shop">
                <div class="clearfix">
                    <a class="shop-brand" href="">
                        <img src="/Public/Home/uploads/1.png" alt="" />
                    </a>
                    <div class="shop-intro">
                        <div class="shop-name" style="margin-top: 15px">女神屋</div>
                        <!-- <a class="shop-follow-btn" href="javascript:;"><i class="iconfont icon-jiaguanzhu"></i>关注</a> -->
                        <!-- <a class="shop-follow-btn active" href="javascript:;"><span class="showtxt"><i class="iconfont icon-check01"></i>已关注</span><span class="hidetxt">取消关注</span></a> -->
                        <!-- <div class="shop-follow-count"><strong>268</strong>粉丝</div> -->
                    </div>
                </div>
                <div class="shop-assess clearfix">
                    <div class="col col-3">
                        <div class="tit">描&ensp;述</div>
                        <div class="point">
                            <span class="num">4.8</span><i class="iconfont">--</i>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="tit">质&ensp;量</div>
                        <div class="point up">
                            <span class="num">4.9</span><i class="iconfont">--</i>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="tit">服&ensp;务</div>
                        <div class="point down">
                            <span class="num">4.7</span><i class="iconfont">--</i>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="tit">发&ensp;货</div>
                        <div class="point">
                            <span class="num">4.8</span><i class="iconfont">--</i>
                        </div>
                    </div>
                </div>
                <ul class="shop-info">
                    <li>所在地区：广东广州</li>
                    <li>商品数量：518</li>
                    <li>销售数量：60285</li>
                </ul>
                <a class="detail-shop-enter">
                    <i class="iconfont icon-dianpu"></i>进入店铺
                </a>
            </div>
        </div>
        <!-- 商品推荐 -->
        <div class="ui-tabs">
            <span class="item active">商品推荐</span>
        </div>
        <ul class="detail-rec clearfix">
            <?php if(is_array($clickGood)): foreach($clickGood as $key=>$v): ?><li style="width: 188px;height: 237px">
                    <a href="<?php echo U('Goods/index', ['id'=>$v['id']]);?>" class="figure"><img src="/Uploads<?php echo ($v['pic1']); ?>" alt="" /></a>
                    <div class="name"><a href=""><?php echo ($v['gname']); ?></a></div>
                    <div class="price">￥<?php echo ($v['nowPrice']); ?></div>
                </li><?php endforeach; endif; ?>
        </ul>
        <!-- 商品推荐 -->

        <div class="detail-bottom clearfix">
             <div class="detail-main">
                <div class="detail-tabs">
                    <a class="item" href="javascript:;">详情描述</a>
                    <a class="item" onclick="getDes(<?php echo ($good['id']); ?>)" href="javascript:;">规格参数</a>
                    <a class="item" href="javascript:;">商品评价</a>
                </div>
                <div class="tab-con">
                    <div class="mod-type-cont">
                        <img src="/Uploads<?php echo ($good['dress1']); ?>" alt="" />
                        <img src="/Uploads<?php echo ($good['dress2']); ?>" alt="" />
                        <img src="/Uploads<?php echo ($good['dress3']); ?>" alt="" />
                        <img src="/Uploads<?php echo ($good['dress4']); ?>" alt="" />
                    </div>
                </div>
                <div class="tab-con">
                    <div class="detail-stand">
                        <div class="tit">主体规格参数</div>
                        <div class="attr">
                            <div class="name">商品描述</div>
                            <div class="value"><span class="lz-des"></span></div>
                        </div>
                        <div class="attr">
                            <div class="name">季节描述</div>
                            <div class="value"><span class="lz-season"></span></div>
                        </div>
                        <div class="attr">
                            <div class="name">风格描述</div>
                            <div class="value"><span class="lz-style"></span></div>
                        </div>
                        <div class="attr">
                            <div class="name">品牌描述</div>
                            <div class="value">361°</div>
                        </div>
                        <div class="attr">
                            <div class="name">购买指南</div>
                            <div class="value"><span class="lz-guide"></span></div>
                        </div>
                    </div>
                </div>

                <!-- 定位评价 -->
                <div class="tab-con">
                    <div class="detail-pj">
                        <div class="detail-pj-nav list clearfix">
                            <div class="col col1">评价心得</div>
                            <div class="col col2">满意度</div>
                            <div class="col col3">商品信息</div>
                            <div class="col col4">评价用户</div>
                        </div>
                        <!-- 自定义div定位 -->
                        <div class="jin-comment">
                            <?php if(empty($comment)): ?><div class="detail-pj-cont">
                                    <span>暂无评价哦~</span>
                                </div>
                            <?php else: ?>
                                <?php if(is_array($comment)): foreach($comment as $key=>$v): ?><div class="detail-pj-cont">
                                        <div class="reply list clearfix">
                                            <div style="position: relative;top:-10px;" class="col col1"><?php echo ($v['content']); ?><br>
                                                <?php echo ($v['img1']); echo ($v['img2']); echo ($v['img3']); ?>
                                                <br><span style="position: relative;left:-10px;" class="time"><?php echo ($v['addtime']); ?></span>
                                            </div>
                                            <div class="col col2">
                                                <img style="width:35px;height:20px;" src="/Uploads/<?php echo ($v['level_img']); ?>">[<?php echo ($v['level']); ?>]
                                            </div>
                                            <div class="col col3">尺码:<?php echo ($v['size']); ?><br>颜色分类：<?php echo ($v['color']); ?></div>
                                            <div class="col col4"><img src="<?php echo ($v['user_img']); ?>" class="hdpic" width="40" height="40" src=""><div><?php echo ($v['nikname']); ?></div></div>
                                        </div>

                                    </div><?php endforeach; endif; endif; ?>
                        </div>
                        <!-- 分页定位 -->
                        <div class="jin-page" style="position:relative;left:20px;">
                            <?php echo ($btn); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="detail-aside">
                <div class="detail-aside-box mb15">
                    
                </div>

                <div class="detail-aside-box">
                    <div class="big-tit">同类热销</div>
                    <ul class="detail-hot">
                    <?php if(is_array($buyGood)): foreach($buyGood as $key=>$v): ?><li>
                            <a href="" class="figure"><img src="/Uploads<?php echo ($v['pic1']); ?>" alt="" /></a>
                            <div class="name"><a href=""><?php echo ($v['gname']); ?></a></div>
                            <div class="price">
                                <span class="now">¥<?php echo ($v['nowPrice']); ?></span><span class="origin"><del>¥<?php echo ($v['price']); ?></del></span>
                            </div>
                        </li><?php endforeach; endif; ?>
                    </ul>
                </div>
            </div>
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
    

<script>
<?php  if (make) : ?>
        var tim = $('.lz-time').attr('val');
        var nowH= parseInt(tim / 3600);
        var nowM = parseInt((tim - nowH * 3600) / 60);
        var nowS = (tim - nowH * 3600 - nowM * 60);
        var nowtime = nowH+':'+nowM+':'+nowS;
        console.log(nowtime);
        $('.lz-time').html('剩余时间：'+nowtime);
        var timer = setInterval(function() {
            var tim = $('.lz-time').attr('val');
            var nowH= parseInt(tim / 3600);
            var nowM = parseInt((tim - nowH * 3600) / 60);
            var nowS = (tim - nowH * 3600 - nowM * 60);
            var nowtime = nowH+':'+nowM+':'+nowS;
            $('.lz-time').attr('val', tim-1)
            $('.lz-time').html('剩余时间：'+nowtime);
            if (tim <= 0) {
                location.replace(location.href);
                clearInterval(timer);  
            }
        }, 1000);
<?php endif;?>

//评价的js 评价分页样式
$('.jin-page a, .jin-page span').unwrap('<div></div>').wrap('<li></li>').parent().parent().wrapInner('<ul class="pagination"></ul>');
$('.jin-page').find('.current').css({'color':'white', 'backgroundColor':'red'});

$('body').delegate('.jin-page a', 'click', function() {
    var url = $(this).attr('href');
    $.ajax({
        url: url,
        type: 'get',
        async: true,
        success: function(res) {
            var div = '';
            $('.jin-page').html(res.btn);
            $('.jin-page a, .jin-page span').unwrap('<div></div>').wrap('<li></li>').parent().parent().wrapInner('<ul class="pagination"></ul>');
            $('.jin-page').find('.current').css({'color':'white', 'backgroundColor':'red'});
            delete res.btn;

            for (var k in res) {
                div += '<div class="detail-pj-cont"><div class="reply list clearfix"><div style="position: relative;top:-10px;" class="col col1">'+res[k].content+'<br>'+res[k].img1+''+res[k].img2+''+res[k].img3+'<br><span style="position: relative;left:-10px;" class="time">'+res[k].addtime+'</span></div><div class="col col2"><img style="width:35px;height:20px;" src="/Uploads'+res[k].level_img+'">['+res[k].level+']</div><div class="col col3">尺码:'+res[k].size+'<br>颜色分类：'+res[k].color+'</div><div class="col col4"><img src="'+res[k].user_img+'" class="hdpic" width="40" height="40" src=""><div>'+res[k].nikname+'</div></div></div></div></div>';
            }
            $('.jin-comment').html(div);
        },
        error: function(res) {
            alert(res);
        }
    });
    return false;
});



// 上面是lzz
/*加入购物车*/
$("#add-cart").click( function () {
    // console.log('添加商品'); 
    var gid = $("#lz-gid").val();
    var propid = $("#lz-propid").val();
    var size = $("#lz-size").val();
    var color = $("#lz-color").val();
    var num = $("#lz-num").val();
    var gname = $("#lz-gname").html();
    var price = $("#lz-price").html();
    // console.log('gid:'+gid,'propid:'+propid,'size:'+size,'color:'+color,'num:'+num,'gname:'+gname,'price:'+price);
        $.ajax({
        type: "POST",
        url: "<?php echo U('Cart/addCart');?>",
        //要发送的数据（参数）格式为{'val1':"1","val2":"2"}
        data: {gid:gid,propid:propid,size:size,color:color,num:num,gname:gname,price:price},
        dataType: "json",
        success: function(msg){
            top_total = $('.cart-num').html();
            $('.cart-num').html(Number(top_total)+Number(num));
            alert(msg);
            // console.log(msg);
            location.reload();
        }
    });
});
/*加入收藏夹*/
$("#add-fav").click( function () {
    console.log(this);
    fav = this;
    var gid = $("#lz-gid").val();
        $.ajax({
        type: "POST",
        url: "<?php echo U('Goods/addFav');?>",
        data: {gid:gid},
        dataType: "json",
        success: function(msg){
            if (msg.status === 1) {
                $(fav).html('已收藏');
                console.log(msg.msg);
            } else {
                $(fav).html('<i class="iconfont icon-star"></i>收藏</a>');
                console.log(msg.msg);
            }
        }
    });
});



$('.chose-common li:first a, .chose-img li:first a').addClass('active').find('input').attr('checked', true); // 默认选中颜色尺寸

$("body").css({'background-color': "#fff" }); // 更改 背景颜色


var gid = $('#lz-gid').val(); //找到对应的商品id的值

//默认传颜色对象
getStore($('#lzz-div-color .active').get(0));

function getStore (obj, size) {
    if (size === undefined) { //点的颜色
        var size = $('#lzz-div-size .active').attr('val');
        var color = $(obj).attr('val');

        $('#lzz-div-color a').removeAttr("class");
        $(obj).attr('class', 'active');
    } else {
        var color = $('#lzz-div-color .active').attr('val');

        $('#lzz-div-size a').removeAttr("class");
        $(obj).attr('class', 'active');
    }

    console.log(gid,color,size);
    var da = {
        'color' : color,
        'size' : size,
        'gid' : gid
    } ;
    $.ajax({
        url: "<?php echo U('Goods/getStore');?>",
        type: 'post',
        data: da,
        success: function(res){
            if (res == 0 || res['store'] == 0) {
                res = 0;
                $('#lz-num').attr('value',0); 
                $('#max-store').html('<span style="color:red">（暂无库存）</span>');
                $('#max-num').val(0);
            } else {
                var propid = res['id'];
                res = res['store'];
                res = Number(res);
                $('#lz-num').attr('value', 1); 
                $('#max-store').html('（若库存足够，仅限购10件）');
                $('#max-num').val(1);

            }
                goodstore = res;


           $('#store').html('(库存'+res+'件)'); 
           $('#max-num').attr('max',res); 
           $('#max-num').attr('value', 1); 
           $('#data-max').attr('data-max',res+1); 
           $('#lz-propid').attr('value',propid); 
           $('#lz-color').attr('value',color); 
           $('#lz-size').attr('value',size); 
            
        } 
    });
}



$('#lzz-inr').click(function() {
    var lzznum = Number($('#max-num').attr('value'));
    var maxnum = Number($('#max-num').attr('max'));
    if (lzznum >= maxnum) {
        $('#max-num').html(goodstore); 
        $('#max-num').attr('value', goodstore); 
        alert('超出库存');
        return;
    }
    $('#max-num').attr('value', lzznum+1); 
    $('#max-num').val(lzznum+1); 
    $('#lz-num').attr('value', lzznum+1); 
});

$('#lzz-dec').click(function() {
    var lzznum = Number($('#max-num').attr('value'));
    if (lzznum <= 0 ) {
        $('#max-num').val(0); 
        $('#max-num').attr('value', 0); 
        return;
    }
    $('#max-num').attr('value', lzznum-1);
    $('#max-num').val(lzznum-1);
    $('#lz-num').attr('value', lzznum-1); 
});

var arr ;
console.log(arr);
var marke = 1;
function getDes(id) {
    if (arr === undefined) {
        var id = $('#lz-gid').val();
        $.ajax({
        url: "<?php echo U('Goods/getDes');?>",
        type: 'post',
        data: 'id='+id,
        async : false,
        success: function(res){
            console.log(res);
            if (res != 0) {
                arr = res;
            }
        } 
    });
    } 
    if (marke > 0) {
        
        $('.lz-des').html(arr.des);
        $('.lz-season').html(arr.season);
        $('.lz-style').html(arr.style);
        $('.lz-guide').append('<img style="width:700px" src="/Uploads'+arr.guide+'">');
        marke = -marke;
    }   

}



    /*商品数量操作*/
    function goodsCount(o){
            if(!(o instanceof Object)) var o={};
            var inputCell = o.inputCell || ".count-input",
                minusCell = o.minusCell || ".count-minus",
                plusCell = o.plusCell || ".count-plus",
                disClass = o.disClass || "disabled";
            return this.each(function(){
                var $wrap = $(this),
                    $input = $(inputCell,$wrap),
                    $minus = $(minusCell,$wrap),
                    $plus = $(plusCell,$wrap),
                    maxnum=parseInt($wrap.attr('data-max')) || false,
                    minnum=$wrap.attr('data-min') || 1,
                    initnum=$input.val() || minnum;
                /*初始*/
                $input.val(initnum);
                checkIlegal();
                function checkIlegal(){
                    var value =parseInt($input.val());

                    //
                     if (maxnum&&value>maxnum) {
                        $input.val(maxnum);
                    }
                    else if (value<minnum) {
                        $input.val(minnum);
                    }
                    if(value<=minnum){
                        $minus.addClass(disClass);
                    }else{
                        $minus.removeClass(disClass);
                    }
                    if (value>=maxnum) {
                        $plus.addClass(disClass);
                    }else {
                        $plus.removeClass(disClass);
                    }

                }
                function checknull() {
                    var value =$input.val();
                    if(value === "" || value === "0"){
                        $input.val(minnum);
                    }
                }
                $input.keyup(function(evt){
                    var value = $(this).val();
                    var newvalue = value.replace(/[^\d]/g,"");
                    $(this).val(newvalue);
                    checknull();
                });
                $input.blur(function(){
                    checknull();
                    checkIlegal();
                })

                $minus.click(function(){
                    minus();
                     checkIlegal();
                });

                $plus.click(function(){
                    add();
                    checkIlegal();
                });

                function add () {
                    var value = $input.val();
                    var plus = parseInt(value)+1;
                    $input.val(plus);
                }
                function minus () {
                    var value = parseInt($input.val());
                    var minus = value-1;
                    $input.val(minus);
                }
            });
        }
        $.fn.goodsCount = goodsCount;
</script>

<script >
    $(function () {

        +function () {
            var index=0,
            bsrc='',
            timer=null,
            box=$('.detail-show'),
            origin=$('.origin-show'),
            bigimg=box.find('.big-pic'),
            tumb=box.find('.thumb-show'),
            tumbItem=tumb.find('.item'),
            zoomup=box.find('.zoomup'),
            zoomshow=box.find('.zoom-show');

            /*图片切换*/
            tumbItem.on('mouseenter',function () {
                index=$(this).index();
                clearTimeout(timer);
                timer=setTimeout(function (){
                    update(index);
                }, 300)

            });

            function update (index) {
                bsrc=tumbItem.eq(index).find('.s-pic').attr('bsrc');
                bigimg.attr('src', bsrc);
                tumbItem.find('.s-pic').removeClass('active').end().eq(index).find('.s-pic').addClass('active');
            }

            update(index);

            if ($('.detail-show .thumb-show .item').length>5) {
                $('.detail-show .thumb-show').slick({
                    slidesToShow: 5,
                    infinite:false
                });
            }

            /*放大镜*/
            origin.on('mouseover mouseout',function (e) {
                if (e.type=="mouseover") {
                    var oX=$(this).offset().left,
                    oY=$(this).offset().top,
                    zX=e.pageX,
                    zY=e.pageY,
                    pW=$(this).outerWidth(),
                    pH=$(this).outerHeight(),
                    zW=zoomup.outerWidth(),
                    zH=zoomup.outerHeight(),
                    scale=pW/zW,
                    zsW=zoomshow.width()*scale,//放大后的宽度
                    factor=zsW/pW

                    zoomshow.find('img').attr('src',bigimg.attr('src')).width(zsW);

                    $(document).on('mousemove.zoom',function (e) {
                        zX=e.pageX-oX- zW/2;
                        zY=e.pageY-oY- zH/2;
                        move();
                    });

                    function move () {
                        zX=zX<=0?0:zX;
                        zX=zX>=pW-zW?pW-zW:zX;
                        zY=zY<=0?0:zY;
                        zY=zY>=pH-zH?pH-zH:zY;
                        zoomup.show().css({top:zY,left:zX});
                        zoomshow.show().find('img').css({top:-zY*factor,left:-zX*factor});
                    }
                }
                else {
                    $(document).off('mousemove.zoom');
                     zoomup.hide()
                     zoomshow.hide();
                }
            });
        }();

        $('.mod-numbox').goodsCount(); //数量加减

        $('.detail-main').zTab({
            tabnav:'.detail-tabs',
            trigger:'click'
        });
    });

/*tab切换*/
(function ($) {
  $.fn.zTab=function(options) {
   var dft={
      tabnav:'.tab-nav',          //导航按钮元素
      tabcon:'.tab-con',          //被切换元素
      trigger:'mouseenter', //触发方式，默认点击触发
      curName:'active', //给高亮设置类名
      removeMod:null,     //改为触发时移除导航的类名
      cur:0,                //初始高亮的顺序，默认第一个
      delay:0,              //触发延时
      auto:null,           //是否自动改变
      after: null ,      //回调
      first:null             //首次加载时执行
    };

    var ops=$.extend(dft,options);
    return this.each(function () {
      var self=$(this),
      nav=self.find(ops.tabnav),
      con=self.find(ops.tabcon),
      navBtn=nav.children(),
      num=navBtn.length,
      timer=null,
      timer2=null,
      isInit=false;

      //初始化执行
      init();

      navBtn.on(ops.trigger,function () {
        ops.cur=$(this).index();
        clearTimeout(timer);
        clearTimeout(timer2);
        timer=setTimeout(run,ops.delay);
        return false;
      });

      navBtn.on('mouseleave',function () {
        clearTimeout(timer);
        if (ops.auto) {
          timer2=setInterval(auto,ops.auto.interval);
        }
      });
      //
      function init () {
        ops.trigger=='click'?ops.trigger='click':ops.trigger='mouseenter click'; //导航触发方式判定
        run();
        if (ops.auto) {
          timer2=setInterval(auto,ops.auto.interval);
        }
        else {
          run();
        }

        if(ops.first){
          ops.first(self,ops.cur,num);
        }

        isInit=true;
      }
      //
      function run () {
        if (ops.removeMod) {
          navBtn.addClass(ops.curName).eq(ops.cur).removeClass(ops.curName); //
        }
        else {
          navBtn.removeClass(ops.curName).eq(ops.cur).addClass(ops.curName); //
        }

          con.hide().eq(ops.cur).show(); //

         if(ops.after&&isInit){
          ops.after(ops.cur,ops);
        }
      }
      //
      function auto () {
        ops.cur+=1;
        if (ops.cur==num) {ops.cur=0;}
        run();
      }

    });
}
})(jQuery);
</script>

    </html>