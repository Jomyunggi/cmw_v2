<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	ini_set('memory_limit', -1);
	ini_set('max_execution_time', 0);

	$startdate = $M_FUNC->M_Filter(POST, 'startdate');
	$enddate = $M_FUNC->M_Filter(POST, 'enddate');
	$yesterday = date('Ymd', strtotime('1 day ago'));

	if($yesterday < $enddate){
		echo "false";
		exit;
	}

	@include_once ADMIN_CLASS_PATH . '/class.calculatePage.php';
	$M_Calculate = new M_CalculatePage;
	
	//ReportDataByVmDay 기간에 맞게 delete
	$M_Calculate->deleteForReportDataByVmDay($startdate, $enddate);
	

	//ReportDataByVmDay 삭제 후 그 기간에 맞게 재정산
	$M_Calculate->calculateForVmDataByDay($startdate, $enddate);

	echo 'success';
	exit;
?>