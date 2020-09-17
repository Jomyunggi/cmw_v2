<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	include_once ADMIN_CLASS_PATH . '/class.calculatePage.php';
	$M_Calculate = new M_CalculatePage;

	include_once ADMIN_CLASS_PATH . '/class.reportPage.php';
	$M_Report = new M_ReportPage;

	$m_id = $M_FUNC->M_Filter(GET, 'm_id');
	$dbUse = $M_FUNC->M_Filter(GET, 'dbUse');
	$dbName = $M_FUNC->M_Filter(GET, 'dbName');
	$DBMS = $M_FUNC->M_Filter(GET, 'DBMS');
	$startdate = $M_FUNC->M_Filter(GET, 'startdate');
	$v_startDate = $M_FUNC->M_Filter(GET, 'v_startDate');
	$progressType = $M_FUNC->M_Filter(GET, 'progressType');

	if($m_id == 0 || $m_id == ''){
		echo "NO";
		exit;
	}

	$result = array();

	if($dbUse == 1){
		if($progressType == 2){
			$calc_query = " SELECT * "
							   ." FROM CalcProgress_Info "
							   ." WHERE 1 = 1 AND status = 4 "
							   ." AND vmIdx = ". $m_id
							   ." AND left(startdate, 6) = ". date('Ym', time())
							   //." AND startdate <= ". date('Ymd', strtotime('1 day ago', time())) ." and enddate >= ". $startdate
							   ;
			$calc_row = $db->getListSet($calc_query);
			
			$exceptDate = array();
			if($calc_row->size() > 0){
				for($i=0; $i<$calc_row->size(); $i++){
					$calc_row->next();
					array_push($exceptDate, array($calc_row->get('startdate'), $calc_row->get('enddate')));
				}
			}
		}

		$costArr = $M_Calculate->getUnicost();

		//VMDataByDay에 쌓인 VM을 변경할 DBMS 서비스 시작일 전날로 하여서 delete 후 변경된 VM정보로 다시 쌓기
		$enddate = (int)$startdate - 1;
		$M_Calculate->deleteVMDataByDay($m_id, $enddate);
		
		$no = 0;
		$startdate2 = strtotime($startdate);
		$enddate2 = strtotime(date('Ymd', time()));
		for($dd=$startdate2; $dd<$enddate2; $dd=strtotime('+1 day', $dd)){
			$dateYmd = date('Ymd', $dd);
			
			$vm_Arr = $M_Calculate->insertDataByVmDataByidx($dateYmd, $m_id);
			$resource_Arr = $M_Calculate->insertDataByVmDataAdditionalByidx($dateYmd, $m_id);

			$result[$no]['dateYmd']			= $dateYmd;
			$result[$no]['vmIdx']				= $m_id;
			$result[$no]['vmType']				= $vm_Arr['vmType'];
			$result[$no]['vmName']			= $vm_Arr['vmName'];
			$result[$no]['cpu']					= (int)$vm_Arr['cpu'] + (int)$resource_Arr['cpu'];
			$result[$no]['memory']			= (int)$vm_Arr['memory'] + (int)$resource_Arr['memory'];
			$result[$no]['disk']					= (int)$vm_Arr['disk'] + (int)$resource_Arr['disk_os'] + (int)$resource_Arr['disk_data'];
			$result[$no]['disk_os']				= (int)$vm_Arr['disk_os'] + (int)$resource_Arr['disk_os'];
			$result[$no]['disk_data']			= (int)$vm_Arr['disk_data'] + (int)$resource_Arr['disk_data'];
			$result[$no]['contract_c']			= $vm_Arr['contract_c'];
			$result[$no]['contract_d']		= $vm_Arr['contract_d'];
			$result[$no]['v_startDate']		= $vm_Arr['v_startDate'];
			$result[$no]['v_endDate']		= $vm_Arr['v_endDate'];
			$result[$no]['OS']					= $vm_Arr['OS'];
			$result[$no]['OS_check']			= $vm_Arr['OS_check'];
			$result[$no]['dbUse']				= $vm_Arr['dbUse'];
			$result[$no]['dbName']			= $vm_Arr['dbName'];
			$result[$no]['DBMS']				= $vm_Arr['DBMS'];
			$result[$no]['db_startDate']	= $vm_Arr['db_startDate'];
			$no++;
		}

		for($i=0; $i<count($result); $i++){
			if(count($exceptDate) == 0){
				$M_Calculate->insertVmDataByDayByData($result[$i]);
			} else {
				for($j=0; $j<count($exceptDate); $j++){
					if(!($exceptDate[$j][0] <= $result[$i]['dateYmd'] && $exceptDate[$j][1] >= $result[$i]['dateYmd'])){
						$M_Calculate->insertVmDataByDayByData($result[$i]);
					}
				}
			}
		}

		/*****************************
		//해당 기간만큼 재정산을 해준다.
		*****************************/
		$calculate_startdate = $startdate;
		$calculate_enddate = date('Ymd', strtotime('1 day ago'));

		//ReportDataByVmDay 기간에 맞게 delete
		$M_Calculate->deleteForReportDataByVmDay($calculate_startdate, $calculate_enddate);

		//ReportDataByVmDay 삭제 후 그 기간에 맞게 재정산
		$M_Calculate->calculateForVmDataByDay($calculate_startdate, $calculate_enddate);
		
		$update_query = "update VM_Info set reCalcStatus = reCalcStatus + 2 where idx = ". $m_id;
		$db->execute($update_query);
	} else {
		echo "NO";
		exit;
	}

	echo "success";
?>