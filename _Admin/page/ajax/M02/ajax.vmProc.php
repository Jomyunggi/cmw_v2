<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	$mode = $M_FUNC->M_Filter(POST, "mode");
	$status = $M_FUNC->M_Filter(POST, "status");
	$accountIdx = $M_FUNC->M_Filter(POST, "accountIdx");
	$vm_Arr = $_POST['idx'];
	$MENU_ID = $M_FUNC->M_Filter(POST, "MENU_ID");

	if($MENU_ID == "M0201" && $mode == "delete"){
		$where = " WHERE idx in ( ". implode(",", $vm_Arr) .") ";

		$data = array(
			'status'	=> $status,
			'v_endDate'	=> date('Ymd', time()),
		);

		$db->update("VM_Info", $data, $where);

		echo "success";
	} elseif($MENU_ID == "M0201" && $mode == "stop") {
		$where = " WHERE idx in ( ". implode(",", $vm_Arr) .") ";

		$data = array(
			'status'	=> $status,
			'v_endDate'	=> date('Ymd', time()),
		);

		$db->update("VM_Info", $data, $where);

		echo "success";
	} else {
		$where = " WHERE idx in ( ". implode(",", $vm_Arr) .") ";

		$data = array(
			'status'	=> $status,
			'v_endDate'	=> 0,
		);

		$db->update("VM_Info", $data, $where);

		echo "success";
	}
?>