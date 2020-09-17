<?php
@include_once "../../common/setCommonPath.php";
@include_once "Inc/inc.include.php";

@include_once COMMON_CLASS . "/class.billing.php";
@include_once COMMON_CLASS . "/class.account.php";
@include_once COMMON_CLASS . "/class.report.php";

$M_BILLING = new M_BILLING;
$M_ACCOUNT = new M_ACCOUNT;
$M_REPORT = new M_REPORT;

$companyIdx = $M_FUNC->M_Filter(GET, 'companyIdx');
$year = $M_FUNC->M_Filter(GET, 'year');
$month = $M_FUNC->M_Filter(GET, 'month');
$dateYmd = $year.sprintf('%02d', $month);


$addWhere = " AND r.dateYmd like '".$dateYmd."%' ";
$group =  " companyIdx ";

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
$serviceArr = $M_BILLING->getVmDataByserviceIdx($dateYmd);


//진행중인 VM은 없지만 서비스를 이용중인 Service idx 가져오기 (ip, lbs 사용중인)
$addOption_where = " AND r.dateYmd like '".$dateYmd."%' AND r.serviceIdx not in (". implode(",", $serviceArr) .") and r.total_price <> 0 ";
if($companyIdx > 0) $addOption_where .= " AND ai.companyIdx = ". $companyIdx;

$addOption = $M_BILLING->getUsingServiceOption_v2($addOption_where, true);

for($i=0; $i<$addOption->size(); $i++){
	$addOption->next();
	array_push($serviceArr, $addOption->get('serviceIdx'));
}


$serviceWhere .= " AND s.idx in (". implode(",", $serviceArr) .") ";

//고객사별 서버대수 배열로 담기
$company_serverNo = $M_REPORT->getServerCntByCompanyIdx($year.sprintf('%02d', $month), "group by ai.companyIdx");

$reportByServiceDay = $M_BILLING->getReportDataByServiceDay($serviceWhere, "companyIdx");

$reportByVmDay = $M_BILLING->getReportDataByVmDayList($cnt, $addWhere,$addOrder,$addLimit, $group);

$result_array = array();
if($reportByVmDay->size() > 0){
	for($i=0; $i<$reportByVmDay->size(); $i++){
		$reportByVmDay->next();

		$service_price =  $reportByServiceDay[$reportByVmDay->get('companyIdx')]['ip_price'] + $reportByServiceDay[$reportByVmDay->get('companyIdx')]['loadbalancer_price'];

		$result_array[$i]['companyName'] = $M_ACCOUNT->getCompanyInfoByIdx($reportByVmDay->get('companyIdx'), 'companyName');
		$result_array[$i]['total_price'] = $reportByVmDay->get('total_price');
		$result_array[$i]['service_price'] = $service_price;
		$result_array[$i]['serverNo'] = $company_serverNo[$reportByVmDay->get('companyIdx')];
	}
}

echo json_encode($result_array);
?>