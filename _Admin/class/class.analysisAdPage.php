<?php

if(!class_exists('M_ADPage')) {
	include_once COMMON_CLASS . '/class.analysisAD.php';
}

class M_ADPage extends M_AD {
	// Constructor

	function __construct() {
		$this->First_Menu = array(
			'M01'	=> '계정관리',
			'M02'	=> '상품단가표',
			'M03'	=> '거래현황',
			'M04'	=> '정산현황',
			'M05'	=> '세금',
			'M06'	=> '게시판'
		);
		$this->Second_Menu = array(
			'M0101' => '계정관리',
			'M0201' => '상품단가표',
			'M0301' => '오프라인',
			'M0302' => '온라인',
			'M0401' => '오프라인',
			'M0402' => '온라인',
			'M0501' => '세금',
			'M0601' => '공지사항',
			'M0602' => 'Q&A',
			'M0603' => '작업게시판'
		);
		$this->gubun = array(
			1	=>	'관리자',
			11	=>	'판매처',
			21	=>	'매입처',
			31	=>	'인터넷',
			41	=>	'택배'
		);
		$this->onoff = array(
			'On'=>'ON', 
			'Off'=>'OFF'
		);
		$this->status = array(
			1 => '사용', 
			4 => '중지',
			9 => '삭제'
		);

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

		$this->monthByDay = array(
			1	=> 31,
			2	=> 28,
			3	=> 31,
			4	=> 30,
			5	=> 31,
			6	=> 30,
			7	=> 31,
			8	=> 31,
			9	=> 30,
			10	=> 31,
			11	=> 30,
			12	=> 31
		);

		$this->online_goods = array(
			0	=> '새피아 3겹 10롤',
			1	=> '새피아 3겹 24롤',
			2	=> '순수 3겹 30롤',
			3	=> '자연 3겹 30롤',
			4	=> '꿈집 3겹 30롤',
			5	=> '꽃지꿈집 3겹 30롤',
			6	=> '황토 3겹 30롤',
			7	=> '데코꿈집 3겹 30롤',
			8	=> '고급점보롤 2겹 16롤 160m',
			9	=> '꽃지점보롤 2겹 16롤 160m',
			10	=> '고급점보롤 2겹 16롤',
			11	=> '꽃지점보롤 2겹 16롤',
			12	=> '네프킨',
			13	=> '핸드타월'
		);

		$this->goodsCost = array(
			0	=>	1948,	//새피아 10 10개묶음
			1	=>	1998,	//땡큐 10 10개 묶음
			2	=>	17046,	//새피아 24
			3	=>	12248,	//땡큐 30
			4	=>	12248,	//순수
			5	=>	14190,	//자연
			6	=>	14190,	//꿈집
			7	=>	16044,	//꽃지꿈집
			8	=>	16519,	//황토
			9	=>	16906,	//데코꿈집
			10	=>	13777,	//고급점보롤 180
			11	=>	14459,	//꽃지점보롤 180
			12	=>	0	,	//빈값
			13	=>	11983,	//고급점보롤 150
			14	=>	13777,	//고급점보롤 180
			15	=>	13208,	//꽃지점보롤 160
			16	=>	9000,	//네프킨
			17	=>	16000	//핸드타월
		);

		$this->goodsName = array(
			0	=>	"새피아 10롤",
			1	=>	"땡큐 10롤",
			2	=>	"새피아 24롤",
			3	=>	"땡큐 30롤",
			4	=>	"순수 30롤",
			5	=>	"자연 30롤",
			6	=>	"꿈집 30롤",
			7	=>	"꽃지꿈집 30롤",
			8	=>	"황토 30롤",
			9	=>	"데코꿈집 30롤",
			10	=>	"고급점 150m",
			11	=>	"고급점 180m",
			12	=>	"고급점 200m",
			13	=>	"고급점 300m",
			14	=>	"꽃지점 160m",
			15	=>	"꽃지점 180m",
			16	=>	"꽃지점 200m",
			17	=>	"꽃지점 300m",
			18	=>	"네프킨",
			19	=>	"핸드타월",
		);

		$this->mainItem = array(
			0	=> '전체',
			1	=> '고급점보롤',
			2	=> '꽃지점보롤',
			3	=> '순수',
			4	=> '꿈집'
		);

		$this->online_goods_info = array(
			0	=> 1968,
			1	=> 1998,
			2	=> 17046,
			3	=> 12248,
			4	=> 12248,
			5	=> 14190,
			6	=> 14190,
			7	=> 16044,
			8	=> 16519,
			9	=> 16906,
			10	=> 11983,
			11	=> 13777,
			12	=> 14963,
			13	=> 21207,
			14	=> 13208,
			15	=> 14459,
			16	=> 15710,
			17	=> 21993,
			18	=> 9000,
			19	=> 16000
		);

		$this->online_price = array(
			0	=> 3070,
			1	=> 3070,
			2	=> 27400,
			3	=> 20400,
			4	=> 20400,
			5	=> 24700,
			6	=> 24700,
			7	=> 26300,
			8	=> 27480,
			9	=> 28090,
			10	=> 20700,
			11	=> 23990,
			12	=> 25990,
			13	=> 34990,
			14	=> 22590,
			15	=> 24990,
			16	=> 26990,
			17	=> 35990,
			18	=> 9000,
			19	=> 16000
		);

		$this->siteNum = 7;	//쿠팡, 티몬, 위메프, 11번가, 옥션, 지마켓, 네이버

		$this->searchGubun = array(
			0			=>	'검색분류 선택',
			'recipient'	=>	'수취인',
			'tel'		=>	'핸드폰',
			'addr'		=>	'주소'
		);

		$this->searchDelivery = array(		//0:전체, 1:배송중, 4:배송미처리, 11:배송완료
			0	=>	'전체',
			4	=>	'발송처리',
			1	=>	'배송중',
			11	=>	'배송완료'
		);

		$this->searchHistory = array(
			0	=>	'전체',
			1	=>	'인수',
			2	=>	'정산',
			3	=>	'광고비'
		);
				
		$this->today_date	= mktime(0,0,0, date('m'), date('d'), date('Y'));	//오늘
		$this->yesterday	= strtotime("-1 day", $this->today_date);			//어제
		$this->a_week_ago	= strtotime("-6 day", $this->today_date);			//이번주 첫째일
		$this->last_week1	= strtotime("-13 day", $this->today_date);			//일주 전 첫째일
		$this->last_week2	= strtotime("-7 day", $this->today_date);			//일주 전 마지막
		$this->this_month	= mktime(0,0,0, date('m'), 1, date('Y'));			//이번달 1일
		$this->prev_month	= strtotime("-1 month", $this->this_month);			//한달전
		$this->prev2_month	= strtotime("-1 month", $this->prev_month);			//두달전
		$this->prev3_month	= strtotime("-1 month", $this->prev2_month);		//세달전
		$this->prev5_month	= strtotime("-1 month", $this->prev3_month);		//다섯달전

		$this->searchTerm = array(		//1:오늘, 2:어제, 3:최근 7일, 4:이번주, 5:지난주, 6:이번달, 7:지난달
			1	=>	'어제 : '.date('Y-m-d', $this->yesterday)." ~ ".date('Y-m-d', $this->yesterday) ,
			2	=>	'이번주 : '.date('Y-m-d', $this->a_week_ago)." ~ ".date('Y-m-d', $this->today_date) ,
			3	=>	'지난주 : '.date('Y-m-d', $this->last_week1)." ~ ".date('Y-m-d', $this->last_week2) ,
			4	=>	'이번달 : '.date('Y-m-d',$this->this_month)." ~ ".date('Y-m-d', $this->today_date) ,
			5	=>	'지난달 : '.date('Y-m-d',$this->prev_month)." ~ ".date('Y-m-t', $this->prev_month),
			6	=>	'최근 2주 : '.date('Y-m-d', $this->last_week1)." ~ ".date('Y-m-d', $this->today_date) ,
			7	=>	'최근 2달 : '.date('Y-m-d',$this->prev_month)." ~ ".date('Y-m-d', $this->today_date),
			8	=>	'최근 3달 : '.date('Y-m-d',$this->prev2_month)." ~ ".date('Y-m-d', $this->today_date)
		);

		$this->TermArr = array(
			1	=> array($this->yesterday, $this->yesterday),
			2	=> array($this->a_week_ago, $this->today_date),
			3	=> array($this->last_week1, $this->last_week2),
			4	=> array($this->this_month, $this->today_date),
			5	=> array($this->prev_month, date('Y-m-t', $this->prev_month)),
			6	=> array($this->last_week1, $this->today_date),
			7	=> array($this->prev_month, $this->today_date),
			8	=> array($this->prev2_month, $this->today_date)
		);

		$this->analyTerm = array(		//1:오늘, 2:어제, 3:최근 7일, 4:이번주, 5:지난주, 6:이번달, 7:지난달
			1	=>	'이번달 : '.date('Y-m-d',$this->this_month)." ~ ".date('Y-m-d', $this->today_date) ,
			2	=>	'한달전 : '.date('Y-m-d',$this->prev_month)." ~ ".date('Y-m-t', $this->prev_month),
			3	=>	'두달전 : '.date('Y-m-d',$this->prev2_month)." ~ ".date('Y-m-t', $this->prev2_month),
			4	=>	'최근 2달 : '.date('Y-m-d',$this->prev_month)." ~ ".date('Y-m-d', $this->today_date),
			5	=>	'최근 3달 : '.date('Y-m-d',$this->prev2_month)." ~ ".date('Y-m-d', $this->today_date)
		);

		$this->analyTermArr = array(
			1	=> array($this->this_month, $this->today_date),
			2	=> array($this->prev_month, $this->prev_month),
			3	=> array($this->prev2_month, $this->prev2_month),
			4	=> array($this->prev_month, $this->today_date),
			5	=> array($this->prev2_month, $this->today_date)
		);
		
		$this->fixedRatio = array(
			1	=> array('인건비', 8300000),
			2	=> array('임대료', 2400000),
			3	=> array('광고비', 1500000),
			4	=> array('대출이자', 500000),
			5	=> array('전기세', 400000),
			6	=> array('보험료', 400000),
			7	=> array('보안시스템', 90000),
			8	=> array('통신비', 40000)
		);


		parent::__construct();
	}

