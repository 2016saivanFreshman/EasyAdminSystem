<?php
/*
 * ebay平台对接接口
 * add by: linzhengxiang @date 20140618
 */
class AmazonButtAct extends CheckAct{
	
	private $authorize = array();
	
	public function __construct(){
		parent::__construct();
	}
	
	public function setToken($account, $site){
		######################以后扩展到接口获取 start ######################
		$AWS_ACCESS_KEY_ID		= 'AKIAI633X6V6H4DXL6QA';
		$AWS_SECRET_ACCESS_KEY	= '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';
		$MERCHANT_ID			= 'A2RQL5RV8Y34GK';
		$MARKETPLACE_ID			= 'ATVPDKIKX0DER';
		$APPLICATION_NAME		= 'DB Order Synchronization';
		$APPLICATION_VERSION	= '0.1';
		$serviceUrl				= 'https://mws.amazonservices.com/Orders/2011-01-01';
		######################以后扩展到接口获取  end  ######################
		$this->authorize = array(	
									'appname'			=>$APPLICATION_NAME,
									'appversion'		=>$APPLICATION_VERSION,
									'acckeyid'			=>$AWS_ACCESS_KEY_ID,
									'acckey'			=>$AWS_SECRET_ACCESS_KEY,
									'merchantid'		=>$MERCHANT_ID,
									'marketplaceid'		=>$MARKETPLACE_ID,
									'serviceUrl'		=>$serviceUrl,
									'account'			=>$account,
									'site'				=>$site,
							);
	}
	
	/**
	 * 根据订单号抓取订单列表
	 * @param datetime $starttime
	 * @param datetime $endtime
	 * @return array
	 * @author lzx
	 */
	public function spiderOrderLists($starttime, $endtime){
		$OrderObject = F('amazon.package.GetOrders');
		$OrderObject->setRequestConfig($this->authorize);
		$simplelists = $OrderObject->getOrderLists($starttime, $endtime);
		return $simplelists;
	}
}
?>