<?php

@include_once "../../common/setCommonPath.php";
include_once 'Inc/inc.include.php';
include_once COMMON_CLASS . "/class.billing.php";

$M_BILLING = new M_BILLING;

$mode = $M_FUNC->M_Filter(POST, 'mode'); // 1 : CPU 2 : DISK 
$cpu = $M_FUNC->M_Filter(POST, 'cpu');
$memory = $M_FUNC->M_Filter(POST, 'memory');
$disk = $M_FUNC->M_Filter(POST, 'disk');

if($mode === '1')  {
	$addWhere = ' AND cpu = ' . intval($cpu) . ' AND memory = ' . intval($memory);
	$count = $M_BILLING->getCountUnit($addWhere); 
	if($count > 0) {
		echo 'FAIL';
	} else {
		echo 'SUCCESS';
	}
} else if($mode === '2') {
	$addWhere = ' AND disk = ' . intval($disk);
	$count = $M_BILLING->getCountDisk($addWhere);
	if($count > 0) {
		echo 'FAIL';
	} else {
		echo 'SUCCESS';
	}
} else {
	echo 'UNMATCH';
}

exit();
?>