	// Destructor
	function __destruct() {

	}

	function adReport(){
		global $PAGE_PATH, $MENU_ID, $P_ACTION;
		global $M_FUNC, $M_HTML;
		
		$searchItem		= $_GET["searchItem"] == "" ? array(0=>0) : $_GET["searchItem"];
		$searchTerm		= $M_FUNC->M_Filter(GET, "searchTerm") == "" ? 1 : $M_FUNC->M_Filter(GET, "searchTerm");
		$searchCompany	= $M_FUNC->M_Filter(GET, "searchCompany") == "" ? 0 : $M_FUNC->M_Filter(GET, "searchCompany");

		//온라인 판매처 가져오기
		$companyArr = $this->getCompanyArr(" AND onoff = 'On' ");
		unset($companyArr[42]);

		//기간에 따른 사이트별 데이터 가져오기
		if($searchTerm == 5){
			$addWhere = " AND left(time, 8) between ". date('Ymd', $this->TermArr[$searchTerm][0]) ." AND ". date('Ymd', strtotime($this->TermArr[$searchTerm][1])) ;
		} else {
			$addWhere = " AND left(time, 8) between ". date('Ymd', $this->TermArr[$searchTerm][0]) ." AND ". date('Ymd', $this->TermArr[$searchTerm][1]) ;
		}
		if(count($searchItem) > 0){
			for($i=0; $i<count($searchItem); $i++){
				if($i == 0){
					if($searchItem[$i] == 0) break;
					else $addWhere .= " AND ( ";
				}

				$addWhere .= " deal like '".$this->mainItem[$searchItem[$i]]."%'";
				if(count($searchItem) != 0){
					if(count($searchItem)-1 != $i){
						$addWhere .= " OR ";
					} else if(count($searchItem) >= $i){
						$addWhere .= " ) ";
					}
				}
			}
		}

		if($searchCompany){
			$addWhere .= " AND companyIdx = ". $searchCompany;
			
			$ADRow = $this->getAnalyAD(date('Y', $this->TermArr[$searchTerm][0]), $addWhere);
		} else {
			$ADRow = new L_ListSet();
		}

		include_once $PAGE_PATH . '/analysisAD.html';
	}

