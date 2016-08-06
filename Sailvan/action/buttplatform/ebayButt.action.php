<?php
/*
 * ebay平台对接接口
 * add by: linzhengxiang @date 20140618
 */
class EbayButtAct extends CheckAct{
	
	private $authorize = array();
	
	public function __construct(){
		parent::__construct();
		F('xmlhandle');
	}
	
	public function setToken($account){
		######################以后扩展到接口获取 start ######################
		$siteID =0;
		$production  = false;
		$compatabilityLevel = 765;
		$devID		= "c979de22-fe99-4d93-b417-940c637d38bb";
		$appID		= "Shenzhen-f583-48e8-95ed-0f88fabff4ee";
		$certID		= "45c0312b-ed8d-4274-b037-1107e1d63d25";
		$serverUrl	= "https://api.ebay.com/ws/api.dll";
		$userToken 	= 'AgAAAA**AQAAAA**aAAAAA**MojnUQ**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AEkICjD5OKpQ6dj6x9nY+seQ**J+gBAA**AAMAAA**j/TkaXLF/w3zv+FnwuaFIedPpZ/Q7K45cB9aIzu3mWMLFY9h/wewKQh6AGmYBnYOGjAnDxkee8g0JCus8arGJU338tnJ8rxzEdGx9BrFVaRGI8+1vzZAYW04lu3PTlotvan0mIP6H2OzbrQ7871ob3N7KaqSBcDeYYQGLwbHX8n+rK26Dl8umlZQW8aKSNb2qk3ZFB3HqlnDk65WgbxUpQrQalcvA+J0sEoNO6ThIQNJttTmtVPsF4cx5lBJmx7peWrHJvcv6ABiD6QmtC78OAa/j/68iZ2mD+CDgU/OhlC17S2DpdzHTHpL8A2X88y1KSL7VRKpUB77MS+MgybSVrNkMkI4eeVktjkal+OFAHnXfnWOfevc8UJRqSMSeyBv54+hoi+llEpsqcrVBPxkMGbjoD3zv3wOpHb+NOSU/DKCXRP5qIc0rF4kqSL72MDHu4SJCA4Oc4mPrQwQ2grqIAwq675zsPC1Bt3TDrvyEtfNfBAiydQKrmv1h5TvbvSDAuvIkDNMJi7TtG0Kl7cJ/7SBO+RhQX0Xyp+PaXlfMEKAfubSSFIlwoiwivm+sg0YcB2TC8Fi35vkO3sFFkfzVTPE8NGTfJ9NZOnkToAzBMCSd3NoJ50ZNjCMGzaJqYJdnsmhxSzfDxmud48RT3e7QxYsqZrRflbMsAFIICt0U5EzuoD52DX8XrMdH9bUU9+Woy5fkYl8YG6x7QAQSl++CTcHKzFfwATbLHtAdJuhg9jJ30aPD97MM3HCnZv+16xO';
		######################以后扩展到接口获取  end  ######################
		$this->authorize = array(	
									'userToken'				=>$userToken,
									'devID'					=>$devID,
									'appID'					=>$appID,
									'certID'				=>$certID,
									'serverUrl'				=>$serverUrl,
									'compatabilityLevel'	=>$compatabilityLevel,
									'siteID'				=>$siteID,
									'account'				=>$account,
							);
	}
	
