<?php

if(!class_exists('M_MANAGER')) {
	include_once COMMON_CLASS . '/class.manager.php';
}

class M_ManagerPage extends M_MANAGER {
	// Constructor

	function __construct() {
		parent::__construct();

		$this->bCategory = array(
			0 => '카테고리 선택',
			1 => '작업내역',
			2 => '온라인',
			3 => '오프라인',
			4 => '회사운영'
		);

		$this->Status = array(
			1 => '등록',
			2 => '진행중',
			3 => '완료',
			4 => '중지',
			9 => '삭제',
			99 => '완전삭제'
		);

		$this->gCategory = array(
			0 => '카테고리 선택',
			1 => '화장지/휴지',
			2 => '점보롤',
			4 => '네프킨',
			8 => '핸드타월',
			16 => '기타'
		);

		$this->gStatus = array(
			1 => '판매',
			9 => '삭제',
		);

		$this->cLevel = array(
			0 => '전체선택',
			1 => '오프라인(세금O)',
			2 => '오프라인(세금X)',
			4 => '온라인',
			8 => '매입처',
			//16 => '전체선택',
		);

		$this->cTax = array(
			0 => '세금 미처리',
			1 => '세금 처리'
		);

		$this->cStatus = array(
			1 => '사용',
			4 => '중지',
			9 => '삭제'
		);
	}

	// Destructor
	function __destruct() {

	}

	function managerList(){
		global $PAGE_PATH, $MENU_2DEPTH, $MENU_ID, $P_ACTION, $PAGE;
		global $M_FUNC, $M_HTML;
		
		switch($MENU_2DEPTH){
			case 1 : //게시판
				$row = $this->getBoardData();
				include_once $PAGE_PATH . '/boardList.html';
				break;
			case 2 : //상품등록
				$row = $this->getGoodsData();
				include_once $PAGE_PATH . '/goodsList.html';
				break;
			case 3 : //거래처등록
				$PAGE			= $M_FUNC->M_Filter(GET, 'PAGE');
				$level			= $M_FUNC->M_Filter(GET, 'level');

				if($PAGE == '') $PAGE = 1;
				if($level != '') $addWhere = " AND level = ". $level;
				else $addWhere = " AND status <> 9 ";

				$row = $this->getCompanyData($total_page, $NO, $cnt_no, $addWhere, $order, $limit);
				include_once $PAGE_PATH . '/companyList.html';
				break;
			case 4 : //거래처별 단가등록
				$row = $this->getUnitPriceData();
				include_once $PAGE_PATH . '/unitPriceList.html';
				break;
		}
	}

	function managerRV($mode='regist'){
		global $PAGE_PATH, $P_ACTION, $MENU_ID, $MENU_2DEPTH, $PAGE;
		global $M_HTML, $M_FUNC;

		$m_id = $M_FUNC->M_Filter(GET, "m_id");				
		
		if($mode == 'regist'){
			$action = '/?'.$MENU_ID.'P&mode=insert';
			$row = new L_ListSet();
		} else {
			$action = '/?'.$MENU_ID.'P&mode=update';
		}

		switch($MENU_2DEPTH){
			case 1 : 
				switch($mode){
					case 'view' : 
						$row = $this->getBoardByIdx($m_id);
						$row->next();
						break;
				}
				include_once $PAGE_PATH . '/boardRV.html'; break;
			case 2 : 
				switch($mode){
					case 'view' : 
						$row = $this->getGoodsByIdx($m_id);
						$row->next();
						break;
				}
				include_once $PAGE_PATH . '/goodsRV.html'; break;
			case 3 : 
				switch($mode){
					case 'view' : 
						$row = $this->getCompanyByIdx($m_id);
						$row->next();
						break;
				}
				include_once $PAGE_PATH . '/companyRV.html'; break;
			case 4 : 
				switch($mode){
					case 'view' : 
						$companyIdx = $M_FUNC->M_Filter(GET, "companyIdx");	
						$row = $this->getUnitPriceByIdx($companyIdx);
						break;
				}
				include_once $PAGE_PATH . '/unitPriceRV.html'; break;
		}
	}

	function managerProc(){
		global $MENU_ID, $MENU_2DEPTH;
		global $M_JS, $M_FUNC;

		$mode = $M_FUNC->M_Filter(GET, "mode");
		$m_id = $M_FUNC->M_Filter(POST, "m_id");
			
		if($mode == 'insert'){
			$answer = "등록되었습니다";
			$p_action = "L";
		} elseif($mode == 'update') {
			$answer = "수정되었습니다";
			$p_action = "V&m_id=".$m_id;
		} else {
			$p_action = "L";
			$answer = "삭제되었습니다";
		}

		switch($MENU_2DEPTH){
			case 1 : 
				$this->ChangeBoardData($mode); break;
			case 2 :
				$this->ChangeGoodsData($mode); break;
			case 3 :
				$this->ChangeCompanyData($mode); break;
			case 4 :
				$companyIdx = $M_FUNC->M_Filter(POST, "companyIdx");
				$MENU_ID = "M0104";
				$p_action = "V&companyIdx=".$companyIdx;
				$answer = "등록&수정 되었습니다";
				$this->ChangeUnitPriceData($mode); break;
		}
		
		$M_JS->Go_URL('/?'. $MENU_ID . $p_action, $answer);	
	}

	function detachArr($data){
		
		$arr = array();
		if($data == ""){
			$arr[0]  = "";
			$arr[1]  = "";
			$arr[2]  = "";
		} else {
			$tmp = explode("-", $data);
			$arr[0]  = $tmp[0];
			$arr[1]  = $tmp[1];
			$arr[2]  = $tmp[2];
		}

		return $arr;
	}
}

?>