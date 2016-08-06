<?php
	/**
	* 
	*/
	class UserModel extends CommonModel
	{
		
		function __construct()
		{
			parent::__construct();
		}

		public function validate($username, $password){
			$parameter = array("username", "password");
			$where = array("username"=> $username, "password" => $password);
			return $this->getData($parameter, $where);
		}

		public function registe($username, $password){
			$array = array("username"=>$username, "password"=>$password, "name"=>"", "sex"=>"", "age"=>"", "introduction"=>"", "picture"=>"");
			return $this->save($array);
		}

		public function getUserList(){
			$where 	= "";
			$fields = array("id", "username");
			return $this->getList();
		}

		public function getUserInformation($id){
			$parameter = array("Id", "username", "password", "name", "sex", "age", "introduction", "picture");
			$where = array("Id"=>$id);
			return $this->getData($parameter, $where);
		}

		public function updateUserInformation($id, $array){
			return $this->updateData($id, $array);
		}

		public function getUserInformationByUsername($username){
			$parameter = array("Id", "username", "password", "name", "sex", "age", "introduction", "picture");
			$where = array("username"=>$username);
			$temp = $this->getData($parameter, $where);
			return $temp;
		}
}