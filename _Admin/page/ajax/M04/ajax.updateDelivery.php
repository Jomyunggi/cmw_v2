<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	$cIdx			= $M_FUNC->M_Filter(POST, "cIdx");
	$dIdx			= $M_FUNC->M_Filter(POST, "dIdx");
	$hRevenue		= $M_FUNC->M_Filter(POST, "hRevenue");

	$data = array(
		'revenue'	=>	$hRevenue
	);

	$db->update("Revenue_Info", $data, " WHERE cIdx = ".$cIdx." AND dIdx = ". $dIdx);

	echo "success";
?>