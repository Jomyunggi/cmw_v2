<?php
	@include_once "../../common/setCommonPath.php";
	include_once 'Inc/inc.include.php';
	include_once COMMON_CLASS . "/class.account.php";

	$M_ACCOUNT = new M_ACCOUNT;

	$companyName =  $M_FUNC->M_Filter(POST, 'companyName');

	$count = $M_ACCOUNT->check_companyName($companyName);
	if($count > 0) {
		echo 'fail';
	} else {
		echo 'success';
	}

	exit();
?>
