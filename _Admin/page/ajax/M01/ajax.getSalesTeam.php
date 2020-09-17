<?php
	@include_once "Inc/inc.include.php";

	@include_once COMMON_CLASS . "/class.board.php";
	$M_BOARD = new M_BOARD;

	@include_once COMMON_CLASS . "/class.account.php";
	$M_ACCOUNT = new M_ACCOUNT;
	
	$CI_idx		=  $M_FUNC->M_Filter(GET, 'CI_idx');
	$searchTxt	= $M_FUNC->M_Filter(GET, 'searchTxt');
	$mode = $_REQUEST['mode'];

	if($mode == "salesTeam_list"){
		
		$page		= isset($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
		$list_cnt = isset($_REQUEST['list_cnt']) ? $_REQUEST["list_cnt"] : 10;
		if($page < 1) $page = 1 ;
		
		$start_row = ($page - 1) * $list_cnt;
		$addOrder = " ORDER BY S.regUnixtime DESC ";
		$addWhere = "";

		$addLimit = " LIMIT " . $start_row .", ". $list_cnt;
		
		if(($CI_idx != 0 || $CI_idx != "") && $searchTxt != ""){
			$addWhere .= " AND CI_idx = " . $CI_idx . " AND teamName LIKE '%" .$searchTxt. "%' ";
		}
		if($CI_idx == "" && $searchTxt != ""){
			$addWhere .= " AND teamName LIKE '%" .$searchTxt. "%' ";
		}
		if($CI_idx > 0 && $searchTxt == ""){
			$addWhere .= " AND CI_idx = " . $CI_idx;
		}

		
		$salesTeamRow = $M_ACCOUNT->getSalesTeamList($cnt, $addWhere,$addOrder,$addLimit);

		$result_row = array();
		if($salesTeamRow->size() > 0){
			for($i=0;$i<$salesTeamRow->size();$i++){
				$salesTeamRow->next();
				$no = $cnt - $i - (($page - 1) * $list_cnt);

				if($salesTeamRow->get("regUnixtime") == 0) {
					$regUnixtime = "-";
				} else {
					$regUnixtime = date('Y-m-d H:i:s',$salesTeamRow->get("regUnixtime"));
				}
				
				$num = ($i + 1) + ($list_cnt * ($page-1));
				
				$result_row[$no]['num'] = $num;
				$result_row[$no]['idx'] = $salesTeamRow->get('idx');
				$result_row[$no]['CI_idx'] = $salesTeamRow->get('CI_idx');
				$result_row[$no]['companyName'] = $salesTeamRow->get("companyName");
				$result_row[$no]['teamName'] = $salesTeamRow->get("teamName");
				$result_row[$no]['regUnixtime'] = $regUnixtime;
				$result_row[$no]['total_page'] = $cnt/$list_cnt;
				/*
				$result_row[$no]['idx'] = $salesTeamRow->get('idx');
				$result_row[$no]['userID'] = $salesTeamRow->get("userID");
				$result_row[$no]['userName'] = $salesTeamRow->get("userName");
				$result_row[$no]['regUnixtime'] = $regUnixtime;
				$result_row[$no]['total_page'] = $cnt/$list_cnt;
				*/
			}
		}
		echo json_encode($result_row);
	} else if($mode == "team_delete"){
		$idx_arr = $_POST['idx'];

		for($i=0; $i<count($idx_arr); $i++){
			$M_ACCOUNT->deleteSalesTeamDataByIdx($idx_arr[$i]);
		}
		echo "success";
	}

	exit();
?>