	/**
	 * 根据开始和结束时间，抓取订单抓取号
	 * @param datetime $starttime
	 * @param datetime $endtime
	 * @return bool
	 * @author lzx
	 */
	public function spiderOrderId($starttime, $endtime){
	
		$OrderObject = F('ebay.package.GetOrders');
		$OrderObject->setRequestConfig($this->authorize);
		$page = 1;
		$hasmore = true;
		$simplelists = array();
		while ($hasmore){
			$receivelists = $OrderObject->getOrderIds($starttime, $endtime, $page);
			$receivelists = XML_unserialize($receivelists);
			if (!isset($receivelists['GetOrdersResponse']['Ack'])||$receivelists['GetOrdersResponse']['Ack']=='Failure'){
				self::$errMsg[10095] = get_promptmsg(10095);
				break;
			}
			if ($receivelists['GetOrdersResponse']['PaginationResult']['TotalNumberOfPages']<$page){
				self::$errMsg[10096] = get_promptmsg(10096, $page, $receivelists['GetOrdersResponse']['PaginationResult']['TotalNumberOfPages']);
				break;
			}
			$page++;
			$hasmore	= $receivelists['GetOrdersResponse']['HasMoreOrders']=='true' ? true : false;
			foreach( $receivelists['GetOrdersResponse']['OrderArray']['Order'] as $simpleorder){
				/*参考变量
				 * $orderid = $simpleorder['OrderID'];
				$eBayPaymentStatus = $simpleorder['CheckoutStatus']['eBayPaymentStatus'];
				$OrderStatus = $simpleorder['CheckoutStatus']['Status'];
				$PaidTime = $simpleorder['PaidTime'];
				$ShippedTime = isset($simpleorder['ShippedTime']) ? $simpleorder['ShippedTime'] : '';*/
				if ($simpleorder['CheckoutStatus']['Status']!='Complete') {
					continue;
				}
				/*//如果要抓取刷单的这里需要做修改
				if ($simpleorder['CheckoutStatus']['eBayPaymentStatus']!='NoPaymentFailure') {
					break;
				}*/
				$simplelists[] = array('ebay_orderid'=>$simpleorder['OrderID'], 'ebay_account'=>$this->authorize['account']);
			}
		}
		return $simplelists;
	}
	
