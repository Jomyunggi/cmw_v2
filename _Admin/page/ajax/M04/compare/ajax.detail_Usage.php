<?php
	@include_once "../../../common/setCommonPath.php";
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
	
	//cpu, memory, disk 각각에 대한 총량을 가져오는 부분
	$query = " select sum(cpu) as sumCpu, sum(memory) as sumMemory, sum(disk) as sumDisk, sum(disk_os) as sumDisk_os, count(v.disk_os) as disk_osCnt, sum(disk_data) as sumDisk_data, sum(OS_check) as sumOS, sum(DBMS) as sumDBMS"
			." from Service_Info s "
			."		left join VM_Account_Link va on s.keyAccountIdx = va.accountIdx "
			."		left join VM_Info v on va.vmIdx = v.idx "
			." where s.idx = ".$serviceIdx." and ((v.`status` = 1 and  v_startDate <= ".$enddate.") OR (v.`status` in (4, 99) and v_endDate >= ".$startdate." and v_startDate <= ".$enddate."))";
	$row = $db->getListSet($query);
	$row->next();

	//cpu, memory, disk 각각에 대한 총량의 금액
	$addWhere = " and r.dateYmd between ".$startdate." and ".$enddate." and r.serviceIdx = ".$serviceIdx;
	$reportRow = $M_BILLING->getReportDataByVmDayList_t($cnt, $addWhere, $order, $limit, "serviceIdx");
	$reportRow->next();

	$addWhere = " AND left(r.dateYmd, 6) = ".$dateYmd." AND r.serviceIdx = " . $reportRow->get('serviceIdx');

	$reportByServiceDay = $M_BILLING->getReportDataByServiceDay2_t($addWhere);
	
	if($reportByServiceDay[$reportRow->get('serviceIdx')]['ip_price'] > 0){
		$ip_price = $reportByServiceDay[$reportRow->get('serviceIdx')]['ip_price'];
		$loadbalancer_price = $reportByServiceDay[$reportRow->get('serviceIdx')]['loadbalancer_price'];
	} else {
		$ip_price = 0;
		$loadbalancer_price = 0;
	}

	//공인IP 및 LBS 값 가져오기
	$query_option = " select * from ServiceByAdditionalOption_backup where serviceIdx = " . $reportRow->get('serviceIdx');
	$optionRow = $db->getListSet($query_option);

	if($optionRow->size() > 0){
		$ipCnt = 0;
		$balancerName = '';
		$lbsS = 0;
		$lbsM = 0;
		$lbsL = 0;
		for($i=0; $i<$optionRow->size(); $i++){
			$optionRow->next();

			$ipCnt += count(explode(",", $optionRow->get('ip')));
			$lbs = explode(",", $optionRow->get('loadbalancer'));
			/*
			for($j=0; $j<count($lbs); $j++){
				switch($lbs[$j]){
					case 0 : $balancerName .= '-'; break;
					case 1 : $balancerName .= 'LBS Small'; break;
					case 2 : $balancerName .= 'LBS Middle'; break;
					case 4 : $balancerName .= 'LBS Large'; break;
				}
				if($j != (count($lbs) -1)) $balancerName .= "<br/>";
			}
			if($i != ($optionRow->size() -1)) $balancerName .= "<br/>";
			*/
			for($j=0; $j<count($lbs); $j++){
				switch($lbs[$j]){
					case 1 : $lbsS += 1; break;
					case 2 : $lbsM += 1; break;
					case 4 : $lbsL += 1; break;
				}
			}
			$balancerName = 'SLB [S: '.$lbsS.', M: '.$lbsM.', L: '.$lbsL.']';
		}
	} else {
		$ipCnt = 0;
		$balancerName = '-';
	}

	$result_array['sumCpu']			= $row->get('sumCpu');
	$result_array['sumMemory']		= $row->get('sumMemory');
	$result_array['sumDisk']		= $row->get('sumDisk');
	$result_array['sumDisk_os']		= $row->get('sumDisk_os');
	$result_array['disk_osCnt']		= $row->get('disk_osCnt');
	$result_array['sumDisk_data']	= $row->get('sumDisk_data');
	$result_array['sumOS']			= $row->get('sumOS');
	$result_array['sumDBMS']		= $row->get('sumDBMS');
	$result_array['ipCnt']			= $ipCnt;
	$result_array['balancerName']	= $balancerName;
	$result_array['cpu_price']		= $reportRow->get('cpu_price');
	$result_array['memory_price']	= $reportRow->get('memory_price');
	$result_array['disk_price']		= $reportRow->get('disk_price');
	$result_array['OS_price']		= $reportRow->get('OS_price');
	$result_array['mssql_price']	= $reportRow->get('mssql_price');
	$result_array['ip_price']		= $ip_price;
	$result_array['loadbalancer_price']		= $loadbalancer_price;
	$result_array['total_price']		= $reportRow->get('total_price');

	echo json_encode($result_array);

?>