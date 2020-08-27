<?php
if(!defined('_TUBEWEB_')) exit; // 개별 페이지 접근 불가

// 실행일 비교
if(isset($default['de_optimize_date']) && $default['de_optimize_date'] >= TB_TIME_YMD)
    return;

// 설정일이 지난 장바구니 상품 삭제
if($default['de_cart_keep_term'] > 0) {
	$tmp_before_date = date("Y-m-d", TB_SERVER_TIME - ($default['de_cart_keep_term'] * 86400));
	$sql = " delete from shop_cart where left(ct_time,10) < '$tmp_before_date' and ct_select='0' and od_id='' ";
	sql_query($sql, FALSE);
}

// 설정일이 지난 찜상품 삭제
if($default['de_wish_keep_term'] > 0) {
	$tmp_before_date = date("Y-m-d", TB_SERVER_TIME - ($default['de_wish_keep_term'] * 86400));
	$sql = " delete from shop_wish where left(wi_time,10) < '$tmp_before_date' ";
	sql_query($sql, FALSE);
}

// 설정일이 지난 배송완료상품 구매확정
if($default['de_final_keep_term'] > 0) {
	$tmp_before_date = date("Y-m-d", TB_SERVER_TIME - ($default['de_final_keep_term'] * 86400));
	$sql = " update shop_order
				set user_ok = '1'
				  , user_date = '".TB_TIME_YMDHIS."'
			  where left(invoice_date,10) < '$tmp_before_date'
		        and user_ok = '0'
			    and dan = '5' ";
	sql_query($sql, FALSE);
}

// 설정일이 지난 미입금된 주문내역 자동취소
if($default['de_misu_keep_term'] > 0) {
	$tmp_before_date = date("Y-m-d", TB_SERVER_TIME - ($default['de_misu_keep_term'] * 86400));
	$sql = " select *
			   from shop_order
			  where left(od_time,10) < '$tmp_before_date'
				and dan = '1'
			  order by index_no ";
	$res = sql_query($sql);
	while($row=sql_fetch_array($res)) {
		change_order_status_6($row['od_no']);

		// 메모남김
		$sql = " update shop_order
					set shop_memo = CONCAT(shop_memo,\"\\n미입금 자동 주문취소 - ".TB_TIME_YMDHIS." (취소이유 : {$default['de_misu_keep_term']}일경과)\")
				  where od_no = '{$row['od_no']}' ";
		sql_query($sql);
	}
}

include_once(TB_LIB_PATH.'/naverpay_review.lib.php');
$day = TB_TIME_YMD;
$beforeDay = date("Y-m-d", strtotime($day." -1 day"));
set_naverreview_orderlist($beforeDay."T00:00:00+09:00", TB_TIME_YMD."T00:00:00+09:00");

// 실행일 기록
if(isset($default['de_optimize_date'])) {
    sql_query(" update shop_default set de_optimize_date = '".TB_TIME_YMD."' ");
}

unset($tmp_before_date);
?>