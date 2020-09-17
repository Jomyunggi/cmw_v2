<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	$serviceIdx = $M_FUNC->M_Filter(GET, 'serviceIdx');
	$year = $M_FUNC->M_Filter(GET, 'year');
	$month = $M_FUNC->M_Filter(GET, 'month');
	$startdate = $year.sprintf('%02d', $month)."01";
	$enddate = $year.sprintf('%02d', $month)."31";


	include_once COMMON_CLASS . "/class.billing.php";
	$M_BILLING = new M_BILLING;

	$result_array = array();
	
	$addWhere = " and dateYmd between ".$startdate." and ".$enddate." and serviceIdx = ".$serviceIdx;
	$serverCntRow = $M_BILLING->getVMDataServerCntByserviceIdx($addWhere);
	
	if($serverCntRow->size() > 0){
		for($i=0; $i<$serverCntRow->size(); $i++){
			$serverCntRow->next();
			
			$dateYmd = substr($serverCntRow->get('dateYmd'), 6, 2);
			
			$result_array[$i]['dateYmd']	= intval($dateYmd);
			$result_array[$i]['serverCnt']	= $serverCntRow->get('serverCnt');
		}
	}

	echo json_encode($result_array);

?>