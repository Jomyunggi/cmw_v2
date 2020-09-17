<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	ini_set('memory_limit', -1);
	ini_set('max_execution_time', 0);

	@include_once ADMIN_CLASS_PATH . '/class.calculatePage.php';
	$M_Calculate = new M_CalculatePage;

	$mode = $M_FUNC->M_Filter(GET, "mode");
	$companyIdx = $M_FUNC->M_Filter(GET, "companyIdx");
	$accountIdx = $M_FUNC->M_Filter(GET, "accountIdx");
	$vmIdx = $M_FUNC->M_Filter(GET, 'vmIdx');

	$cpu					= $M_FUNC->M_Filter(GET, 'cpu');
	$cpu_startdate			= $M_FUNC->M_Filter(GET, 'cpu_startdate');
	$memory					= $M_FUNC->M_Filter(GET, 'memory');
	$memory_startdate		= $M_FUNC->M_Filter(GET, 'memory_startdate');
	$disk_os				= $M_FUNC->M_Filter(GET, 'disk_os');
	$disk_os_startdate		= $M_FUNC->M_Filter(GET, 'disk_os_startdate');
	$disk_data				= $M_FUNC->M_Filter(GET, 'disk_data');
	$disk_data_startdate	= $M_FUNC->M_Filter(GET, 'disk_data_startdate');
	$dbUse					= $M_FUNC->M_Filter(GET, 'dbUse');
	$db_startdate			= $M_FUNC->M_Filter(GET, 'db_startdate');
	$DBMS					= $M_FUNC->M_Filter(GET, 'DBMS');	

	$cpu_startdate			= str_replace("-", "", $cpu_startdate);
	$memory_startdate		= str_replace("-", "", $memory_startdate);
	$disk_os_startdate		= str_replace("-", "", $disk_os_startdate);
	$disk_data_startdate	= str_replace("-", "", $disk_data_startdate);
	$db_startdate			= str_replace("-", "", $db_startdate);

	//cpu, memory, disk, dbms 시작일이 각각 다르므로 날짜 오름차순
	$date_arr = array($cpu_startdate, $memory_startdate, $disk_os_startdate, $disk_data_startdate, $db_startdate);
	for($i=1; $i<count($date_arr); $i++){
		$tmp_startdate = $date_arr[$i];
		$aux = $i-1;
		
		while(($aux >= 0) && ($date_arr[$aux] > $tmp_startdate)){
			$date_arr[$aux+1] = $date_arr[$aux];
			$aux--;
		}
		
		$date_arr[$aux+1] = $tmp_startdate;
	}
	//선택을 안할경우 0이므로 해당배열에서 unset시켜준다.
	$date = array();
	for($i=0; $i<count($date_arr); $i++){
		if($date_arr[$i] == '' || $date_arr[$i] == 0){
		} else {
			array_push($date, $date_arr[$i]);
		}
	}

	//증분값이 있는 경우 
	$query = ''
			.' SELECT * '
			.' FROM VM_Info '
			.' WHERE idx = '.$vmIdx
			;
	$row = $db->getListSet($query);

	if($row->size() > 0){
		$row->next();
	} else {
		exit;
	}

	$resourceData = array(
		'vmIdx'			=>	$vmIdx,
		'cpu'			=>	$cpu,
		'memory'		=>	$memory,
		'disk_os'		=>	$disk_os,
		'disk_data'		=>	$disk_data,
		'dbUse'			=>	$dbUse,
		'DBMS'			=>	$DBMS,
		'c_startdate'	=>	$cpu_startdate,
		'm_startdate'	=>	$memory_startdate,
		'o_startdate'	=>	$disk_os_startdate,
		'd_startdate'	=>	$disk_data_startdate,
		'db_startdate'	=>	$db_startdate,
		'regUnixtime'	=>	time()
	);
	$db->insert('VM_Resource_Info', $resourceData);

	if($row->get('status') == 1){
		$enddate = strtotime(date('Ymd', strtotime('1 day ago')));
	} else {
		$enddate = strtotime($row->get('v_endDate'));
	}

	for($dd=strtotime($date[0]); $dd<=$enddate; $dd=strtotime('+1 day', $dd)){
		$dateYmd = date('Ymd', $dd);
		$cpuValue = 0;
		$memoryValue = 0;
		$disk_osvalue = 0;
		$disk_dataValue = 0;
		$dbUseValue = 0;
		$DBMSValue = 0;
		$db_startdateValue = 0;
		
		if($cpu_startdate <= $dateYmd && $cpu <> 0){
			$cpuValue = $cpu;
		} else {
			$cpuValue = 0;
		}
		
		if($memory_startdate <= $dateYmd  && $memory <> 0){
			$memoryValue = $memory;
		}

		if($disk_os_startdate <= $dateYmd && $disk_os <> 0){
			$disk_osValue = $disk_os;
		}

		if($disk_data_startdate <= $dateYmd && $disk_data <> 0){
			$disk_dataValue = $disk_data;
		}

		$diskValue = $disk_osValue + $disk_dataValue;
		
		if($dbUse == 1){
			if($db_startdate <= $dateYmd && $DBMS <> 0){
				$dbUseValue = $dbUse;
				$DBMSValue = $DBMS;
				$db_startdateValue = $db_startdate;
			}
		}

		$data = array(
			'dateYmd'		=>	$dateYmd,
			'vmIdx'			=>	$vmIdx,
			'vmType'		=>	$row->get('vmType'),
			'vmName'		=>	$row->get('vmName'),
			'cpu'			=>	$cpuValue,
			'memory'		=>	$memoryValue,
			'disk'			=>	$diskValue,
			'disk_os'		=>	$disk_osValue,
			'disk_data'		=>	$disk_dataValue,
			'contract_c'	=>	$row->get('contract_c'),
			'contract_d'	=>	$row->get('contract_d'),
			'v_startDate'	=>	$row->get('v_startDate'),
			'v_endDate'		=>	$row->get('v_endDate'),
			'status'		=>	$row->get('status'),
			'OS'			=>	$row->get('OS'),
			'OS_check'		=>	$row->get('OS_check'),
			'dbUse'			=>	$dbUseValue,	
			'DBMS'			=>	$DBMSValue,
			'db_startDate'	=>	$db_startdateValue,
		);
		
		$db->insert('VMDataByDay', $data);
	}
	
	/*****************************
	//해당 기간만큼 재정산을 해준다.
	*****************************/
	$calculate_startdate = $date[0];
	$calculate_enddate = date('Ymd', strtotime('1 day ago'));

	//ReportDataByVmDay 기간에 맞게 delete
	$M_Calculate->deleteForReportDataByVmDay($calculate_startdate, $calculate_enddate);

	//ReportDataByVmDay 삭제 후 그 기간에 맞게 재정산
	$M_Calculate->calculateForVmDataByDay($calculate_startdate, $calculate_enddate);

	$M_FUNC->recordActionLog("S", "M0201", $vmIdx, "[".$data['vmName']."] VM 추가기능 등록 및 정산");

	echo "success";
	exit;
?>