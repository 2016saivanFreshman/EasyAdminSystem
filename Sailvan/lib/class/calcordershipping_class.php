<?php
/*
 * 订单运输方式选择和运费计算
 * @add by : linzhengxiang ,date : 20140611
 */

class CalcOrderShipping {

	private $errMsg = array();				//装载重量计算过程中的异常信息（无重量、无包材等），异常信息需要提交到数据库统一管理
	private $orderData = array();
	
	public function __construct(){
		
	}
	
	/**
	 * 赋值订单变量
	 * @param array $orderData
	 * @author lzx
	 */
	public function setOrder($orderData){
		$this->orderData = $orderData;
	}
	
	/**
	 * 获取错误信息
	 * @eturn array 错误信息数据需要打到订单相关表中，记录错误编号用于订单查询
	 * @author lzx 
	 */
	public function getErrMsg(){
		return $this->errMsg;
	}
	
	/**
	 * 订单重量计算
	 * @return float $orderweight
	 * @author herman.xi 20140620
	 */
	public function calcOrderWeight() {
		if (empty($this->orderData)){
			//请先初始化订单，需要自己写到消息提示配置里面。然后复制给$this->errMsg,供前段调用
			return false;
		}
		//var_dump($this->orderData); return false;
		$orderDetailData = $this->orderData['orderDetail'];
		//var_dump($$orderDetailData); exit;
		$orderWeight = 0; //初始化要返回的订单重量变量
		$pweight = 0; //初始化包材重量
		$orderCosts = 0;
		$orderPrices = 0;
		foreach($orderDetailData as $detailValue){
			$orderDetailValue = $detailValue['orderDetail'];
			$sku = $orderDetailValue['sku'];
			//echo $sku; echo "\n";
			$amount = $orderDetailValue['amount'];
			$itemPrice = $orderDetailValue['itemPrice'];
			$skuinfo = M("InterfacePc")->getSkuInfo($sku);
			//var_dump($skuinfo); echo "\n";
			$skuinfoDetail = $skuinfo['skuInfo'];
			//组合料号
			if(count($skuinfoDetail) == 1){
				foreach($skuinfoDetail as $ssku => $skuinfoDetailValue){
					//$ssku = $skuinfoDetail[0]['sku'];
					$scount = $skuinfoDetailValue['amount'];
					$skuDetail = $skuinfoDetailValue['skuDetail'];
					//$goodsinfo = GoodsModel::getSkuInfo($ssku);//获取单料号信息
					if($skuDetail){
						$pmId = $skuDetail['pmId'];
						$goodsWeight = $skuDetail['goodsWeight'];
						$pmCapacity = $skuDetail['pmCapacity'];
						//$goodsCost = $skuDetail['goodsCost'];
					}
					$pmInfo = M("InterfacePc")->getMaterInfoById($pmId);//获取包材信息
					//var_dump($pmInfo); echo "\n";
					if($pmInfo){
						$pweight = $pmInfo['pmWeight'];
					}
					if($scount <= $pmCapacity){
						$orderWeight += $pweight + ($goodsWeight * $scount);
					}else{
						if (!empty($pmCapacity)) {
							$orderWeight += (1 + ($scount-$pmCapacity)/$pmCapacity*0.6)*$pweight + ($goodsWeight * $scount);
						} else {
							$orderWeight += $pweight + ($goodsWeight * $scount);	
						}	
					}
				}
			}else if(count($skuinfoDetail) > 1){
				foreach($skuinfoDetail as $ssku => $skuinfoDetailValue){
					//$ssku = $skuinfoDetailValue['sku'];
					$scount = $skuinfoDetailValue['amount'];
					$skuDetail = $skuinfoDetailValue['skuDetail'];
					//$goodsinfo = M("InterfacePc")->getSkuInfo($ssku);//获取单料号信息
					if($skuDetail){
						$pmId = $skuDetail['pmId'];
						$goodsWeight = $skuDetail['goodsWeight'];
						$pmCapacity = $skuDetail['pmCapacity'];
						$goodsCost = $skuDetail['goodsCost'];
					}
					$pmInfo = M("InterfacePc")->getMaterInfoById($pmId);//获取包材信息
					if($pmInfo){
						$pweight = $pmInfo['pmWeight'];
					}
					$orderWeight += ($scount/$pmCapacity)*0.6*$pweight + ($goodsWeight * $scount);
				}
			}
		}
		return array($orderWeight,$pmId);
	}
	
