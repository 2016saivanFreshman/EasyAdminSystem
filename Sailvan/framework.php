<?php

class Core {
    private static $_instance = array();
    private static $classFile;

	private function __construct(){
		//-----------需要页面显示调试信息,	注释掉下面两行即可---

		//-------------------------------------------------------
		set_error_handler(array("Core",'appError'));//规定发生错误时运行的函数。
		set_exception_handler(array("Core",'appException'));//设定异常处理
        date_default_timezone_set("Asia/Shanghai");//时区
		if(version_compare(PHP_VERSION,'5.4.0','<') ) {
			@set_magic_quotes_runtime (0);//过滤转义符，如/ “”
			define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
		}//判断PHP版本，get_magic_quotes_gpc()判断解析用户提示的数据（转义符），以确保这些数据不会引起程序错误，尤其是安全问题
        if(!defined('WEB_PATH')){
			define("WEB_PATH",str_replace(DIRECTORY_SEPARATOR, '/',dirname(__FILE__)).DIRECTORY_SEPARATOR);	
		}//定义路径
		
		include	WEB_PATH."lib/common.php";
		

		/*C方法是ThinkPHP用于设置、获取，以及保存配置参数的方法
		C方法读取全局配置，以及当前模块的配置。参数没有的话，将读取全部的有效配置。
		include也可以有返回值的，取决于被包含的文件是否return了一个返回值，
		所以这里的参数其实就是一个数组。这用到的是C()函数最后的批量设置，把这两个文件的数组合并后放到$_config这个静态数组里。
		*/
		
		//加载全局配置信息
		C(include WEB_PATH.'conf/common.php');
		
		C(include WEB_PATH.'conf/default_var.php');//订单状态码列表定义
		
		include	WEB_PATH."lib/auth.php";	//鉴权，与鉴权系统交互

		//include	WEB_PATH."lib/authuser.class.php";	//新鉴权
		

		include	WEB_PATH."lib/php-export-data.class.php";	//excel（估计是一个导出到excel的功能文件）
		//Auth::setAccess(include WEB_PATH.'conf/access.php');

		include	WEB_PATH."lib/log.php";//日志功能设定文件
        include WEB_PATH."conf/constant_order.php";//定义订单系统常量

		//加载数据接口层及所需支撑（先放着）

		include	WEB_PATH."lib/service/http.php";	//网络接口
		include	WEB_PATH."lib/functions.php";		
		include	WEB_PATH."lib/page.php";			//分页
		include	WEB_PATH."lib/template.php";		//PHPLIB 的模板类
		include	WEB_PATH."lib/cache/cache.php";		//memcache
		include WEB_PATH."lib/productstatus.class.php";

		
		//加载语言包
		//$lang	=	WEB_PATH."lang/".C("LANG").".php";		//memcache

		if(file_exists($lang)){
			//echo $lang;
			//C(include $lang);
		}
		
		//用C函数获取"DATAGATE"配置参数
		if(C("DATAGATE") == "db"){
			$db	=	C("DB_TYPE");
			include	WEB_PATH."lib/db/".$db.".php";	//db直连（mysql数据库的连接在这里）
			
			if($db	==	"mysql"){
				global	$dbConn;
				$db_config	=	C("DB_CONFIG");

				$dbConn	=	new mysql();
				$dbConn->connect($db_config["master1"][0],$db_config["master1"][1],$db_config["master1"][2]);
				
				$dbConn->select_db($db_config["master1"][4]);
			}
			if($db	==	"mongodb"){
				//.......
			}
		}

		//自动加载类
		//碰到没有引用的类是，自动加载Core里的autoload方法
		 spl_autoload_register(array('Core', 'autoload'));
		

	}

	//这是一个自动加载函数，在PHP5中，当我们实例化一个未定义的类时，就会触发此函数。
	//自动加载实现
	public function autoload($class){
		//加载act
		//strpos（）查找 子串（"Act"） 在字符串中第一次出现的位置,没有就是0
	
		if(strpos($class,"Act")){
			$name	=	preg_replace("/Act/","",$class);//执行一个正则表达式的搜索和替换
			
			$fileName	=	lcfirst($name).".action.php";
			Core::getFile($fileName,WEB_PATH."action/");
			if(empty(Core::$classFile)){
				throw new Exception("action no exits");
			}//抛出异常后就不会include了
			include_once Core::$classFile;
		}

		if(strpos($class,"Model")){
			$name	=	preg_replace("/Model/","",$class);
			$fileName	=	lcfirst($name).".model.php";
			Core::getFile($fileName,WEB_PATH."model/");
			if(empty(Core::$classFile)){
				throw new Exception("action no exits");
			}
			include_once Core::$classFile;
		}

		if(strpos($class,"View")){
			//------------------
			
			//---------------
			$name	=	preg_replace("/View/","",$class);
			
			$fileName	=	lcfirst($name).".view.php";//把首字母改为小写
			Core::getFile($fileName,WEB_PATH."view/");

			
			if(empty(Core::$classFile)){
				throw new Exception("action no exits");
			}
			include_once Core::$classFile;
		}
	}


	public static function getFile($fileName,$path){//(2016/7/25)
		
		if ($handle = @opendir($path)) {//opendir() 函数打开一个目录句柄,若成功，则该函数返回一个目录流，否则返回 false 以及一个 error,@" 来隐藏 error 的输出
		    while(false !== ($file = @readdir($handle))) {//遍历该目录下的文件名，会有.和..
		        if(is_dir($path.$file) && ($file != "." && $file != "..")){//is_dir（）如果文件名存在并且为目录则返回 TRUE
					
		        	Core::getFile($fileName,$path.$file."/");
		        }else{
		       	 	if($file==$fileName){
		        		Core::$classFile	=	$path.$file;
		        	}
					
		        }
		    }
		}
		@closedir($handle);
	}


	private function __clone() {}

	//单实例
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
             self::$_instance = new Core();
        }
        return self::$_instance;
    }


    /**
     +----------------------------------------------------------
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
    static public function appException($e) {
		//echo $e;
        //halt($e->__toString());
    }

    /**
     +----------------------------------------------------------
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     +----------------------------------------------------------
     */
    static public function appError($errno, $errstr, $errfile, $errline) {
		//echo $errstr;
		//exit;
    	switch ($errno) {
			case E_WARNING:
				$errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
				if(C('LOG_RECORD')) Log::write($errorStr,Log::ERR);
				//echo ($errorStr)."<br>"."<br>";
				break;
			case E_ERROR:
			case E_USER_ERROR:
				$errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
				if(C('LOG_RECORD')) Log::write($errorStr,Log::ERR);
				//echo($errorStr)."<br>"."<br>";
				break;
			case E_STRICT:
			case E_USER_WARNING:
			case E_USER_NOTICE:
			default:
				$errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
				Log::record($errorStr,Log::NOTICE);
				break;
		}
    }
}