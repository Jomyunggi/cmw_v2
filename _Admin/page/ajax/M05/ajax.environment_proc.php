<?php
@include_once "Inc/inc.include.php";

include_once COMMON_CLASS . "/class.account.php";
$M_ACCOUNT = new M_ACCOUNT;

$mode = $_REQUEST["mode"];
$idx = $_REQUEST['idx'];
$uType_value		= $_REQUEST["uType_value"];
$searchTxt		= $_REQUEST["searchTxt"];
$sType	= $_REQUEST['sType'];

if($mode == "environment_list") {
	$page		= isset($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
	$list_cnt = isset($_REQUEST['list_cnt']) ? $_REQUEST["list_cnt"] : 10;
	if($page < 1) $page = 1 ;

	$start_row = ($page - 1) * $list_cnt;
	$addOrder = " ORDER BY regUnixtime DESC ";
	$addWhere = " AND status = 1 AND level = 9 " . $sType;
	if($uType_value == 3){
		if($searchTxt == "MARO" || $searchTxt == "maro"){
			$searchTxt = "1";
		}
		if($searchTxt == "SKB" || $searchTxt == "skb"){
			$searchTxt = "2";
		}
	}
	if($uType_value == 1 && $searchTxt != ""){
		$addWhere .= " AND userID LIKE '%" .$searchTxt. "%'";
	} elseif($uType_value == 2 && $searchTxt != ""){
		$addWhere .= " AND userName LIKE '%" .$searchTxt. "%'";
	} elseif($uType_value == 3 && $searchTxt != ""){
		$addWhere .= " AND sType LIKE '%" .$searchTxt. "%'";
	}

	$addLimit .= " LIMIT " . $start_row .", ". $list_cnt;
	$getUserInfo = $M_ACCOUNT->getUserInfo($addWhere,$addOrder,$addLimit);
	$total_row = $M_ACCOUNT->getUserInfo($addWhere,"","");

	$result_row = array();
	if($getUserInfo->size()>0){
		for($i=0;$i<$getUserInfo->size();$i++){
			$getUserInfo->next();
			$regUnixtime = date('Y-m-d H:i',$getUserInfo->get("regUnixtime"));		
			$no = $total_row->size() - $i - (($page - 1) * $list_cnt);
			$result_row[$no]['idx'] = $getUserInfo->get('idx');
			$result_row[$no]['userID'] = $getUserInfo->get('userID');
			$result_row[$no]['userName'] = $getUserInfo->get('userName');
			$result_row[$no]['userPW'] = $getUserInfo->get('userPW');
			$result_row[$no]['regUnixtime'] = $regUnixtime;
			$result_row[$no]['sType'] = $_SESSION['sType'];
			$result_row[$no]['sTypeStatus'] = $M_ACCOUNT->User_type[$getUserInfo->get('sType')];
			$result_row[$no]['total_page'] = $total_row->size()/$list_cnt;
		}
	}
	echo json_encode($result_row);
} 

else if($mode == "environment_delete"){
	$idx_arr = $_POST['idx'];

	$idx_array = array();
	
	for($i=0; $i<count($idx_arr); $i++){
		$M_ACCOUNT->deleteAccountDataByIdx($idx_arr[$i]);
	}

	echo "success";
}
else if($mode == "environment_pass"){
	$u_idx = $_POST["u_idx"];
	if($M_ACCOUNT->check_PW($u_idx, $_POST["org_pw"]) == false) {
		echo "noMatchPw";
		exit;
	}
	
	$addWhere = " AND idx = ".$u_idx." ";
	$user_info=$M_ACCOUNT->getUserInfo($addWhere,"","");
	$user_info->next();
	if($user_info->get('sType')==1){
		$MENU_ID=ADMIN_code;
	} else {
		$MENU_ID=ADMINSKB_code;
	}

	$M_ACCOUNT->update_pw($u_idx, $_POST["new_pw"]);
	$M_ACCOUNT->insertLog($_SESSION['U_idx'], $pageURL, $u_idx.'번 관리자 유저 비밀번호 변경 ', $u_idx ,$MENU_ID);

	echo "success";
}
?>