	/**
	 * 综合调用函数返回最后计算出来的运费和运输方式
	 */
	public function calcOrderCarrierAndShippingFee(){
		if (empty($this->orderData)){
			//请先初始化订单，需要自己写到消息提示配置里面。然后复制给$this->errMsg,供前段调用
			return false;
		}
		//echo "123123"; echo "\n";
		if (!$carriers = $this->calcOrderCarriers()){
			//记录错误，需要自己写到消息提示配置里面。
			return false;
		}
		//var_dump($carriers); echo "\n";
		//echo "================"; echo "\n";
		if (!$shippingfees = $this->calcOrderShippingFee($carriers)){
			//记录错误，需要自己写到消息提示配置里面。
			return false;
		}
		//var_dump($shippingfees); echo "\n"; exit;
		return $this->chooseOrderShipping($shippingfees);
	}
	
	/**
	 * 运输方式匹配
	 * @return array $carriers
	 * @author lzx
	 */
	public function calcOrderCarriers() {

		#1、对应平台录入平台运输方式和对应可以走的运输方式，如果为匹配返回false，提示用户添加对应匹配关系
		
		#2、获取特殊料号运输方式对应转化，剔除掉对应被转化运输方式
		
		#3、根据平台获取对应平台suffix进行扩展运输方式检验确认.demo 如下
		$carriers = false;
		$accountId = $this->orderData['order']['accountId'];
		//var_dump($accountId); echo "\n";
		//echo "============"; echo "\n";
		$suffix = M('Account')->getSuffixByAccout($accountId);
		//var_dump($suffix); echo "\n";
		$extenmethod = "calc".ucfirst($suffix)."OrderExtension";
		//echo $extenmethod; echo "\n";
		if(method_exists($this, $extenmethod)){
			//echo $extenmethod; echo "\n";
			$carriers = $this->$extenmethod();
		}
		//var_dump($carriers); echo "\n";
		return $carriers;
	}
	
	/**
	 * 根据上面的运输方式到运输方式管理系统获取运费
	 * @param array $carriers
	 * @return array
	 * @author lzx
	 * @last modified by Herman.Xi 20140628
	 */
	public function calcOrderShippingFee($carriers){
		//$transportId = $this->orderData['order']['transportId'];
		$calcWeight = $this->orderData['order']['calcWeight'];
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$postCode = $this->orderData['orderUserInfo']['postCode'];
		$carrierId = join(',', $carriers);
		//$carrierId = "1";
		//var_dump($carrierId); echo "\n";
		$carrierinfos = M('InterfaceTran')->getBatchFixShippingFee($carrierId, $countryName, $calcWeight, $postCode, 2);
		//var_dump($carrierinfos);
		return $carrierinfos;
	}
	
	/**
	 * 根据运输方式和价格确定最后真正走的运输方式
	 * @param array $shippingfees
	 * @return array
	 * @author lzx
	 */
	public function chooseOrderShipping($shippingfees){
		$accountId = $this->orderData['order']['accountId'];
		$suffix = M('Account')->getSuffixByAccout($accountId);
		$extenmethod = "choose".ucfirst($suffix)."OrderShippingExtension";
		//echo $extenmethod; echo "\n";
		if(method_exists($this, $extenmethod)){
			$carriers = $this->$extenmethod($shippingfees);
		}
		return $carriers;
	}
	
	/**
	 * 对应ebay平台特殊运输方式转换，
	 * demo：如ebay在某段时间内不能内什么运输方式，在这里可以剔除掉
	 */
	private function calcEbayOrderExtension(){
		/*$transportId = $this->orderData['order']['transportId'];
		$calcWeight = $this->orderData['order']['calcWeight'];
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$postCode = $this->orderData['orderUserInfo']['postCode'];*/
		$carriers = array(1,2,3,4,5,6,8,9,10,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,93,94);
		return $carriers;
	}
	
