<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	$dIdx			= $M_FUNC->M_Filter(POST, "dIdx");
	$csPercent		= $M_FUNC->M_Filter(POST, "csPercent");

	if(!$csPercent) $csPercent = 0;

	$data = array(
		'csPercent'	=>	$csPercent
	);

	$db->update("Delivery_Info", $data, " WHERE idx = ".$dIdx);

	echo "success";
?>