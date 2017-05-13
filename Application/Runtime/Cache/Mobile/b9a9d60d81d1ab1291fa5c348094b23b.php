<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

		<!-- Bootstrap 核心 CSS 文件 -->
		<link rel="stylesheet" href="/Public/bootstrap/css/bootstrap.min.css">
		<!-- <link rel="stylesheet" type="text/css" href="/Public/bootstrap-datetimepicker-master/bootstrap-datetimepicker.min.css"> -->
		<link rel="stylesheet" type="text/css" href="/Public/css/calendar.min.css">
		<link rel="stylesheet" type="text/css" href="/Public/css/member.css">

		<!-- HTML5 Shim 和 Respond.js 用于让 IE8 支持 HTML5元素和媒体查询 -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

		<![endif]-->

		

		<title>用户中心|会员注册</title>
	</head>

	<body>
		<section class="login">
            <form action="<?php echo U('mobileReg');?>" method="post" onSubmit="return mobile_reg(this)">
                <div class="login-textbox">
                    <span class="glyphicon glyphicon-phone left-icon"></span>
                    <input type="text" id="mobile" name="phone" dataType="Require" placeholder="请输入您的手机号码">
                </div>

                <div class="login-textbox">
                    <span class="glyphicon glyphicon-lock left-icon"></span>
                    <input type="number" name="smsCode" dataType="Require" placeholder="请输入验证码">
                    <div class="right-button">
                        <button type="button" class="btn btn-success" onclick="send_sms(this)">获取验证码</button>
                    </div>
                </div>

                <div class="login-textbox">
                    <span class="glyphicon glyphicon-calendar left-icon"></span>
                    <input type="text" name="birthday" dataType="Require" placeholder="请输入您的生日" class="calendars">
                    <input type="hidden" name="arr" value="<?php echo ($translate); ?>"/>
                </div>

                <div class="login-tips">*请正确输入生日，当天有大折扣</div>

                <!--<button class="btn btn-danger login-btn" onclick="location='<?php echo U("member/index");?>'">注册</button>-->
                <button class="btn btn-danger login-btn" type="submit">注册</button>
            </form>
		</section>
		<!-- <script src="/Public/bootstrap-datetimepicker-master/bootstrap-datetimepicker.min.js"></script>
		<script src="/Public/bootstrap-datetimepicker-master/bootstrap-datetimepicker.zh-CN.js"></script> -->
		<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
		<script src="/Public/js/jquery-3.1.0.min.js"></script>
		<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
        <script src="/Public/js/layer.js"></script>
		<script src="/Public/bootstrap/js/bootstrap.min.js"></script>
		<script src="/Public/js/calendar.min.js"></script>
        <script src="/Public/js/validator.js"></script>
        <script src="/Public/js/lamson.js"></script>


	</body>

    <script>
        // AJAX表单处理
        function mobile_reg(obj)
        {
            // 利用“我佛山人”插件进行合法的验证
            if( Validator.Validate(obj, 3))
            {
                // AJAX提交表单
                $.post(obj.action, $(obj).serialize(), function(data){
                    // console.log(data);
                    if(data.status == 0)
                    {
                        alert(data.info);
                    }else
                    {
                        location = data.url;
                    }
                });
            }
            return false;
        }

        // 手机短信验证的发送
        function send_sms(obj)
        {
            layer.msg('短信已发送，请查收！');return;
            // console.log(obj);
            // 正则表达式
            var egre = /^1\d{10}$/;
            // 当前对象所在的表单下面的name属性
            var mobile = obj.form.phone;
            // alert(mobile.value);
            // alert($("#mobile").val());
            // egre.test(mobile.value) 此方式也行
            if(! mobile.value.match(egre)){
                alert("您输入的手机号码格式不正确！");
                // 获取焦点
                mobile.onfocus();
                // 阻止提交
                return false;
            }

            // 状态变为不可用
            obj.disabled = true;
            // 添加样式（鼠标放上去有变色）
            $(obj ).addClass("disabled");

            var s = 5;
            var T = window.setInterval(function(){
                s--;
                if(s>0){
                    obj.innerHTML = s + "秒";
                }else{
                    // 当秒数少于1时
                    // 清除定时器
                    window.clearInterval(T);
                    // 按钮上显示回发送验证码
                    obj.innerHTML = "发送验证码";
                    // 状态为可用
                    obj.disabled = false;
                    // 移除样式
                    // 注意：这里的removeClass是jq对象
                    $(obj ).removeClass("disabled");
                }
            },1000);

            // 发送Ajax请求，短信是否发送的请求
            /*$.post("<?php echo U('sms');?>",{mobile:mobile.value},function(data){
               // console.log(data);
                if(data == 1){
//                    alert("短信已发送，请查收！");
                    layer.msg('短信已发送，请查收！');
                }else{
                    alert(data);
                }
            });*/
        }
    </script>