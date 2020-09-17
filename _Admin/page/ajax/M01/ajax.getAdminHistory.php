<?php
@include_once "Inc/inc.include.php";

@include_once COMMON_CLASS . "/class.board.php";
$M_BOARD = new M_BOARD;

@include_once COMMON_CLASS . "/class.account.php";
$M_ACCOUNT = new M_ACCOUNT;

$mode = $_REQUEST["mode"];
$idx = $_POST['idx'];
$Q_UI_idx = $M_FUNC->M_Filter(GET, 'Q_UI_idx');
$pageCnt =  $M_FUNC->M_Filter(GET, 'pageCnt');

$title_value		= $_REQUEST["title_value"];
$searchTxt		= $_REQUEST["searchTxt"];
$sType			= $_REQUEST["sType"];
$fType_arr = $M_BOARD->fType_arr;
$QNA_status_arr = $M_BOARD->QNA_status;

if($mode == "work_history_list") {
	$page		= isset($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
	$list_cnt = isset($_REQUEST['list_cnt']) ? $_REQUEST["list_cnt"] : 10;
	if($page < 1) $page = 1 ;

	$start_row = ($page - 1) * $list_cnt;
	$addOrder = " ORDER BY regUnixtime DESC ";
	$addLimit = "";

	$addLimit .= " LIMIT " . $start_row .", ". $list_cnt;
	
	if($title_value == 1 && $searchTxt != ""){
		$addWhere .= " AND companyName LIKE '%" .$searchTxt. "%' ";
	}

	$getAdminHistoryInfo = $M_ACCOUNT->getAdminHistoryInfo($addWhere,$addOrder,$addLimit);
	$totalRow = $M_ACCOUNT->getAdminHistoryInfo($addWhere);
	
	$result_row = array();

	$pageNo = "";
	$pageName = "";

	if($getAdminHistoryInfo->size()>0){
		for($i=0;$i<$getAdminHistoryInfo->size();$i++){
			$getAdminHistoryInfo->next();
			$no = $totalRow->size() - $i - (($page - 1) * $list_cnt);

			if($getAdminHistoryInfo->get("regUnixtime") == 0) {
				$regUnixtime = "-";
			} else {
				$regUnixtime = date('Y-m-d H:i:s',$getAdminHistoryInfo->get("regUnixtime"));
			}

			switch($getAdminHistoryInfo->get("action")) 
			{
				case "P"	: $action = "등록";		break;
				case "U"	: $action = "수정";		break;
				case "D"	: $action = "삭제";		break;
				case "L"	: $action = "로그인";	break;
				case "E"	: $action = "기타";		break;
			}

			switch($getAdminHistoryInfo->get("MENU_ID"))
			{
				case "M0101" : $MENU_ID = "계정관리"; break;
				case "M0102" : $MENU_ID = "영업대행사관리"; break;
				case "M0103" : $MENU_ID = "SalesTeam 관리"; break;
				case "M0104" : $MENU_ID = "Sales 관리"; break;
			}
			
			$num = ($i + 1) + ($list_cnt * ($page-1));
			
			$result_row[$no]['num'] = $num;
			$result_row[$no]['idx'] = $getAdminHistoryInfo->get('idx');
			$result_row[$no]['account_ID'] = $getAdminHistoryInfo->get('account_ID');
			$result_row[$no]['MENU_ID_STR'] = $MENU_ID;
			$result_row[$no]['MENU_ID'] = $getAdminHistoryInfo->get("MENU_ID");
			$result_row[$no]['m_id'] = $getAdminHistoryInfo->get('m_id');
			$result_row[$no]['logContent'] = $getAdminHistoryInfo->get('logContent');
			$result_row[$no]['action'] = $action;
			$result_row[$no]['regUnixtime'] = $regUnixtime;
			$result_row[$no]['total_page'] = $totalRow->size()/$list_cnt;
		}
	}
	echo json_encode($result_row);
}

exit();
?>
