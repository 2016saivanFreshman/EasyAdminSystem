<?php
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "../../framework.php";
Core::getInstance();
//初始化缓存，memcache类
$memc_obj = new Cache(C('CACHEGROUP'));
//$act = isset($_GET['action']) ? $_GET['action']: "";

$act = isset($_GET['action']) ? $_GET['action']: "index";
$v 	 = isset($_GET['v']) ? $_GET['v']: "1.0";

if(empty($act)){
	json_return(10170);
}
//preg_match (pattern , subject, matches)pattern正则表达式 subject需要匹配检索的对象，成功返回 1 ，否则返回 0 。
if (preg_match("/^[a-z0-9_]*$/i", $act)==0){
	json_return(10171, '', $act);
}

if (preg_match("/^[\.0-9_]*$/i", $v)==0){
	json_return(10175, '', $v);
}

$data = MC("SELECT * FROM ".C('DB_PREFIX')."interface_version WHERE requestname='{$act}' AND version='{$v}' AND is_delete=0", 0);

//isset只检查是否为null,null就返回false
if (!isset($data[0]['is_disable'])){
	json_return(10173, '', $act, $v);
}
if ($data[0]['is_disable']==1){
	json_return(10174, '', $act, $v);
}

/*
三次运行action，第一次是面对请求，调用相应的action获取数据
第二次是调用相应的action进行返回数据的封装，相应的格式和名字都在om_interface_version数据库里面写好。
第三次是调用rule里面的实际方法
*/
/*
	第一次加载
 */
//对接口请求内容进行验证或转换
//需要看这个transform是用来干什么的？转换？
$transform = !empty($data[0]['extend_transform']) ? $data[0]['extend_transform'] : 'Transform:commonTransform';
//explode() 函数把字符串打散为数组。":"为打散标志
list($vclass, $vfun) = explode(':', $transform);
//和前面一样，找action
$vmethod = ucfirst($vclass."Act");
$vfun   = 'act_'.$vfun;

if (!class_exists($vmethod)){
	echo "第一次没找到action";
	json_return(10176);
}
//检查类的方法是否存在于指定的 object中,前面是object
if (!method_exists($vmethod, $vfun)){
	json_return(10176);
}
//如果数据出现问题，或者有安全漏洞，直接返回，中断脚本
//验证数据
if (!A($vclass)->$vfun()){
	json_return(A($vclass)->act_getErrorMsg());
}
/*
	第二次加载Action
*/
//加载实际执行函数
list($class, $fun) = explode(':', $data[0]['rule']);
$method = ucfirst($class."Act");
$fun   = 'act_'.$fun;
if (!class_exists($method)){
	json_return(10176);
}
if (!method_exists($method, $fun)){
	json_return(10176);
}
//运行action的方法
$ret = A($class)->$fun();
if ($_GET['debug']==1){
	//getAllRunSql（）返回所有被执行的sql语句数组
	echo "<!-- \n\t\t".implode("\n\t\t", M($class)->getAllRunSql())."\n\t -->\n";
}

if (empty($ret)){
	$errmsg = A($class)->act_getErrorMsg();
	if (!empty($errmsg)){
		json_return($errmsg);
	}
}
/*
	第三次加载
 */

$package = !empty($data[0]['extend_package']) ? $data[0]['extend_package'] : 'Package:commonPackage';
list($pclass, $pfun) = explode(':', $package);
$pmethod = ucfirst($pclass."Act");
$pfun   = 'act_'.$pfun;
if (!class_exists($pmethod)){

	json_return(10176);
}
if (!method_exists($pmethod, $pfun)){
	json_return(10176);
}
$ret = A($pclass)->$pfun($ret);

$callback	=	isset($_GET['callback']) ? $_GET['callback'] : "";
$jsonp		=	isset($_GET['jsonp']) ? $_GET['jsonp']: "";

$data = array("errCode"=>200, "errMsg"=>get_promptmsg(10172), "status"=>true, "data"=>$ret);
if(!empty($callback)){
	if(!empty($jsonp)){
		echo "try{ ".$callback."(".json_encode($data)."); }catch(){alert(e);}";
	}else{
		echo $callback."(".json_encode($data).");";
	}
}else{
	echo json_encode($data);
}
exit;
?>