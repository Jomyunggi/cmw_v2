<?php
@include_once "Inc/inc.include.php";

include_once COMMON_CLASS . "/class.board.php";
$M_BOARD = new M_BOARD;

include_once COMMON_CLASS . "/class.account.php";
$M_ACCOUNT = new M_ACCOUNT;

include_once COMMON_CLASS . "/class.file.php";
$M_FILE = new M_FILE;

$mode = $_REQUEST["mode"];
$idx = $_POST['idx'];
$searchTxt		= $_REQUEST["searchTxt"];
$sType	= $_REQUEST['sType'];

if($mode == "notice_list") {
	$page = isset($_POST['page']) ? $_POST["page"] : 1;
	$list_cnt = isset($_POST['list_cnt']) ? $_POST["list_cnt"] : 10;
	if($page < 1) $page = 1 ;
	
	$start_row = ($page - 1) * $list_cnt;
	$addOrder = " ORDER BY regUnixtime DESC ";
	$addWhere = $sType;
	$addLimit = "";

	if($searchTxt != "" || $searchTxt != 0){
		$addWhere = " and subject LIKE '%". $searchTxt ."%'";
	}
	$addLimit .= " LIMIT " . $start_row .", ". $list_cnt;
	$getNoticeInfoRow = $M_BOARD->getNoticeInfoRow($addWhere,$addOrder,$addLimit);
	$totalRow = $M_BOARD->getNoticeInfoRow($addWhere);
	$result_row = array();
	if($getNoticeInfoRow->size()>0){
		for($i=0;$i<$getNoticeInfoRow->size();$i++){
			$getNoticeInfoRow->next();
			$no = $totalRow->size() - $i - (($page - 1) * $list_cnt);
			$regUnixtime = date('Y-m-d H:i',$getNoticeInfoRow->get("regUnixtime"));		
			$result_row[$no]['idx'] = $getNoticeInfoRow->get('idx');
			$result_row[$no]['subject'] = $getNoticeInfoRow->get('subject');
			$result_row[$no]['regUnixtime'] = $regUnixtime;
			$result_row[$no]['sType'] = $_SESSION['sType'];
			$result_row[$no]['sTypeStatus'] = $M_ACCOUNT->User_type[$getNoticeInfoRow->get('sType')];
			$result_row[$no]['total_page'] = $totalRow->size()/$list_cnt;
		}
	}
	echo json_encode($result_row);

} else if($mode == "notice_view"){
	$getNoticeInfoView = $M_BOARD->getNoticeInfoView($idx);
	$MENU_ID = $_POST['MENU_ID'];

	$result_view = array();
	$fileName_origin = "";

	if($getNoticeInfoView->size()>0){
		$getNoticeInfoView->next();
		if($getNoticeInfoView->get('sType') == 1){
			$MENU_ID = Notice_code;
		} else {
			$MENU_ID = NoticeSKB_code;
		}
		$getFileRowVeiw = $M_FILE->getFileList($MENU_ID, $idx);
		for($f=0; $f<$getFileRowVeiw->size(); $f++){
			$getFileRowVeiw->next();
			$fileName_origin .= "<a href = '/page/common/download.php?id=".$getFileRowVeiw->get('idx')."'>".$getFileRowVeiw->get('fileName_origin')."</a>";
			if($f < $getFileRowVeiw->size() - 1) { $fileName_origin .= " <br> "; }
		}

		$result_view['idx'] = $getFileRowVeiw->get("idx");
		$result_view['subject'] = $getNoticeInfoView->get("subject");
		$result_view['contents'] = $getNoticeInfoView->get("contents");
		$result_view['file'] = $fileName_origin;
	}
	echo json_encode($result_view);
} else if($mode == "notice_edit"){
	$getNoticeInfoEdit = $M_BOARD->getNoticeInfoView($idx);
	
	$result_edit = array();
	$file_data = "";
	if($getNoticeInfoEdit->size()>0){
		$getNoticeInfoEdit->next();
			if($getNoticeInfoEdit->get('sType')==1){
				$MENU_ID = Notice_code;
			} else {
				$MENU_ID = NoticeSKB_code;
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
			$result_edit['subject'] = $getNoticeInfoEdit->get("subject");
			$result_edit['contents'] = $getNoticeInfoEdit->get("contents");
			$result_edit['file_name'] = $file_data;
			$result_edit['sType'] = $getNoticeInfoEdit->get("sType");
	}

	echo json_encode($result_edit);
} 

else if($mode == "notice_list_paging"){

	
	echo json_encode($result_paging);
}

else if($mode == "notice_delete"){
	$idx_arr = $_POST['idx'];
	
	$idx_array = array();

	for($i=0; $i<count($idx_arr); $i++){
		$M_BOARD->deleteNoticeDataByIdx($idx_arr[$i]);
	}

	echo "success";
}

?>