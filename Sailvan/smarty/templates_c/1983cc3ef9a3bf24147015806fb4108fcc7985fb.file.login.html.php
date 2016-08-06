<?php /* Smarty version Smarty-3.1.12, created on 2016-08-04 09:39:23
         compiled from "D:\Sailvan\html\template\user_system\login.html" */ ?>
<?php /*%%SmartyHeaderCode:324857a1471f5a5628-99990039%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1983cc3ef9a3bf24147015806fb4108fcc7985fb' => 
    array (
      0 => 'D:\\Sailvan\\html\\template\\user_system\\login.html',
      1 => 1470274683,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '324857a1471f5a5628-99990039',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_57a1471f5cc721_10744603',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57a1471f5cc721_10744603')) {function content_57a1471f5cc721_10744603($_smarty_tpl) {?><!DOCTYPE html>
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