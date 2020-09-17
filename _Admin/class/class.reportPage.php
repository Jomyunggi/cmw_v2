<?php

if(!class_exists('M_REPORT')) {
	include_once COMMON_CLASS . '/class.report.php';
}

class M_ReportPage extends M_REPORT {
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

		$this->today_date	= mktime(0,0,0, date('m'), date('d'), date('Y'));	//오늘
		$this->this_month	= mktime(0,0,0, date('m'), 1, date('Y'));			//이번달 1일
		$this->prev_month	= strtotime("-1 month", $this->this_month);			//한달전
		$this->prev2_month	= strtotime("-1 month", $this->prev_month);			//두달전
		$this->prev3_month	= strtotime("-1 month", $this->prev2_month);		//세달전
		$this->prev5_month	= strtotime("-1 month", $this->prev3_month);		//다섯달전

		$this->searchTerm = array(		//1:오늘, 2:어제, 3:최근 7일, 4:이번주, 5:지난주, 6:이번달, 7:지난달
			1	=>	'이번달 : '.date('Y-m-d',$this->this_month)." ~ ".date('Y-m-d', $this->today_date) ,
			2	=>	'지난달 : '.date('Y-m-d',$this->prev_month)." ~ ".date('Y-m-t', $this->prev_month),
			3	=>	'최근 2개월 : '.date('Y-m-d',$this->prev_month)." ~ ".date('Y-m-d', $this->today_date),
			4	=>	'최근 3개월 : '.date('Y-m-d',$this->prev2_month)." ~ ".date('Y-m-d', $this->today_date),
			5	=>	'최근 5개월 : '.date('Y-m-d',$this->prev5_month)." ~ ".date('Y-m-d', $this->today_date)
		);

		$this->TermArr = array(
			1	=> array($this->this_month, $this->today_date),
			2	=> array($this->prev_month, strtotime(date('Y-m-t', $this->prev_month))),
			3	=> array($this->prev_month, $this->today_date),
			4	=> array($this->prev2_month, $this->today_date),
			5	=> array($this->prev5_month, $this->today_date)
		);

		$this->unitPrice = array(
			'NO' => 0,
			'새피아 10롤' => 31990,
			'땡큐 10롤' => 0,
			'새피아 24롤' => 28700,
			'땡큐 30롤' => 0,
			'순수' => 20550,
			'자연' => 24640,
			'꿈집' => 24640,
			'꽃지꿈집' => 27280,
			'황토' => 27950,
			'데코꿈집' => 28480,
			'고급점보롤 2겹 16롤 150m' => 20990,
			'고급점보롤 2겹 16롤 180m' => 23990,
			'고급점보롤 2겹 16롤 200m' => 25720,
			'고급점보롤 2겹 16롤 300m' => 34780,
			'꽃지점보롤 2겹 16롤 150m' => 21830,
			'꽃지점보롤 2겹 16롤 180m' => 24980,
			'꽃지점보롤 2겹 16롤 200m' => 26800,
			'꽃지점보롤 2겹 16롤 300m' => 35990,
			'네프킨' => 11000,
			'핸드타월' => 19000
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
	function variable_check($way, $value, $initial){
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

		return $data;
	}

	function divisionZero($val1, $val2, $percentage=false){
		if($val2 != 0){
			$data = $val1 / $val2;
		} else {
			$data = 0;
		}

		if($percentage){
			$data = $data * 100;
		}
		
		return $data;
	}

	function reportRV(){
		global $PAGE_PATH, $M_HTML;

		//판매처 정보 다 가져오기
		$companyArr = $this->getCompanyArr(array()," and level = 31 and idx not in (42) ");
	
		include_once $PAGE_PATH . '/report_excel.html';
	}

	function InsertByReportDB($tName){
		global $PAGE_PATH;
		echo "HI";
		echo FILE_LOCATION; exit;
		include_once $PAGE_PATH . '/readExcel.php';
		M_JS::Go_URL('/?M0703', "리포트DB 등록되었습니다.");
	}

	function reportAnaly($tName){
		global $PAGE_PATH, $MENU_ID, $PAGE;
		global $M_HTML, $M_FUNC;

		$searchTerm = $this->variable_check(GET, "searchTerm", 1);
		$searchCompany = $this->variable_check(GET, "searchCompany", $tName);

		//판매처 정보 다 가져오기
		$companyArr = $this->getCompanyArr(array()," and level = 31 and idx not in (42) ");

		$reportRow = $this->getAnalysisOfReports($tName, $searchTerm);

		include_once $PAGE_PATH . '/keyword_'.$tName.'.html';
	}
	
}

?>