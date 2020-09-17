<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	$vmIdx = $M_FUNC->M_Filter(POST, 'vmIdx');
	$startdate = $M_FUNC->M_Filter(POST, 'startdate');
	$enddate = $M_FUNC->M_Filter(POST, 'enddate');

	include_once ADMIN_CLASS_PATH."/class.billingPage.php";
	$M_billing = new M_BillingPage;

	include_once ADMIN_CLASS_PATH."/class.vmPage_v2.php";
	$M_vm = new M_VmPage;

	include_once ADMIN_CLASS_PATH."/class.calculatePage.php";
	$M_calc = new M_CalculatePage;
	
	$addWhere = " AND vmIdx = ". $vmIdx ." AND dateYmd between ". $startdate ." AND ". $enddate;
	$calcProRow = $M_billing->getReportDataByVmDay($addWhere);
	
	if($calcProRow->size() > 0){
		$data = array(
			'calcStatus'	=> 2
		);

		$updateWhere = " Where vmIdx = ". $vmIdx ." AND startdate = ". $startdate ." AND enddate = ".$enddate;
		$M_vm->updateCalcProgressData($data, $updateWhere);

		$M_calc->deleteVMDataByDay2("where 1=1 ". $addWhere);
		$M_calc->deleteReportDataByVmDayByIdx("where 1=1 ". $addWhere);		

		//중지되어있는 동안의 서버대수도 차감시켜야된다.

		echo "success";
	} else {
		echo "NoData";
	}
?>