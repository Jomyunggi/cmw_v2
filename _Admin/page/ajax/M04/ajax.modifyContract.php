<?php
	@include_once "../../common/setCommonPath.php";
	include_once 'Inc/inc.include.php';
	
	include_once COMMON_CLASS . "/class.billing.php";
	$M_BILLING = new M_BILLING;

	include_once COMMON_CLASS . "/class.vm.php";
	$M_VM = new M_VM;
	
	$modifyDate = $M_FUNC->M_Filter(POST, 'modifyDate'); 
	$contract = $M_FUNC->M_Filter(POST, 'contract');

	//대표적으로 VM_Info에서 약정이 몇년으로 잡혀있는지 확인 후 동일한 약정일 경우는 처리하지 않는다.
	$prior_contract = $M_VM->getVMInfoByContract();

	if($prior_contract->get('contract') == $contract){
		echo 'NO';
		exit;
	}
	
	$result = $M_BILLING->modifyContract($contract, $modifyDate);
	
	$M_FUNC->recordActionLog("S", $MENU_ID, '', date('Y-m-d', strtotime($modifyDate))."일 ". $prior_contract->get('contract') ."년->". $contract ."년으로 약정 변경");

	echo $result;
	exit;
?>

