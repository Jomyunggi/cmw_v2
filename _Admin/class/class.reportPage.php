<?php

if(!class_exists('M_REPORT')) {
	include_once COMMON_CLASS . '/class.report.php';
}

class M_ReportPage extends M_REPORT {
	// Constructor
	function __construct() {
		parent::__construct();

	}

	// Destructor
	function __destruct() {

	}

	/************************************************
		$way		=> GET, POST
		$value		=> 받을 변수
		$initial	=> 받는 변수가 없을 경우 초기지정값
	************************************************/
	function variable_check($way, $value, $initial='', $array=0){
		global $M_FUNC;

		if($way == 'GET'){
			if(isset($_GET[$value]) && $_GET[$value]){
				$data = $M_FUNC->M_Filter($way, $value);
			} else {
				$data = $initial;
			}
		} else {
			if(isset($_POST[$value]) && $_POST[$value]){
				$data = $M_FUNC->M_Filter($way, $value);
			} else {
				$data = $initial;
			}
		}

		if($array){
			if($way == 'GET')	return $_GET[$value];
			else	return $_POST[$value];
		} else {
			return $data;
		}
	}
	
	//보고서 업로드
	function reportUpload(){
		global $PAGE_PATH;
		global $M_HTML;		

		//판매처 정보 다 가져오기
		$companyArr = $this->getCompanyArr(array()," and level = 4 ");

		$url = "./?M0501P";

		include_once $PAGE_PATH . '/reportUpload.html';
	}

	function reportInsert(){
		global $PAGE_PATH;
		
		include_once $PAGE_PATH . '/reportInsert.php';
		M_JS::Go_URL('/?M0501', "보고서 등록되었습니다.");
	}

	function getReportData(){
		global $PAGE_PATH, $MENU_ID, $P_ACTION, $PAGE;
		global $M_HTML;

		$companyIdx = $this->variable_check(GET, 'companyIdx', '');
		$startD = $this->variable_check(GET, 'startD', '');
		$endD = $this->variable_check(GET, 'endD', '');
		$step = $this->variable_check(GET, 'step', 1);
		$campaign = $this->variable_check(GET, 'campaign', '');
	
		//판매처 정보 다 가져오기
		$companyArr = $this->getCompanyArr(array()," and level = 4 ");
		
		if($companyIdx != '' && $startD != '' && $endD != ''){
			switch($step){
				case 1 : $row = $this->getReportByCampaign($companyIdx, $startD, $endD); break;
				case 2 : $row = $this->getReportByGoods($companyIdx, $startD, $endD, $campaign); break;
			}
		} else {
			$row = new L_ListSet();
		}

		switch($step){
			case 1 :	include_once $PAGE_PATH . '/reportViewer1.html'; break;
			case 2 :	include_once $PAGE_PATH . '/reportViewer2.html'; break;
		}
				
	}
}

?>