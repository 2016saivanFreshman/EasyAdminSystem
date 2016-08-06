<?php
session_start();
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include_once dirname(__DIR__)."/conf/define.php";
include_once WEB_PATH."framework.php";
Core::getInstance();

$mod	=	isset($_REQUEST['mod']) ? $_REQUEST['mod']: "";
$act	=	isset($_REQUEST['act']) ? $_REQUEST['act']: "";

if(empty($mod)){
	redirect_to(WEB_URL."index.php?mod=user&act=index"); // 跳转到登陆页
}
if(empty($act)){
	redirect_to(WEB_URL."index.php?mod=public&act=login");
}

//初始化memcache类,memcache是一套分布式的高速缓存系统，用于提升网站的访问速度
$memc_obj 	= new Cache(C('CACHEGROUP'));

$modName	= ucfirst($mod."View");
$modClass	= new $modName();

$actName	= "view_".$act;

if(method_exists($modClass, $actName)){
	$ret	=	$modClass->$actName();
}else{
	echo "no this act!!";
}
?>