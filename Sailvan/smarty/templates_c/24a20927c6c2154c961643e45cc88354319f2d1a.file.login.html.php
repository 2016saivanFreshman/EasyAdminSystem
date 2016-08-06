<?php /* Smarty version Smarty-3.1.12, created on 2016-08-06 15:57:57
         compiled from "D:\EasyAdminSystem\Sailvan\html\template\user_system\login.html" */ ?>
<?php /*%%SmartyHeaderCode:466057a59885e30bd7-30786817%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '24a20927c6c2154c961643e45cc88354319f2d1a' => 
    array (
      0 => 'D:\\EasyAdminSystem\\Sailvan\\html\\template\\user_system\\login.html',
      1 => 1470274683,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '466057a59885e30bd7-30786817',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_57a59885ecd002_25613981',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57a59885ecd002_25613981')) {function content_57a59885ecd002_25613981($_smarty_tpl) {?><!DOCTYPE html>
<html>

<head>
	<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="css/login.css">
	<script type="text/javascript" src="js/jquery/jquery-3.1.0.min.js"></script>
	<script type="text/javascript" src="js/login.js"></script>

</head>
<body>
<!--action是直接带入url里面的-->
	<form role="form">
		<div class="loginForm">
			<div class="usernameInput">
				<label for="username">账号：</label>
				<input type="text" name="username" value="root" id="username"/>		
			</div>
			<div class="passwordInput">
				<label for="password">密码：</label>			
				<input type="password" name="password" value="root" id="password"/>
			</div>
			<div class="errmsg" id="errmsg">
				<i></i>
			</div>
			<div class="buttonGroup">
					<button id="login" type="button">登录</button>
					<button type="button" id="register">注册</button>
			</div>
		</div>
	</form>

</body>

</html>
<?php }} ?>