<?php
@include_once "Inc/inc.include.php";

@include_once COMMON_CLASS . "/class.board.php";
$M_BOARD = new M_BOARD;

@include_once COMMON_CLASS . "/class.account.php";
$M_ACCOUNT = new M_ACCOUNT;

$t_id =  $M_FUNC->M_Filter(GET, 't_id');
$user_id =  $M_FUNC->M_Filter(GET, 'user_id');
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
$IS_TRANS = true;
switch($t_id) {
	case 'S' : $curl_url = 'http://222.239.97.28/api/getPriceInfo.php?id=' . $user_id; break;
	case 'D' : $curl_url = 'http://222.239.97.28/api/getPriceInfo.php?t=last&id=' . $user_id . '&s_sd=' . $search_startdate_result . '&s_ed=' . $search_enddate_result; break;
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

echo json_encode($result_array);

exit();
?>