	/**
	 * 对应亚马逊平台特殊运输方式转换
	 */
	private function calcAmazonOrderExtension(){
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$calcWeight = $this->orderData['order']['calcWeight'];
		$accountId = $this->orderData['order']['accountId'];
		$currency = $this->orderData['orderUserInfo']['currency'];
		$postCode = $this->orderData['orderUserInfo']['postCode'];
		$actualTotal = $this->orderData['order']['actualTotal'];
		$transportId = $this->orderData['order']['transportId'];
		/*$array_intersect_py = array_intersect($sku_arr, $__liquid_items_TempModifyPY);
		$array_intersect_py2 = array_intersect($sku_arr, $__liquid_items_TempModifyPY2);
		$array_intersect_zhijiayou = array_intersect($sku_arr, $__liquid_items_cptohkpost);
		$array_intersect_yieti = array_intersect($sku_arr, $__liquid_items_postbyhkpost);
		$array_intersect_Wristwatch = array_intersect($sku_arr, $__liquid_items_Wristwatch);
			
		$shippment_py = false;
		if(count($array_intersect_py) > 0){
			$shippment_py = true;	
		}
		$shippment_py2 = false;
		if(count($array_intersect_py2) > 0){
			$shippment_py2 = true;	
		}*/
		
		if(in_array($countryName, array("United States", "Puerto Rico", "PuertoRico", "Virgin Islands (U.S.)"))){
			$transportId = 'EUB';
			if($calcWeight > 2){
				$transportId = 'FedEx';
			}
		}else if($countryName == "Canada"){
			$transportId = '中国邮政挂号';
		}else if($countryName == "United Kingdom"){
			$transportId = '德国邮政挂号';
			if($calcWeight > 0.74){
				$transportId = 'FedEx';
			}
		}else if($countryName == ''){
			return '';
		}else{
			$transportId = '德国邮政挂号';
		}
		
		if($accountId == 'sunweb'){
			$transportId = '中国邮政平邮';
			if(in_array($countryName, array("United States", "Puerto Rico", "PuertoRico", "Virgin Islands (U.S.)","Austria","Belgium","Canary Islands","Channel Islands","Denmark","France","Germany","Ireland","Italy","Luxembourg","Monaco","Netherlands","Norway","San Marino","Spain","Sweden","Switzerland","United Kingdom","Vatican City State")) && $calcWeight > 2){
				$transportId = 'FedEx';
			}else if(in_array($countryName, array("Canada","Mexico")) && $calcWeight > 3){
				$transportId = 'FedEx';	
			}
			//return $transportId;
		}
		
		if($currency == 'USD' && in_array($accountId,array('zeagoo889','Finejo2099'))){
			$ama_countrys = array('Portugal', 'Bosnia and Herzegovina', 'Czech Republic','Finland,Belgium','Slovakia','Austria','Andorra','Montenegro','Estonia','Sweden','Malta','Vatican City State','Croatia', 'Republic of Iceland','Russian Federation','Luxembourg','Bulgaria','Germany','Spain','Norway','Italy','Serbia','Albania','Belarus','Switzerland','Denmark','Cyprus','Greece','Hungary,Slovenia','Moldova','Macedonia','Liechtenstein','Svalbard and Jan Mayen','San Marino','Latvia','Guernsey','Romania','Gibraltar','Netherlands','Lithuania','Monaco','Jersey','France','Ireland','Ukraine','Polan');
			if(in_array($countryName,$ama_countrys)){
				$transportId = 'Global Mail';
			}
		}
		
		if(in_array($ebay_currency, array('GBP','EUR')) && in_array($accountId,array('zeagoo889','finejo2099','lantomall'))){
			$ama_countrys = array('Holland','Czech','Estonia','Slovakia','Slovenia','Sweden','Hungary','France','Germany','Denmark','Belgium','Finland','Spain','Poland','United Kingdom');
			if(in_array($ebay_countryname,$ama_countrys)){
				$transportId = 'Global Mail';
			}
		}
		
		if($shippment_py && in_array($accountId,array('zeagoo889'))){
			$transportId = '中国邮政平邮';
		}
		
		if($shippment_py2 && in_array($accountId,array('Finejo2099'))){
			$transportId = '中国邮政平邮';
		}
		
		if(strpos($transportId, '中国邮政') !== FALSE){
			if(count($array_intersect_zhijiayou) > 0 || count($array_intersect_yieti) > 0 || count($array_intersect_Wristwatch) > 0){
				if($actualTotal >= 70 ){
					$transportId = '香港小包挂号';
				}else{
					$transportId = '香港小包平邮';
				}
			}
		}
		$transportId = M('InterfaceTran')->getCarrierIdByName($transportId);
		return array($transportId);
	}
	
