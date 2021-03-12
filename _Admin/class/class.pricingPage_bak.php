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
			3 => '대',
			4 => '소2',
			5 => '중2',
			6 => '대2',
			7 => '소3',
			8 => '중3',
			9 => '대3'
		);

		$this->Category = array(
			0	=>	'전체선택',
			1	=>	'화장지',
			2	=>	'점보롤',
			4	=>	'네프킨',
			8	=>	'핸드타월',
			16	=>	'기타'
		);

		$this->cDown = array(
			1 => array(
					0 => '전체선택',
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
					6 => 6
				)
		);

		$this->smallTax = 0.45;
		$this->target_Margin = 0.05;

		$this->deliveryP = array(
			1	=> 2300,
			2	=> 2700,
			3	=> 3200,
			4	=> 4600,
			5	=> 5400,
			6	=> 6400,
			7	=> 6900,
			8	=> 8100,
			9	=> 9600
		);
		
		$this->adPriceYN = array(
			0	=> '노포함',
			1	=> '포함'
		);

		$this->adPrice = 0.045;
		$this->hopeMargin = 0.23;
		$this->supply_hope = 0.25;

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
	
	/*
	function SalesList(){
		global $PAGE_PATH, $MENU_ID;
		global $M_HTML, $M_FUNC;

		if(is_null($MENU_ID)){
			$MENU_ID = "M0402";
		} 

		$cIdx		= $M_FUNC->M_Filter(GET, 'cIdx');
		$categorys	= $M_FUNC->M_Filter(GET, 'categorys');
		$plusCost	= $M_FUNC->M_Filter(GET, "plusCost");
		$gName	= $M_FUNC->M_Filter(GET, "gName");
		$getRollArr	= $_GET['rollType'.$categorys];
		$cDown		= $_GET['cDown'];

		if($plusCost == '') $plusCost = 200;

		//Company에서 온라인거래처만 가져온다.
		$onlineArr = $this->getCompanyByOn('idx', 'companyName');
		$feesArr = $this->getCompanyByOn('companyName', 'fees');
		$feesS_Arr = $this->getCompanyByOn('companyName', 'license');
		unset($onlineArr[0]);
		unset($feesArr[0]);
		unset($this->Category[0]);
		
		//카테고리별 롤타입가져오기
		$rollArr = $this->getRollType();

		if($categorys){
			$addWhere = " AND r.cIdx = ".$cIdx;
			$addWhere .= " AND g.category = ".$categorys;
			if($getRollArr){
				$addWhere .= " AND g.rollType in (".implode(',', $getRollArr).") ";
			}
			
			if($cDown){
				$addWhere .= " AND g.count in (".implode(',', $cDown).") ";			
			}

			if($gName){
				$addWhere .= " AND g.gName like '%".$gName."%' ";
			}

			$row = $this->getFinalSales($addWhere);
		} else {
			$row = new L_ListSet();
		}

		include_once $PAGE_PATH . '/finalSales.html';
	}
	*/

	function SalesList(){
		global $PAGE_PATH, $MENU_ID;
		global $M_HTML, $M_FUNC;

		if(is_null($MENU_ID)){
			$MENU_ID = "M0402";
		} 

		$cIdx		= $M_FUNC->M_Filter(GET, 'cIdx');
		$categorys	= $M_FUNC->M_Filter(GET, 'categorys');
		$gName	= $M_FUNC->M_Filter(GET, "gName");
		$getRollArr	= $_GET['rollType'.$categorys];
		$cDown		= $_GET['cDown'];

		//Company에서 온라인거래처만 가져온다.
		$onlineArr = $this->getCompanyByOn('idx', 'companyName');
		$feesArr = $this->getCompanyByOn('companyName', 'fees');
		$feesS_Arr = $this->getCompanyByOn('companyName', 'license');
		unset($onlineArr[0]);
		unset($feesArr[0]);
		unset($this->Category[0]);
		
		//카테고리별 롤타입가져오기
		$rollArr = $this->getRollType();

		if($categorys){
			$addWhere = " AND r.cIdx = ".$cIdx;
			$addWhere .= " AND g.category = ".$categorys;
			if($getRollArr){
				$addWhere .= " AND g.rollType in (".implode(',', $getRollArr).") ";
			}
			
			if($cDown){
				$addWhere .= " AND g.count in (".implode(',', $cDown).") ";			
			}

			if($gName){
				$addWhere .= " AND g.gName like '%".$gName."%' ";
			}

			$row = $this->getFinalSales($addWhere);
		} else {
			$row = new L_ListSet();
		}

		include_once $PAGE_PATH . '/finalSales_bak.html';
	}

	function S_revenueList(){
		global $PAGE_PATH;
		
		//온라인 판매처 가져오기
		$companyArr = $this->getCompanybyOn('idx', 'companyName');
		unset($companyArr[0]);

		include_once $PAGE_PATH . '/S_revenueList.html';
	}

	function S_revenueRV(){
		global $PAGE_PATH, $MENU_ID, $P_ACTION;
		global $M_FUNC, $M_HTML;

		$idx		= $M_FUNC->M_Filter(GET, 'idx');

		//온라인 판매처 가져오기
		$companyArr = $this->getCompanybyOn('idx', 'companyName');
		//택배상품 배열로 가져오기
		$D_G_th= $this->getD_GoodsByArr('', 'th');
		$D_G_td= $this->getD_GoodsByArr(' AND r.cIdx ='.$idx, 'td');

		$action = '/?'.$MENU_ID.'P';
		
		include_once $PAGE_PATH . '/S_revenueRV.html';
	}

	function RevenueProc(){
		global $M_FUNC, $M_JS;
		global $MENU_ID;

		$cIdx = $M_FUNC->M_Filter(POST, "cIdx");

		$answer = "등록되었습니다";
		$p_action = "C&idx=".$cIdx;

		
		$this->ChangeRevenueData($cIdx);
		
		$M_JS->Go_URL('/?'. $MENU_ID . $p_action, $answer);	
	}
}

?>