<?php
	ini_set('include_path', realpath(dirname(__FILE__) ."/../_Common"));
	@include "Inc/inc.include.php";
	
	/*
	$dateYmd = '20170327';
	insertDataByVmData();

	function insertDataByVmData(){
		global $db, $dateYmd;

		$query = " SELECT v.* "
				." FROM VM_Info v "
				."	LEFT JOIN CalcProgress_Info c on v.idx = c.vmIdx "
				." WHERE ( v.calcProgressType = 1 and (v.status = 1 and v_startDate <= " . $dateYmd ." ) "
				."	OR (v.status in (4, 99) and v_startDate <= " . $dateYmd ." and v_endDate >= " . $dateYmd . " ) ) "
				."	OR ( v.calcProgressType = 2 and c.status = 1 and c.startdate <= ".$dateYmd ." and c.enddate >= ". $dateYmd ." ) "
				;
		$row = $db->getListSet($query);

		if($row->size() > 0){
			for($i=0; $i<$row->size(); $i++){
				$row->next();

				$query2 = " SELECT * "
	 					 ." FROM VMDataByDay "
						 ." WHERE dateYmd = " . $dateYmd . " AND vmIdx = " . $row->get('idx');
				$row2 = $db->getListSet($query2);

				if($row2->size() > 0){
					continue;
				} else {
					//해당 vm정보가 없으므로 insert
					$data = array(
						'dateYmd'		=> $dateYmd,
						'vmIdx'			=> $row->get('idx'),
						'vmType'		=> $row->get('vmType'),
						'vmName'		=> $row->get('vmName'),
						'cpu'			=> $row->get('cpu'),
						'memory'		=> $row->get('memory'),
						'disk'			=> $row->get('disk'),
						'disk_os'		=> $row->get('disk_os'),
						'disk_data'		=> $row->get('disk_data'),
						'contract_c'	=> $row->get('contract_c'),
						'contract_d'	=> $row->get('contract_d'),
						'v_startDate'	=> $row->get('v_startDate'),
						'v_endDate'		=> $row->get('v_endDate'),
						'status'		=> $row->get('status'),
						'OS'			=> $row->get('OS'),
						'OS_check'		=> $row->get('OS_check'),
						'dbUse'			=> $row->get('dbUse'),
						'dbName'		=> $row->get('dbName'),
						'DBMS'			=> $row->get('DBMS'),
						'db_startDate'	=> $row->get('db_startDate'),
					);

					$db->insert("VMDataByDay", $data);
				}
			}
			return true;
		} else {
			return false;
		}
	}
	*/

	$dateYmd = '20170327';

	//UnitCost 정보 가져오기
	$costArr = array();
	$costQuery = " select * from UnitCost_Info ";
	$costRow = $db->getListSet($costQuery);
	$costRow->next();

	$costArr['cpu'][1]		  = $costRow->get('cpu_1year');
	$costArr['cpu'][3]		  = $costRow->get('cpu_3year');
	$costArr['memory'][1]	  = $costRow->get('memory_1year');
	$costArr['memory'][3]	  = $costRow->get('memory_3year');
	$costArr['disk'][1]		  = $costRow->get('disk_1year');
	$costArr['disk'][3]		  = $costRow->get('disk_3year');
	$costArr['OS'][1]		  = $costRow->get('OS');
	$costArr['mssql_4'][1]	  = $costRow->get('mssql_4_year');
	$costArr['mssql_8'][1]	  = $costRow->get('mssql_8_year');
	$costArr['mssql_12'][1]	  = $costRow->get('mssql_12_year');
	$costArr['lbsS'][1]		  = $costRow->get('lbsS_1year');
	$costArr['lbsS'][3]		  = $costRow->get('lbsS_3year');
	$costArr['lbsM'][1]		  = $costRow->get('lbsM_1year');
	$costArr['lbsM'][3]		  = $costRow->get('lbsM_3year');
	$costArr['lbsL'][1]		  = $costRow->get('lbsL_1year');
	$costArr['lbsL'][3]		  = $costRow->get('lbsL_3year');
	$costArr['ssl'][1]			= $costRow->get('ssl_1year');
	$costArr['ssl'][3]			= $costRow->get('ssl_3year');
	$costArr['officialIP'][1] = $costRow->get('officialIP_1year');
	$costArr['officialIP'][3] = $costRow->get('officialIP_3year');
	
	//진행중인 vmIdx 가져오기
	$vmIdx_arr = array();
	$querying = ' SELECT v.idx '
			.' FROM VM_Info v '
			.'	LEFT JOIN CalcProgress_Info c on v.idx = c.vmIdx '
			.' WHERE ( v.calcProgressType = 1 and (v.status = 1 and v.v_startDate <= ' . $dateYmd .' ) '
			.'	OR (v.status in (4, 99) and v.v_startDate <= ' . $dateYmd .' and v_endDate >= ' . $dateYmd . ' ) ) '
			.'	OR ( v.calcProgressType = 2 and c.status = 1 and c.startdate <= ' . $dateYmd .' and c.enddate >= ' . $dateYmd . ' ) '
			;
	$vmingRow = $db->getListSet($querying);
	
	if($vmingRow->size() > 0){
		for($i=0; $i<$vmingRow->size(); $i++){
			$vmingRow->next();

			array_push($vmIdx_arr, $vmingRow->get('idx'));
		}
	}

	//OS, MS-SQL인 경우 한달중 1일만 사용해도 한달 요금 부과
	$query = " SELECT r.vmIdx, IFNULL(sum(r.OS_price), 0) as OS_price, IFNULL(sum(r.mssql_price), 0) as mssql_price "
			." FROM ReportDataByVmDay r "
			." WHERE r.status = 1 and left(r.dateYmd, 6) = ".substr($dateYmd, 0,6)
			." and r.vmIdx in ( " . implode(",", $vmIdx_arr) ." ) "
			." GROUP BY r.vmIdx ";
			;
	$row = $db->getListSet($query);
	
	$os_arr = array();
	$mssql_arr = array();
	if($row->size() > 0){
		for($i=0; $i<$row->size(); $i++){
			$row->next();
			
			if($row->get('OS_price') == 0){
				array_push($os_arr, $row->get('vmIdx'));
			} 
			if($row->get('mssql_price') == 0){
				array_push($mssql_arr, $row->get('vmIdx'));
			} 
		}
	}

	//해당 일에 해당되는 VM 정보
	/*$query = " SELECT ai.businessIdx, ai.companyIdx, ai.roleIdx, v.idx, v.cpu, v.memory, v.disk, v.disk_os, v.disk_data, v.diskSum, v.OS_check, v.dbUse, v.DBMS, v.db_startDate, v.contract_c, v.contract_d, v.status "
			." FROM VM_Info v "
			."		LEFT JOIN VM_Account_Link va on v.idx = va.vmIdx "
			."		LEFT JOIN AccountRole_Info ai on va.accountIdx = ai.accountIdx "
			." WHERE (v.status = 1 and v.v_startDate <= " . $dateYmd ." ) "
			."	OR (v.status in (4, 99) and v.v_startDate <= " . $dateYmd ." and v_endDate >= " . $dateYmd . " ) ";*/
	$query = " SELECT ai.businessIdx, ai.companyIdx, ai.roleIdx, v.vmIdx as idx"
			."	, sum(v.cpu) as cpu, sum(v.memory) as memory, sum(v.disk) as disk, sum(v.disk_os) as disk_os, sum(v.disk_data) as disk_data "
			."	, v.OS_check, sum(v.dbUse) as dbUse, sum(v.DBMS) DBMS, v.db_startDate, v.contract_c, v.contract_d, v.status "
			." FROM VMDataByDay v "
			."		LEFT JOIN VM_Account_Link va on v.vmIdx = va.vmIdx "
			."		LEFT JOIN AccountRole_Info ai on va.accountIdx = ai.accountIdx "
			." WHERE v.vmIdx in (". implode(",", $vmIdx_arr) ." ) "
			."	AND v.dateYmd = ".$dateYmd
			." GROUP BY v.vmIdx ";
	$vmRow = $db->getListSet($query);

	if($vmRow->size() > 0){
		for($i=0; $i<$vmRow->size(); $i++){
			$vmRow->next();
			
			$cpu_price = ($vmRow->get('cpu') * $costArr['cpu'][$vmRow->get('contract_c')]);
			$memory_price = ($vmRow->get('memory') * $costArr['memory'][$vmRow->get('contract_c')]);
			if($vmRow->get('disk_os') > 0){
				$disk_os_price = (($vmRow->get('disk_os') - 50) / 50 * $costArr['disk'][$vmRow->get('contract_d')]);
				$disk_data_price = ($vmRow->get('disk_data') / 50 * $costArr['disk'][$vmRow->get('contract_d')]);
				$disk_price = $disk_os_price + $disk_data_price;
			} else {
				$disk_price = ($vmRow->get('disk') / 50 * $costArr['disk'][$vmRow->get('contract_d')]);
			}
			
			//contract_s는 1년가정 임의 지정, DBMS는 자신이 원하는 DB 값
			$data = array(
				'businessIdx'		=> $vmRow->get('businessIdx'),
				'companyIdx'		=> $vmRow->get('companyIdx'),
				'serviceIdx'		=> $vmRow->get('roleIdx'),
				'vmIdx'				=> $vmRow->get('idx'),
				'dateYmd'			=> $dateYmd,
				'year'				=> substr($dateYmd, 0, 4),
				'month'				=> substr($dateYmd, 4, 2),
				'contract_c'		=> $vmRow->get('contract_c'),
				'contract_d'		=> $vmRow->get('contract_d'),
				'contract_o'		=> $vmRow->get('OS_check'),
				'contract_s'		=> $vmRow->get('DBMS'),
				'cpu_unitcost'		=> $costArr['cpu'][$vmRow->get('contract_c')],
				'memory_unitcost'	=> $costArr['memory'][$vmRow->get('contract_c')],
				'disk_unitcost'		=> $costArr['disk'][$vmRow->get('contract_d')],
				'OS_unitcost'		=> $costArr['OS'][1],
				//'mssql_unitcost'	=> $mssql,
				'cpu_price'			=> $cpu_price,
				'memory_price'		=> $memory_price,
				'disk_price'		=> $disk_price,
			);
			
			$OS_price = 0;
			if($vmRow->get('OS_check') == 1){
				if(in_array($vmRow->get('idx'), $os_arr)){
					$OS_price = $costArr['OS'][1];
				} else if($row->size() == 0){
					$OS_price = $costArr['OS'][1];
				}
			}
			
			$mssql_price = 0;
			if($vmRow->get('dbUse') == 1){
				if($vmRow->get('DBMS') != 0 && $vmRow->get('db_startDate') <= $dateYmd){
					switch($vmRow->get('DBMS')){
						case 1 : $mssql = 0; break;
						case 4 : $mssql = $costArr['mssql_4'][1]; break;
						case 8 : $mssql = $costArr['mssql_8'][1]; break;
						case 12 : $mssql = $costArr['mssql_12'][1]; break;
					}
					if(in_array($vmRow->get('idx'), $mssql_arr)){
						$mssql_price = $mssql;
					} else if($row->size() == 0){
						$mssql_price = $mssql;
					}
				}
			}

			$total_price = $cpu_price + $memory_price + $disk_price + $OS_price + $mssql_price;
			$data['mssql_unitcost'] = $mssql;
			$data['OS_price'] = $OS_price;
			$data['mssql_price'] = $mssql_price;
			$data['total_price'] = $total_price;

			$db->insert("ReportDataByVmDay", $data);
		}
	}
?>