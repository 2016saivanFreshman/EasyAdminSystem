<?php
	/**
	* 
	*/
	class TransformDpAct extends CommonAct
	{
		
		function __construct()
		{
			parent::__construct();
		}

		public function act_getUserInformationByUsername(){
			//这里只进行简单验证，可以用正则表达式验证是否符合邮箱格式
			
			$username = $_GET['username'];
			if(empty($username)){
				return false;
			}else{
				//不符合邮箱规则
				if(preg_match("/^[\w]+\@[A-Za-z0-9_]+\.[A-Za-z0-9]+$/", $username) == 0){
					return false;
				}
				return true;
			}
		}
	}