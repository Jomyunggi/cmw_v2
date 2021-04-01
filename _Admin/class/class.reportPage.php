<?php

if(!class_exists('M_REPORT')) {
	include_once COMMON_CLASS . '/class.report.php';
}

class M_ReportPage extends M_REPORT {
	// Constructor
	function __construct() {
		//보고서는 기본적으로 오늘은 데이터는 얻을수 없데 오늘 보는 데이터는 어제부터 가능하다
		$this->today_date	= mktime(0,0,0, date('m'), date('d'), date('Y'));	//오늘
		$this->yesterday	= strtotime("-1 day", $this->today_date);			//어제
		$this->a_week_ago	= strtotime("-7 day", $this->today_date);			//이번주 7일 시작일
		$this->month_30		= strtotime("-30 day", $this->today_date);			//최근 30일 시작일
		$this->prev_month	= strtotime("-1 month", $this->yesterday);			//저번달 1일
		$this->prev2_month	= strtotime("-1 month", $this->prev_month);			//두달 전 1일
		$this->prev3_month	= strtotime("-1 month", $this->prev2_month);		//세달 전 1일
		$this->prev5_month	= strtotime("-1 month", $this->prev3_month);		//다섯달 전 1일
		$this->prev6_month	= strtotime("-1 month", $this->prev5_month);		//여섯달 전 1일
		$this->prev1_year	= strtotime("-1 year", $this->yesterday);			//일년 전 1일

		$this->searchTerm = array(
			0	=>	array(
					1	=>	'어제 : '.date('Y-m-d', $this->yesterday)." ~ ".date('Y-m-d', $this->yesterday) ,
					2	=>	'최근 7일 : '.date('Y-m-d', $this->a_week_ago)." ~ ".date('Y-m-d', $this->yesterday) ,
					3	=>	'최근 30일 : '.date('Y-m-d',$this->month_30)." ~ ".date('Y-m-d', $this->yesterday) ,
					4	=>	'지난달 : '.date('Y-m-d',$this->prev_month)." ~ ".date('Y-m-t', $this->prev_month),
					5	=>	'최근 2개월 : '.date('Y-m-d',$this->prev_month)." ~ ".date('Y-m-d', $this->yesterday),
					6	=>	'최근 3개월 : '.date('Y-m-d',$this->prev2_month)." ~ ".date('Y-m-d', $this->yesterday),
					7	=>	'최근 6개월 : '.date('Y-m-d',$this->prev6_month)." ~ ".date('Y-m-d', $this->yesterday),
					8	=>	'최근 1년 : '.date('Y-m-d',$this->prev1_year)." ~ ".date('Y-m-d', $this->yesterday),
					9	=>	'전체데이터'
				),
			1	=> array(
					1	=> array($this->yesterday, $this->yesterday),
					2	=> array($this->a_week_ago, $this->yesterday),
					3	=> array($this->month_30, $this->yesterday),
					4	=> array($this->prev_month, $this->prev_month),
					5	=> array($this->prev_month, $this->yesterday),
					6	=> array($this->prev2_month, $this->yesterday),
					7	=> array($this->prev6_month, $this->yesterday),
					8	=> array($this->prev1_year, $this->yesterday),
					9	=> array(0,0)
			)
		);
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
		$searchTerm = $this->variable_check(GET, 'searchTerm', date('Ymd', $this->yesterday));
		$step = $this->variable_check(GET, 'step', 1);
		$campaign = $this->variable_check(GET, 'campaign', '');
		$optionN = $this->variable_check(GET, 'optionN', 0);
		$keyword = $this->variable_check(GET, 'keyword', '');

		//판매처 정보 다 가져오기
		$companyArr = $this->getCompanyArr(array()," and level = 4 ");
		
		if($companyIdx != '' && $searchTerm != ''){
			switch($step){
				case 1 : $row = $this->getReportByCampaign($companyIdx, $searchTerm); break;
				case 2 : $row = $this->getReportByGoods($companyIdx, $searchTerm, $campaign); break;
				case 3 : $row = $this->getReportByKeyword($companyIdx, $searchTerm, $campaign, $optionN); break;
				case 4 : $row = $this->getReportByDaily($companyIdx, $searchTerm, $campaign, $optionN, $keyword); break;
			}
		} else {
			$row = new L_ListSet();
		}

		switch($step){
			case 1 :	include_once $PAGE_PATH . '/reportViewer1.html'; break;
			case 2 :	include_once $PAGE_PATH . '/reportViewer2.html'; break;
			case 3 :	include_once $PAGE_PATH . '/reportViewer3.html'; break;
			case 4 :	include_once $PAGE_PATH . '/reportViewer4.html'; break;
		}
	}

	function getReportByOption(){
		global $PAGE_PATH, $MENU_ID, $P_ACTION, $PAGE;
		global $M_HTML;

		$companyIdx = $this->variable_check(GET, 'companyIdx', '');
		$searchTerm = $this->variable_check(GET, 'searchTerm', 1);
		$optionN = $this->variable_check(GET, 'optionN', 0);
		$keyword = $this->variable_check(GET, 'keyword', '');
		$roas = $this->variable_check(GET, 'roas', 0);

		//판매처 정보 다 가져오기
		$companyArr = $this->getCompanyArr(array()," and level = 4 ");
		//ROAS 측정여부
		$roasArr = array(0 => '전체', 1=>'0% 이상', 2=>'0% 이하');

		//상품 옵션ID와 상품명 가져오기
		$infoArr = $this->getOption_goodsName($companyIdx);
		
		$html = '/reportByOption.html'; 
		$group = ' GROUP BY keyword';
		$having = ' Having roas > 0 ';
		
		if($companyIdx != '' && $searchTerm != '' && $optionN != 0){
			$row = $this->getKeywordByOption($companyIdx, $searchTerm, $optionN, $keyword, $roas, $group, $having);
		} else {
			$row = new L_ListSet();
		}

		include_once $PAGE_PATH . $html; break;
	}

	function getReportByKeyword(){
		global $PAGE_PATH, $MENU_ID, $P_ACTION, $PAGE;
		global $M_HTML;

		$companyIdx = $this->variable_check(GET, 'companyIdx', '');
		$searchTerm = $this->variable_check(GET, 'searchTerm', 9);
		$optionN = $this->variable_check(GET, 'optionN', 0);

		//판매처 정보 다 가져오기
		$companyArr = $this->getCompanyArr(array()," and level = 4 ");
		
		//상품 옵션ID와 상품명 가져오기
		$infoArr = $this->getOption_goodsName($companyIdx);
		
		$html = '/reportByKeyword.html'; 
		$group = ' GROUP BY keyword';
		$having = ' Having cvr > 0 ';
		
		if($companyIdx != '' && $searchTerm != '' && $optionN != 0){
			$row = $this->getOptionByKeyword($companyIdx, $searchTerm, $optionN, $group, $having);
		} else {
			$row = new L_ListSet();
		}

		include_once $PAGE_PATH . $html; break;
	}
}

?>