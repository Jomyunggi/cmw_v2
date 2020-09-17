<?php
@include_once "../../../common/setCommonPath.php";
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

//고객사별 서버대수 배열로 담기
$company_serverNo = $M_REPORT->getServerCntByCompanyIdx($year.sprintf('%02d', $month), "group by ai.companyIdx");

$reportByServiceDay = $M_BILLING->getReportDataByServiceDay_t($serviceWhere, "companyIdx");

$reportByVmDay = $M_BILLING->getReportDataByVmDayList_t($cnt, $addWhere,$addOrder,$addLimit, $group);

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