	/**
	 * 根据订单号抓取订单列表
	 * @param array $orderids
	 * @return array
	 * @author lzx
	 */
	public function spiderOrderLists($orderids){
		$OrderObject = F('ebay.package.GetOrders');
		$OrderObject->setRequestConfig($this->authorize);
		$receivelists = $OrderObject->getOrderLists($orderids);
		$receivelists = XML_unserialize($receivelists);
		$orders = array();
		if(!empty($receivelists['GetOrdersResponse']['OrderArray']['Order']['OrderID'])){
			$orders = array($receivelists['GetOrdersResponse']['OrderArray']['Order']);
		}else{
			$orders = $receivelists['GetOrdersResponse']['OrderArray']['Order'];
		}
		//将这个抓取的数组格式化为订单新增（OrderAddModel）的标准化格式
		foreach($orders as $orderInfo){
			$isNote = 0; //默认为没有留言订单
			//顾客留言
			if(!empty($orderInfo['ExternalTransaction']['BuyerCheckoutMessage'])){
				$isNote				= 1;
				$customerMessage	= @str_rep($orderInfo['ExternalTransaction']['BuyerCheckoutMessage']);
				$customerMessage	= str_replace('<![CDATA[','',$customerMessage);
				$customerMessage	= str_replace(']]>','',$customerMessage);
			}
			
			//获取第一个Transaction,多个料号和单个料号的格式不一样
			$orderAttribute = 1; //1为单料订单
			$firstTransaction 	= $orderInfo['TransactionArray']['Transaction'];
			if(!empty($firstTransaction) && empty($orderInfo['TransactionArray']['Transaction']['Buyer'])){
				$orderAttribute 	= 3; //多料号订单
				$firstTransaction = $orderInfo['TransactionArray']['Transaction'][0];
			}
			//站点
			$site = $firstTransaction['Item']['Site'];
			if(empty($site)){
				$site = $firstTransaction['TransactionID']; 
			}

			//订单表数据
			$order = array(
					'recordNumber'		=> $orderInfo['OrderID'],
					'platformId'		=> 1,
					'accountId'			=> M('Account')->getAccountIdByName($this->authorize['account']),
					'ordersTime'		=> strtotime($orderInfo['CreatedTime']),
					'paymentTime' 		=> strtotime($orderInfo['PaidTime']),
					'onlineTotal' 		=> $orderInfo['AmountPaid'],
					'actualTotal' 		=> $orderInfo['AmountPaid'],
					'ORtransportId' 	=> '',
					'transportId'  		=> 0, //数据库字段不能为空，先置为0
					'actualShipping' 	=> $orderInfo['ShippingServiceSelected']['ShippingServiceCost'],
					'marketTime'		=> 0,
					'ShippedTime' 		=> 0,
					'ORorderStatus' 	=> 0,
					'ORorderType'		=> 0,
					'orderStatus'		=> C("STATEPENDING"),
					'orderType' 		=> C("STATEPENDING_CONV"),
					'orderAttribute'	=> 1, //
					'pmId'				=> 0,
					'isFixed' 			=> 2,
					'ORchannelId'		=> 0,
					'channelId' 		=> 0,
					'ORcalcWeight' 		=> 0,
					'calcWeight' 		=> 0,
					'calcShipping'		=> 0,
					'orderAddTime'		=> time(),
					'isSendEmail'		=> 0,
					'isNote'			=> $isNote,
					'isCopy'			=> 0,
					'isSplit'			=> 0,
					'combinePackage'	=> 0,
					'combineOrder'		=> 0,
					'isBuji'			=> 0,
					'isLock' 			=> 0,
					'lockUser' 			=> 0,
					'lockTime'			=> 0,
					'completeTime'		=> 0,
					'storeId'			=> 1,
					'is_overseaorder'	=> 0,
					'is_offline'		=> 0,
					'is_delete'			=> 0,
			);
			
			//订单扩展表
			$orderExtension = array(
				'declaredPrice'		=> 0.00, 
				'paymentStatus'		=> $orderInfo['CheckoutStatus']['Status'],
				'transId'			=> 1,
				'PayPalPaymentId'	=> !empty($orderInfo['ExternalTransaction']['ExternalTransactionID']) ? $orderInfo['ExternalTransaction']['ExternalTransactionID'] : 0,
				'site'				=> $site,
				'orderId'			=> $orderInfo['OrderID'],
				'platformUsername'	=> $orderInfo['BuyerUserID'],
				'currency'			=> $firstTransaction['TransactionPrice attr']['currencyID'],
				'feedback'			=> !empty($customerMessage)?$customerMessage:'none',
				'PayPalEmailAddress'=> $this->getPayPalEmailAddress($firstTransaction['Item']['ItemID']),//收款邮箱为抓取
				'eBayPaymentStatus' => $orderInfo['CheckoutStatus']['eBayPaymentStatus'],
			);
			
			//用户表
			$orderUserInfo = array(
				'username'			=> mysql_real_escape_string(str_rep($orderInfo['ShippingAddress']['Name'])),
			 	'platformUsername'	=> $orderInfo['BuyerUserID'],
			 	'email'				=> $firstTransaction['Buyer']['Email'],
			 	'countryName'		=> str_rep($orderInfo['ShippingAddress']['CountryName']),
			 	'countrySn'			=> str_rep($orderInfo['ShippingAddress']['Country']),
			 	'currency'			=> $firstTransaction['TransactionPrice attr']['currencyID'],
			 	'state' 			=> str_rep($orderInfo['ShippingAddress']['StateOrProvince']),
			 	'city' 				=> str_rep($orderInfo['ShippingAddress']['CityName']),
			 	'street' 			=> str_rep($orderInfo['ShippingAddress']['Street1']),
			 	'address2' 			=> !empty($orderInfo['ShippingAddress']['Street2']) ? str_rep($orderInfo['ShippingAddress']['Street2']):'11',
			 	'address3' 			=> '11',
			 	'landline'	 		=> $orderInfo['ShippingAddress']['Phone'],
			 	'phone' 			=> '11',  //此字段哪里获取值
			 	'zipCode' 			=> $orderInfo['ShippingAddress']['PostalCode'],
			);
			
			//订单明细表数据
			if($orderAttribute == 1){
				//单料号订单，转为多料号数组一并处理
				$transactionArray = array($firstTransaction);
			}else if($orderAttribute == 3){
				$transactionArray = $orderInfo['TransactionArray']['Transaction'];
			}
			$orderDetail = array();
			foreach($transactionArray as $transaction){
				//检查是否多属性刊登
				$onlineSku = $transaction['Item']['SKU'];
				if(!empty($transaction['Item']['Variation']['VariationSpecifics']['NameValueList'])){
					$onlineSku = $transaction['Item']['Variation']['SKU'];
				}
				$skus = explode(':',$onlineSku);
				$sku  = strtoupper($skus[0]);
				$orderDetail[] = array(
						'orderDetail'=> array(
								'recordNumber'		=> $transaction['ShippingDetails']['SellingManagerSalesRecordNumber'],
								'itemPrice'			=> $transaction['TransactionPrice'],
								'ORsku'				=> '11',
								'sku'				=> $sku,
								'onlinesku'			=> $onlineSku,
								'amount'			=> $transaction['QuantityPurchased'],
								'shippingFee'		=> $transaction['ActualShippingCost'],
								'reviews'			=> 0,
								'createdTime'		=> time(),
								'storeId'			=> 1,
								'is_delete'			=> 0,
						),
						'orderDetailExtension' => array(
								'itemId'			=> $transaction['Item']['ItemID'],
								'transId'			=> $transaction['TransactionID'],
								'itemTitle'			=> str_rep($transaction['Item']['Title']),
								'itemURL'			=>'11',
								'shippingType'		=> $orderInfo['ShippingServiceSelected']['ShippingService'],
								'FinalValueFee'		=> $transaction['FinalValueFee'],
								'FeeOrCreditAmount'	=> !empty($orderInfo['ExternalTransaction']['FeeOrCreditAmount'])?$orderInfo['ExternalTransaction']['FeeOrCreditAmount']:0,
								'ListingType'		=> '11',
								'note'				=> !empty($customerMessage)?empty($customerMessage):'11',
					)
				);
			}
			//组装数据
			$inserOrder = array(
				'order' 			=>	$order,
				'orderExtension' 	=> 	$orderExtension,
				'orderUserInfo' 	=>	$orderUserInfo,
				'orderDetail'		=> 	$orderDetail
			);
			//拦截逻辑，计算重量运费
			/**待实现**/
			//file_put_contents('test.php', var_export($inserOrder, true));
			if(M('OrderAdd')->insertOrderPerfect($inserOrder)){
				return 1;
			}

			return $inserOrder;
		}
	}
	