	function salesRateByGoods(){
		global $PAGE_PATH, $MENU_ID, $P_ACTION;
		global $M_FUNC, $M_HTML;

		$analyTerm	= $M_FUNC->M_Filter(GET, "analyTerm");
		$analyCompany = $M_FUNC->M_Filter(GET, "analyCompany");
		if($analyTerm == "") $analyTerm = 5;
		if($analyCompany == "") $analyCompany = 0;
		
		//오프라인 각 순서에 맞게 dealNum값 가져오기
		if($analyTerm < 2 && $analyTerm > 3){
			$addWhere = " AND onoff = 'Off' AND date between ".date('Ymd', $this->analyTermArr[$analyTerm][0])." AND ".date('Ymd', $this->analyTermArr[$analyTerm][1])." ";
		} else {
			$addWhere = " AND onoff = 'Off' AND date between ".date('Ymd', $this->analyTermArr[$analyTerm][0])." AND ".date('Ymt', $this->analyTermArr[$analyTerm][1])." ";
		}	
		if($analyCompany > 0){
			$addWhere .= " AND companyIdx = ". $analyCompany;
		}
		
		//거래처가져오기
		$CompanyArr = $this->getCompanyArr(" AND onoff = 'Off' ");
		$CompanyArr[0] = '전체 선택';

		$goodsRow = $this->getSalesRate(date('Y', $this->analyTermArr[4][0]), $addWhere);

		$modifyRow = $this->modifyData($goodsRow);

		include_once $PAGE_PATH . '/salesRateByGoods.html';
	}
}
?>