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
$search_startdate = $M_FUNC->M_Filter(GET, 'search_startdate');
$search_enddate = $M_FUNC->M_Filter(GET, 'search_enddate');


$search_startdate_result = "";
$search_enddate_result = "";

if($search_startdate != "" && $search_enddate != ""){
	$search_startdate_1 = substr($search_startdate,0,4);
	$search_startdate_2 = substr($search_startdate,4,2);
	$search_startdate_3 = substr($search_startdate,6,2);
	$search_startdate_result = $search_startdate_1 ."-". $search_startdate_2 . "-" . $search_startdate_3;
	//print_r($search_startdate_4);exit;
	$search_enddate_1 = substr($search_enddate,0,4);
	$search_enddate_2 = substr($search_enddate,4,2);
	$search_enddate_3 = substr($search_enddate,6,2);
	$search_enddate_result = $search_enddate_1 ."-". $search_enddate_2 . "-" . $search_enddate_3;
}

$result_array = array();

// CI_idx : 대행사 (SKB 같은) ST_idx : 팀번호
$m_id = $_SESSION['U_idx'];
$level = $_SESSION['Level'];

if($page < 1) $page = 1 ;
if($list_cnt < 1) $list_cnt = 10 ;
$start_row = ($page - 1) * $list_cnt;

$addOrder = " ORDER BY regUnixtime DESC ";
$addLimit = " LIMIT " . $start_row .", ". $list_cnt;
if($mode === 'view') {

	$IS_TRANS = true;

	// $user_id = 'bang30416@gmail.com'; // 네트워크 사용량 있는 아이디 (테스트용)
	switch($t_id) {
		case 'S' : //당월
			$type =  $M_FUNC->M_Filter(GET, 'type');
			$serverName =  $M_FUNC->M_Filter(GET, 's');
			$viewmode = $M_FUNC->M_Filter(GET, 'view_mode');
			if($viewmode == "DISK"){
				$curl_url = 'http://222.239.97.28/api/getVolumesInfo.php?id=' . $user_id . "&viewmode=".$viewmode. "&t_id=".$t_id . "&s=".urlencode($serverName)." "; break;
			} else if($viewmode == "IP"){
				$curl_url = 'http://222.239.97.28/api/getIpAddressInfo.php?id=' . $user_id . "&t_id=".$t_id . " "; break;
			} else {
				$curl_url = 'http://222.239.97.28/api/getVMInstanceInfo.php?id=' . $user_id .'&t=' . $type ."&s=".$serverName ."&t_id=" . $t_id; break;
			}

		case 'D' : //전월
			$type =  $M_FUNC->M_Filter(GET, 'type');
			$serverName =  $M_FUNC->M_Filter(GET, 's');
			$viewmode = $M_FUNC->M_Filter(GET, 'view_mode');
			if($viewmode == "DISK"){
				$curl_url = 'http://222.239.97.28/api/getVolumesInfo.php?id=' . $user_id . "&viewmode=".$viewmode ."&t_id=". $t_id."&s=".urlencode($serverName)."&s_sd=".$search_startdate_result. "&s_ed=".$search_enddate_result . " "; break;
			} else if($viewmode == "IP"){
				$curl_url = 'http://222.239.97.28/api/getIpAddressInfo.php?id=' . $user_id . "&t_id=".$t_id . "&s_sd=".$search_startdate_result. "&s_ed=".$search_enddate_result . " "; break;
			} else {
				$curl_url = 'http://222.239.97.28/api/getVMInstanceInfo.php?id=' . $user_id .'&t=' . $type ."&s=".$serverName ."&t_id=" . $t_id. "&s_sd=".$search_startdate_result. "&s_ed=".$search_enddate_result; break;
			}
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