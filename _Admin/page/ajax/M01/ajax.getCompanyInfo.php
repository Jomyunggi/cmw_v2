<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	@include_once COMMON_CLASS . "/class.account.php";
	$M_ACCOUNT = new M_ACCOUNT;

	$businessIdx = $M_FUNC->M_Filter(POST, "businessIdx");

	$companyRow = $M_ACCOUNT->getCompanyInfo(" AND businessIdx = ".$businessIdx);

	$result_row = array();
	if($companyRow->size() > 0){
		for($i=0; $i<$companyRow->size(); $i++){
			$companyRow->next();
			
			$result_row[$i]['idx'] = $companyRow->get('idx');
			$result_row[$i]['companyName'] = $companyRow->get('companyName');
		}
	}

	echo json_encode($result_row);
	
	exit();
?>
