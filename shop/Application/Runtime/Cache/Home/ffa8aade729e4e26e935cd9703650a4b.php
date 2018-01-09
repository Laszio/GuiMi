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
                <a href="index.html"><img src="img/logo.png" alt="logo" /></a>
            </div>
            <div class="step-box">
                <div class="row">
                    <div class="step first active col-3">
                        <span class="num">1</span><span class="line"></span><span class="label">我的购物车</span>
                    </div>
                    <div class="step active col-3">
                        <span class="num">2</span><span class="line"></span><span class="label">确认订单信息</span>
                    </div>
                    <div class="step col-3">
                        <span class="num">3</span><span class="line"></span><span class="label">选择支付方式</span>
                    </div>
                    <div class="step last col-3">
                        <span class="num">4</span><span class="line"></span><span class="label">完成付款</span>
                    </div>
                </div>
            </div>
        </div>
    <!--头部-->
<form action="<?php echo U('Order/pay');?>" method="post">
    <div class="wrapper confirm-wrap">
        <div class="confirm-tit">
            <span class="tit">选择收货地址 :</span>
        </div>
        <div class="confirm-address clearfix">
            <?php if(is_array($addressList)): foreach($addressList as $key=>$v): ?><label>
                    <div class="col col-4" >
                        <div class="item <?php echo ($v['state'] == 2 ? 'active': ''); ?>" >
                            <input type="radio" <?php echo ($v['state'] == 2 ? 'checked': ''); ?> style="display: none;" name="aid" value="<?php echo ($v['id']); ?>">
                            <div class="action"  val='<?php echo ($v["id"]); ?>'>

                                <?php echo ($v['state'] == 2 ? '': "<a class='setdft lz-default' href='javascript:;'>设为默认</a>"); ?>
                            </div>
                            <div class="info">
                                <div class="ellipsis"><img src="img/ico/user.jpg" alt="" /><?php echo ($v['area']); ?>（<?php echo ($v['name']); ?> 收）</div>
                                <div class="ellipsis"><img src="img/ico/dizhi.jpg" alt="" /><?php echo ($v['address']); ?></div>
                                <div class="ellipsis"><img src="img/ico/tel.jpg" alt="" /><?php echo ($v['phone']); ?></div>
                            </div>
                        </div>
                    </div>
                </label><?php endforeach; endif; ?>
            <div class="col col-4">
                <a class="item va-m-box ta-c add" href="<?php echo U('Address/index');?>">
                    <div class="add-new">
                        <span class="ico"><i class="iconfont icon-tianjia"></i></span>
                        <div class="label">添加收货地址</div>
                    </div>
                </a>
            </div>
        </div>
        <div class="confirm-address-bar"  style="display: none;">
            <a href="javascript:;" class="drop" data-action="drop">显示全部地址</a>
        </div>
    

        <div class="confirm-tit">
           <span class="tit">确认商品信息:</span><div class="right"><a class="back" href="<?php echo U('Cart/index');?>">返回购物车></a></div>
        </div>
        <!-- 订单信息 -->
        <?php $i = 0; ?>
        <?php if(is_array($goodsList)): foreach($goodsList as $key=>$goods): ?><input type="hidden" value="<?php echo ($goods['propid']); ?>" name="goods[<?php echo ($i); ?>][propid]">
        <input type="hidden" value="<?php echo ($goods['gid']); ?>" name="goods[<?php echo ($i); ?>][gid]">
        <input type="hidden" value="<?php echo ($goods['size']); ?>" name="goods[<?php echo ($i); ?>][size]">
        <input type="hidden" value="<?php echo ($goods['color']); ?>" name="goods[<?php echo ($i); ?>][color]">
        <input type="hidden" value="<?php echo ($goods['num']); ?>" name="goods[<?php echo ($i); ?>][num]">
        <input type="hidden" value="<?php echo ($goods['colorpic']); ?>" name="goods[<?php echo ($i); ?>][colorpic]">
        <div class="confirm-goods">
            <div class="confirm-goods-hd clearfix">
                <div class="col col1">商品简介：</div>
                <div class="col col2">单价（元）</div>
                <div class="col col3">数量</div>
                <div class="col col4">小计（元）</div>
            </div>
            <div class="confirm-goods-bd">
                <div class="goods clearfix">
                    <div class="col col1">
                        <img src="/Uploads<?php echo $goods['colorpic'];?>" alt="">
                        <div class="info">
                            <div class="name"><?php echo ($goods['gname']); ?></div>
                            <div class="meta"><span><?php echo ($goods['color']); ?>　</span> <span>　<?php echo ($goods['size']); ?></span></div>
                        </div>
                    </div>
                    <div class="col col2">￥<?php echo ($goods['price']*$goods['discount']/100); ?></div>
                    <div class="col col3"><?php echo ($goods['num']); ?></div>
                    <div class="col col4">￥<?php echo ($goods['price']*$goods['discount']*$goods['num']/100); ?></div>
                </div>
            </div>
            <div class="confirm-goods-ft clearfix">
            </div>
        </div>
        <?php $i++; endforeach; endif; ?>
         <div class="confirm-total">
            <div class="fl"><span class="vm-ib">买家留言： </span><textarea class="ui-txtin" style="width: 410px;height: 100px" name="text" placeholder="在此输入给商家的留言"></textarea></div>
            <div class="box">
                <div class="item">应付总额：<strong> ¥ <?php echo $allTotalPrice;?></strong></div>
                <button class="ui-btn-theme go-charge" href="<?php echo U('Order/pay');?>">去结算</button>
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
    
<link rel="stylesheet" href="/Public/Home/js/icheck/style.css"/>
<script src="/Public/Home/js/icheck/icheck.min.js"></script>
<script src="/Public/Home/js/layer/layer.js"></script>
<script src="/Public/Home/js/global.js"></script>
<script>
//这里不使用自带按钮，因为设计按钮较特殊，不准备作为通用样式
$('input:radio').change(function() {
    console.log(this);
    $('input:radio').parent().removeClass("active");
    $(this).parent().addClass('active');
});

$('.lz-default').click(function() {
    var val = $(this).parent().attr('val');
    $.ajax({
        url:"<?php echo U('Address/setDefault');?>",
        data:'id='+val,
        type:'get',
        success:function (res) {
            console.log(res);
            if (res !== 0) {
                window.location.reload();
            }
        }
    });
});

//
function juggeAddressNum () {
    var col=$('.confirm-address .col'),
    cH=col.height();
    $('.confirm-address').height(cH)
    if (col.length>3) {
        $('.confirm-address-bar').show();
    }
    else {
        $('.confirm-address-bar').hide();
    }
}
juggeAddressNum();
zAction.add({
    'drop':function () {
        $('.confirm-address').height('auto')
        var h=$('.confirm-address').height()
        juggeAddressNum();
        $('.confirm-address').animate({height: h}, 300);
        $(this).parent().remove();
    }
});
</script>

    </html>