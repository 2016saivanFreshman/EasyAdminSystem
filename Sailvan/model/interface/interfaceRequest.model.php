<?php
	defined('WEB_PATH') ? '' : exit;
	/**
	* 
	*/
	class InterfaceRequestModel extends InterfaceModel
	{
		
		function __construct()
		{
			parent::__construct();
		}

		public function getAllSkuInfoPageByConditions(){
			$conf = $this->getRequestConf(__FUNCTION__);
			if (empty($conf)){
				return false;
			}
			$conf['perPage']		= 10;
			$conf['currentPage']	= 1;
			$conf['isNew']			= 1;
			$result = callOpenSystem($conf);
			$data = json_decode($result,true);
			if (isset($data['errCode'])&&$data['errCode']!=200) {
				self::$errMsg[$data['errCode']] = "{$data['errMsg']}";
				return false;
			}else{
				return $data['data'];
			}
		}
	}