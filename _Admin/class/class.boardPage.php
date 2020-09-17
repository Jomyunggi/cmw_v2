<?php

if(!class_exists('M_BOARD')) {
	include_once COMMON_CLASS . '/class.board.php';
}

class M_BoardPage extends M_BOARD {
	// Constructor

	function __construct() {
		$this->searchType = array(
			'total'		=> '제목+내용',
			'subject'	=> '제목',
			'content'	=> '내용',
		);

		$this->statusType = array(
			1	=> "등록",
			4	=> "중지",
			11	=> "작업중",
			14	=> "보류",
			22	=> "완료",
			9	=> "삭제"
		);

		$this->itemLength1 = array(
			0	=> '1차 메뉴선택',
			9	=> '홈',
			1	=> '계정관리',
			2	=> '상품 단가표',
			3	=> '거래현황',
			4	=> '인수 & 배송',
			5	=> '세금',
			6	=> '게시판',
		);
		$this->itemLength2 = array(
			0	=> '2차 메뉴선택',
			99	=> '데쉬보드',
			11	=> '계정관리',
			21	=> '상품 단가표',
			31	=> '오프라인 매출',
			32	=> '오프라인 매입',
			33	=> '온라인 매출',
			34	=> '온라인 매입',
			41	=> '인수&인쇄',
			42	=> '배송현황',
			51	=> '오프라인',
			52	=> '온라인',
			61	=> '공지사항',
			62	=> 'Q&A',
			63	=> '작업내역',
			98=> '문자알림'
		);

		//게시판 Type 안내 = 1:작업내역, 2:공지사항

		parent::__construct();
	}

	// Destructor
	function __destruct() {

	}

	function boardList(){
		global $PAGE_PATH, $P_ACTION, $MENU_ID, $PAGE;
		global $M_HTML, $M_FUNC;
	
		$searchType = $M_FUNC->M_Filter(GET, 'searchType');
		$searchTxt = $M_FUNC->M_Filter(GET, 'searchTxt');
		
		$addWhere = " AND type = 1 ";
		switch($searchType){
			case 'total' : 
				if($searchTxt != "") $addWhere .= " AND (subject like '%".$searchTxt."%' OR content like '%".$searchTxt."%' ) ";
				break;
			case 'subject' :
				if($searchTxt != "") $addWhere .= " AND subject like '%".$searchTxt."%' ";
				break;
			case 'content' :
				if($searchTxt != "") $addWhere .= " AND content like '%".$searchTxt."%' ";
				break;
		}

		$URLParam = "&searchType=".$searchType."&searchTxt=".$searchTxt;
		
		$boardRow = $this->getBoardList($total_page, $NO, $cnt_no, $addWhere, " status asc ");

		include_once $PAGE_PATH . '/boardList.html';
	}

	function boardRV($mode='regist'){
		global $PAGE_PATH, $P_ACTION;
		global $M_HTML, $M_FUNC;
		global $m_id;

		if($mode == "regist"){
			$m_id = 0;
			$action = "?M0601P";

			$row = new L_ListSet();
			
			$readonly = "";
			$item_first = substr($row->get('item'), 0, 1);
			$item_second = $row->get('item');
		} else {
			$m_id = $M_FUNC->M_Filter(GET, 'm_id');
			$action = "?M0601U";

			$row = $this->getBoardByIdx($m_id);
			$row->next();
			
			$readonly = "disabled";
			$item_first = substr($row->get('item'), 0, 1);
			$item_second = $row->get('item');
			
			unset($this->statusType[1]);
			if($row->get('status') == 22){
				unset($this->statusType[4]);
				unset($this->statusType[9]);
				unset($this->statusType[11]);
				unset($this->statusType[14]);
			}
		}

		include_once $PAGE_PATH . '/boardRV.html';
	}

	function boardDataInsertProc(){
		global $MENU_ID;
		global $M_JS;

		$this->InsertBoardData();
		$M_JS->Go_URL('/?'. $MENU_ID . 'L', "등록되었습니다.");	
	}

	function boardDataUpdateProc(){
		global $MENU_ID;
		global $M_JS, $M_FUNC;

		$m_id = $M_FUNC->M_Filter(POST, "m_id");

		$this->UpdateBoardData();
		$M_JS->Go_URL('/?'. $MENU_ID . 'V&m_id='. $m_id, "수정되었습니다.");	
	}

	function boardDataDeleteProc(){
		global $MENU_ID;
		global $M_JS, $M_FUNC;

		$this->DeleteBoardData();
		$M_JS->Go_URL('/?'. $MENU_ID . 'L', "삭제되었습니다.");	
	}

	function noticeList(){
		global $PAGE_PATH, $P_ACTION, $MENU_ID, $PAGE;
		global $M_HTML, $M_FUNC;
	
		$searchType = $M_FUNC->M_Filter(GET, 'searchType');
		$searchTxt = $M_FUNC->M_Filter(GET, 'searchTxt');
		
		$addWhere = " AND type = 3 ";
		switch($searchType){
			case 'total' : 
				if($searchTxt != "") $addWhere .= " AND (subject like '%".$searchTxt."%' OR content like '%".$searchTxt."%' ) ";
				break;
			case 'subject' :
				if($searchTxt != "") $addWhere .= " AND subject like '%".$searchTxt."%' ";
				break;
			case 'content' :
				if($searchTxt != "") $addWhere .= " AND content like '%".$searchTxt."%' ";
				break;
		}

		$URLParam = "&searchType=".$searchType."&searchTxt=".$searchTxt;
		
		$noticeRow = $this->getBoardList($total_page, $NO, $cnt_no, $addWhere, " regUnixtime desc ");

		include_once $PAGE_PATH . '/noticeList.html';
	}

	function noticeRV(){
		global $PAGE_PATH, $P_ACTION;
		global $M_HTML, $M_FUNC;
		global $m_id;

		$m_id = 0;
		$action = "?M0603P";
		
		include_once $PAGE_PATH . '/noticeRV.html';
	}

	function noticeDataInsertProc(){
		global $MENU_ID;
		global $M_JS;

		$this->InsertNoticeData();
		$M_JS->Go_URL('/?'. $MENU_ID . 'L', "등록되었습니다.");	
	}

}
?>