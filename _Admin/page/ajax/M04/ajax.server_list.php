<?php
@include_once "../../common/setCommonPath.php";
@include_once "Inc/inc.include.php";

@include_once COMMON_CLASS . "/class.billing.php";
@include_once COMMON_CLASS . "/class.account.php";

$M_BILLING = new M_BILLING;
$M_ACCOUNT = new M_ACCOUNT;

$mode =  $M_FUNC->M_Filter(GET, 'mode');
$year = $M_FUNC->M_Filter(GET, 'year');
$month = $M_FUNC->M_Filter(GET, 'month');
$dateYmd = $year.sprintf('%02d', $month);
$option_dateYmd = $dateYmd;
$list_cnt = $M_FUNC->M_Filter(GET, 'list_cnt');
$page = $M_FUNC->M_Filter(GET, 'page');
$companyIdx =  $M_FUNC->M_Filter(GET, 'companyIdx');

$result_array = array();

if($page < 1) $page = 1 ;
if($list_cnt < 1) $list_cnt = 10 ;
$start_row = ($page - 1) * $list_cnt;

$addOrder = " serviceIdx DESC, v.v_startDate DESC ";
$addLimit = 'NO';
$addWhere = "";

if($mode == "list"){
	$busineeArr = $M_ACCOUNT->getBusinessInfoArr();
	$companyArr = $M_ACCOUNT->getCompanyInfoArr();
	$serviceArr = $M_ACCOUNT->getServiceInfoArr();
	$vmRow = $M_BILLING->getVMInfoForFastDate($dateYmd, 'ai.roleIdx');

	$vmInfo = array();
	for($i=0; $i<$vmRow->size(); $i++){
		$vmRow->next();

		$vmInfo[$vmRow->get('roleIdx')]['startDate'] = $vmRow->get('v_startDate');
		$vmInfo[$vmRow->get('roleIdx')]['endDate'] = $vmRow->get('v_endDate');
	}

	$addWhere = " AND r.dateYmd like '".$dateYmd."%' ";
	$group =  " serviceIdx ";

	if($_SESSION['Level'] == 4){
		$serviceWhere = $addWhere ." AND s.companyIdx =". $_SESSION['companyIdx'];
		$addWhere .= " AND r.companyIdx =". $_SESSION['companyIdx'];
	}
	if($companyIdx > 0){
		$serviceWhere = $addWhere ." AND s.companyIdx =". $companyIdx;
		$addWhere .= " AND r.companyIdx =". $companyIdx;
	} else {
		$serviceWhere = " AND r.dateYmd like '".$dateYmd."%' ";
	}

	//VM_Info 기준으로 사용중인 VM만 가져오기
	$vmArr = $M_BILLING->getVMInfoForUsing($dateYmd);
	if(count($vmArr) > 0){
		$addWhere .= " AND r.vmIdx in (". implode(",", $vmArr) .") ";
	}

	$reportByServiceDay = $M_BILLING->getReportDataByServiceDay($serviceWhere, "idx");

	$row = $M_BILLING->getReportDataByVmDayList($cnt, $addWhere,$addOrder,$addLimit, $group);
	
	$service_Arr = array();
	for($i=0;$i<$row->size();$i++){
		$row->next();
		$no = $row->get('serviceIdx');

		if($row->get("dateYmd") == 0) {
			$dateYmd = "-";
		} else {
			$dateYmd = date('Y-m-d H:i:s',$row->get("dateYmd"));
		}
		
		$num = ($i + 1) + ($list_cnt * ($page-1));

		$service_price =  $reportByServiceDay[$row->get('serviceIdx')]['ip_price'] + $reportByServiceDay[$row->get('serviceIdx')]['loadbalancer_price'];

		$result_array[$no]['num']			= $num;
		$result_array[$no]['vmIdx']			= $row->get('vmIdx');
		$result_array[$no]['serviceIdx']	= $row->get('serviceIdx');
		$result_array[$no]['serviceName']	= $serviceArr[$row->get('serviceIdx')];
		$result_array[$no]['companyName']	= $companyArr[$row->get('companyIdx')];
		$result_array[$no]['businessName']	= $busineeArr[$row->get('businessIdx')];
		$result_array[$no]['dateYmd']		= substr($row->get('dateYmd'), 0, 4)."년 ".substr($row->get('dateYmd'), 4, 2)."월";
		$result_array[$no]['service_price']	= $service_price;
		$result_array[$no]['total_price']	= $row->get('total_price');
		$result_array[$no]['v_startDate']	= $vmInfo[$row->get('serviceIdx')]['startDate'];
		$result_array[$no]['v_endDate']	= $vmInfo[$row->get('serviceIdx')]['endDate'];
		$result_array[$no]['total_page']		= $cnt/$list_cnt;

		array_push($service_Arr, $row->get('serviceIdx'));
	}

	//진행중인 VM은 없지만 서비스를 이용중인 Service idx 가져오기 (ip, lbs 사용중인)
	if(count($service_Arr) > 0){
		$addOption_where = " AND r.dateYmd like '".$option_dateYmd."%' AND r.serviceIdx not in (". implode(",", $service_Arr) .") and r.total_price <> 0 ";
		if($companyIdx > 0) $addOption_where .= " AND ai.companyIdx = ". $companyIdx;
	}
	$addOption = $M_BILLING->getUsingServiceOption_v2($addOption_where);

	foreach($addOption as $serviceIdx => $arr){
		$no = $serviceIdx;

		$result_array[$no]['num']				= 0;
		$result_array[$no]['vmIdx']			= 0;
		$result_array[$no]['serviceIdx']		= $serviceIdx;
		$result_array[$no]['serviceName']	= $serviceArr[$serviceIdx];
		$result_array[$no]['companyName']	= $companyArr[$arr['companyIdx']];
		$result_array[$no]['businessName']	= $busineeArr[$arr['businessIdx']];
		$result_array[$no]['dateYmd']			= substr($option_dateYmd, 0, 4)."년 ".substr($option_dateYmd, 4, 2)."월";
		$result_array[$no]['service_price']	= $arr['total_price'];
		$result_array[$no]['total_price']		= 0;
		$result_array[$no]['v_startDate']	= 0;
		$result_array[$no]['v_endDate']		= 0;
		$result_array[$no]['total_page']		= 0;
	}

	$result_array = $M_BILLING->arrayByIndex_Reorder($result_array);

} elseif($mode == "graph") {
	$serviceIdx = $M_FUNC->M_Filter(GET, 'serviceIdx');

	$threeMonth_ago = $year.sprintf('%02d', ($month-3))."01";
	$addWhere = " AND r.dateYmd between ".$threeMonth_ago." and ".$dateYmd."31 AND r.serviceIdx = " . $serviceIdx;
	$group =  " serviceIdx, r.year, r.month ";

	$row = $M_BILLING->getReportDataByVmDayList($cnt, $addWhere,"year, r.month desc",$addLimit, $group);

	for($i=0;$i<$row->size();$i++){
		$row->next();
		$no = $cnt - $i - (($page - 1) * $list_cnt);

		if($row->get("dateYmd") == 0) {
			$dateYmd = "-";
		} else {
			$dateYmd = date('Y-m-d H:i:s',$row->get("dateYmd"));
		}
		
		$result_array[$no]['dateYmd']		= substr($row->get('dateYmd'), 0, 4)."년 ".substr($row->get('dateYmd'), 4, 2)."월";
		$result_array[$no]['total_price']	= $row->get('total_price');
	}

}

echo json_encode($result_array);
?>