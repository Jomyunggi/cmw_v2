<?
	@include_once "../../common/setCommonPath.php";
	include_once 'Inc/inc.include.php';
	include_once COMMON_CLASS . "/class.account.php";
	$M_ACCOUNT = new M_ACCOUNT;

	$userID = $_POST['userID'];
	
	$row = $M_ACCOUNT->check_ID($userID);
	if($row->size() > 0){	
		echo "fail";
	} else {
		echo "success";
	}
?>
