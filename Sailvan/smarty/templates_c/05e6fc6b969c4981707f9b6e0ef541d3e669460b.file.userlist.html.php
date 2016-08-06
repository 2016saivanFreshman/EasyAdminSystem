<?php /* Smarty version Smarty-3.1.12, created on 2016-08-05 19:07:23
         compiled from "D:\Sailvan\html\template\user_system\userlist.html" */ ?>
<?php /*%%SmartyHeaderCode:484357a14eedd57565-86449528%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '05e6fc6b969c4981707f9b6e0ef541d3e669460b' => 
    array (
      0 => 'D:\\Sailvan\\html\\template\\user_system\\userlist.html',
      1 => 1470395241,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '484357a14eedd57565-86449528',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_57a14eedd99bf0_27287615',
  'variables' => 
  array (
    'userlist' => 0,
    'user' => 0,
    'pagenum' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57a14eedd99bf0_27287615')) {function content_57a14eedd99bf0_27287615($_smarty_tpl) {?><!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="css/tableFormat.css">
	<script type="text/javascript" src="js/jquery/jquery-3.1.0.min.js"></script>
	<script type="text/javascript" src="js/userInfo.js"></script>
	
</head>
<body>
欢迎:<?php echo $_SESSION['username'];?>

<h1 style="text-align:center">用户列表</h1>
<div class="tablePosition">
	<table class="table">
 	  	
 	  	<thead>
	      <tr>
	         <th>id</th>
	         <th>username</th>
	         <th></th>
	      </tr>
 	  	</thead>
	 	<tbody>
		    <?php  $_smarty_tpl->tpl_vars['user'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['user']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['userlist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['user']->key => $_smarty_tpl->tpl_vars['user']->value){
$_smarty_tpl->tpl_vars['user']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['user']->key;
?>
		    	<tr>
		    		<td><?php echo $_smarty_tpl->tpl_vars['user']->value->id;?>
</td>
		    		<td><?php echo $_smarty_tpl->tpl_vars['user']->value->username;?>
</td>
		    		<td><a href="index.php?mod=user&act=information&userid=<?php echo $_smarty_tpl->tpl_vars['user']->value->id;?>
">编辑</td>
		    	</tr>
		    <?php } ?>
	 	</tbody>
	 	<tfoot>
	 	</tfoot>
	</table>
</div>
<div class="listPostion">
	<ul>
		<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['loop'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['name'] = 'loop';
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['pagenum']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total']);
?>
			<li><a href="index.php?mod=user&act=realLogin&page=<?php echo $_smarty_tpl->getVariable('smarty')->value['section']['loop']['index']+1;?>
"><?php echo $_smarty_tpl->getVariable('smarty')->value['section']['loop']['index']+1;?>
</a></li>
		<?php endfor; endif; ?>
	</ul>
</div>

<button type="button" id="getInfoThroughApi">从开放系统获取货品信息</button>
</body>
</html><?php }} ?>