
/**
 * 获取信息并填入
 * @Author   ryan
 * @DateTime 2017-12-01
 * @email    931035553@qq.com
 * @param    {[type]}         obj [description]
 * @param    {[type]}         id  [description]
 * @return   {[type]}             [description]
 */
function getMsg(obj,id)
{
    if (obj.attr('data') == undefined) {
        $.ajax({
            type:'get',
            data:{id:id},
            url:'/index.php/Home/Index/bar.html',
            success:function (data)
            {
                if (data) {
                    obj.children('.c-box').css({'display':''});
                    obj.attr('data',data);
                    for (k in data) {

                        str = "<a href='/index.php/Home/List/index/tid/"+data[k]['id']+"'>"+data[k]['name']+"</a>";
                        
                        msg = obj.find('.list-wrap').append(str);
                    }
                    $('#container .content').scrollTop($('#container .content')[0].scrollHeight); 
                }
            }
        });
    }
}


window.onload = function(){
     //初始值用于判断是否需要清空数据
    var ms = 0;
    //用户的uid用于用于连接标识
    var uid;
    var errormsg;
    // 定义连接标识
    var socket;
    //定义聊天图标
    var arrIcon = 'http://www.xttblog.com/wp-content/uploads/2016/03/123.png';
    var btn = document.getElementById('btn');
    var text = document.getElementById('text');
    var root = '/index.php';
    //点击关闭事件
     $('#closebox').on('click',function () {
        ms = 0;
        $('.chatbox').css({'display':'none'});
        return false;
     });

    //发送消息事件
    btn.onclick = function(){

        if(text.value ==''){
            alert('不能发送空消息');
        }else {
            $('.content').append ( '<li><img class="imgleft" src="'+arrIcon+'"><span class="spanleft">'+text.value+'</span></li>');       
            text.value = '';
            // 内容过多时,将滚动条放置到最底端
            var msg = $('.content li:last').text();
            $('#container .content').scrollTop($('#container .content')[0].scrollHeight); 

            $.ajax({
                    type:'post',
                    data:{                            
                        msg:msg,
                    },
                    url:root + '/Home/Chat/chating.html',
                    success:function (data) {
                        
                    }

                });       
        }
    }

//点击客服弹出聊天框
 $('.kefu').on('click',function(){
             ms = 1;
            $('.chatBox').css('display','block');
            
            $('#online').html('&#xe61d;');
             $.ajax({
                    type:'post',
                    data:{uid:uid},
                    url:root + '/Home/Chat/clearUnread.html',
                    success:function (data) {
                        if (data.status === 0) { 
                            alert(data.info);
                            location.href = data.url;
                        }
                    },          
                });
             $('#container .content').scrollTop($('#container .content')[0].scrollHeight); 
            return false;
        });


// 创建一个Socket实例
 if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
// 如果浏览器不支持websocket，会使用这个flash自动模拟websocket协议，此过程对开发者透明
WEB_SOCKET_SWF_LOCATION = "/Web/swf/WebSocketMain.swf";
//创建websocket
socket = new WebSocket('ws://120.78.175.126:30000'); 
// 打开Socket 
socket.onopen = function(event) { 

    //设置定时器
    errormsg = setTimeout(function(){
        alert('客服连接失败');
    },2000);
    //监听信息
    socket.onmessage = function(event) { 
        clearTimeout(errormsg);
        data = JSON.parse(event.data);
        //判断消息类型
        switch (data.type)
        {
            //初始化信息
            case 'init':

                $.ajax({
                    type:'post',
                    data:{id:data.id},
                    url:root + '/Home/Chat/bindUid.html',
                    success:function (data) {
                        //全局变量赋值
                        uid = data.uid;
                        
                        if (ms == 0) {//ms为0则表示未点击查看
                            if (data.unread == 0) {
                                $('#online').html('&#xe61d;');                             
                            } else {
                                $('#online').html(data.unread);
                            }

                        } else {//ms为1表示查看，清除未读信息
                            $.ajax({
                                type:'post',
                                data:{uid:data.uid},
                                url:'/Home/Chat/bindUid.html',
                                success:function () {},          
                            });
                        }
                        //数据资料
                        data = data['msg'];
                        for (k in data) {
                            res = data[k].split('-@@-',2);

                            //添加数据并赋予样式
                            if (res[0] == 'user') {
                                $('.content').append('<li><img class="imgleft" src="'+arrIcon+'"><span class="spanleft">'+res[1]+'</span></li>');
                            } else {
                                $('.content').append('<li><img class="imgright" src="'+arrIcon+'"><span class="spanright">'+res[1]+'</span></li>');
                            }
                        }
                        //移至聊天框底部
                        $('#container .content').scrollTop($('#container .content')[0].scrollHeight); 
                    }
                });

                break;
            //邮箱
            case 'email':
                var number = $('#email').text().match(/\d/);
                number++;
                $('#email').text('('+number +')');
                break;

            //默认为聊天
            default :
                //判断是否需要清空数据

                if (ms == 0) {
                    if (data.unread == 0) {
                        $('#online').html('&#xe61d;');                             
                    } else {
                        $('#online').html(data.unread);
                    }
                } else {
                    $.ajax({
                        type:'post',
                        data:{uid:uid},
                        url:root + '/Home/Chat/clearUnread.html',
                        success:function () {},          
                    });
                }
                //往聊天框添加数据
                $('.content').append('<li><img class="imgright" src="'+arrIcon+'"><span class="spanright">'+data.msg+'</span></li>');
                //移至底部
                $('#container .content').scrollTop($('#container .content')[0].scrollHeight); 
        }
    };


    // 监听Socket的关闭
    socket.onclose = function(event) { 

        console.log(event.data); 
    }; 
};

}