	/**
	 * 对应速卖通平台特殊运输方式转换
	 */
	private function calcAliexpressOrderExtension(){
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$calcWeight = $this->orderData['order']['calcWeight'];
		$accountId = $this->orderData['order']['accountId'];
		$transportId = $this->orderData['order']['transportId'];
		$currency = $this->orderData['orderUserInfo']['currency'];
		$postCode = $this->orderData['orderUserInfo']['postCode'];
		$actualTotal = $this->orderData['order']['actualTotal'];
		/*$EUB_account	=	array(
			'cn1510515579',
			'cn1510509503',
			'cn1510509429',
			'cn1510514024',
			"cn1510891016",
			"cn1510895038",
			"cn1510890054",
			"cn1510893515",
			"cn1000999030",
			"cn1501287427",
			"cn1500226033",
			"cn1500688776",
			"cn1501655558",
			"cn1501657451",
			//---------------
			"cn1511272624",
			"cn1511324726",
			"cn1501686262",
			"cn1501638127",
		);*/
		//判断平邮账号的字段om_account添加
		if(in_array($transportId, array('Hongkong Post Air Mail', 'HK Post Air Mail', 'HKPAM', 'Hongkong Post Airmail', 'HK Post Airmail','HongKong Post Air Mail'))){
			$transportId		= '香港小包挂号';
			//2013-12-23新增运输方式拦截
			if(in_array($aliexpress_user, $EUB_account) && $actualTotal < 40){
				$transportId		= '香港小包平邮';
			}
			//end----
		}
		if(in_array($transportId, array('UPSS', 'UPS Express Saver'))){
			$transportId		= 'UPS';
		}
		
		if($transportId   == 'DHL'){
			$transportId		= 'DHL';
		}
		
		if($transportId   == 'EMS'){
			$transportId		= 'EMS';
		}
		
		if(in_array($transportId, array('ChinaPost Post Air Mail', 'China Post Air Mail', 'CPAM', 'China Post Airmail'))){
			$transportId		= '中国邮政挂号';
			//2013-12-23新增运输方式拦截
			if(in_array($aliexpress_user, $EUB_account) && $actualTotal < 40)
			{
				$transportId		= '中国邮政平邮';
			}
			//end-----
		}
		
		if($transportId=='ePacket'){
			$transportId = 'EUB';
		}
		
		if($transportId   == 'Singapore Post'){
			$transportId = '新加坡小包挂号';
		}

		if($transportId == "Fedex IE"){
			$transportId = 'FedEx';
		}
		
		if($transportId == "Russian Air"){
			$transportId = '俄速通挂号';
			if($actualTotal < 40)
			{
				$transportId = '俄速通平邮';
			}
		}
		$transportId = M('InterfaceTran')->getCarrierIdByName($transportId);
		return array($transportId);
	}
	
	/**
	 * 对应独立商城平台特殊运输方式转换，
	 * demo：如CNDL在某段时间内不能内什么运输方式，在这里可以剔除掉
	 */
	private function calcCndlOrderExtension(){
		//echo "==========="; echo "\n";
		$transportId = $this->orderData['order']['transportId'];
		/*$calcWeight = $this->orderData['order']['calcWeight'];
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$postCode = $this->orderData['orderUserInfo']['postCode'];*/
		if($transportId == 'dhlfixed' || $transportId == 'dhlperweight' || $transportId == 'dhl'){
			$transportId ='DHL';
		}else if ($transportId=='fedex' || $transportId=='Fedex'){
			$transportId ='FedEx';
		}else if ($transportId=='chinapostreg' || $transportId=='Chinapostreg'){
			$transportId ='中国邮政挂号';
		}else if ($transportId=='chinapost' || $transportId=='Chinapost'){
			$transportId ='中国邮政平邮';
		}else if($transportId=='ems'){
			$transportId ='EMS';
		}else if($transportId=='emszones'){ 
			$transportId ='EMS';
		}else if($transportId=='sfexpress'){
			$transportId ='顺丰快递';
		}else if($transportId=='stoexpress'){
			$transportId ='申通快递';
		}else if($transportId=='ups'){
			$transportId ='UPS美国专线';
		}else if($transportId=='epacket'){
			$transportId ='EUB';
		}
		$transportId = M('InterfaceTran')->getCarrierIdByName($transportId);
		return array($transportId);
	}
	
