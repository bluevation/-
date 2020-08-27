<?php
if(!defined('_TUBEWEB_')) exit; // 개별 페이지 접근 불가
ini_set("include_path",TB_PATH);
require_once 'nhnapi-simplecryptlib.php';
require_once TB_PATH.'/pear_temp/PEAR/HTTP/Request.php';
//require_once 'domxml.php';
if (PHP_VERSION>='5')
	require_once(TB_PATH.'/pear_temp/domxml-php4-to-php5.php');

function set_naverpay_orderinfo($idx, $od_id) {
	//상수 선언
	$accessLicense = "0100010000b3561eca661edf7b06f954ad1c645f804820ae683d715e7a18ef1d8b4f2b0002"; //AccessLicense Key 입력, PDF파일 참조
	$key= "AQABAACSRxWFlcZ69MGgI0qdmAwPGb3MZwvX/PgR8FdVYlYj0Q=="; ////SecretKey 입력, PDF파일 참조
	$service = "MallService41";

	$operation = "GetProductOrderInfoList";
	$detailLevel = "Full";
	$version = "4.1";
	$targetUrl = "http://sandbox.api.naver.com/Checkout/MallService41";

	//echo "<html><head><META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=UTF-8\"></head>";
	//echo "<body><pre>\n";

	//NHNAPISCL 객체생성
	$scl = new NHNAPISCL();
	//타임스탬프를 포맷에 맞게 생성
	$timestamp = $scl->getTimestamp();
	//hmac-sha256서명생성
	$signature = $scl->generateSign($timestamp . $service . $operation, $key);

	//soap template에 생성한 값을 입력하여 요청메시지 완성
	$request_body="
	<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:mall=\"http://mall.checkout.platform.nhncorp.com/\" xmlns:base=\"http://base.checkout.platform.nhncorp.com/\">
	   <soapenv:Header/>
	   <soapenv:Body>
		  <mall:GetProductOrderInfoListRequest>
			 <base:AccessCredentials>
				<base:AccessLicense>".$accessLicense."</base:AccessLicense>
				<base:Timestamp>".$timestamp."</base:Timestamp>
				<base:Signature>".$signature."</base:Signature>
			 </base:AccessCredentials>
			 <base:RequestID></base:RequestID>
			 <base:DetailLevel>".$detailLevel."</base:DetailLevel><base:Version>".$version."</base:Version>
			 <mall:ProductOrderIDList>".$od_id."</mall:ProductOrderIDList>
			 <mall:MallID>salesman1</mall:MallID>
		  </mall:GetProductOrderInfoListRequest>
	   </soapenv:Body>
	</soapenv:Envelope>";
	//요청메시지 확인
	//echo "request=" . str_replace('<','&lt;', str_replace('>', '&gt;', $request_body)) . "\n\n";

	//http post방식으로 요청 전송
	$rq = new HTTP_Request($targetUrl);
	$rq->addHeader("Content-Type", "text/xml;charset=UTF-8");
	$rq->addHeader("SOAPAction", $service . "#" . $operation);
	$rq->setBody($request_body);
	$result = $rq->sendRequest();
	if (PEAR::isError($result)) {
	   echo "error:". $result->toString(). "\n";
	   return;
	}

	//응답메시지 확인
	$rcode = $rq->getResponseCode();
	if($rcode!='200'){
	   echo "error: http response code=". $rcode. "\n";
	   return;
	}
	$response = $rq->getResponseBody();
	echo "response=" . str_replace('<','&lt;', str_replace('>', '&gt;', $response)) . "\n\n";

	//domxml을 이용하여 xml 파싱
	if(!$dom = domxml_open_mem($response)){
	 echo "error:xml parsing error\n";
	 return;
	}

	$root = $dom->document_element();
	$soapbody = $root->get_elements_by_tagname('Body');
	if(count($soapbody)!=1){
	   echo "error:invalid body counts\n";
	   return;
	}


	$Response = $soapbody[0]->get_elements_by_tagname('GetProductOrderInfoListResponse');
	if(!$Response && count($Response)==0) {
	   echo "error:invalid GetProductOrderInfoListResponse counts\n";
	   return;
	}

	$ResponseType_Temp = $Response[0]->get_elements_by_tagname('ResponseType');
	if(!$ResponseType_Temp && count($ResponseType_Temp)==0) {
	   echo "error:invalid ResponseType counts\n";
	   return;
	}

	// ResponseType이 SUCCESS 일 경우에만 값을 가져옴

	$ResponseType = $ResponseType_Temp[0]->get_content();
	if($ResponseType == "SUCCESS") {
		$ProductOrderInfoList = $Response[0]->get_elements_by_tagname('ProductOrderInfoList');
		if(!$ProductOrderInfoList && count($ProductOrderInfoList)==0) {
		   echo "error:invalid ProductOrderInfoList counts\n";
		   return;
		}

		$OrderID = $Response[0]->get_elements_by_tagname('OrderID');
		if(!$OrderID && count($OrderID)==0) {
		   echo "error:invalid OrderID counts\n";
		   return;
		}

		$ProductOrderID = $Response[0]->get_elements_by_tagname('ProductOrderID');
		if(!$ProductOrderID && count($ProductOrderID)==0) {
		   echo "error:invalid ProductOrderID counts\n";
		   return;
		}

		$ProductName = $Response[0]->get_elements_by_tagname('ProductName');
		if(!$ProductName && count($ProductName)==0) {
		   echo "error:invalid ProductName counts\n";
		   return;
		}
		
		$ProductOrderStatus = $Response[0]->get_elements_by_tagname('ProductOrderStatus');
		if(!$ProductOrderStatus && count($ProductOrderStatus)==0) {
		   echo "error:invalid ProductOrderStatus counts\n";
		   return;
		}

		$Quantity = $Response[0]->get_elements_by_tagname('Quantity');
		if(!$Quantity && count($Quantity)==0) {
		   echo "error:invalid Quantity counts\n";
		   return;
		}

		$UnitPrice = $Response[0]->get_elements_by_tagname('UnitPrice');
		if(!$UnitPrice && count($UnitPrice)==0) {
		   echo "error:invalid UnitPrice counts\n";
		   return;
		}

		$PaymentMeans = $Response[0]->get_elements_by_tagname('PaymentMeans');
		if(!$PaymentMeans && count($PaymentMeans)==0) {
		   echo "error:invalid PaymentMeans counts\n";
		   return;
		}

		$TotalPaymentAmount = $Response[0]->get_elements_by_tagname('TotalPaymentAmount');
		if(!$TotalPaymentAmount && count($TotalPaymentAmount)==0) {
		   $TotalPayment = 0;
		} else {
			$TotalPayment = $TotalPaymentAmount[0]->get_content();
		}

		$ProductOption = $Response[0]->get_elements_by_tagname('ProductOption');
		if(!$ProductOption && count($ProductOption)==0) {
		   $Option = "";
		} else {
			$Option = $ProductOption[0]->get_content();
		}

		$OrderDate = $Response[0]->get_elements_by_tagname('OrderDate');
		if(!$OrderDate && count($OrderDate)==0) {
		   echo "error:invalid OrderDate counts\n";
		   return;
		}

		$DeliveryFeeAmount = $Response[0]->get_elements_by_tagname('DeliveryFeeAmount');
		if(!$DeliveryFeeAmount && count($DeliveryFeeAmount)==0) {
		   $DeliveryFee = 0;
		} else {
			$DeliveryFee = $DeliveryFeeAmount[0]->get_content();
		}

		$bg = 'list'.($idx%2);
		switch($ProductOrderStatus[0]->get_content()) {
			case 'PAYMENT_WAITING':
				$status = "입금 대기";
				break;
			case 'PAYED':
				$status = "결제 완료";
				break;
			case 'DELIVERING':
				$status = "배송 중";
				break;
			case 'DELIVERED':
				$status = "배송 완료";
				break;
			case 'PURCHASE_DECIDED':
				$status = "구매 확정";
				break;
			case 'EXCHANGED':
				$status = "교환";
				break;
			case 'CANCELED':
				$status = "취소";
				break;
			case 'RETURNED':
				$status = "반품";
				break;
			case 'CANCELED_BY_NOPAYMENT':
				$status = "미입금 취소";
				break;
		}

		echo "<tr class=\"".$bg." ".$idx."\">\n";
			echo "<td>\n";
			echo "<input type=\"hidden\" name=\"od_id[".$idx."]\" value=\"".$OrderID[0]->get_content()."\">\n";
			echo "<label for=\"chk_".$idx."\" class=\"sound_only\">주문번호 ".$OrderID[0]->get_content()."</label>\n";
			echo "<input type=\"checkbox\" name=\"chk[]\" value=\"".$idx."\" id=\"chk_".$idx."\">\n";
			echo "</td>\n";
			echo "<td>\n";
			echo $OrderID[0]->get_content() . "\n";
			echo "</td>\n";
			echo "<td>\n";
			echo $ProductOrderID[0]->get_content(). "\n";
			echo "</td>\n";
			echo "<td>\n";
			echo $OrderDate[0]->get_content(). "\n";
			echo "</td>\n";
			echo "<td>\n";
			echo $status. "\n";
			echo "</td>\n";
			echo "<td>\n";
			echo $ProductName[0]->get_content() . "\n";
			echo "</td>\n";
			echo "<td>\n";
			echo $Option."\n";
			echo "</td>\n";
			echo "<td>\n";
			echo $Quantity[0]->get_content() . "\n";
			echo "</td>\n";
			echo "<td>\n";
			echo $UnitPrice[0]->get_content() . "\n";
			echo "</td>\n";
			echo "<td>\n";
			echo $DeliveryFee. "\n";
			echo "</td>\n";
			echo "<td>\n";
			echo $PaymentMeans[0]->get_content() . "\n";
			echo "</td>\n";
			echo "<td>\n";
			echo $TotalPayment . "\n";
			echo "</td>\n";
		echo "</tr>\n";

		//echo "ProductName=" . $ProductName[$i]->get_content(). "\n";
		//echo "ProductOrderStatus=" . $ProductOrderStatus[$i]->get_content(). "\n";
	} else {
		echo "error:ResponseType is not SUCCESS\n";
	}

}

