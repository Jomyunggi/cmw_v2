<?php
	@include_once "Inc/inc.include.php";
	
	@include_once COMMON_CLASS . "/class.board.php";
	$M_BOARD = new M_BOARD;

	@include_once COMMON_CLASS . "/class.account.php";
	$M_ACCOUNT = new M_ACCOUNT;
	
	$CI_idx		=  $M_FUNC->M_Filter(GET, 'CI_idx');
	$ST_idx		=  $M_FUNC->M_Filter(GET, 'ST_idx');
	$searchType	=  $M_FUNC->M_Filter(GET, 'searchType');
	$searchTxt	=  $M_FUNC->M_Filter(GET, 'searchTxt');
	$mode = $_REQUEST['mode'];

	if($mode == "salesTeam_list"){
		$page		= isset($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
		$list_cnt = isset($_REQUEST['list_cnt']) ? $_REQUEST["list_cnt"] : 10;
		if($page < 1) $page = 1 ;
		
		$start_row = ($page - 1) * $list_cnt;
		$addOrder = " ORDER BY level ";
		$addWhere = " AND level in (4,8) AND CI_idx = " . $CI_idx . " AND ST_idx = " . $ST_idx;

		$addLimit = " LIMIT " . $start_row .", ". $list_cnt;
		
		if($searchTxt != ""){
			switch($searchType){
				case 1 : $addWhere .= " AND userID LIKE '%" .$searchTxt. "%' "; break;
				case 2 : $addWhere .= " AND userName LIKE '%" .$searchTxt. "%' "; break;
				default : $addWhere .= " AND ( userID LIKE '%" .$searchTxt. "%' OR userName LIKE '%" .$searchTxt. "%' ) "; break;
			}
		}

		//company정보, salesTeam정보 가져오기
		$where = " AND C.idx = ".$CI_idx." AND S.idx = ".$ST_idx;
		$company_salesTeamInfo = $M_ACCOUNT->getSalesTeamList($cnt, $where, "", "");
		$company_salesTeamInfo->next();

		$companyName = $company_salesTeamInfo->get("companyName");
		$teamName = $company_salesTeamInfo->get("teamName");

		$salesTeamRow = $M_ACCOUNT->getSalesTeam_UserList($cnt, $addWhere, $addOrder, $addLimit);

		$result_row = array();
		if($salesTeamRow->size() > 0){
			for($i=0; $i<$salesTeamRow->size(); $i++){
				$salesTeamRow->next();
				$no = $cnt - $i - (($page - 1) * $list_cnt);			
				
				if($salesTeamRow->get("regUnixtime") == 0) {
					$regUnixtime = "-";
				} else {
					$regUnixtime = date('Y-m-d H:i:s',$salesTeamRow->get("regUnixtime"));
				}

				if($salesTeamRow->get('level') == 4){
					$job = "팀장";
				} else {
					$job = "팀원";
				}

				$num = ($i + 1) + ($list_cnt * ($page-1));
				
				$result_row[$no]['num'] = $num;			
				$result_row[$no]['idx'] = $salesTeamRow->get('idx');
				$result_row[$no]['userID'] = $salesTeamRow->get("userID");
				$result_row[$no]['userName'] = $salesTeamRow->get("userName");
				$result_row[$no]['job'] = $job;
				$result_row[$no]['companyName'] = $companyName;
				$result_row[$no]['teamName'] = $teamName;
				$result_row[$no]['regUnixtime'] = $regUnixtime;
				$result_row[$no]['total_page'] = $cnt/$list_cnt;
			}
		}
		echo json_encode($result_row);
	} elseif($mode=="Sales_delete"){
		$idx_arr = $_POST['idx'];
		for($i=0; $i<count($idx_arr); $i++){
			$M_ACCOUNT->deleteAccountDataByIdx($idx_arr[$i]);
		}
		echo "success";
	}

	exit();
?>