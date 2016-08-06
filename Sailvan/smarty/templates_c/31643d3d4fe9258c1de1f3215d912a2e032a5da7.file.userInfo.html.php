<?php /* Smarty version Smarty-3.1.12, created on 2016-08-04 12:06:13
         compiled from "D:\Sailvan\html\template\user_system\userInfo.html" */ ?>
<?php /*%%SmartyHeaderCode:2938357a166889d3701-36152674%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '31643d3d4fe9258c1de1f3215d912a2e032a5da7' => 
    array (
      0 => 'D:\\Sailvan\\html\\template\\user_system\\userInfo.html',
      1 => 1470283542,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2938357a166889d3701-36152674',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_57a16688a19c15_04818799',
  'variables' => 
  array (
    'user' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57a16688a19c15_04818799')) {function content_57a16688a19c15_04818799($_smarty_tpl) {?><!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/tableFormat.css">
	<script type="text/javascript" src="js/jquery/jquery-3.1.0.min.js"></script>
	<script type="text/javascript" src="js/userInfo.js"></script>
</head>
<body>

欢迎:<?php echo $_SESSION['username'];?>

	<form>
	<div>
		<table class="table">
			<thead>
				<th>&nbsp</th>
				<th>&nbsp</th>
			</thead>
			<tbody>
				<tr>
					<input hidden name="id" value="<?php echo $_smarty_tpl->tpl_vars['user']->value->id;?>
" id="id"/>
					<td><label for="username">Username:</label></td>
					<td style="text-align:left" width="50px"><input type="text" name="username" id="username" value="<?php echo $_smarty_tpl->tpl_vars['user']->value->username;?>
" disabled /></td>
				</tr>

				<tr>
					<td><label for="password">Password:</label></td>
					<td style="text-align:left"><input type="password" name="password" id="password" value="<?php echo $_smarty_tpl->tpl_vars['user']->value->password;?>
"/></td>
				</tr>

				<tr>
					<td><label for="name">Name:</label></td>
					<td style="text-align:left"><input type="text" name="name" id="name" value="<?php echo $_smarty_tpl->tpl_vars['user']->value->name;?>
"/></td>
				</tr>

				<tr>
					<td><label for="sex">Sex:</label></td>
					<td style="text-align:left"><input type="radio" name="sex" id="sex" value="male" checked="checked"/>男
					<input type="radio" name="sex" value="female"/>女</td>
				</tr>

				<tr>
					<td><label for="age">Age:</label></td>
					<td style="text-align:left"><input type="text" name="age" id="age" value="<?php echo $_smarty_tpl->tpl_vars['user']->value->age;?>
"/></td>
				</tr>

				<tr>
					<td><label for="introduction">Introduction:</label></td>
					<td style="text-align:left"><textarea id="introduction" name="introduction"><?php echo $_smarty_tpl->tpl_vars['user']->value->introduction;?>
</textarea></td>
				</tr>

				<tr>
					<td><label for="picture">Picture:</label></td>
					<td style="text-align:left"><input type="file" name="picture" id="picture" value="<?php echo $_smarty_tpl->tpl_vars['user']->value->picture;?>
"/></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2"><button type="button" id="edit">提交</button>
					&nbsp&nbsp<button id="exit" type="button" onclick="goback();">返回</button></td>
				</tr>
			</tfoot>
		</table>
	</div>
	</form>
</body>
</html>

<script type="text/javascript">
	function goback(){
		history.back();
	}
</script><?php }} ?>