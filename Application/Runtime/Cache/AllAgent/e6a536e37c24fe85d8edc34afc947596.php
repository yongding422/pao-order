<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>方雅总后台登录</title>
	<link rel="stylesheet" type="text/css" href="/Public/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/Public/css/agent.css">
	<script type="application/javascript" src="/Public/js/jquery-3.1.0.min.js"></script>
</head>
<body class="login-bg">
	<div class="login">
		<div class="login-content">
			<h1 class="login-head">
				<img src="/Public/images/admin_logo.png">
				<span>方雅总后台</span>
			</h1>
			<form id="myform">
			<div class="login-main">
				<h3 class="main-head">欢迎登录</h3>
				<input type="text" name="username" class="form-control login-input" placeholder="用户名">
				<input type="password" name="pwd" class="form-control login-input" placeholder="密码">
				<div class="code-content">
					<input type="text" name="code" class="form-control login-input" placeholder="验证码">
					<div class="code-box">
						<img src="/index.php/AllAgent/Index/verifyImg" class="code-img" onclick="this.src='/index.php/AllAgent/Index/verifyImg/'+Math.random()">
					</div>
				</div>				
				<button class="form-control login-btn" type="button" onclick="commit()">登录</button>
				<input type="reset" id="reset" style="display: none;"/>
			</div>
			</form>
		</div>
	</div>
</body>
<script src="/Public/js/AllAgent/index_login.js"></script>
</html>