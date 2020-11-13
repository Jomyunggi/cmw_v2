<?php

if(!class_exists('M_PRICING')) {
	include_once COMMON_CLASS . '/class.pricing.php';
}

class M_PricingPage extends M_PRICING {
	// Constructor

	function __construct() {
		parent::__construct();

		$this->D_size = array(
			1 => '소',
			2 => '중',
			3 => '대'
		);
	}

	// Destructor
	function __destruct() {

	}

	function D_P_List(){
		global $PAGE_PATH;

		$row = $this->getD_P_Date();

		include_once $PAGE_PATH . '/D_P_List.html';
	}

	function D_P_RV($mode='regist'){
		global $PAGE_PATH, $P_ACTION, $MENU_ID;
		global $M_HTML, $M_FUNC;

		$m_id = $M_FUNC->M_Filter(GET, "m_id");	

		//상품가져오기
		$goodsArr = $this->getGoodsData();
		$goodsArr[0] = '상품선택';
		ksort($goodsArr);
		
		if($mode == 'regist'){
			$action = '/?'.$MENU_ID.'P&mode=insert';
			$row = new L_ListSet();
		} else {
			$action = '/?'.$MENU_ID.'P&mode=update';

			$row = $this->getDeliberyByIdx($m_id);
			$row->next();

			$dIdx = $row->get('idx');
		}

		if($row->get('size') == ""){
			$size = 1;
		} else {
			$size = $row->get('size');
		}

		include_once $PAGE_PATH . '/D_P_RV.html';
	}

	function DeliveryProc($mode){
		global $M_FUNC, $M_JS;
		global $MENU_ID;

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
		
		$this->ChangeDeliveryData($mode);
		
		$M_JS->Go_URL('/?'. $MENU_ID . $p_action, $answer);	
	}
}

?>