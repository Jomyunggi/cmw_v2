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

		$this->Category = array(
			0	=>	'전체선택',
			1	=>	'화장지',
			2	=>	'점보롤',
			4	=>	'네프킨',
			8	=>	'핸드타월',
			16	=>	'기타'
		);

		$this->smallTax = 0.4;

		$this->deliveryP = array(
			1	=> 2300,
			2	=> 2700,
			3	=> 3200
		);
		
		$this->adPriceYN = array(
			0	=> '노포함',
			1	=> '포함'
		);

		$this->adPrice = 0.045;
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

	function SalesList(){
		global $PAGE_PATH, $MENU_ID;
		global $M_HTML, $M_FUNC;

		$cIdx		= $_GET['cIdx'];
		$categorys	= $_GET['categorys'];
		$searchTxt = $M_FUNC->M_Filter(GET, "searchTxt");
		$adPriceYN = $M_FUNC->M_Filter(GET, "adPriceYN");
		$adPrice = $M_FUNC->M_Filter(GET, "adPrice");
		$plusCost = $M_FUNC->M_Filter(GET, "plusCost");

		if($adPriceYN) {
			if($adPrice == '') $adPrice = 5;
		}
		if($plusCost == '') $plusCost = 0;

		//Company에서 온라인거래처만 가져온다.
		$onlineArr = $this->getCompanyByOn('idx', 'companyName');
		$feesArr = $this->getCompanyByOn('companyName', 'fees');
		$feesS_Arr = $this->getCompanyByOn('companyName', 'license');
		unset($onlineArr[0]);
		unset($feesArr[0]);

		if(count($cIdx) && count($categorys)){
			$addWhere = " AND g.category in (".implode(',', $categorys).") ";
			$row = $this->getFinalSales($addWhere);
		} else {
			$row = new L_ListSet();
		}

		include_once $PAGE_PATH . '/finalSales.html';
	}
}

?>