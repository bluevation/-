<?php
if(!defined('_TUBEWEB_')) exit; // 개별 페이지 접근 불가
ini_set("include_path",TB_PATH);
require_once 'nhnapi-simplecryptlib.php';
require_once TB_PATH.'/pear_temp/PEAR/HTTP/Request.php';
//require_once 'domxml.php';
if (PHP_VERSION>='5')
	require_once(TB_PATH.'/pear_temp/domxml-php4-to-php5.php');


function set_naverreview($idx, $od_id, $st_date, $ed_date) {
	//상수 선언
	$accessLicense = "0100010000dcd00c71d04414d2a83c910b0c329afec0096ef125829ce2efb3e53c8685fd4d"; //AccessLicense Key 입력, PDF파일 참조
	$key= "AQABAAA6dDAJfxlVUymJE5j7+M0TC6J1yEgpx8fECDBVasJx3A=="; ////SecretKey 입력, PDF파일 참조
	$service = "MallService41";

	$operation = "GetPurchaseReviewList";
	$detailLevel = "Full";
	$version = "4.1";
	$targetUrl = "http://ec.api.naver.com/Checkout/MallService41";

	echo "<html><head><META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=UTF-8\"></head>";
	echo "<body><pre>\n";

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
		  <mall:GetPurchaseReviewListRequest>
			 <base:AccessCredentials>
				<base:AccessLicense>".$accessLicense."</base:AccessLicense>
				<base:Timestamp>".$timestamp."</base:Timestamp>
				<base:Signature>".$signature."</base:Signature>
			 </base:AccessCredentials>
			 <base:RequestID></base:RequestID>
			 <base:DetailLevel>".$detailLevel."</base:DetailLevel>
			 <base:Version>".$version."</base:Version>
			 <base:InquiryTimeFrom>".$st_date."</base:InquiryTimeFrom>
			 <mall:MallID>np_inaia518049</mall:MallID>
		  </mall:GetPurchaseReviewListRequest>
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
	   //echo "error:". $result->toString(). "\n";
	   return;
	}

	//응답메시지 확인
	$rcode = $rq->getResponseCode();
	if($rcode!='200'){
	   //echo "error: http response code=". $rcode. "\n";
	   return;
	}
	$response = $rq->getResponseBody();
	//echo "response=" . str_replace('<','&lt;', str_replace('>', '&gt;', $response)) . "\n\n";

	//domxml을 이용하여 xml 파싱
	if(!$dom = domxml_open_mem($response)){
	 //echo "error:xml parsing error\n";
	 return;
	}

	$root = $dom->document_element();
	$soapbody = $root->get_elements_by_tagname('Body');
	if(count($soapbody)!=1){
	   //echo "error:invalid body counts\n";
	   return;
	}


	$GetPurchaseReviewListResponse = $soapbody[0]->get_elements_by_tagname('GetPurchaseReviewListResponse');
	if(!$GetPurchaseReviewListResponse && count($GetPurchaseReviewListResponse)==0) {
	   //echo "error:invalid GetPurchaseReviewListResponse counts\n";
	   return;
	}

	$ResponseType_Temp = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('ResponseType');
	if(!$ResponseType_Temp && count($ResponseType_Temp)==0) {
	   //echo "error:invalid ResponseType counts\n";
	   return;
	}

	// ResponseType이 SUCCESS 일 경우에만 값을 가져옴
	$ResponseType = $ResponseType_Temp[0]->get_content();
	if($ResponseType == "SUCCESS") {
		$MallID = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('MallID');
		if(!$MallID && count($MallID)==0) {
		   //echo "error:invalid MallID counts\n";
		   return;
		}

		$ProductOrderID = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('ProductOrderID');
		if(!$ProductOrderID && count($ProductOrderID)==0) {
		   //echo "error:invalid ProductOrderID counts\n";
		   return;
		}

		$ProductID = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('ProductID');
		if(!$ProductID && count($ProductID)==0) {
		   //echo "error:invalid ProductID counts\n";
		   return;
		}

		$ProductName = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('ProductName');
		if(!$ProductName && count($ProductName)==0) {
		   //echo "error:invalid ProductName counts\n";
		   return;
		}

		$ReviewTitle = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('Title');
		if(!$ReviewTitle && count($ReviewTitle)==0) {
		   //echo "error:invalid ReviewTitle counts\n";
		   return;
		}

		$PurchaseReviewId = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('PurchaseReviewId');
		if(!$PurchaseReviewId && count($PurchaseReviewId)==0) {
		   //echo "error:invalid PurchaseReviewId counts\n";
		   return;
		}

		$ReviewScore = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('PurchaseReviewScore');
		if(!$ReviewScore && count($ReviewScore)==0) {
		   //echo "error:invalid ReviewScore counts\n";
		   return;
		}

		$ReturnedDataCount = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('ReturnedDataCount');
		for($i=0; $i<$ReturnedDataCount[0]->get_content(); $i++){
			$CreateYmdt = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('CreateYmdt');
			if(!$CreateYmdt && count($CreateYmdt)==0) {
				$createDate = '';
			} else {
				$createDate = date("Y-m-d H:i:s",   strtotime($CreateYmdt[$i]->get_content()));
			}

			$ModifyYmdt = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('ModifyYmdt');
			if(!$ModifyYmdt && count($ModifyYmdt)==0) {
				$modifyDate = '';
			} else {
				$modifyDate = date("Y-m-d H:i:s",   strtotime($ModifyYmdt[$i]->get_content()));
			}

			$ProductOption = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('ProductOption');
			if(!$ProductOption && count($ProductOption)==0) {
			   $option = '';
			} else {
				$option = $ProductOption[$i]->get_content();
			}

			/*echo "MallID=" . $MallID[$i]->get_content(). "\n"; // 가맹점 아이디 mall_id
			echo "ProductID=" . $ProductID[$i]->get_content(). "\n"; // 상품 번호 product_id
			echo "ProductName=" . $ProductName[$i]->get_content(). "\n"; // 상품명 product_name
			echo "ProductOption=" . $option . "\n"; // 상품 옵션(옵션명) product_option
			echo "ProductOrderID=" . $ProductOrderID[$i]->get_content(). "\n"; // 상품 주문 번호 product_order_id
			echo "PurchaseReviewId=" . $PurchaseReviewId[$i]->get_content(). "\n"; // 구매평 일련 번호 purchase_review_id
			echo "ReviewScore=" . $ReviewScore[$i]->get_content(). "\n"; // 구매 만족도 -일반: 불만족(0), 보통(1), 만족(2) -프리미엄: 추천안함(10), 보통(11), 추천(12), 적극추천(13) review_score
			echo "ReviewTitle=" . $ReviewTitle[$i]->get_content(). "\n"; // 일반 구매평 내용 또는 프리미엄 구매평 제목 review_title
			echo "CreateYmdt=" . $createDate . "\n"; // 작성일 create_date
			echo "ModifyYmdt=" . $modifyDate . "\n"; // 수정일 modify_date
			echo "----------------------------------------------------------\n";*/
			
			$product_id = $ProductID[$i]->get_content();
			$product_order_id = $ProductOrderID[$i]->get_content();
			$purchase_review_id = $PurchaseReviewId[$i]->get_content();

			$sql_review = " select * from shop_naver_review where product_id = '{$product_id}' and product_order_id = '{$product_order_id}' and purchase_review_id = '{$purchase_review_id}' ";
			$rv = sql_fetch($sql_review);
			if(!$rv) {
				unset($value);
				$value['mall_id'] = $MallID[$i]->get_content();
				$value['review_type'] = '0';
				$value['product_id'] = $product_id;
				$value['product_name'] = $ProductName[$i]->get_content();
				$value['product_option'] = $option;
				$value['product_order_id'] = $product_order_id;
				$value['purchase_review_id'] = $purchase_review_id;
				$value['review_score'] = $ReviewScore[$i]->get_content();
				$value['review_title'] = $ReviewTitle[$i]->get_content();
				$value['create_date']	= $createDate;
				$value['modify_date'] = $modifyDate;
				insert("shop_naver_review", $value);
			} else {
				if($rv['review_title'] != $ReviewTitle[$i]->get_content()|| $rv['review_score'] != $ReviewScore[$i]->get_content()) {
					unset($value);
					$value['review_score'] = $ReviewScore[$i]->get_content();
					$value['review_title'] = $ReviewTitle[$i]->get_content();
					$value['modify_date'] = $modifyDate;
					update("shop_naver_review",$value,"where product_id = '{$product_id}' and product_order_id = '{$product_order_id}' and purchase_review_id = '{$purchase_review_id}' and review_type = '0' ");
				}
			}
		}
	}
	else {
		//echo "error:ResponseType is not SUCCESS\n";
	}
}

