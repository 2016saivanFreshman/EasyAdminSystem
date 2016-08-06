<?php

	/**
	* 这个PHP用于显示登录界面
	*/
	class UserView extends BaseView
	{
		
		function __construct()
		{
			parent::__construct();
		}

		public function view_index(){
			$this->smarty->display('user_system/login.html');
		}

		public function view_login(){
			//需要判断用户名是否为空
			$username = $_POST['username'];
			$password = $_POST['password'];
			//验证:实例化action，再实例化model，操作sql语句
			$result = A($this->getAction())->act_login($username, $password);
			if($result == false){
				//返回错误信息，然后用JQUERY插入到HTML中。
				$resultArray = array("status"=>"success", "message"=>"fail");
				echo json_encode($resultArray);
			}else{
				//echo $username;
				$_SESSION['username'] = $username;
				$resultArray = array("status"=>"success", "message"=>"login");
				echo json_encode($resultArray);
			}
		}

		public function view_realLogin(){
			$page 				= $_GET['page'];//这个是从1开始的
			if(empty($page)){
				$page 			= 1;//设置默认的页面
			}
			$pagenum			= 3;//这个用来定义每页有多少个
			$newArray 			= A($this->getAction())->act_getUserList($page);
			$userlist 			= array_chunk($newArray,$pagenum);//array_chunk(数组名，size) 函数把数组分割为新的数组块。其中每个数组的单元数目由 size 参数决定。最后一个数组的单元数目可能会少几个
			$this->smarty->assign("userlist", $userlist[$page - 1]);
			$this->smarty->assign("pagenum", count($userlist));
			$this->smarty->display('user_system/userlist.html');
		}

		public function view_register(){
			$username = $_POST['username'];
			$password = $_POST['password'];
			/*if(preg_match("/^[\w]+\@[A-Za-z0-9]+\.[A-Za-z0-9]+$/",$username) == 0){
				echo "false";
			}后台测试正则表达式匹配成功*/
			$result = A($this->getAction())->act_registe($username, $password);
			if($result == false){
				echo "false";
			}else{
				//成功代码
			}
		}

		public function view_information(){
			$id 			= $_GET['userid'];
			$result = A($this->getAction())->act_getInformation($id);
			//开始赋值
			$this->smarty->assign("user", $result);
			//实现跳转
			$this->smarty->display('user_system/userInfo.html');
		}

		public function view_update(){
			$id = $_POST['id'];
			$username 		= $_POST["username"];
			$password 		= $_POST["password"];
			$name 			= $_POST["name"];
			$sex 			= $_POST["sex"];
			$age 			= $_POST["age"];
			$introduction 	= $_POST["introduction"];
			$picture 		= $_POST["picture"];
			$message 		= $this->isValid($username, $password, $name, $age);
			if(empty($message)){
				$array 		= array('username' =>$username , "password"=>$password, "name"=>$name, "sex"=>$sex, "age"=>$age, "introduction"=>$introduction, "picture"=>$picture);
				$result 	= A($this->getAction())->act_updateUserInformation($id,$array);
				if($result == false){
					//update()失败
					echo "false";
				}else{
					//成功需要返回什么
					echo "SUCCESS";
				}
			}else{
				echo $message;
			}
		}

		//此函数用于判断用户资料是否输入正确
		function isValid($username, $password, $name, $age){
			$message		= "";
			//名字不能太长，10个字符内
			if(strlen($username) > 12){
				$message 	.= "  用户名错误(不得超过12位)";
			}
			//密码长度不能大于12
			if(strlen($password) > 12 ){
				$message 	.= "  密码格式(不得超过12位)错误";
			}
			//名字长度不能大于12
			if(strlen($name) > 12 ){
				$message 	.= "  名字错误(不得超过12位)";
			}
			//年龄需要大于1 ， 小于100 ， 数字
			if((!is_numeric($age))){
				$message 	.= "  年龄错误(必须为数字)";
			}else if(intval($age) < 0  || intval($age) > 100 ){
				$message 	.= "  年龄错误(必须在1到100之间)";
			}
			return $message;
		}

		function view_getInfoThroughApi(){
			print_r(A($this->getAction())->act_getInfoThroughApi());
		}

	}