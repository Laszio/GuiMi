window.onload = function(){
    //图标
    var cusIcon = 'http://www.xttblog.com/icons/favicon.ico';
    var userIcon = 'http://www.xttblog.com/icons/favicon.ico';
    var root = '/index.php';
    var socket; 
    var errormsg;
    var icon = document.getElementById('user_face_icon').getElementsByTagName('img');
    var btn = document.getElementById('btn');
    var text = document.getElementById('text');
    var content = document.getElementsByTagName('ul')[0];
    // 创建一个Socket实例
    if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
    // 如果浏览器不支持websocket，会使用这个flash自动模拟websocket协议，此过程对开发者透明
    WEB_SOCKET_SWF_LOCATION = "/Web/swf/WebSocketMain.swf";
    //实例化socket
    socket = new WebSocket('ws://120.78.175.126:30000'); 

    //点击发送
    btn.onclick = function(){
        //获取交流对象
        var uid = $('#title').text();
        //判断是否交流
        if (uid == '') {
            alert('请选择交流对象');
        //判断是否为空
        } else if (text.value ==''){

            alert('不能发送空消息');
        //交流
        } else {

            var msg = '<li><img class="imgleft" src="'+cusIcon+'"><span class="spanleft">'+text.value+'</span></li>';
            //界面显示
            $('.content').append (msg);
            //往属性值里面写东西
            storeDatas(uid,msg);
            //清空input信息
            text.value = '';
            //发送消息交流
            var msg = $('.content li:last').text();
            $.ajax({
                type:'post',
                data:{
                    uid:uid,
                    msg:msg,
                },
                url: root + '/Admin/Chat/chating.html',
                success:function (data) {

                }
            }); 
        }
        //滚动到底部
        $('#container .content').scrollTop($('#container .content')[0].scrollHeight); 
    }




    
    // 打开Socket 
    socket.onopen = function(event) { 

        //设置定时器
        errormsg = setTimeout(function(){
            alert('连接失败');
        },2000);
        // 监听消息
        socket.onmessage = function(event) {
            clearTimeout(errormsg);
            //转化为json对象 
            data = JSON.parse(event.data);
            //判断消息类型                      
            switch (data.type)
            {
                case 'init':
                    //初始化得到唯一的client_id
                    
                    $.ajax({
                        type:'post',
                        data:{id:data.id},
                        url: root + '/Admin/Chat/bindUid.html',
                        success:function (data) {
                            setData(data);  
                        },

                    });

                    break;
                //默认为聊天
                default :

                    //1.判断是否有该用户的选项卡有则存储信息，没有则添加选项卡，并往选项卡内添加信息
                    
                    var uid = data.uid;
                    var msg = '<li><img class="imgright" src="'+userIcon+'"><span class="spanright">'+data.msg+'</span></li>';
                    storeDatas(uid,msg);
                    //2.判断是否针对该用户进行回复，有则分别往选项卡填写数据
                    if( $('#title span:first').html() == uid ) {
                         //接收消息，往里面写数据
                        $('.content').append('<li><img class="imgright" src="'+userIcon+'"><span class="spanright">'+data.msg+'</span></li>');

                        // ajax请求删除未读消息
                        $.ajax({
                            type:'post',
                            data:{uid:uid},
                            url:root + '/Admin/Chat/clearUnread.html',
                            success:function (data) {                                
                            }
                        });
                    } else {
                        if ($('#'+uid+' span').length > 1) {
                            $('#'+uid+' span:last').remove();
                        }
                        strMsg = '<span>(' + data.unread + ')</span>';
                        $('#'+uid ).append(strMsg);
                    }

                    //移至最后
                    $('#container .content').scrollTop($('#container .content')[0].scrollHeight);      
            }


        };


        // 监听Socket的关闭
        socket.onclose = function(event) { 
            console.log(4); 
        }; 


    }; 

    //3.点击选项卡时，遍历对象内的数据
    $('#userbar').on('click','li',function(){

        $('.content li').remove();
        var obj = $(this);
        if (obj.children('span').length > 1) {
            obj.children('span').last().remove();
        }
        var str = obj.attr('datas');
        var datas = JSON.parse(str);
        for (k in datas) {
            $('.content').append(datas[k]);
        }
        $('#title').html(obj.html());
        obj.css('background','lightpink');
        obj.siblings().css('background','lightgreen');
        // ajax请求删除未读消息

        $.ajax({
            type:'post',
            data:{uid:obj.prop('id')},
            url:root + '/Admin/Chat/clearUnread.html',
            success:function (data) {                                
            }
        });
        //滚动到底部
        $('#container .content').scrollTop($('#container .content')[0].scrollHeight); 
    });


    /**
     * 存储页面信息
     * @Author   ryan
     * @DateTime 2017-12-01
     * @email    931035553@qq.com
     * @param    {[type]}         id  [description]
     * @param    {[type]}         msg [description]
     * @return   {[type]}             [description]
     */
     function storeDatas (id,msg)
     {

        if ($('#'+id).length) {

            str = $('#'+id).attr('datas');
            datas = JSON.parse(str);
            datas.push(msg);
            str = JSON.stringify(datas);
            $('#'+id).attr('datas',str);
        } else {                                     
            $('#userbar').append('<li id='+id+'><span>'+id+'</span></li>');
            var datas = [];
            datas.push(msg);
            str = JSON.stringify(datas);
            $('#'+id).attr('datas',str);
        }
     }

    /**
     * 设置初始化信息
     * @Author   ryan
     * @DateTime 2017-12-01
     * @email    931035553@qq.com
     * @param    {[type]}         data [description]
     */
    function setData (data) {
        //将初始化数据写入
        for (k in data) {
            //写入缓存
            var msgs = data[k]['msg'];                            
                
            //添加数据
            for(j in msgs ) {
                res = msgs[j].split('-@@-',2);
                if (res[0] == 'cus') {
                    msg = '<li><img class="imgleft" src="'+cusIcon+'"><span class="spanleft">'+res[1]+'</span></li>';
                } else {
                    msg = '<li><img class="imgright" src="'+userIcon+'"><span class="spanright">'+res[1]+'</span></li>';
                }

                //页面存储
                storeDatas(k,msg);
            }

            //判断是否为未读
            if (data[k].unread != 0 ) {
                $('#'+k).append('<span>('+data[k].unread+')</span>');
            }
        }   
            
    }

}




//设置定时器
setInterval(function(){   
    var oDate = new Date(); //实例一个时间对象；
    var time = oDate.getHours() + ":" + oDate.getMinutes() + ":" + oDate.getSeconds();
    $('#container .header span:last').text(time);
},1000);