function set_naverreview_premium($idx, $od_id, $st_date, $ed_date) {
	//상수 선언
	$accessLicense = "0100010000dcd00c71d04414d2a83c910b0c329afec0096ef125829ce2efb3e53c8685fd4d"; //AccessLicense Key 입력, PDF파일 참조
	$key= "AQABAAA6dDAJfxlVUymJE5j7+M0TC6J1yEgpx8fECDBVasJx3A=="; ////SecretKey 입력, PDF파일 참조
	$service = "MallService41";

	$operation = "GetPurchaseReviewList";
	$detailLevel = "Full";
	$version = "4.1";
	$targetUrl = "http://ec.api.naver.com/Checkout/MallService41";

	echo "<html><head><META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=UTF-8\"></head>";
	echo "<body><pre>\n";

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
		  <mall:GetPurchaseReviewListRequest>
			 <base:AccessCredentials>
				<base:AccessLicense>".$accessLicense."</base:AccessLicense>
				<base:Timestamp>".$timestamp."</base:Timestamp>
				<base:Signature>".$signature."</base:Signature>
			 </base:AccessCredentials>
			 <base:RequestID></base:RequestID>
			 <base:DetailLevel>".$detailLevel."</base:DetailLevel>
			 <base:Version>".$version."</base:Version>
			 <base:InquiryTimeFrom>".$st_date."</base:InquiryTimeFrom>
			 <mall:MallID>np_inaia518049</mall:MallID>
			 <mall:PurchaseReviewClassType>PREMIUM</mall:PurchaseReviewClassType>
		  </mall:GetPurchaseReviewListRequest>
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
	   //echo "error:". $result->toString(). "\n";
	   return;
	}

	//응답메시지 확인
	$rcode = $rq->getResponseCode();
	if($rcode!='200'){
	   //echo "error: http response code=". $rcode. "\n";
	   return;
	}
	$response = $rq->getResponseBody();
	//echo "response=" . str_replace('<','&lt;', str_replace('>', '&gt;', $response)) . "\n\n";

	//domxml을 이용하여 xml 파싱
	if(!$dom = domxml_open_mem($response)){
	 //echo "error:xml parsing error\n";
	 return;
	}

	$root = $dom->document_element();
	$soapbody = $root->get_elements_by_tagname('Body');
	if(count($soapbody)!=1){
	   //echo "error:invalid body counts\n";
	   return;
	}


	$GetPurchaseReviewListResponse = $soapbody[0]->get_elements_by_tagname('GetPurchaseReviewListResponse');
	if(!$GetPurchaseReviewListResponse && count($GetPurchaseReviewListResponse)==0) {
	   //echo "error:invalid GetPurchaseReviewListResponse counts\n";
	   return;
	}

	$ResponseType_Temp = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('ResponseType');
	if(!$ResponseType_Temp && count($ResponseType_Temp)==0) {
	   //echo "error:invalid ResponseType counts\n";
	   return;
	}

	// ResponseType이 SUCCESS 일 경우에만 값을 가져옴
	$ResponseType = $ResponseType_Temp[0]->get_content();
	if($ResponseType == "SUCCESS") {
		$MallID = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('MallID');
		if(!$MallID && count($MallID)==0) {
		   //echo "error:invalid MallID counts\n";
		   return;
		}

		$ProductOrderID = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('ProductOrderID');
		if(!$ProductOrderID && count($ProductOrderID)==0) {
		   //echo "error:invalid ProductOrderID counts\n";
		   return;
		}

		$ProductID = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('ProductID');
		if(!$ProductID && count($ProductID)==0) {
		   //echo "error:invalid ProductID counts\n";
		   return;
		}

		$ProductName = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('ProductName');
		if(!$ProductName && count($ProductName)==0) {
		   //echo "error:invalid ProductName counts\n";
		   return;
		}

		$ReviewTitle = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('Title');
		if(!$ReviewTitle && count($ReviewTitle)==0) {
		   //echo "error:invalid ReviewTitle counts\n";
		   return;
		}

		$PurchaseReviewId = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('PurchaseReviewId');
		if(!$PurchaseReviewId && count($PurchaseReviewId)==0) {
		   //echo "error:invalid PurchaseReviewId counts\n";
		   return;
		}

		$ReviewScore = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('PurchaseReviewScore');
		if(!$ReviewScore && count($ReviewScore)==0) {
		   //echo "error:invalid ReviewScore counts\n";
		   return;
		}

		$Content = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('Content');
			if(!$Content && count($Content)==0) {
				return;
			}

		$ReturnedDataCount = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('ReturnedDataCount');
		for($i=0; $i<$ReturnedDataCount[0]->get_content(); $i++){
			$CreateYmdt = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('CreateYmdt');
			if(!$CreateYmdt && count($CreateYmdt)==0) {
				$createDate = '';
			} else {
				$createDate = date("Y-m-d H:i:s",   strtotime($CreateYmdt[$i]->get_content()));
			}

			$ModifyYmdt = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('ModifyYmdt');
			if(!$ModifyYmdt && count($ModifyYmdt)==0) {
				$modifyDate = '';
			} else {
				$modifyDate = date("Y-m-d H:i:s",   strtotime($ModifyYmdt[$i]->get_content()));
			}

			$ProductOption = $GetPurchaseReviewListResponse[0]->get_elements_by_tagname('ProductOption');
			if(!$ProductOption && count($ProductOption)==0) {
			   $option = '';
			} else {
				$option = $ProductOption[$i]->get_content();
			}

			/*echo "MallID=" . $MallID[$i]->get_content(). "\n"; // 가맹점 아이디 mall_id
			echo "ProductID=" . $ProductID[$i]->get_content(). "\n"; // 상품 번호 product_id
			echo "ProductName=" . $ProductName[$i]->get_content(). "\n"; // 상품명 product_name
			echo "ProductOption=" . $option . "\n"; // 상품 옵션(옵션명) product_option
			echo "ProductOrderID=" . $ProductOrderID[$i]->get_content(). "\n"; // 상품 주문 번호 product_order_id
			echo "PurchaseReviewId=" . $PurchaseReviewId[$i]->get_content(). "\n"; // 구매평 일련 번호 purchase_review_id
			echo "ReviewScore=" . $ReviewScore[$i]->get_content(). "\n"; // 구매 만족도 -일반: 불만족(0), 보통(1), 만족(2) -프리미엄: 추천안함(10), 보통(11), 추천(12), 적극추천(13) review_score
			echo "ReviewTitle=" . $ReviewTitle[$i]->get_content(). "\n"; // 일반 구매평 내용 또는 프리미엄 구매평 제목 review_title
			echo "Content=" . $Content[$i]->get_content(). "\n"; // 프리미엄 구매평 내용
			echo "CreateYmdt=" . $createDate . "\n"; // 작성일 create_date
			echo "ModifyYmdt=" . $modifyDate . "\n"; // 수정일 modify_date
			echo "----------------------------------------------------------\n";*/
			
			$product_id = $ProductID[$i]->get_content();
			$product_order_id = $ProductOrderID[$i]->get_content();
			$purchase_review_id = $PurchaseReviewId[$i]->get_content();

			$sql_review_pre = " select * from shop_naver_review where product_id = '{$product_id}' and product_order_id = '{$product_order_id}' and purchase_review_id = '{$purchase_review_id}' ";
			$rv_pre = sql_fetch($sql_review_pre);
			if(!$rv_pre) {
				unset($value_pre);
				$value_pre['mall_id'] = $MallID[$i]->get_content();
				$value_pre['review_type'] = '1';
				$value_pre['product_id'] = $product_id;
				$value_pre['product_name'] = $ProductName[$i]->get_content();
				$value_pre['product_option'] = $option;
				$value_pre['product_order_id'] = $product_order_id;
				$value_pre['purchase_review_id'] = $purchase_review_id;
				$value_pre['review_score'] = $ReviewScore[$i]->get_content();
				$value_pre['review_title'] = $ReviewTitle[$i]->get_content();
				$value_pre['review_content'] = $Content[$i]->get_content();
				$value_pre['create_date']	= $createDate;
				$value_pre['modify_date'] = $modifyDate;
				insert("shop_naver_review", $value_pre);
			} else {
				if($rv_pre['review_title'] != $ReviewTitle[$i]->get_content()|| $rv_pre['review_score'] != $ReviewScore[$i]->get_content() || $rv_pre['review_content'] = $Content[$i]->get_content()) {
					unset($value_pre);
					$value_pre['review_score'] = $ReviewScore[$i]->get_content();
					$value_pre['review_title'] = $ReviewTitle[$i]->get_content();
					$value_pre['review_content'] = $Content[$i]->get_content();
					$value_pre['modify_date'] = $modifyDate;
					update("shop_naver_review",$value_pre,"where product_id = '{$product_id}' and product_order_id = '{$product_order_id}' and purchase_review_id = '{$purchase_review_id}' and review_type = '1' ");
				}
			}
		}
	}
	else {
		//echo "error:ResponseType is not SUCCESS\n";
	}
}

