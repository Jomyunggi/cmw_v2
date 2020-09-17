<?php
@include_once "Inc/inc.include.php";

include_once COMMON_CLASS . "/class.board.php";
$M_BOARD = new M_BOARD;

include_once COMMON_CLASS . "/class.file.php";
$M_FILE = new M_FILE;

include_once COMMON_CLASS . "/class.account.php";
$M_ACCOUNT = new M_ACCOUNT;

$mode = $_REQUEST["mode"];
$idx = $_REQUEST['idx'];
$fType_value		= $_REQUEST["fType_value"];
$searchTxt		= $_REQUEST["searchTxt"];
$sType	= $_REQUEST['sType'];

$fType_arr = $M_BOARD->fType_arr;
unset($fType_arr[0]);

if($mode == "faq_list") {
	$page		= isset($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
	$list_cnt = isset($_REQUEST['list_cnt']) ? $_REQUEST["list_cnt"] : 10;
	if($page < 1) $page = 1 ;

	$start_row = ($page - 1) * $list_cnt;
	$addOrder = " ORDER BY regUnixtime DESC ";
	$addWhere = $sType;
	
	if($fType_value != "" && $fType_value != 0){
		$addWhere .= " and fType LIKE '%" . $fType_value . "%'";
	}

	if($searchTxt != "" || $searchTxt != 0){
		$addWhere .= " and subject LIKE '%". $searchTxt ."%'";
	}

	$addLimit .= " LIMIT " . $start_row .", ". $list_cnt;
	$getFAQInfo = $M_BOARD->getFAQInfo($addWhere,$addOrder,$addLimit);
	$total_row = $M_BOARD->getFAQInfo($addWhere,"","");

	$result_row = array();
	if($getFAQInfo->size()>0){
		for($i=0;$i<$getFAQInfo->size();$i++){
			$getFAQInfo->next();
			$no = $total_row->size() - $i - (($page - 1) * $list_cnt);
			$result_row[$no]['idx'] = $getFAQInfo->get('idx');
			$result_row[$no]['fType'] = $fType_arr[$getFAQInfo->get('fType')];
			$result_row[$no]['subject'] = $getFAQInfo->get('subject');
			$result_row[$no]['sType'] = $_SESSION['sType'];
			$result_row[$no]['sTypeStatus'] = $M_ACCOUNT->User_type[$getFAQInfo->get('sType')];
			$result_row[$no]['total_page'] = $total_row->size()/$list_cnt;
		}
	}
	echo json_encode($result_row);
} else if($mode == "faq_view"){
	$MENU_ID = $_POST['MENU_ID'];
	$getFAQInfoByIdx = $M_BOARD->getFAQInfoByIdx($idx);
	
	$result_view = array();
	$fileName_origin = "";

	if($getFAQInfoByIdx->size()>0){
		if($getFAQInfoByIdx->get("sType")==1){
			$MENU_ID = FAQ_code;
		}else{
			$MENU_ID = FAQSKB_code;
		}
		$getFileRowVeiw = $M_FILE->getFileList($MENU_ID, $idx);
		for($f=0; $f<$getFileRowVeiw->size(); $f++){
			$getFileRowVeiw->next();
			$fileName_origin .= "<a href = '/page/common/download.php?id=".$getFileRowVeiw->get('idx')."'>".$getFileRowVeiw->get('fileName_origin')."</a>";
			if($f < $getFileRowVeiw->size() - 1) { $fileName_origin .= " <br> "; }
		}
		
		$result_view['idx'] = $getFileRowVeiw->get("idx");
		$result_view['subject'] = $getFAQInfoByIdx->get("subject");
		$result_view['contents'] = $getFAQInfoByIdx->get("contents");
		$result_view['fType'] = $fType_arr[$getFAQInfoByIdx->get("fType")];
		$result_view['file'] = $fileName_origin;
	}
	echo json_encode($result_view);
} else if($mode == "faq_edit"){
	$getFAQInfoEdit = $M_BOARD->getFAQInfoByIdx($idx);
	
	$result_edit = array();
	$file_data = "";
	if($getFAQInfoEdit->size()>0){
		if($getFAQInfoEdit->get("sType")==1){
			$MENU_ID = FAQ_code;
		}else{
			$MENU_ID = FAQSKB_code;
		}
	$getFileRowEdit = $M_FILE->getFileList($MENU_ID,$idx);
	if($getFileRowEdit->size()>0){
		for($j=0; $j<$getFileRowEdit->size(); $j++){
			$getFileRowEdit->next();
			$file_data .= $j + 1 . " 번 : ";
			$file_data .= "". $getFileRowEdit->get("fileName_origin") ."";
			$file_data .= "&nbsp;&nbsp;<a href='javascript:file_delete(".$getFileRowEdit->get('idx').",".$idx.", \"".$MENU_ID."\")'>";
			$file_data .= "<img src='".IMG_DIR."/ico_d.png' style='vertical-align:middle;'></a>";

			if ($j < $getFileRowEdit->size() - 1) { $file_data .= " <br> "; }
		}
		$file_data .= " &nbsp;&nbsp; / x버튼을 누르면 삭제됩니다.";
	}
			$result_edit['subject'] = $getFAQInfoEdit->get("subject");
			$result_edit['fType'] = $getFAQInfoEdit->get("fType");
			$result_edit['contents'] = $getFAQInfoEdit->get("contents");
			$result_edit['file_name'] = $file_data;
	}
	//print_r($result_edit);exit;
	echo json_encode($result_edit);
}

else if($mode == "faq_delete"){
	$idx_arr = $_POST['idx'];
	
	$idx_array = array();

	for($i=0; $i<count($idx_arr); $i++){
		$M_BOARD->deleteFaqDataByIdx($idx_arr[$i]);
	}
	echo "success";
}
?>