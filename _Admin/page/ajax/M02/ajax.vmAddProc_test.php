<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	$mode = $M_FUNC->M_Filter(GET, "mode");
	$companyIdx = $M_FUNC->M_Filter(GET, "companyIdx");
	$accountIdx = $M_FUNC->M_Filter(GET, "accountIdx");
	$vmIdx = $M_FUNC->M_Filter(GET, 'vmIdx');
print_r($_GET);
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
		'c_startdate'	=>	str_replace("-", "", $cpu_startdate),
		'm_startdate'	=>	str_replace("-", "", $memory_startdate),
		'o_startdate'	=>	str_replace("-", "", $disk_os_startdate),
		'd_startdate'	=>	str_replace("-", "", $disk_data_startdate),
		'db_startdate'	=>	str_replace("-", "", $db_startdate),
		'regUnixtime'	=>	time()
	);
	//$db->insert('VM_Resource_Info', $resourceData);
	//echo "success";
	//exit;

	$cpu_startdate			= str_replace("-", "", $cpu_startdate);
	$memory_startdate		= str_replace("-", "", $memory_startdate);
	$disk_os_startdate		= str_replace("-", "", $disk_os_startdate);
	$disk_data_startdate	= str_replace("-", "", $disk_data_startdate);
	$db_startdate			= str_replace("-", "", $db_startdate);
	
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
	
	$date = array();
	for($i=0; $i<count($date_arr); $i++){
		if($date_arr[$i] == '' || $date_arr[$i] == 0){
		} else {
			array_push($date, $date_arr[$i]);
		}
	}

	$enddate = strtotime(date('Ymd', strtotime('1 day ago')));
	for($dd=strtotime($date[0]); $dd<=$enddate; $dd=strtotime('+1 day', $dd)){
		$dateYmd = date('Ymd', $dd);
		$cpuValue = 0;
		$memoryValue = 0;
		$disk_osvalue = 0;
		$disk_dataValue = 0;
		$adUseVaue = 0;
		$DBMSValue = 0;
		$db_startdateValue = 0;
		
		if($cpu_startdate <= $dateYmd){
			$cpuValue = $cpu;
		} else {
			$cpuValue = 0;
		}
		
		if($memory_startdate <= $dateYmd){
			$memoryValue = $memory;
		}

		if($disk_os_startdate <= $dateYmd){
			$disk_osvalue = $disk_os;
		}

		if($disk_data_startdate <= $dateYmd){
			$disk_dataValue = $disk_data;
		}

		$diskValue = $disk_osvalue + $disk_dataValue;
		
		if($dbUse == 1){
			if($db_startdate <= $dateYmd){
				$adUseVaue = $dbUse;
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
			'db_startDate'	=>	$db_startDateValue,
		);

		echo "<pre>";
		print_r($data);
	}
	exit;
	$data = array(
		//'dateYmd'		=>	$dateYmd,
		'vmIdx'			=>	$vmIdx,
		'vmType'		=>	$row->get('vmType'),
		'vmName'		=>	$row->get('vmName'),
		//'cpu'			=>	$cpu,
		//'memory'		=>	0,
		//'disk'			=>	0,
		//'disk_os'		=>	0,
		//'disk_data'		=>	0,
		'contract_c'	=>	$row->get('contract_c'),
		'contract_d'	=>	$row->get('contract_d'),
		'v_startDate'	=>	$row->get('v_startDate'),
		'v_endDate'		=>	$row->get('v_endDate'),
		'status'		=>	$row->get('status'),
		'OS'			=>	$row->get('OS'),
		'OS_check'		=>	$row->get('OS_check'),
		//'dbUse'			=>	0,	
		//'DBMS'			=>	0,
		//'db_startDate'	=>	0,
	);

	//cpu, memory, disk_os, disk_data, db별로 데이터를 insert 해준다.
	//1.cpu
	if($cpu > 0){
		for($dd=$cpu_startdate;$dd<=$enddate;$dd=strtotime('+1 day', $dd)){
				$dateYmd = date('Ymd', $dd);

				$cpu_data = $data;
				$cpu_data['dateYmd']		= $dateYmd;
				$cpu_data['cpu']			= $cpu;
				$cpu_data['memory']			= 0;
				$cpu_data['disk']			= 0;
				$cpu_data['disk_os']		= 0;
				$cpu_data['disk_data']		= 0;
				$cpu_data['dbUse']			= 0;
				$cpu_data['DBMS']			= 0;
				$cpu_data['db_startDate']	= 0;

				$db->insert('VMDataByDay_backup', $cpu_data);
		}
	}

	//2.memory
	if($memory > 0){
		for($dd=$memory_startdate;$dd<=$enddate;$dd=strtotime('+1 day', $dd)){
				$dateYmd = date('Ymd', $dd);

				$memory_data = $data;
				$memory_data['dateYmd']		= $dateYmd;
				$memory_data['cpu']			= 0;
				$memory_data['memory']		= $memory;
				$memory_data['disk']		= 0;
				$memory_data['disk_os']		= 0;
				$memory_data['disk_data']	= 0;
				$memory_data['dbUse']		= 0;
				$memory_data['DBMS']		= 0;
				$memory_data['db_startDate']= 0;
				
				$db->insert('VMDataByDay_backup', $memory_data);
		}
	}

	//3.disk_os
	if($disk_os > 0){
		for($dd=$disk_os_startdate;$dd<=$enddate;$dd=strtotime('+1 day', $dd)){
				$dateYmd = date('Ymd', $dd);

				$disk_os_data = $data;
				$disk_os_data['dateYmd']		= $dateYmd;
				$disk_os_data['cpu']			= 0;
				$disk_os_data['memory']			= 0;
				$disk_os_data['disk']			= $disk_os;
				$disk_os_data['disk_os']		= $disk_os;
				$disk_os_data['disk_data']		= 0;
				$disk_os_data['dbUse']			= 0;
				$disk_os_data['DBMS']			= 0;
				$disk_os_data['db_startDate']	= 0;

				$db->insert('VMDataByDay_backup', $disk_os_data);
		}
	}

	//4.disk_data
	if($disk_data > 0){
		for($dd=$disk_data_startdate;$dd<=$enddate;$dd=strtotime('+1 day', $dd)){
				$dateYmd = date('Ymd', $dd);

				$disk_data_data = $data;
				$disk_data_data['dateYmd']		= $dateYmd;
				$disk_data_data['cpu']			= 0;
				$disk_data_data['memory']		= 0;
				$disk_data_data['disk']			= $disk_data;
				$disk_data_data['disk_os']		= 0;
				$disk_data_data['disk_data']	= $disk_data;
				$disk_data_data['dbUse']		= 0;
				$disk_data_data['DBMS']			= 0;
				$disk_data_data['db_startDate']	= 0;

				$db->insert('VMDataByDay_backup', $disk_data_data);
		}
	}

	//5.db
	if($dbUse = 1 && $DBMS > 0){
		for($dd=$db_startdate;$dd<=$enddate;$dd=strtotime('+1 day', $dd)){
				$dateYmd = date('Ymd', $dd);

				$db_data = $data;
				$db_data['dateYmd']		= $dateYmd;
				$db_data['cpu']			= 0;
				$db_data['memory']		= 0;
				$db_data['disk']		= 0;
				$db_data['disk_os']		= 0;
				$db_data['disk_data']	= 0;
				$db_data['dbUse']		= $dbUse;
				$db_data['DBMS']		= $DBMS;
				$db_data['db_startDate']= date('Ymd', $db_startdate);

				$db->insert('VMDataByDay_backup', $db_data);
		}
	}
	
	echo 'success';
	exit;
?>