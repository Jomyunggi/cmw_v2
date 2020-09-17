<?php
@include_once "../../common/setCommonPath.php";
@include_once "Inc/inc.include.php";

@include_once COMMON_CLASS . "/class.manager.php";
$M_MANAGER = new M_MANAGER;

$mode = $_POST["mode"];

if($mode == "delete") {
	$idxArr = $_POST['idx'];
	
	for($i=0; $i<count($idxArr); $i++){
		$data = array('status' => 9);
		$where = " WHERE idx = ".$idxArr[$i];

		$db->update("Company_Info", $data, $where);
	}

	echo "success";
}

exit();
?>
