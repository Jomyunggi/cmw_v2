<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	@include_once ADMIN_CLASS_PATH . '/class.vmPage_v2.php';
	$M_VmPage = new M_VmPage;

	@include_once ADMIN_CLASS_PATH . '/class.calculatePage.php';
	$M_Calculate = new M_CalculatePage;

	$m_id				= $M_FUNC->M_Filter(GET, 'm_id');
	$status			= $M_FUNC->M_Filter(GET, 'status');
	$v_startDate	= $M_FUNC->M_Filter(GET, 'v_startDate');
	$v_endDate		= $M_FUNC->M_Filter(GET, 'v_endDate');
	$reCalcStatus	= $M_FUNC->M_Filter(GET, 'reCalcStatus');
	$yesterday		= date('Ymd', strtotime('-1day', time()));

	if(!isset($v_startDate))	$v_startDate = 0;
	if(!isset($v_endDate))	$v_endDate = 0;
	
	/***** 재 계산을 하는 경우 ******
	** 1. 상태 변경						**
	**		1.1 사용 -> 중지				**
	**		1.2 사용 -> 삭제				**
	**		1.3 중지 -> 삭제				**
	**		1.4 중지 -> 사용				**
	** 2. DBMS 변경					**
	**		2.1 미사용 -> 사용			**
	**			2.1.1 서비스 시작일 변경 **
	**			2.1.2 DBMS 변경		**
	**		2.2 사용 -> 미사용			**
	** 3. OS 변경						**
	************************/
	//상태 변경이 있을 경우에만 처리
	if($reCalcStatus & 1){
		$M_Calculate->changeVMInfoByStatus($m_id, $status, $v_startDate, $v_endDate);
	}

	if($reCalcStatus == 1 && $status != 1){
		//ReportDataByVmDay 종료일 이후 데이터 삭제
		$M_Calculate->deleteReportDataByVmDay($m_id, $v_endDate);
	} else {
		$vmInfo = $M_VmPage->getVmByIdx($m_id);
		$dateArr = array();
		if($reCalcStatus & 1){
			if($status == 1) array_push($dateArr, $v_startDate);
			if($vmInfo->get('v_endDate') != 0)	array_push($dateArr, $vmInfo->get('v_endDate'));
		}
		if($reCalcStatus & 4){
			if($vmInfo->get('OS_Date') != 0) array_push($dateArr, $vmInfo->get('OS_Date'));
		}
		if($reCalcStatus & 2){
			if($vmInfo->get('db_startDate') != 0) array_push($dateArr, $vmInfo->get('db_startDate'));
		}

		$firstDate = $dateArr;
		if(count($dateArr) > 1){
			$firstDate = $M_VmPage->checkDataByServicefunction2($dateArr);
		}

		//ReportDataByVmDay 종료일 이후 데이터 삭제
		$where = "where vmIdx = ". $m_id ." AND dateYmd >= " . $firstDate[0];
		$M_Calculate->deleteReportDataByVmDayByIdx($where);
	
		//위에서 VMDataByDay까지 처리가 끝난 뒤 같이 재정산(변경된 날짜중 가장 빠른 날짜 기준으로 재정산)
		$M_Calculate->calculateForVmDataByDay_v2($firstDate[0], $yesterday, $m_id);
	}

	$M_FUNC->recordActionLog("S", "M0201", $m_id, "VM 내역 변경으로 인한 재정산");
	$update_query = "update VM_Info set reCalcStatus = 0 where idx = ". $m_id;
	$db->execute($update_query);	

	echo "success";
?>