<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	$serviceIdx = $M_FUNC->M_Filter(GET, 'serviceIdx');
	$year = $M_FUNC->M_Filter(GET, 'year');
	$month = $M_FUNC->M_Filter(GET, 'month');
	$dateYmd = $year.sprintf('%02d', $month);
	$startdate = $dateYmd."01";
	$enddate = $dateYmd."31";

	include_once COMMON_CLASS . "/class.billing.php";
	$M_BILLING = new M_BILLING;

	$result_array = array();
	$distinct_DB = array();
	$query = " SELECT distinct v.DBMS as DBMS, count(v.DBMS) as dbCnt "
			." from Service_Info s "
			."		left join VM_Account_Link va on s.keyAccountIdx = va.accountIdx "
			."		left join VM_Info v on va.vmIdx = v.idx "
			." where s.idx = ".$serviceIdx." and ((v.`status` = 1 and  v_startDate <= ".$enddate.") OR (v.`status` in (4, 99) and v_endDate >= ".$startdate." and v_startDate <= ".$enddate."))"
			." group by v.DBMS ";
	$result = $db->execute($query);
	while($row = mysql_fetch_assoc($result)){
		$DBMS = $row['DBMS'];

		$distinct_DB[$DBMS] = $row['dbCnt'];
	}
	
	//cpu, memory, disk 각각에 대한 총량을 가져오는 부분
	$query = " select sum(cpu) as sumCpu, sum(memory) as sumMemory, sum(disk) as sumDisk, sum(disk_os) as sumDisk_os, count(v.disk_os) as disk_osCnt, sum(disk_data) as sumDisk_data, sum(OS_check) as sumOS, sum(DBMS) as sumDBMS"
			." from Service_Info s "
			."		left join VM_Account_Link va on s.keyAccountIdx = va.accountIdx "
			."		left join VM_Info v on va.vmIdx = v.idx "
			." where s.idx = ".$serviceIdx." and ((v.`status` = 1 and  v_startDate <= ".$enddate.") OR (v.`status` in (4, 99) and v_endDate >= ".$startdate." and v_startDate <= ".$enddate."))";
	$row = $db->getListSet($query);
	$row->next();
	
	$sumOS = 0;
	if($row->size() > 0 && $row->get('sumOS')) $sumOS = $row->get('sumOS');

	$addVMInfo = $M_BILLING->getVMResourceByserviceIdx_v2($serviceIdx, $enddate);
	
	//해당월에 cpu, memory, disk_os, disk_data가 증가하고 줄어들면 쿼리상에서는 처리가 불가능하므로 소스단에서 추가해준다.
	/**********************
	1.별이되어라(28) : disk_data 200추가
	**********************/
	$additional_Data = array(
		'cpu'	=> array(),
		'memory'	=> array(),
		'disk_os'	=> array(),
		'disk_data'	=> array('28|201709' => 200)
	);

	foreach($additional_Data as $type => $arr){
		foreach($arr as $key => $value){
			$classification = explode("|", $key);
			if($classification[1] == $dateYmd){
				$addVMInfo[$classification[0]][$type] += $value;
			}
		}
	}

	//cpu, memory, disk 각각에 대한 총량의 금액
	$addWhere = " and r.dateYmd between ".$startdate." and ".$enddate." and r.serviceIdx = ".$serviceIdx;
	$reportRow = $M_BILLING->getReportDataByVmDayList($cnt, $addWhere, $order, $limit, "serviceIdx");
	$reportRow->next();
	
	if($reportRow->size() > 0 ){
		$report_serviceIdx	= $reportRow->get('serviceIdx');
		$cpu_price				= $reportRow->get('cpu_price');
		$memory_price			= $reportRow->get('memory_price');
		$disk_price				= $reportRow->get('disk_price');
		$OS_price				= $reportRow->get('OS_price');
		$mssql_price			= $reportRow->get('mssql_price');
		$total_price				= $reportRow->get('total_price');
	} else {
		$report_serviceIdx = $serviceIdx;
		$cpu_price				= 0;
		$memory_price			= 0;
		$disk_price				= 0;
		$OS_price				= 0;
		$mssql_price			= 0;
		$total_price				= 0;
	}

	$addWhere = " AND left(r.dateYmd, 6) = ".$dateYmd." AND r.serviceIdx = " . $report_serviceIdx;

	$reportByServiceDay = $M_BILLING->getReportDataByServiceDay2($addWhere);
	
	if($reportByServiceDay[$report_serviceIdx]['ip_price'] > 0){
		$ip_price = $reportByServiceDay[$report_serviceIdx]['ip_price'];
		$loadbalancer_price = $reportByServiceDay[$report_serviceIdx]['loadbalancer_price'];
	} else {
		$ip_price = 0;
		$loadbalancer_price = 0;
	}

	//공인IP 및 LBS 값 가져오기
	$query_option = " select * from ServiceByAdditionalOption where serviceIdx = " . $report_serviceIdx;
	$optionRow = $db->getListSet($query_option);
	$ipCnt = 0;
	$balancerName = 'SLB [S: 0, M: 0, L: 0, SSL: 0]';
	if($optionRow->size() > 0){
		$lbsS = 0;
		$lbsM = 0;
		$lbsL = 0;
		$ssl = 0;
		for($i=0; $i<$optionRow->size(); $i++){
			$optionRow->next();
			
			if($optionRow->get('ip_contract') > 0){
				$ips = explode(",", $optionRow->get('ip'));
				$ip_sd = explode(",", $optionRow->get('ip_startDate'));
				$ip_ed = explode(",", $optionRow->get('ip_endDate'));
				for($j=0; $j<count($ips); $j++){
					if($ip_ed[$j] == 0){
						if(str_replace("-", "", $ip_sd[$j])<= $enddate){
							$ipCnt += 1;
						}
					} else {
						if(str_replace("-", "", $ip_sd[$j]) <= $enddate && str_replace("-", "", $ip_ed[$j]) >= $startdate){
							$ipCnt += 1;
						}
					}
				}
			}
			
			if($optionRow->get('balance_contract') > 0){
				$lbs = explode(",", $optionRow->get('loadbalancer'));
				
				for($j=0; $j<count($lbs); $j++){
					switch($lbs[$j]){
						case 1 : $lbsS += 1; break;
						case 2 : $lbsM += 1; break;
						case 4 : $lbsL += 1; break;
						case 8 : $ssl += 1; break;
					}
				}
			}
			$balancerName = 'SLB [S: '.$lbsS.', M: '.$lbsM.', L: '.$lbsL.', SSL: '.$ssl.']';
		}
	}
	
	$dbCnt1 = array_key_exists(1, $distinct_DB) ? $distinct_DB[1] : '0';
	$dbCnt4 = array_key_exists(4, $distinct_DB) ? $distinct_DB[4] : '0';
	$dbCnt8 = array_key_exists(8, $distinct_DB) ? $distinct_DB[8] : '0';
	$dbCnt12 = array_key_exists(12, $distinct_DB) ? $distinct_DB[12] : '0';

	$result_array['sumCpu']			= $row->get('sumCpu') + $addVMInfo[$serviceIdx]['cpu'];
	$result_array['sumMemory']		= $row->get('sumMemory') + $addVMInfo[$serviceIdx]['memory'];
	$result_array['sumDisk']		= $row->get('sumDisk') + $addVMInfo[$serviceIdx]['disk'];
	$result_array['sumDisk_os']		= $row->get('sumDisk_os') + $addVMInfo[$serviceIdx]['disk_os'];
	$result_array['disk_osCnt']		= $row->get('disk_osCnt');
	$result_array['sumDisk_data']	= $row->get('sumDisk_data') + $addVMInfo[$serviceIdx]['disk_data'];
	$result_array['sumOS']			= $sumOS;
	$result_array['sumDBMS']		= $row->get('sumDBMS');
	$result_array['db1']			= $dbCnt1;
	$result_array['db4']			= $dbCnt4;
	$result_array['db8']			= $dbCnt8;
	$result_array['db12']			= $dbCnt12;
	$result_array['ipCnt']			= $ipCnt;
	$result_array['balancerName']	= $balancerName;
	$result_array['cpu_price']		= $cpu_price;
	$result_array['memory_price']	= $memory_price;
	$result_array['disk_price']		= $disk_price;
	$result_array['OS_price']		= $OS_price;
	$result_array['mssql_price']		= $mssql_price;
	$result_array['ip_price']		= $ip_price;
	$result_array['loadbalancer_price']		= $loadbalancer_price;
	$result_array['total_price']		= $total_price;

	echo json_encode($result_array);

?>