function set_naverreview_orderinfo($idx, $od_id, $st_date, $ed_date) {
	//상수 선언
	$accessLicense = "0100010000dcd00c71d04414d2a83c910b0c329afec0096ef125829ce2efb3e53c8685fd4d"; //AccessLicense Key 입력, PDF파일 참조
	$key= "AQABAAA6dDAJfxlVUymJE5j7+M0TC6J1yEgpx8fECDBVasJx3A=="; ////SecretKey 입력, PDF파일 참조
	$service = "MallService41";

	$operation = "GetProductOrderInfoList";
	$detailLevel = "Full";
	$version = "4.1";
	$targetUrl = "http://ec.api.naver.com/Checkout/MallService41";

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
			 <mall:MallID>np_inaia518049</mall:MallID>
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
	   //echo "error:". $result->toString(). "\n";
	   return;
	}

	//응답메시지 확인
	$rcode = $rq->getResponseCode();
	if($rcode!='200'){
	   //echo "error: http response code=". $rcode. "\n";
	   return;
	}
	$response = $rq->getResponseBody();
	//echo "response=" . str_replace('<','&lt;', str_replace('>', '&gt;', $response)) . "\n\n";

	//domxml을 이용하여 xml 파싱
	if(!$dom = domxml_open_mem($response)){
	 //echo "error:xml parsing error\n";
	 return;
	}

	$root = $dom->document_element();
	$soapbody = $root->get_elements_by_tagname('Body');
	if(count($soapbody)!=1){
	   //echo "error:invalid body counts\n";
	   return;
	}


	$Response = $soapbody[0]->get_elements_by_tagname('GetProductOrderInfoListResponse');
	if(!$Response && count($Response)==0) {
	   //echo "error:invalid GetProductOrderInfoListResponse counts\n";
	   return;
	}

	$ResponseType_Temp = $Response[0]->get_elements_by_tagname('ResponseType');
	if(!$ResponseType_Temp && count($ResponseType_Temp)==0) {
	   //echo "error:invalid ResponseType counts\n";
	   return;
	}

	// ResponseType이 SUCCESS 일 경우에만 값을 가져옴

	$ResponseType = $ResponseType_Temp[0]->get_content();
	if($ResponseType == "SUCCESS") {
		$ProductOrderInfoList = $Response[0]->get_elements_by_tagname('ProductOrderInfoList');
		if(!$ProductOrderInfoList && count($ProductOrderInfoList)==0) {
		   //echo "error:invalid ProductOrderInfoList counts\n";
		   return;
		}

		$OrderID = $Response[0]->get_elements_by_tagname('OrderID');
		if(!$OrderID && count($OrderID)==0) {
		   //echo "error:invalid OrderID counts\n";
		   return;
		}

		$ProductOrderID = $Response[0]->get_elements_by_tagname('ProductOrderID');
		if(!$ProductOrderID && count($ProductOrderID)==0) {
		   //echo "error:invalid ProductOrderID counts\n";
		   return;
		}

		$ProductName = $Response[0]->get_elements_by_tagname('ProductName');
		if(!$ProductName && count($ProductName)==0) {
		   //echo "error:invalid ProductName counts\n";
		   return;
		}
		
		$ProductOrderStatus = $Response[0]->get_elements_by_tagname('ProductOrderStatus');
		if(!$ProductOrderStatus && count($ProductOrderStatus)==0) {
		   //echo "error:invalid ProductOrderStatus counts\n";
		   return;
		}

		$Quantity = $Response[0]->get_elements_by_tagname('Quantity');
		if(!$Quantity && count($Quantity)==0) {
		   //echo "error:invalid Quantity counts\n";
		   return;
		}

		$UnitPrice = $Response[0]->get_elements_by_tagname('UnitPrice');
		if(!$UnitPrice && count($UnitPrice)==0) {
		   //echo "error:invalid UnitPrice counts\n";
		   return;
		}

		$PaymentMeans = $Response[0]->get_elements_by_tagname('PaymentMeans');
		if(!$PaymentMeans && count($PaymentMeans)==0) {
		   //echo "error:invalid PaymentMeans counts\n";
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
		   //echo "error:invalid OrderDate counts\n";
		   return;
		}

		$DeliveryFeeAmount = $Response[0]->get_elements_by_tagname('DeliveryFeeAmount');
		if(!$DeliveryFeeAmount && count($DeliveryFeeAmount)==0) {
		   $DeliveryFee = 0;
		} else {
			$DeliveryFee = $DeliveryFeeAmount[0]->get_content();
		}
		
		$ReturnedDataCount = $Response[0]->get_elements_by_tagname('ReturnedDataCount');
		//평문
		for($i=0; $i<$ReturnedDataCount[0]->get_content(); $i++){
			$od_id = $ProductOrderID[$i]->get_content();
			set_naverreview($i, $od_id, $st_date, $ed_date);
			set_naverreview_premium($i, $od_id, $st_date, $ed_date);
		}

	} else {
		//echo "error:ResponseType is not SUCCESS\n";
	}

}

