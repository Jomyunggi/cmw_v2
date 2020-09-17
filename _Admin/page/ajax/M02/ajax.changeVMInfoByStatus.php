<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	@include_once ADMIN_CLASS_PATH . '/class.calculatePage.php';
	$M_Calculate = new M_CalculatePage;

	$m_id = $M_FUNC->M_Filter(GET, 'm_id');
	$status = $M_FUNC->M_Filter(GET, 'status');
	$startdate = $M_FUNC->M_Filter(GET, 'startdate');
	$enddate = $M_FUNC->M_Filter(GET, 'enddate');

	if($status == 4 || $status == 99 || $status == 9){
		if($startdate < $enddate){
			//VM 중지시 종료일 이후로 쌍힌 데이터 지우기
			$M_Calculate->changeVMInfoByStatus($m_id, $enddate);
			$M_FUNC->recordActionLog("S", "M0201", $m_id, "VM 상태 변경으로 인한 재정산");

			$update_query = "update VM_Info set reCalcStatus = reCalcStatus + 1 where idx = ". $m_id;
			$db->execute($update_query);
		}
	}
	echo "success";
?>