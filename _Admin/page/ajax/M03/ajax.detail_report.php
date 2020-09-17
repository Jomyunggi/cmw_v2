<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	$companyIdx = $M_FUNC->M_Filter(GET, 'companyIdx');
	$year = $M_FUNC->M_Filter(GET, 'year');
	$month = $M_FUNC->M_Filter(GET, 'month');

	@include_once COMMON_CLASS . "/class.vm.php";
	$M_VM = new M_VM;

	@include_once COMMON_CLASS . "/class.report.php";
	$M_REPORT = new M_REPORT;
	
	$result_array = array();
	$addWhere = " and ai.companyIdx = " . $companyIdx;

	//각 서비스에 대한 사용요금 가져오는 부분
	$where = " and r.dateYmd between ".$year.sprintf('%02d', $month)."01 and ".$year.sprintf('%02d', $month)."31 and r.companyIdx = ".$companyIdx;
	$service_usage = $M_REPORT->getService_PriceByCompanyIdx($where);
	
	//고객사별 서버대수 배열로 담기
	$company_serverNo = $M_REPORT->getServerCntByCompanyIdx($year.sprintf('%02d', $month), "group by ai.roleIdx", "roleIdx");

	$query = " select ai.companyIdx, s.idx, s.serviceName "
			." from AccountRole_Info ai "
			."		left join Service_Info s on ai.roleIdx = s.idx "
			." where ai.`level` = 8 "
			. $addWhere
			;
	$row = $db->getListSet($query);

	if($row->size() > 0){
		$NO = $row->size();
		for($i=0;$i<$row->size();$i++){
			$row->next();

			if($company_serverNo[$row->get('idx')] > 0){
				$Cnt = $company_serverNo[$row->get('idx')];
			} else {
				$Cnt = 0;
			}
			
			$result_array[$NO]['num']			= $NO;
			$result_array[$NO]['serviceName']	= $row->get('serviceName');
			$result_array[$NO]['total_price']	= number_format($service_usage[$row->get('idx')]);
			$result_array[$NO--]['cnt']			= $Cnt;
		}
	}

	echo json_encode($result_array);
?>