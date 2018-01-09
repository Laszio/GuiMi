<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-siteapp">
 
    <meta name="keywords" content="">
    <meta name="description" content="">
    <link href="/Public/Home/css/iconfont/iconfont.css" rel="stylesheet"/>
    <link href="/Public/Home/css/common.css" rel="stylesheet"/>
    <link href="/Public/Home/css/login.css" rel="stylesheet"/>

</head>
<body>
<!--头部-->
    <div class="login-header">
        <div class="wrapper">
            <a href="" class="logo">
                <img src="/Public/Home/img/logo3.png" alt="" />
            </a>
            <div class="zp">
                <span class="ico"></span>
                <div>正品保障</div>
            </div>
        </div>
    </div>


    <div class="login-main-wrap">
        <div class="login-main wrapper">
            <div class="login-box" style=''>
                <form action="" method="post">
                    <div class="box-hd">
                        <span class="tit">用户登录</span>
                        <a href="<?php echo U('Register/index');?>">注册新账号</a>
                    </div>
                    <label class="txtin-box">
                        <span class="ico user"></span>
                        <input class="txtin" type="text" name="character" placeholder="用户名/手机/email" />
                    </label>
                    <label class="txtin-box">
                        <span class="ico pwd"></span>
                        <input class="txtin" type="password" name="pass" placeholder="密码" />
                    </label>
                    <?php if($btn == 1): ?><label class="txtin-box" >
                        <span style='position:absolute;left:16px;top:6px;font-size:20px;color:red;' class='show'>X</span>
                        <input class="txtin" style='width:190px;float:left;margin-right:10px' type="text" name="verify" placeholder="验证码" />
                        <img src="<?php echo U('Login/createVerify');?>" class="get-yzm" style='width:130px;height:42px'><?php endif; ?>
                    </label>
                    <div class="clearfix tool">
                        <label class="check"><input type="checkbox" id="auto_login" name='auto'
                         value='1' />自动登录</label>
                        <a class="find" href="<?php echo U('Register/forgot');?>">忘记密码？</a>
                    </div>
                    <input class="tj" type="submit" value="登&ensp;录" />
                    <div class="other-way clearfix">
                        <a class="item first" href="<?php echo U('QQLogin/qqLogin');?>">
                            <img src="/Public/Home/img/login/qq.jpg" alt="" class="ico" style='width:55px'/>
                            <span class="label">QQ</span>
                        </a>
                        <a class="item" href="">
                            <img src="/Public/Home/img/login/weixin.jpg" alt="" class="ico" style='width:55px'/>
                            <span class="label">微信</span>
                        </a>
                        <a class="item" href="">
                            <img src="/Public/Home/img/login/sina.jpg" alt="" class="ico" style='width:55px'/>
                            <span class="label">新浪</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
   


    <div class="login-footer">
        安徽XXX网络科技有限公司 版权所有 Copyright © 2016-2018   备案号：皖ICP备123456789
        <div class="authentication">
            <a href=""><img src="/Uploads/images/35.jpg" alt="" /></a>
            <a href=""><img src="/Uploads/images/36.jpg" alt="" /></a>
            <a href=""><img src="/Uploads/images/37.jpg" alt="" /></a>
            <a href=""><img src="/Uploads/images/38.jpg" alt="" /></a>
        </div>
    </div>
</body>
<script src="/Public/Home/js/jquery.js"></script>
<link rel="stylesheet" href="/Public/Home/js/icheck/style.css"/>
<script src="/Public/Home/js/icheck/icheck.min.js"></script>
<script src="/Public/Home/js/global.js"></script>
<script>
     $('.check input').iCheck({
            checkboxClass: 'sty1-checkbox'
        });
</script>

    <script>

//验证验证码
$("input[name=verify]").on('blur',function ()
        {
            if ($(this).siblings('.show').html() != '√') {
               
                $.ajax({
                    type:'POST',
                    data:{parm:$(this).val()},
                    url:'<?php echo U('Login/createVerify');?>',
                    success:function (data) {
                        if (data.status == 1)
                        $("input[name=verify]").siblings('.show').html('√');
                        $("input[name=verify]").siblings('.show').css({'color':'green'});
                    },
                });
            }

        });

    //验证码刷新
    $('.get-yzm').on('click',function(){
        if($("input[name=verify]").siblings('.show').html() == 'X') {

            this.src = "<?php echo U('Login/createVerify?rand=');?>";

        }
    });

    //登录前判断是否验证
    $('input[type=submit]').on('click',function () 
        {
            
            if ($("input[name=verify]") != undefined) {
                if ($("input[name=verify]").siblings('.show').html() == 'X') {
                    alert('请输入验证码');
                    return false;
                }
            }
        });
    </script>

   
</html>