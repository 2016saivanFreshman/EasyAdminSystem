<?php
	/**
	* 
	*/
	class ApiOpenAct extends CommonAct
	{
		function __construct()
		{
			parent::__construct();
		}

		public function act_getUserInformationByUsername(){
			$username = $_GET['username'];
			$userAction = new UserAct();
			$temp = M($userAction->act_getModel())->getUserInformationByUsername($username);
			return $temp;
		}
	}