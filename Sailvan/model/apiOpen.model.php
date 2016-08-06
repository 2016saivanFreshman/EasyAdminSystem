<?php
	/**
	* 
	*/
	class ApiOpenModel extends CommonModel
	{
		
		function __construct()
		{
			parent::__construct();
		}

		public function getUserInformationByUsername($username){
			//构造sql语句需要的参数
			$parameter = array("Id", "username", "password", "name", "sex", "age", "introduction", "picture");
			$where = array("username"=>$username);
			$temp = $this->getData($parameter, $where);
			return $temp;
		}

	}