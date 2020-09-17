<?php
@include_once "Inc/inc.include.php";

@include_once COMMON_CLASS . "/class.board.php";
$M_BOARD = new M_BOARD;

@include_once COMMON_CLASS . "/class.account.php";
$M_ACCOUNT = new M_ACCOUNT;

$t_id = $M_FUNC->M_Filter(GET, 't_id');
$search_userID = $M_FUNC->M_Filter(GET, 'search_userID');
$page =  $M_FUNC->M_Filter(GET, 'page');
$pageCnt =  $M_FUNC->M_Filter(GET, 'pageCnt');
$list_cnt =  $M_FUNC->M_Filter(GET, 'list_cnt');

$result_array = array();

// CI_idx : 대행사 (SKB 같은) ST_idx : 팀번호
$m_id = $_SESSION['U_idx'];
$level = $_SESSION['Level'];

$account_info = array();

if($page < 1) $page = 1 ;
if($list_cnt < 1) $list_cnt = 10 ;
$start_row = ($page - 1) * $list_cnt;

$addOrder = " ORDER BY regUnixtime DESC ";
$addLimit = " LIMIT " . $start_row .", ". $list_cnt;

switch($level) {
	case '1' : 
		$addWhere = '';
		$accountRow = $M_ACCOUNT->getUserInfo();
		for($i=0;$i<$accountRow->size();$i++) {
			$accountRow->next();
			$account_info[$accountRow->getInt('idx')] = $accountRow->get('userID');
		}
		break;
	case '2' :
		$userInfo = $M_ACCOUNT->getUserInfoByIdx($m_id);
		$CI_idx = $userInfo->getInt('CI_idx');
		$company_list = array();
		$accountRow = $M_ACCOUNT->getUserInfo(' AND CI_idx = ' . $CI_idx);
		for($i=0;$i<$accountRow->size();$i++) {
			$accountRow->next();
			array_push($company_list, $accountRow->getInt('idx'));
			$account_info[$accountRow->getInt('idx')] = $accountRow->get('userID');
		}
		if(count($company_list) > 0) $addWhere = ' AND account_idx IN (' . implode(', ', $company_list) . ') ';
		else $addWhere = ' AND 1 = 0 ';

		break;
	case '4' : 
		$userInfo = $M_ACCOUNT->getUserInfoByIdx($m_id);
		$ST_idx = $userInfo->getInt('ST_idx');
		$accountRow = $M_ACCOUNT->getUserInfo(' AND ST_idx = ' . $ST_idx);
		$team_list = array();
		for($i=0;$i<$accountRow->size();$i++) {
			$accountRow->next();
			array_push($team_list, $accountRow->getInt('idx'));
			$account_info[$accountRow->getInt('idx')] = $accountRow->get('userID');
		}
		if(count($team_list) > 0) $addWhere = ' AND account_idx IN (' . implode(', ', $team_list) . ') ';
		else $addWhere = ' AND 1 = 0 ';
		break;
	case '8' : 
		$addWhere = ' AND account_idx = ' . $m_id;
		$account_info[$m_id] = $_SESSION['userID'];
		break;
}

if($search_userID != ""){
	$addWhere .= " AND api_userID LIKE '%". $search_userID . "%' ";
}

$row = $M_ACCOUNT->getAccountMatchingInfo($addWhere, $addOrder, $addLimit, &$cnt);
for($i=0;$i<$row->size();$i++) {
	$row->next();
	$api_userID = $row->get('api_userID');
	$account_idx = $row->getInt('account_idx');

	$curl_url = 'http://222.239.97.28/api/getAccountInfo.php?id=' . $api_userID;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $curl_url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	// 문자열출력
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	$result = curl_exec($ch);
	curl_close($ch);


	$IS_TRANS = true;
	switch($t_id) {
		case 'S' : $curl_url2 = 'http://222.239.97.28/api/getPriceInfo.php?id=' . $api_userID; break;
		case 'D' : $curl_url2 = 'http://222.239.97.28/api/getPriceInfo.php?t=last&id=' . $api_userID; break;
		default : $IS_TRANS = false;
	}

	$amount = 0;
	$total_price = 0;
	$charge = 0;

	if($IS_TRANS) {
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_URL, $curl_url2);
		curl_setopt($ch2, CURLOPT_HEADER, false);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);	// 문자열출력
		curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, true);
		$result2 = curl_exec($ch2);
		curl_close($ch2);

		$t = json_decode($result2);
		if(count($t) > 0) {
			for($j=0;$j<count($t);$j++) {
				$total_price += floor($t[$j]->total_price);
				if($t[$j]->charge != ""){
					$charge = $t[$j]->charge;
				} else {
					$charge = 0;
				}
			}			
		} 
	}

	if($total_price > 0) {
		$vat = floor($total_price*0.1);
		$amount = $vat + $total_price;
	}

	$num = ($i + 1) + ($list_cnt * ($page-1));

	$tmp = json_decode($result);
	if(isset($account_info[$account_idx])) {
		$tmp[0]->salesId = $account_info[$account_idx];
		$tmp[0]->salesIdx = $account_idx;
	} else {
		$tmp[0]->salesId = '-';
		$tmp[0]->salesIdx = 0;
	}
	$tmp[0]->amount = $amount;
	$tmp[0]->charge = $charge;
	$tmp[0]->total_page = $cnt;
	$tmp[0]->num = $num;
	array_push($result_array, $tmp);	

}

echo json_encode($result_array);

exit();
?>