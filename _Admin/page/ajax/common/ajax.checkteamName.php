<?
	include_once 'Inc/inc.include.php';
	include_once COMMON_CLASS . "/class.account.php";
	$M_ACCOUNT = new M_ACCOUNT;

	$teamName = $_POST['teamName'];
	
	$row = $M_ACCOUNT->check_teamName($teamName);
	if($row->size() > 0){	
		echo "fail";
	} else {
		echo "success";
	}
?>
