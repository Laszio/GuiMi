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

    
    
    
        <div class="cart-header wrapper">
            <div class="logo">
                <a href="index.html"><img src="/Public/Home/img/logo.png" alt="logo" /></a>
            </div>
            <div class="step-box">
                <div class="row">
                    <div class="step first active col-3">
                        <span class="num">1</span><span class="line"></span><span class="label">我的购物车</span>
                    </div>
                    <div class="step active col-3">
                        <span class="num">2</span><span class="line"></span><span class="label">确认订单信息</span>
                    </div>
                    <div class="step active col-3">
                        <span class="num">3</span><span class="line"></span><span class="label">选择支付方式</span>
                    </div>
                    <div class="step last col-3">
                        <span class="num">4</span><span class="line"></span><span class="label">完成付款</span>
                    </div>
                </div>
            </div>
        </div>
    <!--头部-->

    <div class="wrapper">
        <div class="pay-wrap">
            <div class="order-result">
                <div class="section clearfix">
                    <img src="/Public/Home/img/ico/order-success.jpg" class="ico" />
                    <div class="titbox">
                        <div class="tit">订单提交成功，应付金额 <?php echo ($totalMoney); ?> 元</div>
                        <div class="stit">订单号：<?php echo ($order['id']); ?>    请您在 1 日 内完成支付，否则订单会被自动取消</div>
                    </div>
                    <div class="mt20">
                        <?php if(is_array($detailInfo)): foreach($detailInfo as $key=>$v): ?><div class="meta">
                                <div class="hd">商品</div>
                                <div class="bd"><?= $v['gname'].'　'.$v['color'].'-'.$v['size'].' * '.$v['num']?></div>
                            </div><?php endforeach; endif; ?>
                        <div class="meta">
                            <div class="hd">收货地址</div>
                            <div class="bd"><?= $order['address'].'('.$order['getman'].'收)　'.$order['phone']?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pay-wrap-tit">
            在线支付
        </div>
        <div class="pay-wrap">
            <div class="pay-way">
                <div class="row">
                    <div class="col col-3">
                        <label><input class="check" type="radio" name="a" /><img src="/Public/Home/img/pay/zfb.jpg" /></label>
                    </div>
                    <div class="col col-3">
                        <label><input class="check" type="radio" name="a" /><img src="/Public/Home/img/pay/weixin.jpg" /></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-3">
                        <label><input class="check" type="radio" name="a" /><img class="bd" src="/Public/Home/img/pay/zgyh.jpg" /></label>
                    </div>
                    <div class="col col-3">
                        <label><input class="check" type="radio" name="a" /><img class="bd" src="/Public/Home/img/pay/jsyh.jpg" /></label>
                    </div>
                    <div class="col col-3">
                        <label><input class="check" type="radio" name="a" /><img class="bd" src="/Public/Home/img/pay/nyyh.jpg" /></label>
                    </div>
                    <div class="col col-3">
                        <label><input class="check" type="radio" name="a" /><img class="bd" src="/Public/Home/img/pay/gsyh.jpg" /></label>
                    </div>
                    <div class="col col-3">
                        <label><input class="check" type="radio" name="a" /><img class="bd" src="/Public/Home/img/pay/jtyh.jpg" /></label>
                    </div>
                    <div class="col col-3">
                        <label><input class="check" type="radio" name="a" /><img class="bd" src="/Public/Home/img/pay/zsyh.jpg" /></label>
                    </div>
                    <div class="col col-3">
                        <label><input class="check" type="radio" name="a" /><img class="bd" src="/Public/Home/img/pay/yzcxyh.jpg" /></label>
                    </div>
                    <div class="col col-3">
                        <label><input class="check" type="radio" name="a" /><img class="bd" src="/Public/Home/img/pay/xyyy.jpg" /></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom-panel">
            <a href="/Home/Order/okPay/oid/<?php echo ($order[id]); ?>" class="go-next ui-btn-theme">下一步</a>
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
    
        
    
    </html>