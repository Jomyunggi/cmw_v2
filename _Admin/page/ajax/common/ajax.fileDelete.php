<?php
@include_once 'Inc/inc.include.php';

include_once COMMON_CLASS . "/class.file.php";
$M_FILE = new M_FILE;

// 서버에 올라간 파일 삭제 후 DB에서 file 데이터 삭제할는 프로세스

// 서버에서 올라간 파일이 삭제되지 않았는데 DB 데이터가 사라지거나
// DB 데이터가 삭제되지 않았는데 파일이 삭제되면 안됨
// 예외처리를 잘 해주세요^-^

//1. db데이터에 들어간 파일 삭제,
//2. tmpData 들어가 데이터 삭제,
//3. 둘 중 하나가 삭제 안됐을 때 실패, 둘 다 삭제 됐을때 성공.

	$m_id = $M_FUNC->M_Filter(POST, "m_id");
	$MENU_ID = $M_FUNC->M_Filter(POST, "MENU_ID");
	$idx = $M_FUNC->M_Filter(POST, "idx");
	
	$getFileList = $M_FILE->getFileList($MENU_ID, $m_id);
	$getFileList->next();
	
	$file_name = $getFileList->get("fileName_conv");
	$output_dir = FILEPATH . '/tmpData/';
	$remove_file = $output_dir . $file_name;
	
	$FILE_HTML = "";
	
	if(file_exists($remove_file)) {
		if(unlink($remove_file)) {
			echo $FILE_HTML;
		} else {
			echo $FILE_HTML;
		}
	} else {
		echo $FILE_HTML;
	}

	$where = " where idx = ".$idx;
	$db->delete("File_Data", $where);

	$query = " SELECT * "
				. " FROM File_Data "
				. " WHERE  MENU_ID_idx = ". $m_id." and MENU_ID = '".$MENU_ID."'";
	$row = $db->getListSet($query);
			
	if($row->size() > 0){
		for($i=0; $i<$row->size(); $i++){
			$row->next();
			//$FILE_HTML .= $M_HTML->input_Checkbox("delFileIdx[]", $FileRow->get("idx"), "", "", "");
			$FILE_HTML .= $i + 1 . "번 : "; //"번 : ";
			$FILE_HTML .= "". $row->get("fileName_origin") . "";
			$FILE_HTML .= "&nbsp;&nbsp;<a href='javascript:file_delete(". $row->get('idx') .", ". $m_id .", \"".$MENU_ID."\")'>";
			$FILE_HTML .= "<img src='".IMG_DIR."/ico_d.png' style='vertical-align:middle;'></a>";

			if ($i < $row->size() - 1) { $FILE_HTML .= " <br> "; }
		}
		$FILE_HTML .= " &nbsp;&nbsp; / x버튼을 누르면 삭제됩니다.";
	}

	echo $FILE_HTML;
?>