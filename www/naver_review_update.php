<?php
include_once('./common.php');
include_once(TB_LIB_PATH.'/naverpay_review.lib.php');

$day = TB_TIME_YMD;
$beforeDay = date("Y-m-d", strtotime($day." -1 day"));

//set_naverreview_orderlist("2012-01-02T00:00:00+09:00", "2012-01-03T00:00:00+09:00");
//set_naverreview_orderlist($beforeDay."T00:00:00+09:00", TB_TIME_YMD."T00:00:00+09:00");
//set_naverreview_orderlist("2020-08-24T00:00:00+09:00", "2020-08-25T00:00:00+09:00");
?>