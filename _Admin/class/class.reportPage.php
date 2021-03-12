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
		exit;
		M_JS::Go_URL('/?M0501', "보고서 등록되었습니다.");
	}
}

?>