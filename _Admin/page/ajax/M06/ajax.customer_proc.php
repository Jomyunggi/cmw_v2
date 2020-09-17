<?php

@include_once "Inc/inc.include.php";

@include_once COMMON_CLASS . "/class.board.php";
$M_BOARD = new M_BOARD;

@include_once COMMON_CLASS . "/class.account.php";
$M_ACCOUNT = new M_ACCOUNT;

$mode =  $M_FUNC->M_Filter(GET, 'mode');
$user_id =  $M_FUNC->M_Filter(GET, 'user_id');
$t_id =  $M_FUNC->M_Filter(GET, 't_id');
$pageCnt =  $M_FUNC->M_Filter(GET, 'pageCnt');
$CI_idx =  $M_FUNC->M_Filter(GET, 'CI_idx');
$ST_idx =  $M_FUNC->M_Filter(GET, 'ST_idx');
$search_userID = $M_FUNC->M_Filter(GET, 'search_userID');
$list_cnt = $M_FUNC->M_Filter(GET, 'list_cnt');
$page = $M_FUNC->M_Filter(GET, 'page');
$goods_value = $M_FUNC->M_Filter(GET, 'sn_id');
$a_idx = $M_FUNC->M_Filter(GET, 'a_idx');
$s_id = $M_FUNC->M_Filter(GET, 's_id');

$result_array = array();

// CI_idx : 대행사 (SKB 같은) ST_idx : 팀번호
$m_id = $_SESSION['U_idx'];
$level = $_SESSION['Level'];

if($page < 1) $page = 1 ;
if($list_cnt < 1) $list_cnt = 10 ;
$start_row = ($page - 1) * $list_cnt;

$addOrder = " ORDER BY regUnixtime DESC ";
$addLimit = " LIMIT " . $start_row .", ". $list_cnt;

if($mode == "list"){
	$account_info = array();
	$account_arr = array();
	$addWhere = "";




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


	$row = $M_ACCOUNT->getAccountMatchingInfo($addWhere, $addOrder, $addLimit, $cnt);
	for($i=0;$i<$row->size();$i++) {
		$row->next();
		$api_userID = $row->get('api_userID');
		$account_idx = $row->getInt('account_idx');
		$a_idx = $row->get('idx');

		$curl_url = 'http://222.239.97.28/api/getAccountInfo.php?id=' . $api_userID;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $curl_url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	// 문자열출력
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		$result = curl_exec($ch);
		curl_close($ch);

		$num = ($i + 1) + ($list_cnt * ($page-1));
		
		$goods_array = $M_ACCOUNT->goods_array;
		//ksort($goods_array);
		$service_array = $M_ACCOUNT->service_array;
		/*if($goods_value == "" || $goods_value == null){
			$service_array[0] = "서비스선택";
			unset($service_array[1]);
			unset($service_array[2]);
			unset($service_array[4]);
			unset($service_array[8]);
			unset($service_array[16]);
			unset($service_array[32]);
			unset($goods_array[0]);
		}

		if($a_idx != $M_FUNC->M_Filter(GET, 'a_idx')) {
			$goods_array[0] = "상품선택";
			unset($goods_value);
		}
		*/
		$tmp = json_decode($result);
		$tmp[0]->salesId = $account_info[$account_idx];
		$tmp[0]->salesIdx = $account_idx;
		$tmp[0]->total_page = $cnt;
		$tmp[0]->num = $num;
		$tmp[0]->goods_list = $M_HTML->_Select("goods_type_".$a_idx, $goods_array , $goods_value , "onchange=\"javascript:getServiceName(this.value,'".$page."','".$a_idx."');\"", "");
		$tmp[0]->service_list =  $M_HTML->_Select("service_type_".$a_idx, $service_array , $service_value , "onchange=\"javascript:getServiceInfo(this.value,'".$api_userID."');\"", "width:100px;");
		array_push($result_array, $tmp);
	}
} else if($mode == "view"){
	
	$IS_TRANS = true;

	// $user_id = 'bang30416@gmail.com'; // 네트워크 사용량 있는 아이디 (테스트용)
	switch($s_id) {
		case '1' : 
			$type =  $M_FUNC->M_Filter(GET, 'type');
			$serverName =  $M_FUNC->M_Filter(GET, 's');
			$curl_url = 'http://222.239.97.28/api/getVMInstanceInfo.php?id=' . $user_id; break;
		case '2' : 
			$type =  $M_FUNC->M_Filter(GET, 'type');
			$curl_url = 'http://222.239.97.28/api/getVolumesInfo.php?id=' . $user_id; break;
		case '4' : $curl_url = 'http://222.239.97.28/api/getNetworkInfo.php?id=' . $user_id; break; 
		case '8' : $curl_url = 'http://222.239.97.28/api/getResourceInfo.php?id=' . $user_id; break;
		case '16' : 
			$nName =  $M_FUNC->M_Filter(GET, 'nName');
			$curl_url = 'http://222.239.97.28/api/getPriceInfo.php?id=' . $user_id; break;
		case '32' : 
			$nId	= $M_FUNC->M_Filter(GET, 'nId');
			$curl_url = 'http://222.239.97.28/api/getPriceInfo.php?t=last&id=' . $user_id; break;
		default : $IS_TRANS = false;
	}

	if($IS_TRANS) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $curl_url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	// 문자열출력
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		$result = curl_exec($ch);
		curl_close($ch);

		if(!empty($result)) array_push($result_array, json_decode($result));
	}
}

echo json_encode($result_array);

exit();

?>