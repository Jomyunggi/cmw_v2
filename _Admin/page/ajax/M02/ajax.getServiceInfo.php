<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	@include_once COMMON_CLASS . "/class.account.php";
	$M_ACCOUNT = new M_ACCOUNT;

	$companyIdx = $M_FUNC->M_Filter(POST, "companyIdx");

	$accountRow = $M_ACCOUNT->getUserInfo(" AND status =1 AND level = 4 AND companyIdx = ".$companyIdx);
	
	$result_row = array();
	if($accountRow->size() > 0){
		for($i=0; $i<$accountRow->size(); $i++){
			$accountRow->next();
			
			$result_row[$i]['idx'] = $accountRow->get('idx');
			$result_row[$i]['accountName'] = $accountRow->get('accountName');
		}
	}

	echo json_encode($result_row);
	
	exit();
?>