function set_naverreview_orderlist($st_date, $ed_date) {
	//상수 선언
	$accessLicense = "0100010000dcd00c71d04414d2a83c910b0c329afec0096ef125829ce2efb3e53c8685fd4d"; //AccessLicense Key 입력, PDF파일 참조
	$key= "AQABAAA6dDAJfxlVUymJE5j7+M0TC6J1yEgpx8fECDBVasJx3A=="; ////SecretKey 입력, PDF파일 참조
	$service = "MallService41";

	$operation = 'GetChangedProductOrderList';
	$detailLevel = "Full";
	$version = "4.1";
	$targetUrl = "http://ec.api.naver.com/Checkout/MallService41";

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
			 <base:InquiryTimeFrom>".$st_date."</base:InquiryTimeFrom>
			 <base:InquiryTimeTo>".$ed_date."</base:InquiryTimeTo>
			 <mall:LastChangedStatusCode>PURCHASE_DECIDED</mall:LastChangedStatusCode>
			 <mall:MallID>np_inaia518049</mall:MallID>
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
	   //echo "error:". $result->toString(). "\n";
	   return;
	}
	//응답메시지 확인
	$rcode = $rq->getResponseCode();
	if($rcode!='200'){
	   //echo "error: http response code=". $rcode. "\n";
	   return;
	}
	$response = $rq->getResponseBody();
	//echo "response=" . str_replace('<','&lt;', str_replace('>', '&gt;', $response)) . "\n\n";

	//domxml을 이용하여 xml 파싱
	if(!$dom = domxml_open_mem($response)){
	 //echo "error:xml parsing error\n";
	 return;
	}

	$root = $dom->document_element();
	$soapbody = $root->get_elements_by_tagname('Body');
	if(count($soapbody)!=1){
	   //echo "error:invalid body counts\n";
	   return;
	}


	$Response = $soapbody[0]->get_elements_by_tagname('GetChangedProductOrderListResponse');
	if(!$Response && count($Response)==0) {
	   //echo "error:invalid GetChangedProductOrderList Response counts\n";
	   return;
	}

	$ResponseType_Temp = $Response[0]->get_elements_by_tagname('ResponseType');
	if(!$ResponseType_Temp && count($ResponseType_Temp)==0) {
	   //echo "error:invalid ResponseType counts\n";
	   return;
	}

	// ResponseType이 SUCCESS 일 경우에만 값을 가져옴

	$ResponseType = $ResponseType_Temp[0]->get_content();
	if($ResponseType == "SUCCESS") {
		$OrderID = $Response[0]->get_elements_by_tagname('OrderID');
		if(!$OrderID && count($OrderID)==0) {
		   //echo "error:invalid OrderID counts\n";
		   return;
		}
		
		$ProductOrderID = $Response[0]->get_elements_by_tagname('ProductOrderID');
		if(!$ProductOrderID && count($ProductOrderID)==0) {
		   //echo "error:invalid ProductOrderID counts\n";
		   return;
		}
		
		$LastChangedStatus = $Response[0]->get_elements_by_tagname('LastChangedStatus');
		if(!$LastChangedStatus && count($LastChangedStatus)==0) {
		   //echo "error:invalid LastChangedStatus counts\n";
		   return;
		}
		
		$LastChangedDate = $Response[0]->get_elements_by_tagname('LastChangedDate');
		if(!$LastChangedDate && count($LastChangedDate)==0) {
		   //echo "error:invalid LastChangedDate counts\n";
		   return;
		}
		
		$ProductOrderStatus = $Response[0]->get_elements_by_tagname('ProductOrderStatus');
		if(!$ProductOrderStatus && count($ProductOrderStatus)==0) {
		   //echo "error:invalid ProductOrderStatus counts\n";
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
			set_naverreview_orderinfo($i, $od_id, $st_date, $ed_date);
		}
	}
	else {
		//echo "error:ResponseType is not SUCCESS\n";
	}
}
?>