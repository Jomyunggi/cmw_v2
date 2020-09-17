<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	$startdate = $M_FUNC->M_Filter(POST, 'startdate');
	$enddate = $M_FUNC->M_Filter(POST, 'enddate');
	$yesterday = date('Ymd', strtotime('1 day ago'));

	if($yesterday < $enddate){
		echo "false";
		exit;
	}

	$db = new M_DB('');
	$db_test = new M_DB('TEST');
	

	/*********************************
	//해당기간에 정산된 데이터를 delete
	*********************************/
	$where = " WHERE dateYmd between ". $startdate ." and ". $enddate;
	//$db->delete('ReportDataByVmDay_backup', $where);
	

	/**********************
	//UnitCost 정보 가져오기
	**********************/
	$costArr = array();
	$costQuery = " select * from UnitCost_Info ";
	$costRow = $db->getListSet($costQuery);
	$costRow->next();

	$costArr['cpu'][1]			= $costRow->get('cpu_1year');
	$costArr['cpu'][3]			= $costRow->get('cpu_3year');
	$costArr['memory'][1]		= $costRow->get('memory_1year');
	$costArr['memory'][3]		= $costRow->get('memory_3year');
	$costArr['disk'][1]			= $costRow->get('disk_1year');
	$costArr['disk'][3]			= $costRow->get('disk_3year');
	$costArr['OS'][1]			= $costRow->get('OS');
	$costArr['mssql'][4]		= $costRow->get('mssql_4_year');
	$costArr['mssql'][8]		= $costRow->get('mssql_8_year');
	$costArr['mssql'][12]		= $costRow->get('mssql_12_year');
	

	/************************************************************
	//해당 VM별 serviceIdx, companyIdx, businessIdx 배열로 담아놓기
	************************************************************/
	$vmByIdx_arr = array();
	$query = ' SELECT ai.businessIdx, ai.companyIdx, ai.roleIdx as serviceIdx, va.vmIdx '
			.' FROM VM_Account_Link va '
			.'	INNER JOIN AccountRole_Info ai on va.accountIdx = ai.accountIdx '
			;
	$row = $db->getListSet($query);
	for($i=0; $i<$row->size(); $i++){
		$row->next();
		
		$vmByIdx_arr[$row->get('vmIdx')] = array(
			'businessIdx'	=>	$row->get('businessIdx'),
			'companyIdx'	=>	$row->get('companyIdx'),
			'serviceIdx'	=>	$row->get('serviceIdx')
		);
	}
	

	/*********************************************************
	//위에서 delete 된 후 해당 기간에 insert할 data를 만들어 놓기
	*********************************************************/
	$insertData_Arr = array();
	$query = ''
			.' SELECT '
			.'		v.dateYmd '
			.'		, v.vmIdx '
			.'		, v.contract_c '
			.'		, sum(v.cpu) as cpu '
			.'		, sum(v.memory) as memory '
			.'		, v.contract_d '
			.'		, sum(v.disk_os) as disk_os '
			.'		, sum(v.disk_data) as disk_data '
			.'		, v.v_startDate '
			.'		, v.v_endDate '
			.'		, v.status '
			.'		, v.OS_check '
			.'		, v.dbUse '
			.'		, v.DBMS '
			.'		, v.db_startDate '
			.' FROM VMDataByDay_backup v '
			.' WHERE v.dateYmd between '. $startdate .' and '. $enddate
			.' GROUP BY v.dateYmd, v.vmIdx '
			.' ORDER BY v.dateYmd asc, v.vmIdx asc '
			;
	$result = $db->execute($query);
	while($row = mysql_fetch_assoc($result)){
		$cpu_price = ($row['cpu'] * $costArr['cpu'][$row['contract_c']]);
		$memory_price = ($row['memory'] * $costArr['memory'][$row['contract_c']]);
		if($row['disk_os'] > 0){
			$disk_os_price = (($row['disk_os'] - 50) / 50 * $costArr['disk'][$row['contract_d']]);
			$disk_data_price = ($row['disk_data'] / 50 * $costArr['disk'][$row['contract_d']]);
			$disk_price = $disk_os_price + $disk_data_price;
		} else {
			$disk_price = ($row['disk_data'] / 50 * $costArr['disk'][$row['contract_d']]);
		}

		//contract_s는 1년가정 임의 지정, DBMS는 자신이 원하는 DB 값
		$data = array(
			'businessIdx'		=> $vmByIdx_arr[$row['vmIdx']]['businessIdx'],
			'companyIdx'		=> $vmByIdx_arr[$row['vmIdx']]['companyIdx'],
			'serviceIdx'		=> $vmByIdx_arr[$row['vmIdx']]['serviceIdx'],
			'vmIdx'				=> $row['vmIdx'],
			'dateYmd'			=> $row['dateYmd'],
			'year'				=> substr($row['dateYmd'], 0, 4),
			'month'				=> substr($row['dateYmd'], 4, 2),
			'contract_c'		=> $row['contract_c'],
			'contract_d'		=> $row['contract_d'],
			'contract_o'		=> $row['OS_check'],
			'contract_s'		=> $row['DBMS'],
			'cpu_unitcost'		=> $costArr['cpu'][$row['contract_c']],
			'memory_unitcost'	=> $costArr['memory'][$row['contract_c']],
			'disk_unitcost'		=> $costArr['disk'][$row['contract_d']],
			'OS_unitcost'		=> $costArr['OS'][1],
			'mssql_unitcost'	=> $costArr['mssql'][$row['DBMS']],
			'cpu_price'			=> $cpu_price,
			'memory_price'		=> $memory_price,
			'disk_price'		=> $disk_price,
		);

		$monthlyCheck = monthly_calculate($row['vmIdx']);

		$OS_price = 0;
		if($row['OS_check'] == 1){
			if($monthlyCheck & 1){
				$OS_price = $costArr['OS'][1];
			}
		}
		
		$mssql_price = 0;
		if($row['dbUse'] == 1){
			if($row['DBMS'] != 0 && $row['db_startDate'] <= $row['dateYmd']){
				if($monthlyCheck & 2){
					$mssql_price = $costArr['mssql'][$row['DBMS']];
				}
			}
		}		

		$total_price = $cpu_price + $memory_price + $disk_price + $OS_price + $mssql_price;
		$data['OS_price'] = $OS_price;
		$data['mssql_price'] = $mssql_price;
		$data['total_price'] = $total_price;
		
		$db->insert('ReportDataByVmDay_backup', $data);
		//array_push($insertData_Arr, $data);
	}

	echo "success";
	exit;
	
	function monthly_calculate($vmIdx){
		global $startdate;
		global $db;

		//OS, MSSQL은 월 계산이므로 해당 기간안에 OS, MSSQL이 측정됬는지 확인 여부
		$returnYN = 0;
		$query = " SELECT "
				."		r.vmIdx "
				."		, IFNULL(sum(r.OS_price), 0) as OS_price "
				."		, IFNULL(sum(r.mssql_price), 0) as mssql_price "
				." FROM ReportDataByVmDay_backup r "
				." WHERE r.status = 1 and left(r.dateYmd, 6) = ".substr($startdate, 0,6)." and r.vmIdx = " . $vmIdx
				;
		$Mrow = $db->getListSet($query);
		
		if($Mrow->size() > 0){
			for($i=0; $i<$Mrow->size(); $i++){
				$Mrow->next();
				
				if($Mrow->get('OS_price') == 0){
					$returnYN += 1;
				} 
				if($Mrow->get('mssql_price') == 0){
					$returnYN += 2;
				} 
			}
		} else {
			$returnYN += 3;
		}
		
		return $returnYN;
	}

?>