<?php
	/*********************************************************************
	*    Description	:	Class for User page
	*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
	*    Date			:	2012. 05. 15
	*    Have a nice day, Good Luck to you ^^/
	*********************************************************************/
	class M_CALCULATE {
		function __construct() {
					
		}

		function __destruct() {
			
		}
		
		/*********************************
		//해당기간에 정산된 데이터를 delete
		*********************************/
		function deleteForReportDataByVmDay($startdate, $enddate){
			global $db;

			$where = " WHERE dateYmd between ". $startdate ." and ". $enddate;
			$db->delete('ReportDataByVmDay', $where);
		}

		function deleteReportDataByVmDayByIdx($where){
			global $db;

			$db->delete('ReportDataByVmDay', $where);
		}

		function insertReportDataByVmDay($data){
			global $db;

			$table = 'ReportDataByVmDay';
			$db->insert($table, $data);
		}

		function updateReportDataByVmDayByIdx($data, $where){
			global $db;

			$db->update('ReportDataByVmDay', $data, $where);
		}

		/*********************************************************
		//위에서 delete 된 후 해당 기간에 insert할 data를 만들어 놓기
		*********************************************************/
		function calculateForVmDataByDay($startdate, $enddate){
			global $db;

			$costArr = $this->getUnicost();
			$vmByIdx_arr = $this->getVMRelation_Info();
			
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
					.'		, sum(v.dbUse) as dbUse '
					.'		, sum(v.DBMS) as DBMS '
					.'		, sum(v.db_startDate) as db_startDate '
					.' FROM VMDataByDay v '
					.' WHERE v.dateYmd between '. $startdate .' and '. $enddate
					.' AND ((v.`status` = 1 and v.v_startDate <= '. $enddate .') '
					.'	OR (v.`status` in(4,99) and v.v_endDate >= '.$startdate.' and v.v_startDate <= '.$enddate.')) '
					.' GROUP BY v.dateYmd, v.vmIdx '
					.' ORDER BY v.dateYmd asc, v.vmIdx asc, v.dbUse desc, v.DBMS desc, v.db_startDate desc '
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

				$monthlyCheck = $this->monthly_calculate($row['vmIdx'], $startdate);

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
				
				$db->insert('ReportDataByVmDay', $data);
			}
		}

		function calculateForVmDataByDay_v2($startdate, $enddate, $vmIdx){
			global $db;

			$costArr = $this->getUnicost();
			$vmByIdx_arr = $this->getVMRelation_Info();
			
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
					.'		, sum(v.dbUse) as dbUse '
					.'		, sum(v.DBMS) as DBMS '
					.'		, sum(v.db_startDate) as db_startDate '
					.' FROM VMDataByDay v '
					.' WHERE v.dateYmd between '. $startdate .' and '. $enddate .' and v.vmIdx = '. $vmIdx
					.' AND ((v.`status` = 1 and v.v_startDate <= '. $enddate .') '
					.'	OR (v.`status` in(4,99) and v.v_endDate >= '.$startdate.' and v.v_startDate <= '.$enddate.')) '
					.' GROUP BY v.dateYmd, v.vmIdx '
					.' ORDER BY v.dateYmd asc, v.vmIdx asc, v.dbUse desc, v.DBMS desc, v.db_startDate desc '
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

				$monthlyCheck = $this->monthly_calculate($row['vmIdx'], $row['dateYmd']);

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
				
				$db->insert('ReportDataByVmDay', $data);
			}
		}

		/**********************************************
		//서비스 기간에 맞춰 등록된 VM_Info 정보를 Insert
		**********************************************/
		function insertVmDataByDay($vmIdx, $startdate, $enddate, $data = array()){
			global $db, $M_JS;

			$query = ' SELECT * '
					.' FROM VMDataByDay '
					.' WHERE dateYmd between '. $startdate .' and '. $enddate .' and vmIdx = '. $vmIdx;
			$row = $db->getListSet($query);

			if($row->size() > 0){
				return false;
			} else {
				$start = strtotime($startdate);
				$end = strtotime($enddate);

				for($dd=$start; $dd<=$end; $dd=strtotime('+1 day', $dd)){
					$dateYmd = date('Ymd', $dd);

					$data['dateYmd'] = $dateYmd;
					$db->insert('VMDataByDay', $data);
				}
				return true;
			}
		}

		function insertVmDataByDayByData($data){
			global $db;

			$table = "VMDataByDay";
			$db->insert($table, $data);
		} 

		/******************************************************************************
		//VM 데이터 증가, 감소 기능은 앞선에서 이미 한 작업이므로 제외하고
		//VM 등록시에 해당 서비스 기간동안에 대한 일별 데이터를 Insert 해준다.
		******************************************************************************/
		function insertDataByVMDataByDay($startdate, $enddate){
			global $db;

			$daily_data = array();
			$query = ''
					.' SELECT v.* '
					.' FROM VM_Info v '
					.'		LEFT JOIN VMDataByDay vd ON v.idx = vd.vmIdx '
					.' WHERE ((v.`status` = 1 and v.v_startDate <= '.$enddate.' ) '
					.'		OR (v.`status` in (4,99) and v.v_endDate>= '.$startdate.' and v.v_startDate<= '.$enddate.' )) '
					.'		AND vd.idx is null ';
			$result = $db_test->execute($query);
			while($row = mysql_fetch_assoc($result)){
				$daily_data['vmIdx'] = $row['idx'];
				$daily_data['vmType'] = $row['vmType'];
				$daily_data['vmName'] = $row['vmName'];
				$daily_data['cpu'] = $row['cpu'];
				$daily_data['memory'] = $row['memory'];
				$daily_data['disk'] = $row['disk'];
				$daily_data['disk_os'] = $row['disk_os'];
				$daily_data['disk_data'] = $row['disk_data'];
				$daily_data['contract_c'] = $row['contract_c'];
				$daily_data['contract_d'] = $row['contract_d'];
				$daily_data['v_startDate'] = $row['v_startDate'];
				$daily_data['v_endDate'] = $row['v_endDate'];
				$daily_data['status'] = $row['status'];
				$daily_data['OS'] = $row['OS'];
				$daily_data['OS_check'] = $row['OS_check'];
				$daily_data['dbUse'] = $row['dbUse'];
				$daily_data['dbName'] = $row['dbName'];
				$daily_data['DBMS'] = $row['DBMS'];
				$daily_data['db_startDate'] = $row['db_startDate'];

				$start = strtotime($row['v_startDate']);
				if($row['v_endDate'] > 0){
					$end = strtotime($row['v_endDate']);
				} else {
					$end = strtotime($enddate);
				}
				for($dd=$start; $dd<=$end; $dd=strtotime('+1 day', $dd)){
					$daily_data['dateYmd'] = date('Ymd', $dd);
					
					$db->insert('VMDataByDay', $daily_data);
				}
			}
		}

		function insertDataByVMDataByDay_v2($m_id, $startdate, $enddate){
			global $db;

			$daily_data = array();
			$query = ''
					.' SELECT v.* '
					.' FROM VM_Info v '
					.' WHERE v.idx = '. $m_id .' AND ((v.`status` = 1 and v.v_startDate <= '.$enddate.' ) '
					.'		OR (v.`status` in (4,99) and v.v_endDate>= '.$startdate.' and v.v_startDate<= '.$enddate.' )) '
					;
			$result = $db->execute($query);
			while($row = mysql_fetch_assoc($result)){
				$daily_data['vmIdx'] = $row['idx'];
				$daily_data['vmType'] = $row['vmType'];
				$daily_data['vmName'] = $row['vmName'];
				$daily_data['cpu'] = $row['cpu'];
				$daily_data['memory'] = $row['memory'];
				$daily_data['disk'] = $row['disk'];
				$daily_data['disk_os'] = $row['disk_os'];
				$daily_data['disk_data'] = $row['disk_data'];
				$daily_data['contract_c'] = $row['contract_c'];
				$daily_data['contract_d'] = $row['contract_d'];
				$daily_data['v_startDate'] = $row['v_startDate'];
				$daily_data['v_endDate'] = $row['v_endDate'];
				$daily_data['status'] = $row['status'];
				$daily_data['OS'] = $row['OS'];
				$daily_data['OS_check'] = $row['OS_check'];
				$daily_data['dbUse'] = $row['dbUse'];
				$daily_data['dbName'] = $row['dbName'];
				$daily_data['DBMS'] = $row['DBMS'];
				$daily_data['db_startDate'] = $row['db_startDate'];

				$start = strtotime($row['v_startDate']);
				if($row['v_endDate'] > 0){
					$end = strtotime($row['v_endDate']);
				} else {
					$end = strtotime($enddate);
				}
				for($dd=$start; $dd<=$end; $dd=strtotime('+1 day', $dd)){
					$daily_data['dateYmd'] = date('Ymd', $dd);
					
					$db->insert('VMDataByDay', $daily_data);
				}
			}
		}

		function insertDataByServiceOption($data){
			global $db;
			
			$costArr = $this->getUnicost();
			
			$service_data = array();
			foreach($data as $date => $arr){
				$dateYmd = $date;
				$serviceIdx = $arr['serviceIdx'];
				$ipCnt = explode(",", substr($arr['ip'], 0, -1));
				$ip_unitcost = $costArr['officialIP'][$arr['ip_contract']];
				$lbs = explode(",", substr($arr['loadbalancer'], 0, -1));
				
				if($ipCnt[0] == 0 || $ipCnt[0] == ''){
					$ip_price = 0;
				} else {
					$ip_price = count($ipCnt) * $ip_unitcost;
				}
				$lbs_sumPrice = 0;
				for($i=0; $i<count($lbs); $i++){
					switch($lbs[$i]){
						case 1 : $lbs_unit = $costArr['lbsS'][$arr['lbs_contract']]; break;
						case 2 : $lbs_unit = $costArr['lbsM'][$arr['lbs_contract']]; break;
						case 4 : $lbs_unit = $costArr['lbsL'][$arr['lbs_contract']]; break;
						case 8 : $lbs_unit = $costArr['ssl'][$arr['lbs_contract']]; break;
						default : $lbs_unit = 0; break;
					}
					
					$lbs_sumPrice = $lbs_sumPrice + $lbs_unit;
				}

				$total_price = $ip_price + $lbs_sumPrice;
				
				$service_data['serviceIdx'] = $serviceIdx;
				$service_data['dateYmd'] = $dateYmd;
				$service_data['year'] = substr($dateYmd, 0, 4);
				$service_data['month'] = substr($dateYmd, 4, 2);
				$service_data['ip_unitcost'] = $ip_unitcost;
				$service_data['ipCnt'] = count($ipCnt);
				$service_data['loadbalancer_unitcost'] = $lbs_unit;
				$service_data['ip_price'] = $ip_price;
				$service_data['loadbalancer_price'] = $lbs_sumPrice;
				$service_data['total_price'] = $total_price;
				$service_data['status'] = 1;
				
				$db->insert('ReportDataByServiceDay', $service_data);	
			}
		}

		function insertServiceDailyForVmCnt(){
			global $db;
		}

		function deleteVMDataByDay($vmIdx, $enddate){
			global $db;
			
			$where = "where vmIdx = ". $vmIdx ." AND dateYmd > " . str_replace("-", '', $enddate);
			$db->delete("VMDataByDay", $where);
		}
		
		function deleteVMDataByDay2($where){
			global $db;
			
			$db->delete("VMDataByDay", $where);
		}

		function deleteReportDataByVmDay($vmIdx, $enddate){
			global $db;
			
			$where = "where vmIdx = ". $vmIdx ." AND dateYmd > " . str_replace("-", '', $enddate);
			$db->delete("ReportDataByVmDay", $where);
		}

		function insertDataByVmDataByidx($dateYmd, $m_id){
			global $db;

			$query = " SELECT * "
					." FROM VM_Info "
					." WHERE ((status = 1 and v_startDate <= " . $dateYmd ." ) "
					."	OR (status in (4, 99) and v_startDate <= " . $dateYmd ." and v_endDate >= " . $dateYmd . " )) "
					." AND idx = ". $m_id;
			$row = $db->getListSet($query);

			if($row->size() > 0){
				$row->next();
				
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

				//$db->insert("VMDataByDay", $data);
			} 
			return $data;
		}

		function insertDataByVmDataAdditionalByidx($dateYmd, $m_id){
			global $db;

			$query = ''
				.' SELECT '
				.'	dateYmd, vmIdx, sum(cpu) cpu, sum(memory) memory, sum(disk_os) disk_os, sum(disk_data) disk_data , DBMS, dbUse, db_startdate, vmType, vmName, contract_c, contract_d, v_startDate, v_endDate, status, OS, OS_check '
				.' FROM ( '
				.'	SELECT '
				.'		'.$dateYmd.' as dateYmd '
				.'		, vr.vmIdx '
				.'		, case vr.cpu '
				.'			when 0 then 0 '
				.'			else '
				.'				case '
				.'					when vr.c_startdate <= '.$dateYmd.' then vr.cpu '
				.'					else 0 '
				.'				end '
				.'		end as cpu '
				.'		, case vr.memory '
				.'			when 0 then 0 '
				.'			else '
				.'				case '
				.'					when vr.m_startdate <= '.$dateYmd.' then vr.memory '
				.'					else 0 '
				.'				end '
				.'		end as memory  '
				.'		, case vr.disk_os '
				.'			when 0 then 0 '
				.'			else '
				.'				case '
				.'					when vr.o_startdate <= '.$dateYmd.' then vr.disk_os '
				.'					else 0 '
				.'				end '
				.'		end as disk_os '
				.'		, case vr.disk_data '
				.'			when 0 then 0 '
				.'			else '
				.'				case '
				.'					when vr.d_startdate <= '.$dateYmd.' then vr.disk_data '
				.'					else 0 '
				.'				end '
				.'		end as disk_data '
				.'		, case vr.dbUse '
				.'			when 0 then 0 '
				.'			else '
				.'				case '
				.'					when vr.db_startdate <= '.$dateYmd.' then vr.DBMS '
				.'					else 0 '
				.'				end '
				.'		end as DBMS '
				.'		, vr.dbUse '
				.'		, vr.db_startdate '
				.'		, v.vmType, v.vmName, v.contract_c, v.contract_d, v.v_startDate, v.v_endDate, v.status, v.OS, v.OS_check '
				.' FROM VM_Resource_Info vr '
				.'		INNER JOIN VM_Info v ON vr.vmIdx = v.idx and  ((status = 1 and v_startDate <= ' . $dateYmd .' ) OR (status in (4, 99) and v_startDate <= ' . $dateYmd .' and v_endDate >= ' . $dateYmd . ' )) '
				.' WHERE vmIdx = '. $m_id
			    .' ) u '
				.' Group by u.vmIdx ';
			$row = $db->getListSet($query);

			if($row->size() > 0){
				$row->next();

				$disk = $row->get('disk_os') + $row->get('disk_data');

				//해당 vm정보가 없으므로 insert
				$data = array(
					'dateYmd'		=> $row->get('dateYmd'),
					'vmIdx'			=> $row->get('vmIdx'),
					'vmType'		=> $row->get('vmType'),
					'vmName'		=> $row->get('vmName'),
					'cpu'			=> $row->get('cpu'),
					'memory'		=> $row->get('memory'),
					'disk'			=> $disk,
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
					'DBMS'			=> $row->get('DBMS'),
					'db_startDate'	=> $row->get('db_startDate'),
				);

				//$db->insert("VMDataByDay", $data);			
			}
			return $data;
		}

		function deleteReportDataByService($where){
			global $db;
			
			$db->delete("ReportDataByServiceDay", $where);
		}

		function stackForServerCnt($vmIdx, $startdate, $enddate){
			global $db;
			
			$updateDate = array();
			$vmByIdx_arr = $this->getVMRelation_Info();

			$query = " SELECT * "
						 . " FROM VMDataServerCnt "
						 . " WHERE 1=1 "
						 . "	AND dateYmd between ". $startdate ." AND ". $enddate .""
						 . "	AND serviceIdx = ". $vmByIdx_arr[$vmIdx]['serviceIdx'];
			$row = $db->getListSet($query);
			
			$dateYmd_Arr = array();
			for($i=0; $i<$row->size(); $i++){
				$row->next();
				array_push($dateYmd_Arr, $row->get('dateYmd'));
			}

			$start = strtotime($startdate);
			$end = strtotime($enddate);
			for($dd=$start; $dd<=$end; $dd=strtotime('+1 day', $dd)){
				$dateYmd = date('Ymd', $dd);
				
				if(!in_array($dateYmd, $dateYmd_Arr)){
					//해당일에 등록된 데이터가 없으므로 INSERT
					$data = array(
						'serviceIdx'		=>	$vmByIdx_arr[$vmIdx]['serviceIdx'],
						'companyIdx'	=>	$vmByIdx_arr[$vmIdx]['companyIdx'],
						'businessIdx'	=>	$vmByIdx_arr[$vmIdx]['businessIdx'],
						'dateYmd'			=>	$dateYmd,
						'status'				=>	1,
						'serverCnt'		=>	1
					);
					$db->insert('VMDataServerCnt', $data);
				} else {
					array_push($updateDate, $dateYmd);
				}
			}

			if(count($updateDate) > 0){
				//해당일에 등록된 데이터가 있으므로 UPDATE
				$sql = " update VMDataServerCnt set serverCnt = serverCnt + 1 where serviceIdx = ". $vmByIdx_arr[$vmIdx]['serviceIdx']
						 . "	AND dateYmd in ( ". implode(",", $updateDate) ." ) ";
				$db->execute($sql);
			}
		}

		function popForServerCnt($vmIdx, $enddate){
			global $db;
			
			$vmByIdx_arr = $this->getVMRelation_Info();
			$serviceIdx = $vmByIdx_arr[$vmIdx]['serviceIdx'];

			$sql = " update VMDataServerCnt set serverCnt = serverCnt -1 where serviceIdx = ". $serviceIdx ." AND dateYmd > ". $enddate;
			$db->execute($sql);
		}

		function pushForServerCnt($vmIdx, $startdate){
			global $db;
			
			$vmByIdx_arr = $this->getVMRelation_Info();
			$serviceIdx = $vmByIdx_arr[$vmIdx]['serviceIdx'];

			$sql = " update VMDataServerCnt set serverCnt = serverCnt +1 where serviceIdx = ". $serviceIdx ." AND dateYmd >= ". $startdate;
			$db->execute($sql);
		}

	} //*** End of Class	
?>