<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	$dIdx			= $M_FUNC->M_Filter(POST, "dIdx");
	$adPercent		= $M_FUNC->M_Filter(POST, "adPercent");

	$data = array(
		'adPercent'	=>	$adPercent
	);

	$db->update("Delivery_Info", $data, " WHERE idx = ".$dIdx);

	echo "success";
?>