	/**
	 * 对应国内销售平台特殊运输方式转换，
	 * demo：如CNDL在某段时间内不能内什么运输方式，在这里可以剔除掉
	 */
	private function calcDomesticOrderExtension(){
		//echo "==========="; echo "\n";
		$transportId = $this->orderData['order']['transportId'];
		/*$calcWeight = $this->orderData['order']['calcWeight'];
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$postCode = $this->orderData['orderUserInfo']['postCode'];*/
		switch($transportId){
			case 'hongkong post air mail' :
			case 'hk post air mail' :
			case 'hkpam' :
			case 'hongkong post airmail' :
			case 'hk post airmail' :
				$transportId = '香港小包挂号';
				break;
			case 'upss' :
			case 'ups express saver' :
				$transportId = 'UPS';
				break;
			case 'dhl' :
				$transportId = 'DHL';
				break;
			case 'ems' :
				$transportId = 'EMS';
				break;
			case 'chinapost post air mail' :
			case 'china post air mail' :
			case 'cpam' :
			case 'china post airmail' :
				$transportId = '中国邮政挂号';
				break;
			case 'china post air mail (surface)' :
				$transportId = '中国邮政平邮';
				break;
			case 'epacket' :
				$transportId = 'EUB';
				break;
			case 'fedex' :
			case 'fedex ip' :
			case 'fedex ie' :
				$transportId = 'FedEx';
				break;
			default :
				//$transportId = '';
		}
		$transportId = M('InterfaceTran')->getCarrierIdByName($transportId);
		return array($transportId);
	}
	
	#####################  可以扩展多个平台运输方式选择  一定要按照平台表：suffix 递增 订单信息，明细扩展表后缀,命名规则##########################
	
	/**
	 * 对应ebay最优运输方式选择和价格差别选择
	 * demo： 如EUB的价格高于平台的10%还是会选择EUB
	 */
	private function chooseEbayOrderShippingExtension($shippingfees){
		$carrierinfo = array();
		foreach($shippingfees as $key => $shippfeevalue){
			if($shippfeevalue['fee'] == 0){
				continue;	
			}
			if(isset($carrierinfo) && $shippfeevalue['fee'] < $carrierinfo['fee']){
				$carrierinfo = $shippfeevalue;
			}else{
				$carrierinfo = $shippfeevalue;	
			}
		}
		return $carrierinfo;
	}
	
	/**
	 * 对应Amazon最优运输方式选择和价格差别选择
	 * demo： 如EUB的价格高于平台的10%还是会选择EUB
	 */
	private function chooseAmazonOrderShippingExtension($shippingfees){
		$carrierinfo = array();
		foreach($shippingfees as $key => $shippfeevalue){
			if($shippfeevalue['fee'] == 0){
				continue;
			}
			if(isset($carrierinfo) && $shippfeevalue['fee'] < $carrierinfo['fee']){
				$carrierinfo = $shippfeevalue;
			}else{
				$carrierinfo = $shippfeevalue;
			}
		}
		return $carrierinfo;
	}
	
	/**
	 * 对应速卖通最优运输方式选择和价格差别选择
	 */
	private function chooseAliexpressOrderShippingExtension($shippingfees){
		$carrierinfo = array();
		foreach($shippingfees as $key => $shippfeevalue){
			if($shippfeevalue['fee'] == 0){
				continue;	
			}
			if(isset($carrierinfo) && $shippfeevalue['fee'] < $carrierinfo['fee']){
				$carrierinfo = $shippfeevalue;
			}else{
				$carrierinfo = $shippfeevalue;	
			}
		}
		return $carrierinfo;
	}
	
	/**
	 * 对应独立商城最优运输方式选择和价格差别选择
	 */
	private function chooseCndlOrderShippingExtension($shippingfees){
		$carrierinfo = array();
		foreach($shippingfees as $key => $shippfeevalue){
			if($shippfeevalue['fee'] == 0){
				continue;	
			}
			if(isset($carrierinfo) && $shippfeevalue['fee'] < $carrierinfo['fee']){
				$carrierinfo = $shippfeevalue;
			}else{
				$carrierinfo = $shippfeevalue;	
			}
		}
		return $carrierinfo;
	}
	
	/**
	 * 对应独立商城最优运输方式选择和价格差别选择
	 */
	private function chooseDomesticOrderShippingExtension($shippingfees){
		$carrierinfo = array();
		foreach($shippingfees as $key => $shippfeevalue){
			if($shippfeevalue['fee'] == 0){
				continue;
			}
			if(isset($carrierinfo) && $shippfeevalue['fee'] < $carrierinfo['fee']){
				$carrierinfo = $shippfeevalue;
			}else{
				$carrierinfo = $shippfeevalue;
			}
		}
		return $carrierinfo;
	}
	#####################  可以扩展多个平台最优运输方式选择和价格差别选择  一定要按照平台表：suffix 递增 订单信息，明细扩展表后缀,命名规则##########################
}