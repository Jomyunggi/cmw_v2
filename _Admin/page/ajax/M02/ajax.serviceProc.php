<?php
@include_once "Inc/inc.include.php";

include_once COMMON_CLASS . "/class.service.php";
$M_SERVICE = new M_SERVICE;

include_once COMMON_CLASS . "/class.account.php";
$M_ACCOUNT = new M_ACCOUNT;

$mode = $_REQUEST['mode'];
$UI_idx = $_REQUEST['UI_idx'];
$SI_idx = $_REQUEST['SI_idx'];

$pageCnt =  $M_FUNC->M_Filter(GET, 'pageCnt');
$page =  $M_FUNC->M_Filter(GET, 'page');
$list_cnt =  $M_FUNC->M_Filter(GET, 'list_cnt');
$searchType = $M_FUNC->M_Filter(GET, 'searchType');
$searchTxt = $M_FUNC->M_Filter(GET, 'searchTxt');
$serviceMode = $M_FUNC->M_Filter(GET, 'serviceMode');
$sType = $_REQUEST['sType'];

if($mode == 'service_list') {

	if($page < 1) $page = 1 ;
	if($list_cnt < 1) $list_cnt = 10 ;

	$start_row = ($page - 1) * $list_cnt;
	$addOrder = " ORDER BY s_regUnixtime DESC ";
	$addWhere = ' AND s.status = ' . $serviceMode . $sType;
	
	if($searchType == 5){
		if($searchTxt == "MARO" || $searchTxt == "maro"){
			$searchTxt = "1";
		}
		if($searchTxt == "SKB" || $searchTxt == "skb"){
			$searchTxt = "2";
		}
	}
	if($searchTxt != '' || $searchTxt != null) {
		switch($searchType) {
				case '1' : $addWhere .= ' AND userID LIKE \'%' . $searchTxt . '%\' '; break;
				case '2' : $addWhere .= ' AND userName LIKE \'%' . $searchTxt . '%\' '; break;
				case '3' : $addWhere .= ' AND hp LIKE \'%' . $searchTxt . '%\' '; break;
				case '4' : $addWhere .= ' AND companyName LIKE \'%' . $searchTxt . '%\' '; break;
				case '5' : $addWhere .= ' AND sType LIKE \'%' . $searchTxt . '%\' '; break;
			default :  break;
		}
	}
		
	$addLimit = " LIMIT " . $start_row .", ". $list_cnt;

	$result = array();
	$row = $M_SERVICE->getServiceAllList($addWhere, $addOrder, $addLimit);
	$totalRow = $M_SERVICE->getServiceAllList($addWhere);

	if($row->size()>0){
		
		for($i=0;$i<$row->size();$i++){
			$row->next();	
			$no = $totalRow->size() - $i - (($page - 1) * $list_cnt);

			$pName = $row->get('pName');
			$parent = $row->get('parent');

			if($parent > 0) {
				$productInfo = $M_SERVICE->getProductInfoByIdx($parent);
				$product_1th = $productInfo->get('pName');
				$product_2th = $row->get('pName');
			} else {
				$product_1th = $row->get('pName');
				$product_2th = '-';
			}

			if($row->get('companyName') == "") {
				$c_name = "-";
			} else {
				$c_name = $row->get('companyName');
			}
			
			$result[$no]['SI_idx'] = $row->get('SI_idx');
			$result[$no]['UI_idx'] = $row->get('UI_idx');
			$result[$no]['PI_idx'] = $row->get('PI_idx');
			$result[$no]['userID'] = $row->get('userID');
			$result[$no]['userName'] = $row->get('userName');
			$result[$no]['hp'] = $row->get('hp');
			$result[$no]['c_name'] = $c_name;
			$result[$no]['product_1th'] = $product_1th;
			$result[$no]['product_2th'] = $product_2th;
			$result[$no]['regUnixtime'] = $row->get("s_regUnixtime");
			$result[$no]['s_counseldate'] = $row->get("s_counseldate");
			$result[$no]['s_setdate'] = $row->get("s_setdate");
			$result[$no]['s_startdate'] = $row->get("s_startdate");
			$result[$no]['s_stopdate'] = $row->get("s_stopdate");
			$result[$no]['s_enddate'] = $row->get("s_enddate");
			$result[$no]['regDate'] = date('Y-m-d H:i:s', $row->get("s_regUnixtime"));
			$result[$no]['counselDate'] = date('Y-m-d H:i:s', $row->get("s_counseldate"));
			$result[$no]['setDate'] = date('Y-m-d H:i:s', $row->get("s_setdate"));
			$result[$no]['startDate'] = date('Y-m-d H:i:s', $row->get("s_startdate"));
			$result[$no]['stopDate'] = date('Y-m-d H:i:s', $row->get("s_stopdate"));
			$result[$no]['endDate'] = date('Y-m-d H:i:s', $row->get("s_enddate"));
			$result[$no]['sType'] = $_SESSION['sType'];
			$result[$no]['sTypeStatus'] = $M_ACCOUNT->User_type[$row->get('sType')];
			$result[$no]['total_page'] = $totalRow->size()/$list_cnt;
		}
		
	}

	echo json_encode($result);

} else if($mode == 'service_list2') {

	$result = array();

	if($UI_idx <= 0) {
		echo json_encode($result);
		exit();
	}

	$addWhere = ' AND u.idx = ' . $UI_idx . $sType;
	$row = $M_SERVICE->getServiceAllList($addWhere);

	for($i=0;$i<$row->size();$i++){
		$row->next();	

		$regUnixtime = date('Y-m-d H:i',$row->get("s_regUnixtime"));

		$pName = $row->get('pName');
		$parent = $row->get('parent');
		$status = $row->get('s_status');

		if($parent > 0) {
			$productInfo = $M_SERVICE->getProductInfoByIdx($parent);
			$product_1th = $productInfo->get('pName');
			$product_2th = $row->get('pName');
		} else {
			$product_1th = $row->get('pName');
			$product_2th = '-';
		}

		$sub_result = array(
			'SI_idx' => $row->get('SI_idx'),
			'UI_idx' => $row->get('UI_idx'),
			'PI_idx' => $row->get('PI_idx'),
			'status' => $row->get('s_status'),
			'userID' => $row->get('userID'),
			'userName' => $row->get('userName'),
			'hp' => $row->get('hp'),
			'detail' => $row->get('detail'),
			'product_1th' => $product_1th,
			'product_2th' => $product_2th,
			'regUnixtime' => $row->get('s_regUnixtime'),
			's_counseldate' => $row->get('s_counseldate'),
			's_setdate' => $row->get('s_setdate'),
			's_startdate' => $row->get('s_startdate'),
			's_stopdate' => $row->get('s_stopdate'),
			's_enddate' => $row->get('s_enddate'),
			'regDate' => date('Y-m-d H:i:s', $row->get("s_regUnixtime")),
			'counselDate' => date('Y-m-d H:i:s', $row->get("s_counseldate")),
			'setDate' => date('Y-m-d H:i:s', $row->get("s_setdate")),
			'startDate' => date('Y-m-d H:i:s', $row->get("s_startdate")),
			'stopDate' => date('Y-m-d H:i:s', $row->get("s_stopdate")),
			'endDate' => date('Y-m-d H:i:s', $row->get("s_enddate"))
		);

		if(!isset($result[$status])) {
			$result[$status] = array();
		}

		array_push($result[$status], $sub_result);
	}

	echo json_encode($result);

} else if($mode == 'counsel_list') {

	$result = array();

	if($SI_idx <= 0) {
		echo json_encode($result);
		exit();
	}

	$addWhere = ' AND SI_idx = ' . $SI_idx;
	$row = $M_SERVICE->getCounselList($addWhere);

	if($row->size()>0){
		
		for($i=0;$i<$row->size();$i++){
			$row->next();

			$userInfo = $M_ACCOUNT->getUserInfoBym_id($row->get('UI_idx'));		
			$result[$i]['SI_idx'] = $row->get('SI_idx');
			$result[$i]['UI_idx'] = $row->get('UI_idx');
			$result[$i]['userID'] = $userInfo->get('userID');
			$result[$i]['comment'] = htmlspecialchars_decode($row->get('comment'));
			$result[$i]['status'] = $row->get('status');
			$result[$i]['status2'] = $M_SERVICE->service_status[$row->get('status')];
			$result[$i]['regUnixtime'] = $row->get('regUnixtime');
			$result[$i]['regDate'] = date('Y-m-d H:i:s', $row->get('regUnixtime'));
		}
	}

	echo json_encode($result);

} else if($mode == 'counsel_save') {
	$comment =  $M_FUNC->M_Filter(POST, 'comment');

	if($SI_idx <= 0) {
		echo json_encode($result);
		exit();
	}

	$serviceInfo = $M_SERVICE->getServiceInfoByIdx($SI_idx);
	if($serviceInfo->size() > 0) {
		$data = array(
			'UI_idx' =>  $_SESSION['U_idx'],
			'SI_idx' => $SI_idx,
			'comment' => addslashes($comment),
			'status' => $serviceInfo->get('status'),
			'regUnixtime' => time()
		);

		$addWhere = ' WHERE status <> 99 AND SI_idx = ' . $SI_idx;
		$totalCnt = $M_SERVICE->getCounselCount($addWhere);

		$M_SERVICE->insertComment($data);
		$M_ACCOUNT->insertLog($_SESSION['U_idx'], $pageURL, '상담 저장', $SI_idx,SERVICE_code);

		$result = $data;

		$result['comment'] = htmlspecialchars_decode($data['comment']);
		$result['status2'] = $M_SERVICE->service_status[$data['status']];
		$result['regDate'] = date('Y-m-d H:i:s', $data['regUnixtime']);
		$result['userID']  = $_SESSION['userID'];
		$result['totalCnt']  = $totalCnt;
		
		echo json_encode($result);
	} else {
		echo json_encode($result);
		exit();
	}

} else if($mode == 'service_save') {
	$status =  $M_FUNC->M_Filter(POST, 'status');

	if($SI_idx <= 0) {
		echo 'FAIL';
		exit();
	}

	$serviceInfo = $M_SERVICE->getServiceInfoByIdx($SI_idx);

	if($serviceInfo->size() > 0) {
		$data = array(
			'status' => $status
		);

		switch($status) {
			case '4' : 
				$data['s_setdate'] = time();
				break;
			case '8' : 
				$data['s_counseldate'] = time();
				break;
			case '16' : 
				if($serviceInfo->get('startdate') <= 0) {
					$data['startdate'] = date('Ymd');
				}
				$data['s_startdate'] = time();
				break;
			case '32' : 
				$data['s_stopdate'] = time();
				break;
			case '64' : 
				if($serviceInfo->get('enddate') <= 0) {
					$data['enddate'] = date('Ymd');
				}
				$data['s_enddate'] = time();
				break;
		}

		$where = ' WHERE idx = ' . $SI_idx;
		$M_SERVICE->updateService($data, $where);		
		$M_ACCOUNT->insertLog($_SESSION['U_idx'], $pageURL, $SI_idx . '번 서비스 상태 변경 :  ' . $M_SERVICE->service_status[$status] , $SI_idx,SERVICE_code);
		echo 'OK';
	} else {
		echo 'FAIL';
		exit();
	}

} else if($mode == 'setting_save') {

	if($SI_idx <= 0) {
		echo 'FAIL';
		exit();
	}

	$serviceInfo = $M_SERVICE->getServiceInfoByIdx($SI_idx);

	if($serviceInfo->size() > 0) {

		$consoleID =  $M_FUNC->M_Filter(POST, 'consoleID');
		$consolePW =  $M_FUNC->M_Filter(POST, 'consolePW');
		$startdate =  $M_FUNC->M_Filter(POST, 'startdate');
		$enddate =  $M_FUNC->M_Filter(POST, 'enddate');

		$data = array(
			'consoleID' => $consoleID,
			'consolePW' => $M_FUNC->encryptData($consolePW),
			'startdate' => $startdate,
			'enddate' => $enddate
		);

		$where = ' WHERE idx = ' . $SI_idx;
		$M_SERVICE->updateService($data, $where);

		$logMsg = $SI_idx . ' 번 서비스 세팅정보 변경 : ';
		foreach($data as $key=>$value) {
			$logMsg .= $key . ' : ' . $value . ' / ';
		}

		$M_ACCOUNT->insertLog($_SESSION['U_idx'], $pageURL, $logMsg , $SI_idx,SERVICE_code);

		echo 'OK';

	} else {
		echo 'FAIL';
		exit();
	}
}

exit();
?>