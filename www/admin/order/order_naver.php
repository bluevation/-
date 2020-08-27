<?php
if(!defined('_TUBEWEB_')) exit;


include_once(TB_LIB_PATH.'/naverpay_api.lib.php');
//set_naverpay_orderlist("GetChangedProductOrderList");

$btn_frmline = <<<EOF
<input type="submit" name="act_button" value="입금완료" class="btn_ssmall red" onclick="document.pressed=this.value">
EOF;
?>

<form name="forderlist" id="forderlist" method="post">
	<div class="local_frm01">
		<?php echo $btn_frmline; ?>
	</div>
	<div class="tbl_head01">
		<table id="sodr_list">
			<colgroup>
				<col class="w40">
				<col class="w100">
				<col class="w100">
				<col class="w100">
				<col class="w100">
				<col>
				<col class="w100">
				<col class="w60">
				<col class="w90">
				<col class="w90">
				<col class="w90">
				<col class="w90">
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
					<th scope="col">주문번호</th>
					<th scope="col">상품주문번호</th>
					<th scope="col">주문 일시</th>
					<th scope="col">상품 주문 상태</th>
					<th scope="col">상품명</th>
					<th scope="col">상품 옵션(옵션명)</th>
					<th scope="col">수량</th>
					<th scope="col">상품 가격</th>
					<th scope="col">배송비</th>
					<th scope="col">결제 수단</th>
					<th scope="col">총 결제 금액</th>
				</tr>
			</thead>
			<tbody>
				<?php set_naverpay_orderlist("GetChangedProductOrderList"); ?>
			</tbody>
		</table>
	</div>
</form>