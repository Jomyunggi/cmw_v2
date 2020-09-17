<?php

if(!class_exists('M_TAX')) {
	include_once COMMON_CLASS . '/class.tax.php';
}

class M_TaxPage extends M_TAX {
	// Constructor

	function __construct() {
		$this->searchYear = array(
			2019 => '2019년', 
			2020 => '2020년',
			2021 => '2021년',
			2022 => '2022년'
		);

		$this->searchMonth = array(
			1 => '1월', 
			2 => '2월',
			3 => '3월',
			4 => '4월',
			5 => '5월',
			6 => '6월',
			7 => '7월',
			8 => '8월',
			9 => '9월',
			10 => '10월',
			11 => '11월',
			12 => '12월'
		);

		parent::__construct();
	}

	// Destructor
	function __destruct() {

	}

	function taxList(){
		global $MENU_ID, $P_ACTION, $PAGE_PATH, $PAGE;
		global $M_FUNC, $M_HTML, $M_SIGNUP;
		
		if($_SESSION['Level'] ==! 1){
			$M_SIGNUP->noExistPage();
		}

		$searchYear		= $M_FUNC->M_Filter(GET, "searchYear");
		$searchMonth	= $M_FUNC->M_Filter(GET, "searchMonth");

		if($searchYear == '') $searchYear = date('Y', time());
		if($searchMonth == '') $searchMonth = date('n', strtotime('-1 month', time()));
		$month = strlen($searchMonth) == 1 ? "0".$searchMonth : $searchMonth;

		$table = " DealSum_".$searchYear." d INNER JOIN Company_Info c on d.companyIdx = c.idx ";
		
		$addWhere = " AND d.onoff = 'Off' AND d.dealType = 1 AND c.taxYN = 'Y' AND d.month = ".$searchMonth." ";
		
		$order = " d.companyName asc ";
		//해당 월 업체별 합계
		$taxRow	= $this->getTaxList($table, $addWhere, $order);

		include_once $PAGE_PATH . '/taxList.html';
	}

}

?>