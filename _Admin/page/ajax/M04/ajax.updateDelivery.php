<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	$dIdx			= $M_FUNC->M_Filter(POST, "dIdx");
	$hRevenue		= $M_FUNC->M_Filter(POST, "hRevenue");

	$data = array(
		'revenue_hope'	=>	$hRevenue
	);

	$db->update("Delivery_Info", $data, " WHERE idx = ". $dIdx);

	echo "success";
?>