	/**
	 * 获取收款邮箱
	 * @param number $itemId 产品id
	 * @return string 收款邮箱
	 * @author czq
	 */
	public function getPayPalEmailAddress($itemId){
		$OrderItemObject 	= F('ebay.package.GetItem');
		$OrderItemObject->setRequestConfig($this->authorize);
		$paypalInfo			= $OrderItemObject->getPayPalEmailAddress($itemId);
		$paypalInfo = XML_unserialize($paypalInfo);
		return isset($paypalInfo['GetItemResponse']['Item']['PayPalEmailAddress']) ? $paypalInfo['GetItemResponse']['Item']['PayPalEmailAddress'] : '';	
	}
	
	/**
	 * 标记发货，未上传跟踪号
	 * @param array $trans
	 * @return boolean
	 * @author czq
	 */
	public function markOrderShipped($trans){
		$completeSaleObj = F('ebay.package.CompleteSale');
		$completeSaleObj->setRequestConfig($this->authorize);
		$resve = $completeSaleObj->markOrderShipped($trans);
		$resve = XML_unserialize($resve);
		if($resve['CompleteSaleResponse']['ACK'] == 'Success'){
			return true;
		}
		return false;
	}
	
	/**
	 * 上传跟踪号
	 * @param array $trans
	 * @return  boolean
	 * @author czq
	 */
	public function uploadTrackNo($trans){
		$completeSaleObj = F('ebay.package.CompleteSale');
		$completeSaleObj->setRequestConfig($this->authorize);
		$resve = $completeSaleObj->update_order_shippingdetail_to_ebay($trans);
		$resve = XML_unserialize($resve);
		if($resve['CompleteSaleResponse']['ACK'] == 'Success'){
			return true;
		}
		return false;
	}
	
}
?>	