<?php
@include_once "../../../common/setCommonPath.php";
@include_once "Inc/inc.include.php";

@include_once COMMON_CLASS . "/class.billing.php";
@include_once COMMON_CLASS . "/class.account.php";

$M_BILLING = new M_BILLING;
$M_ACCOUNT = new M_ACCOUNT;

$mode =  $M_FUNC->M_Filter(GET, 'mode');
$year = $M_FUNC->M_Filter(GET, 'year');
$month = $M_FUNC->M_Filter(GET, 'month');
$dateYmd = $year.sprintf('%02d', $month);
$list_cnt = $M_FUNC->M_Filter(GET, 'list_cnt');
$page = $M_FUNC->M_Filter(GET, 'page');
$companyIdx =  $M_FUNC->M_Filter(GET, 'companyIdx');

$result_array = array();

if($page < 1) $page = 1 ;
if($list_cnt < 1) $list_cnt = 10 ;
$start_row = ($page - 1) * $list_cnt;

$addOrder = " serviceIdx DESC ";
$addLimit = " LIMIT " . $start_row .", ". $list_cnt;
$addWhere = "";

if($mode == "list"){
	$busineeArr = $M_ACCOUNT->getBusinessInfoArr();
	$companyArr = $M_ACCOUNT->getCompanyInfoArr();
	$serviceArr = $M_ACCOUNT->getServiceInfoArr();

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

	$reportByServiceDay = $M_BILLING->getReportDataByServiceDay_t($serviceWhere, "idx");

	$row = $M_BILLING->getReportDataByVmDayList_t($cnt, $addWhere,$addOrder,$addLimit, $group);

	for($i=0;$i<$row->size();$i++){
		$row->next();
		$no = $cnt - $i - (($page - 1) * $list_cnt);

		if($row->get("dateYmd") == 0) {
			$dateYmd = "-";
		} else {
			$dateYmd = date('Y-m-d H:i:s',$row->get("dateYmd"));
		}
		
		$num = ($i + 1) + ($list_cnt * ($page-1));

		$service_price =  $reportByServiceDay[$row->get('serviceIdx')]['ip_price'] + $reportByServiceDay[$row->get('serviceIdx')]['loadbalancer_price'];

		$result_array[$no]['num']			= $num;
		$result_array[$no]['vmIdx']			= $row->get('vmIdx');
		//$result_array[$no]['vmName']		= $row->get('vmName');
		$result_array[$no]['serviceIdx']	= $row->get('serviceIdx');
		$result_array[$no]['serviceName']	= $serviceArr[$row->get('serviceIdx')];
		$result_array[$no]['companyName']	= $companyArr[$row->get('companyIdx')];
		$result_array[$no]['businessName']	= $busineeArr[$row->get('businessIdx')];
		$result_array[$no]['dateYmd']		= substr($row->get('dateYmd'), 0, 4)."년 ".substr($row->get('dateYmd'), 4, 2)."월";
		$result_array[$no]['service_price']	= $service_price;
		$result_array[$no]['total_price']	= $row->get('total_price');
		$result_row[$no]['total_page']		= $cnt/$list_cnt;
	}
} elseif($mode == "graph") {
	$serviceIdx = $M_FUNC->M_Filter(GET, 'serviceIdx');

	$threeMonth_ago = $year.sprintf('%02d', ($month-3))."01";
	$addWhere = " AND r.dateYmd between ".$threeMonth_ago." and ".$dateYmd."31 AND r.serviceIdx = " . $serviceIdx;
	$group =  " serviceIdx, r.year, r.month ";

	$row = $M_BILLING->getReportDataByVmDayList_t($cnt, $addWhere,"year, r.month desc",$addLimit, $group);

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