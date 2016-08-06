<?php
session_start();
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');

//--------------------

//--------------------

//__FILE__  php定义的为当前文件路径及文件名的常量
//dirname(__FILE__)  为函数，取得文件路径的目录名，是目录
include_once dirname(__FILE__)."/conf/define.php";//就是include文件而已


include_once WEB_PATH."framework.php";

//直接调用Core中的静态方法
Core::getInstance();

$mod	=	isset($_REQUEST['mod']) ? $_REQUEST['mod']: "";
$act	=	isset($_REQUEST['act']) ? $_REQUEST['act']: "";

error_reporting(-1);



if(empty($mod) || empty($act)){//自己定义第一个要跳转的页面是哪个？
	redirect_to(WEB_URL."index.php?mod=user&act=index"); //mod代表要跳转的页面，act代表之后操作的函数
	exit;
}

//初始化memcache类，先放着，后面学习了memcache之后再来看，先理解为缓存管理

$memc_obj 	= new Cache(C('CACHEGROUP'));

$modName	= ucfirst($mod."View"); //ucfirst将字符串第一个字符改大写

//此处用了framework.php里的autoload函数，自动加载需要的类
$modClass	= new $modName();//初始化一个modView对象，但是没有，需要自己创建（第一个页面的展示）


$actName	= "view_".$act;

//直接运行modView.PHP里面的actName方法
if(method_exists($modClass, $actName)){
	$ret	=	$modClass->$actName();
	
	}else{
	echo "no this act!!";
	}
?>