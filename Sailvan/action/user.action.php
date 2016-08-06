<?php
	/**
	* 用于操作用户登录的
	*/
	class UserAct extends CommonAct
	{
		
		function __construct()
		{
			parent::__construct();
		}

		public function act_login($username, $password){
			$data = array($username, $password);
			$temp = M($this->act_getModel())->validate($username, $password);
			//这里逻辑有错，需要改进
			if($temp){
				return true;
			}else{
				//并在这里把东西存进session
				$_SESSION['id'] = "123";
				//self::errMsg['2020']	= 'jjfijgi';
				return false;
			}	
		}

		public function act_getUserList($page){
			$array 				= M($this->act_getModel())->getUserList();
			$newArray 			= array();
			//用二维数组实现分页，一维数组个数代表总页数，每个一维数组内含有的对象个数代表每页显示行数
			for($x = 0 ; $x < count($array); $x++){
				$user 			= new User();
				$user->id 		= $array[$x]['Id'];
				$user->username = $array[$x]['username'];
				$newArray[] 	= $user;
			}
			return $newArray;
		}

		public function act_registe($username, $password){
			return M($this->act_getModel())->registe($username, $password);
		}

		public function act_getInformation($id){
			$array 			= M($this->act_getModel())->getUserInformation($id);
			$user 						= new User();
			$user->id 					= $array[0]['Id'];
			$user->password 			= $array[0]['password'];
			$user->username 			= $array[0]['username'];
			$user->age 					= $array[0]['age'];
			$user->name 				= $array[0]['name'];
			$user->sex 					= $array[0]['sex'];
			$user->introduction 		= $array[0]['introduction'];
			$user->picture				= $array[0]['picture'];
			
			return $user;
		}

		public function act_updateUserInformation($id, $array){
			return M($this->act_getModel())->updateUserInformation($id,$array);
		}

		public function act_getInfoThroughApi(){
			$interface = new InterfaceRequestModel();
			return $interface->getAllSkuInfoPageByConditions();
		}

	}

	/**
	* 
	*/
	class User
	{
		public $username;
		public $id;
		public $password;
		public $name;
		public $sex;
		public $age;
		public $introduction;
		public $picture;

		function __construct()
		{
			
		}
	}