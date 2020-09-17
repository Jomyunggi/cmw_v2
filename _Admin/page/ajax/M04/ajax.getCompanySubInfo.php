<?php
	@include_once "Inc/inc.include.php";

	@include_once COMMON_CLASS . "/class.account.php";
	$M_ACCOUNT = new M_ACCOUNT;

	$CI_idx =  $M_FUNC->M_Filter(GET, 'CI_idx');
	$result_array = array();

	if($CI_idx > 0){
		$where = " AND status <> 99 AND CI_idx = " .$CI_idx;
		if($_SESSION['Level'] == 4){
			$where .= ' AND idx = '. $_SESSION['ST_idx'];	
		} 

		$teamRow = $M_ACCOUNT->getSalesTeamInfo($where);

		

		for($i=0; $i<$teamRow->size(); $i++){
			$teamRow->next();

			$result_array[$i]['idx'] = $teamRow->get('idx');
			$result_array[$i]['teamName'] = $teamRow->get('teamName');
		}
	}
	
	echo json_encode($result_array);

	exit();
?>