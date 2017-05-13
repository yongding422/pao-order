<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <!-- 引入样式 -->
    <link rel="stylesheet" href="/Public/element-ui/lib/theme-default/index.css">
    <link rel="stylesheet" href="/Public/css/vertical_template.css">
</head>
<body class="finish">
<div id="finish">
    <header class="template-header">
        <button class="return-btn" @click="orderAgain">
            <img src="/Public/images/return.png">
            <span>重新点餐</span>
        </button>
    </header>
    <section class="finish-content">
        <input type="hidden" name="dianpu_id" value="<?php echo ($restaurant_id); ?>" id="dianpu_id"/>
        <div><p>请将手机二维码放置到</p><p>下方扫描器扫描打单</p></div>
        <button class="finish-btn" @click="orderAgain">返回点餐</button>
    </section>
    <footer class="template-footer clear">
        本页面在<span id="time">60</span>秒后自动关闭
    </footer>
</div>
</body>
<!-- 先引入 Vue -->
<script src="/Public/js/vue.js"></script>
<!-- 引入组件库 -->
<script src="/Public/element-ui/lib/index.js"></script>
<script src="/Public/js/jquery-3.1.0.min.js"></script>
<script>


    //设定倒数秒数
    var t = 60;
    //显示倒数秒数
    function showTime(){
        t -= 1;
        document.getElementById('time').innerHTML= t;
        if(t==3){
//				JsObj.CloseVguang();
        }

        if(t==0){
            localStorage.removeItem("order_type");
            location.href='/index.php/Vertical/Template/serviceRoute';
            return;
        }
        //每秒执行一次,showTime()
        setTimeout("showTime()",1000);
    };
    showTime();

    /*
     ** randomWord 产生任意长度随机字母数字组合
     ** randomFlag-是否任意长度 min-任意长度最小位[固定位数] max-任意长度最大位
     */
    function randomWord(randomFlag, min, max){
        var str = "",
                range = min,
                arr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

        // 随机产生
        if(randomFlag){
            range = Math.round(Math.random() * (max-min)) + min;
        }
        for(var i=0; i<range; i++){
            pos = Math.round(Math.random() * (arr.length-1));
            str += arr[pos];
        }
        return str;
    }

    function onOpen(evt) {
        //连接成功
        console.log("连接成功");
    }

    function onMessage(evt) {
        //接收信息
        console.log(evt.data);
        var data = evt.data.replace(/&quot;/g, '"');
        try{
            data = JSON.parse(data);
        }catch(e){
            console.log(e.name +":"+ e.message);
        }
        console.log(data);
        if(data.type == "score"){
            var pay_status = data.pay_status;
            if(pay_status == "1"){
                order_sn = data.order_sn;
                try{
                    JsObj.CompletePay(order_sn);
                    JsObj.CloseVguang();
                }catch(e){
                    console.log(e.name+":"+ e.message);
                }

                location.href='/index.php/Vertical/Template/serviceRoute';
            }else{
                layer.msg(data.msg,{
                    skin: 'layer-class',
                    area: '80%'
                });
            }
        }
    }

    function onError(evt) {
        //出现错误
        console.log(evt.data);
    }
    new Vue({
        data:{

        },
        mounted:function(){
            var uid = randomWord(true, 3,6);
            var restaurant_id = $("#dianpu_id").val();
            var pay_info = "score" +"|"+ uid + "|" + restaurant_id;

            var wsUri ="ws://120.25.99.40:4682/";
            websocket = new WebSocket(wsUri);
            websocket.onopen = function(evt) {
                onOpen(evt);
                websocket.send(uid);
            };
            websocket.onmessage = function(evt) {
                //接收到后台信息
                onMessage(evt);
            };
            websocket.onerror = function(evt) {
                onError(evt);
            };
            console.log('pay_info:');
            console.log(pay_info);
            JsObj.CloseVguang();
            JsObj.OpenVguang(pay_info);
        },
        methods:{
            orderAgain:function(){
                localStorage.removeItem("order_type");
//                JsObj.CloseVguang();
                location.href='/index.php/Vertical/Template/serviceRoute';
            }
        }
    }).$mount('#finish')
</script>
</html>