function set_naverpay_orderlist($type) {
	//상수 선언
	$accessLicense = "0100010000b3561eca661edf7b06f954ad1c645f804820ae683d715e7a18ef1d8b4f2b0002"; //AccessLicense Key 입력, PDF파일 참조
	$key= "AQABAACSRxWFlcZ69MGgI0qdmAwPGb3MZwvX/PgR8FdVYlYj0Q=="; ////SecretKey 입력, PDF파일 참조
	$service = "MallService41";

	$operation = $type;
	$detailLevel = "Full";
	$version = "4.1";
	$targetUrl = "http://sandbox.api.naver.com/Checkout/MallService41";

	//echo "<html><head><META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=UTF-8\"></head>";
	//echo "<body><pre>\n";

	//NHNAPISCL 객체생성
	$scl = new NHNAPISCL();
	//타임스탬프를 포맷에 맞게 생성
	$timestamp = $scl->getTimestamp();
	//hmac-sha256서명생성
	$signature = $scl->generateSign($timestamp . $service . $operation, $key);

	//soap template에 생성한 값을 입력하여 요청메시지 완성
	$request_body="
	<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:mall=\"http://mall.checkout.platform.nhncorp.com/\" xmlns:base=\"http://base.checkout.platform.nhncorp.com/\">
	   <soapenv:Header/>
	   <soapenv:Body>
		  <mall:GetChangedProductOrderListRequest>
			 <base:AccessCredentials>
				<base:AccessLicense>".$accessLicense."</base:AccessLicense>
				<base:Timestamp>".$timestamp."</base:Timestamp>
				<base:Signature>".$signature."</base:Signature>
			 </base:AccessCredentials>
			 <base:RequestID></base:RequestID>
			 <base:DetailLevel>".$detailLevel."</base:DetailLevel><base:Version>".$version."</base:Version>
			 <base:InquiryTimeFrom>2012-01-01T00:00:00+09:00</base:InquiryTimeFrom>
			 <mall:LastChangedStatusCode>PURCHASE_DECIDED</mall:LastChangedStatusCode>
			 <mall:MallID>salesman1</mall:MallID>
		  </mall:GetChangedProductOrderListRequest>
	   </soapenv:Body>
	</soapenv:Envelope>";
	//요청메시지 확인
	//echo "request=" . str_replace('<','&lt;', str_replace('>', '&gt;', $request_body)) . "\n\n";

	//http post방식으로 요청 전송
	$rq = new HTTP_Request($targetUrl);
	$rq->addHeader("Content-Type", "text/xml;charset=UTF-8");
	$rq->addHeader("SOAPAction", $service . "#" . $operation);
	$rq->setBody($request_body);
	$result = $rq->sendRequest();
	if (PEAR::isError($result)) {
	   echo "error:". $result->toString(). "\n";
	   return;
	}
	//응답메시지 확인
	$rcode = $rq->getResponseCode();
	if($rcode!='200'){
	   echo "error: http response code=". $rcode. "\n";
	   return;
	}
	$response = $rq->getResponseBody();
	//echo "response=" . str_replace('<','&lt;', str_replace('>', '&gt;', $response)) . "\n\n";

	//domxml을 이용하여 xml 파싱
	if(!$dom = domxml_open_mem($response)){
	 echo "error:xml parsing error\n";
	 return;
	}

	$root = $dom->document_element();
	$soapbody = $root->get_elements_by_tagname('Body');
	if(count($soapbody)!=1){
	   echo "error:invalid body counts\n";
	   return;
	}


	$Response = $soapbody[0]->get_elements_by_tagname($type.'Response');
	if(!$Response && count($Response)==0) {
	   echo "error:invalid ".$type."Response counts\n";
	   return;
	}

	$ResponseType_Temp = $Response[0]->get_elements_by_tagname('ResponseType');
	if(!$ResponseType_Temp && count($ResponseType_Temp)==0) {
	   echo "error:invalid ResponseType counts\n";
	   return;
	}

	// ResponseType이 SUCCESS 일 경우에만 값을 가져옴

	$ResponseType = $ResponseType_Temp[0]->get_content();
	if($ResponseType == "SUCCESS") {
		$OrderID = $Response[0]->get_elements_by_tagname('OrderID');
		if(!$OrderID && count($OrderID)==0) {
		   echo "error:invalid OrderID counts\n";
		   return;
		}
		
		$ProductOrderID = $Response[0]->get_elements_by_tagname('ProductOrderID');
		if(!$ProductOrderID && count($ProductOrderID)==0) {
		   echo "error:invalid ProductOrderID counts\n";
		   return;
		}
		
		$LastChangedStatus = $Response[0]->get_elements_by_tagname('LastChangedStatus');
		if(!$LastChangedStatus && count($LastChangedStatus)==0) {
		   echo "error:invalid LastChangedStatus counts\n";
		   return;
		}
		
		$LastChangedDate = $Response[0]->get_elements_by_tagname('LastChangedDate');
		if(!$LastChangedDate && count($LastChangedDate)==0) {
		   echo "error:invalid LastChangedDate counts\n";
		   return;
		}
		
		$ProductOrderStatus = $Response[0]->get_elements_by_tagname('ProductOrderStatus');
		if(!$ProductOrderStatus && count($ProductOrderStatus)==0) {
		   echo "error:invalid ProductOrderStatus counts\n";
		   return;
		}
		
		$ClaimTypeTemp = $Response[0]->get_elements_by_tagname('ClaimType');
		if($ClaimTypeTemp) {
		   $ClaimType = $ClaimTypeTemp[0]->get_content();
		}
		
		$ClaimStatusTemp = $Response[0]->get_elements_by_tagname('ClaimStatus');
		if($ClaimStatusTemp) {
		   $ClaimStatus = $ClaimStatusTemp[0]->get_content();
		}
		
		$PaymentDateTemp = $Response[0]->get_elements_by_tagname('ClaimStatus');
		if($PaymentDateTemp) {
		   $PaymentDate = $PaymentDateTemp[0]->get_content();
		}

		$ReturnedDataCount = $Response[0]->get_elements_by_tagname('ReturnedDataCount');
		//평문
		for($i=0; $i<$ReturnedDataCount[0]->get_content(); $i++){
			$od_id = $ProductOrderID[$i]->get_content();
			set_naverpay_orderinfo($i, $od_id);
		}
	}
	else {
		echo "error:ResponseType is not SUCCESS\n";
	}

}

?>