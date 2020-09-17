<?php

if(!class_exists('M_ACCOUNT')) {
	include_once COMMON_CLASS . '/class.deal.php';
}

class M_DealPage extends M_DEAL {
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
			2	=> 29,
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
			8	=> '고급점보롤 2겹 16롤 150m',
			9	=> '꽃지점보롤 2겹 16롤 160m',
			10	=> '고급점보롤 2겹 16롤',
			11	=> '꽃지점보롤 2겹 16롤',
			12	=> '네프킨',
			13	=> '핸드타월'
		);

		$this->online_goods_info = array(
			0	=> array('새피아 10롤' => 2650),
			1	=> array('땡큐 10롤' => 0),
			2	=> array('새피아 24롤' => 5780),
			3	=> array('땡큐 30롤' => 0),
			4	=> array('순수 30롤' => 5670),
			5	=> array('자연 30롤' => 6970),
			6	=> array('꿈집 30롤' => 6970),
			7	=> array('꽃지꿈집 30롤' => 7520),
			8	=> array('황토 30롤' => 7890),
			9	=> array('데코꿈집 30롤' => 8080),
			10	=> array('고급점보롤 160m' => 20990),
			11	=> array('고급점보롤 180m' => 23490),
			12	=> array('고급점보롤 200m' => 25490),
			13	=> array('고급점보롤 300m' => 32990),
			14	=> array('꽃지점보롤 160m' => 21990),
			15	=> array('꽃지점보롤 180m' => 24490),
			16	=> array('꽃지점보롤 200m' => 26490),
			17	=> array('꽃지점보롤 300m' => 33990),
			18	=> array('네프킨' => 11000),
			19	=> array('핸드타월' => 19000)
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

		$this->searchUser = array(
			'all'		=>	'전체',
			'recipient'	=>	'수취인',
			'buyer'		=>	'구매자'
		);

		$this->dstatus = array(
			1	=>	'판매',
			9	=>	'삭제',
			11	=>	'교환',
			14	=>	'반품',
			19	=>	'취소',
			99	=>	'환불',
			0	=>	'수정'
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
			1	=>	'오늘 : '.date('Y-m-d', $this->today_date)." ~ ".date('Y-m-d', $this->today_date) ,
			2	=>	'어제 : '.date('Y-m-d', $this->yesterday)." ~ ".date('Y-m-d', $this->yesterday) ,
		  //3	=>	'최근 7일 &nbsp;: '.date('Y.m.d', strtotime('-6 day', time()))." ~ ".date('Y.m.d', time()) ,
			4	=>	'이번주 : '.date('Y-m-d', $this->a_week_ago)." ~ ".date('Y-m-d', $this->today_date) ,
			5	=>	'지난주 : '.date('Y-m-d', $this->last_week1)." ~ ".date('Y-m-d', $this->last_week2) ,
			6	=>	'이번달 : '.date('Y-m-d',$this->this_month)." ~ ".date('Y-m-d', $this->today_date) ,
			7	=>	'지난달 : '.date('Y-m-d',$this->prev_month)." ~ ".date('Y-m-t', $this->prev_month),
			8	=>	'최근 2개월 : '.date('Y-m-d',$this->prev_month)." ~ ".date('Y-m-d', $this->today_date),
			9	=>	'최근 3개월 : '.date('Y-m-d',$this->prev2_month)." ~ ".date('Y-m-d', $this->today_date),
			10	=>	'최근 5개월 : '.date('Y-m-d',$this->prev5_month)." ~ ".date('Y-m-d', $this->today_date)
		);

		$this->TermArr = array(
			1	=> array($this->today_date, $this->today_date),
			2	=> array($this->yesterday, $this->yesterday),
			4	=> array($this->a_week_ago, $this->today_date),
			5	=> array($this->last_week1, $this->last_week2),
			6	=> array($this->this_month, $this->today_date),
			7	=> array($this->prev_month, date('Y-m-t', $this->prev_month)),
			8	=> array($this->prev_month, $this->today_date),
			9	=> array($this->prev2_month, $this->today_date),
			10	=> array($this->prev5_month, $this->today_date)
		);

		$this->CompanyBycontents = array(
			'쿠팡'	=> "구매확정을 기준으로 매출을 인식합니다.(주정산 지급액: 70%, 최종액정산지급액: 30%)<br/>매출금액에서 판매수수료와 공제금액 등을 제외한 나머지를 정산합니다.</br>예)10월 2째주 주정산 지급일 : 10월 13일(일) + 15영업일 = 11월 1일(금), 15영업일 해당되는 주 금요일",
			'티몬'	=> "[파트너 단위 정산]<br/>- 정산주기 : 월 1회</br>- 거래발생일 : 매월 1일~말일<br/>- 정산대상 : 매월 1일~말일 까지의 배송완료분 + 배송비<br/>- 정산일 : 당월 말일 + 35일<br/>- 정산율 : 100%",
			'위메프'	=> "월 마감후, 영업일 기준 40일 내 100% 지급<br/>배송완료일 기준으로 익익월 4/7/9일에 물품대금이 지급됩니다.<br/> &nbsp;&nbsp;- 물품대금 입금일자가 공유일 경우, 전일 영업일에 물품대금을 입급됩니다.",
			'11번가'	=> "배송 후 고객이 구매결정을 확인한 판매건에 대해 정산 및 입금<br/>구매 확정시 - 1일 영업일 정산처리, 2일 영업일 정산<br/>구매 미확정시 - 발송처리 21일 후 자동구매확정, 1일 영업일 정산처리, 2일 영업일 정산",
			'옥션'	=> "구매결정된 주문건에 대해서 정산됩니다.<br/>판매예치금 - 구매결정 즉시<br/>계좌송금 - 구매결정일 +1영업일<br/>구매자가 구매결정을 하지 않을 경우 배송완료 후 8일 지나면 자동 구매결정이 되며 자동구매결정일 익일 정산 됩니다.(취소/반품 진행중인건은 자동구매결정되지 않으며 모든 정산은 영업일 기준에 의합니다.)<br/>옥션 배송비는 송금방식과 무관하게 모두 판매예치금으로 구매결정 또는 반품 승인시점에 즉시 정산됩니다.",
			'지마켓'	=> "구매결정된 주문건에 대해서 정산됩니다.<br/>구매결정일 +1영업일<br/>구매자가 구매결정을 하지 않을 경우 배송완료 후 8일 지나면 자동 구매결정이 되며 자동구매결정일 익일 정산 됩니다.(취소/반품 진행중인건은 자동구매결정되지 않으며 모든 정산은 영업일 기준에 의합니다.)",
			'스마트스토어'	=> "정산방식 : 계좌이체, 정산주기 : 일정산(구매확정 +1영업일)<br/>-신용/체크카드 : 2.86%(중소2)<br/>-계좌이체 : 1.65%<br/>-무통장입금(가상계좌) : 1%(최대 275원)<br/>-휴대폰 결제 : 3.85%<br/>-보조결제(네이버페이 포인트) : 3.74%<br/>스마트스토어 상품을 네이버쇼핑 서비스에 노출하도록 연동시킨 경우 네이버쇼핑에 노출된 상품의 주문/판매가 이루어지면 건당 연동 수수료 2%가 추가 과금됩니다."
		);

		parent::__construct();
	}

	// Destructor
	function __destruct() {

	}

	function dealSumList(){
		global $MENU_ID, $P_ACTION, $PAGE_PATH, $PAGE;
		global $M_FUNC, $M_HTML, $M_SIGNUP;
		
		if($_SESSION['Level'] ==! 1){
			$M_SIGNUP->noExistPage();
		}

		$searchType		= $M_FUNC->M_Filter(GET, "searchType");
		$searchTxt		= $M_FUNC->M_Filter(GET, "searchTxt");
		$searchYear		= $M_FUNC->M_Filter(GET, "searchYear");
		$searchMonth	= $M_FUNC->M_Filter(GET, "searchMonth");
		$PAGE			= $M_FUNC->M_Filter(GET, 'PAGE');

		if($PAGE == '') $PAGE = 1;
		if($searchYear == '') $searchYear = date('Y', time());
		if($searchMonth == '') $searchMonth = date('n', time());
		$month = strlen($searchMonth) == 1 ? "0".$searchMonth : $searchMonth;
		
		if($MENU_ID == "M0301"){
			$addWhere = " AND onoff = 'Off' AND dealType = 1 AND month = ".$searchMonth." ";
		} else {
			$addWhere = " AND onoff = 'On' AND dealType = 1 AND month = ".$searchMonth." ";
		}
		
		if($searchTxt != 0) {
			switch($searchType) {
				case '1' : $addWhere .= ' AND companyName LIKE \'%' . $searchTxt . '%\' '; break;
				default	 : $addWhere .= ' AND companyName LIKE \'%' . $searchTxt . '%\' '; break;
			}
		}
		
		$order = " price desc ";
		//해당 월 업체별 합계
		$dealRow	= $this->getDealSumList($total_page, $NO, $cnt_no, $addWhere, $order, $limit, " companyIdx, month ");
		//위의 해당 월 업체별 합계에 나머지 회사도 빈데이터로 추가
		$dealRow = $this->addRow($dealRow);
		//$dealRow->add(array('month'=>date('m', time()), 'companyIdx' => 40, 'companyName' => '스마트스토어', 'taxYN' => 'Y', 'price' => 0, 'cnt' => 0));

		if($MENU_ID == "M0301"){
			//해당 월의 인수리스트 가져오기
			$takeoverRow = $this->getDealForTakeOver($searchYear, $month);

			//해당 월의 일별 합계,	최이사님으로 인해서 Where에 제외시키기
			$dayPriceRow = $this->priceByDay($addWhere." AND month = ". $searchMonth ." AND companyIdx <> 41 ", " GROUP BY day ");
			include_once $PAGE_PATH . '/dealSumList.html';
		} else {
			//송장 출력건에 대해서 모두 배송중으로 변경하기 위해서 오늘날짜의 데이터 dstart 변경하기
			$this->TermArr[11]		 = array(time(), time());
			//해당 월 택배비 합계 가져오기
			//$deliveryAll = $this->getdealByDelivery($searchYear, $month);
			$deliveryByCompany = $this->getdealByDelivery($searchYear, $month);

			//해당 월의 일별 합계
			$dayPriceRow = $this->priceByDay($addWhere, " GROUP BY day, companyIdx ");
			//회사별 일별 합계 가져온것을 배열로 전환
			$dayPriceArr = array();
			$secendNum = 0;
			$temp = 0;
			for($i=0; $i<$dayPriceRow->size(); $i++){
				$dayPriceRow->next();

				if($secendNum != $dayPriceRow->get('day')){
					$secendNum = $dayPriceRow->get('day');
					$temp = 0;
				} else $temp++;

				$dayPriceArr[$dayPriceRow->get('day')][$temp] = array('name' => $dayPriceRow->get('companyName'), 'price' => $dayPriceRow->get('price'));				
			}

			//금일 출고리스트
			$searchDay = $M_FUNC->M_Filter(GET, "searchDay") == "" ? date('d', time()) : $M_FUNC->M_Filter(GET, "searchDay");
			$deliveryToday = $this->getTodayList($searchDay);

			//판매처 정보 다 가져오기
			$companyArr = $this->getCompanyArr(" and level = 31 and idx <> 42 ");

			include_once $PAGE_PATH . '/OndealSumList.html';
		}
	}

	function dealSumList2(){
		global $MENU_ID, $P_ACTION, $PAGE_PATH, $PAGE;
		global $M_FUNC, $M_HTML, $M_SIGNUP;
		
		if($_SESSION['Level'] ==! 1){
			$M_SIGNUP->noExistPage();
		}

		$searchYear		= $M_FUNC->M_Filter(GET, "searchYear");
		$searchMonth	= $M_FUNC->M_Filter(GET, "searchMonth");

		if($searchYear == '') $searchYear = date('Y', time());
		if($searchMonth == '') $searchMonth = date('n', time());
		$month = strlen($searchMonth) == 1 ? "0".$searchMonth : $searchMonth;
		
		//201907월부터 가능
		$addWhere = " AND onoff = 'On' AND dealType = 1 AND time > 201906310000 ";
		
		//해당 월의 일별 합계
		$dayPriceRow = $this->priceByDay2($addWhere, " GROUP BY LEFT(time, 8), companyIdx ");
		//회사별 일별 합계 가져온것을 배열로 전환
		$dayPriceArr = array();
		$secendNum = 0;
		$temp = 0;
		for($i=0; $i<$dayPriceRow->size(); $i++){
			$dayPriceRow->next();
			
			$day = substr($dayPriceRow->get('day'), 6, 2);
			if($secendNum != $day){
				$secendNum = $day;
				$temp = 0;
			} else $temp++;

			$dayPriceArr[$day][$temp] = array('name' => $dayPriceRow->get('companyName'), 'price' => $dayPriceRow->get('price'));				
		}

		include_once $PAGE_PATH . '/OndealSumList2.html';
	}

	function dealList(){
		global $MENU_ID, $P_ACTION, $PAGE_PATH, $PAGE;
		global $M_FUNC, $M_HTML, $M_SIGNUP, $M_UnitPage;
		
		if($_SESSION['Level'] ==! 1){
			$M_SIGNUP->noExistPage();
		}
		
		$companyIdx		= $M_FUNC->M_Filter(GET, "m_id");
		$year			= $M_FUNC->M_Filter(GET, "year");
		$month			= $M_FUNC->M_Filter(GET, "month");
		$gubun			= $M_FUNC->M_Filter(GET, "gubun");
		$searchTxt		= $M_FUNC->M_Filter(GET, "searchTxt");
		$PAGE			= $M_FUNC->M_Filter(GET, 'PAGE');
		$whereforMonth	= strlen($month) == 1 ? "0".$month : $month;

		if($PAGE == '') $PAGE = 1;
		
		$addWhere = " AND status = 1 AND companyIdx = ". $companyIdx; 
		
		if($searchTxt != ""){
			switch($gubun){
				case 'recipient'	: $addWhere .= " AND recipient like '%".$searchTxt."%' "; break;
				case 'tel'			: $addWhere .= " AND tel like '%".$searchTxt."%' "; break;
				case 'addr'			: $addWhere .= " AND addr like '%".$searchTxt."%' "; break;
				break;
			}
		}	

		//판매처 정보 다 가져오기
		$companyArr = $M_UnitPage->getCompanyArr("", " AND idx = ". $companyIdx);
		
		if($MENU_ID == "M0301"){
			//전월, 명월 이동을 위한 Year, month 셋팅
			$beforY = $month - 1 <= 0 ? $year - 1 : $year;
			$beforM = $month - 1 <= 0 ? 12 : $month-1;
			$afterY = $month + 1 >= 13 ? $year + 1 : $year;
			$afterM = $month + 1 >= 13 ? 1 : $month+1;

			$addWhere .= " AND date like '".$year.$whereforMonth."%'";
			$dealRow	= $this->getDealList($total_page, $NO, $cnt_no, $addWhere, "date desc, idx desc", "NO");
			include_once $PAGE_PATH . '/dealList.html';
		} else {
			$addWhere .= " AND time like '".$year.$whereforMonth."%'";
			$dealRow	= $this->getOnDealList($total_page, $NO, $cnt_no, $addWhere, "date desc, idx desc", "NO");
			include_once $PAGE_PATH . '/OndealList.html';
		}
	}

	function dealRV($mode="regist"){
		global $MENU_ID, $P_ACTION, $PAGE_PATH, $PAGE;
		global $M_FUNC, $M_HTML, $M_SIGNUP, $M_UnitPage;

		if($_SESSION['Level'] != 1){
			$M_SIGNUP->noExistPage();
		}

		$m_id = $M_FUNC->M_Filter(GET, "m_id");
		$companyIdx = $M_FUNC->M_Filter(GET, "companyIdx");	
		if($companyIdx == "") $companyIdx = 0;
		$date		= $M_FUNC->M_Filter(GET, "date");
		if($date == ""){
			$year		= $M_FUNC->M_Filter(GET, "year");
			$month		= $M_FUNC->M_Filter(GET, "month");
		} else {
			$year		= date('Y', strtotime($date));
			$month		= date('n', strtotime($date));
		}

		if($mode == "view"){
			$action = "?M0301U";

			$addWhere = " AND d.idx = ". $m_id ." AND d.companyIdx = ". $companyIdx;

			$dealViewRow = $this->dealViewByidx($year, $addWhere);
			$dealViewRow->next();
			$takeover = $dealViewRow->get('takeover');
			$goodsNum = explode("|", $dealViewRow->get('dealNum'));
		} else {
			//등록	level == 11인 판매처를 가져오기
			//수정	상품단가만 수정할 수 있게
			$action = "?M0301P";

			$goodsNum = array();
			$dealViewRow = new L_ListSet();
		}

		//판매처 정보 다 가져오기
		$companyArr = $M_UnitPage->getCompanyArr(array(0=>'판매처 선택')," and level = 11 ");

		include_once $PAGE_PATH . '/dealRV.html';
	}
	
	function dealDayList(){
		global $MENU_ID, $P_ACTION, $PAGE_PATH, $PAGE;
		global $M_FUNC, $M_HTML, $M_SIGNUP, $M_UnitPage;

		if($_SESSION['Level'] != 1){
			$M_SIGNUP->noExistPage();
		}

		$date		= $M_FUNC->M_Filter(GET, "date");
		if($date == ""){
			$year		= $M_FUNC->M_Filter(GET, "year");
			$month		= $M_FUNC->M_Filter(GET, "month");
		} else {
			$year		= date('Y', strtotime($date));
			$month		= date('n', strtotime($date));
		}
		
		//유한실업_최 제외시키기(20.03.26 다시 포함시키기)
		$addWhere = " AND d.onoff ='Off' AND d.date = ". $date ." AND d.companyIdx <> 41 ";
		//$addWhere = " AND d.onoff ='Off' AND d.date = ". $date;
		$dealDayRow = $this->dealViewByDay($year, $addWhere);

		//판매처 정보 다 가져오기
		$companyArr = $M_UnitPage->getCompanyArr(array(0=>'판매처 선택')," and level = 11 ");

		include_once $PAGE_PATH . '/dealDayList.html';
	}

	function dealInsertProc() {
		global $MENU_ID, $P_ACTION, $M_FUNC, $M_HTML, $PAGE_PATH;
		global $db;
		
		$date1		= $M_FUNC->M_Filter(POST, "date");
		//date형식 재정의
		$date		= date("Ymd",strtotime($date1));
		$year		= (int)substr($date,0, 4);
		$month		= (int)substr($date, 4, 2);
		$companyIdx	= $M_FUNC->M_Filter(POST, 'companyIdx');
		
		$this->InsertDealData();

		M_JS::Go_URL('/?'. $MENU_ID . 'L&m_id='.$companyIdx.'&year='.$year.'&month='.$month, "등록되었습니다.");	
	}

	function dealUpdateProc() {
		global $MENU_ID, $P_ACTION, $M_FUNC, $M_HTML, $PAGE_PATH;
		global $db;
		
		$m_id			= $M_FUNC->M_Filter(POST, "m_id");
		$companyIdx		= $M_FUNC->M_Filter(POST, "companyIdx");
		$date			= $M_FUNC->M_Filter(POST, "date");
		
		$this->UpdateDealData();

		M_JS::Go_URL('/?'. $MENU_ID . 'V&m_id='.$m_id.'&companyIdx='.$companyIdx.'&date='.$date, "수정되었습니다.");	
	}

	function dealPrint(){
		global $MENU_ID, $P_ACTION, $PAGE_PATH, $PAGE;
		global $M_FUNC, $M_HTML, $M_SIGNUP, $M_UnitPage;

		if($_SESSION['Level'] != 1){
			$M_SIGNUP->noExistPage();
		}

		$m_id = $M_FUNC->M_Filter(GET, "m_id");
		$companyIdx = $M_FUNC->M_Filter(GET, "companyIdx");	
		if($companyIdx == "") $companyIdx = 0;

		$date		= $M_FUNC->M_Filter(GET, "date");
		$year		= date('Y', strtotime($date));
		$month		= date('n', strtotime($date));

		//정산을 위해 accountIdx를 가져오는 부분
		$accountIdx = $this->getAccountByIdx($companyIdx);

		$addWhere = " AND d.idx = ". $m_id ." AND d.companyIdx = ". $companyIdx;

		$dealViewRow = $this->dealViewByidx($year, $addWhere);
		$dealViewRow->next();

		//판매처 정보 다 가져오기
		$companyArr = $M_UnitPage->getCompanyArr(array(0=>'판매처 선택')," and level = 11 ");
		
		/******************************************************************************
			미수금 구하기 ===> Cash_Info를 통해서 구하기
			해당 매출 건이 인수 확인이 안된 상태이면 --------> Cash_Info에 인수, 정산 가격 합쳐서 가져오기
			해당 매출 건이 인수 확인이 된 상태이면   --------> 우선 동일한 날이 건이 1개인지 아닌지 확인 후 
				1개이면 전달부터 오늘 전날까지 인수, 정산 가격 합쳐서 가져오기
				1개이상이면 인수 안된상태와 같은 조건에서 dealIdx가 자신보다 작은걸로해서 가져오기
		*******************************************************************************/
		if($dealViewRow->get('takeover') == 0){
			$rWehre = " AND companyIdx=". $companyIdx ." AND onoff='off' ";
		} else {
			//해당 판매처의 
			$count = $this->getCountByCash(" AND companyIdx = ".$companyIdx." AND date = ". $date);
			
			if($count > 1){
				$rWehre = " AND companyIdx=". $companyIdx ." AND onoff='off' AND ( date <= 20191231 OR dealIdx < ".$m_id." ) ";
			} else {
				//이월하는 거래처로 인하여 5개월으로 해서 합계
				$lastMonth = (int)$date-(399+date('d', strtotime($date)));
				$yesterday = (int)$date-1;
				$rWehre = " AND companyIdx=". $companyIdx ." AND onoff='off' AND date < ". $date;
				//$rWehre = " AND companyIdx=". $companyIdx ." AND onoff='off' AND date between ". $lastMonth ." AND ". $yesterday;
			}
		}
		
		$RPrice = $this->ReceivablesByPrice($rWehre);	//미수금가져오기

		include_once $PAGE_PATH . '/dealPrint.html';
	}

	function dealTakeOver(){
		global $MENU_ID, $P_ACTION, $M_FUNC, $M_HTML, $PAGE_PATH;
		global $db;
		
		$m_id			= $M_FUNC->M_Filter(POST, "m_id");
		$companyIdx		= $M_FUNC->M_Filter(POST, "companyIdx");
		$date			= $M_FUNC->M_Filter(POST, "date");
		
		$this->InsertTakeOverData();
		
		M_JS::Go_URL('/?'. $MENU_ID . 'V&m_id='.$m_id.'&companyIdx='.$companyIdx.'&date='.$date, "인수처리 되었습니다.");	
	}

	function ondealRV($mode="regist"){
		global $MENU_ID, $P_ACTION, $PAGE_PATH, $PAGE;
		global $M_FUNC, $M_HTML, $M_SIGNUP, $M_UnitPage;

		if($_SESSION['Level'] != 1){
			$M_SIGNUP->noExistPage();
		}

		$m_id = $M_FUNC->M_Filter(GET, "m_id");
		$companyIdx = $M_FUNC->M_Filter(GET, "companyIdx");	
		if($companyIdx == "") $companyIdx = 0;
		$date		= $M_FUNC->M_Filter(GET, "date");
		if($date == ""){
			$year		= $M_FUNC->M_Filter(GET, "year");
			$month		= $M_FUNC->M_Filter(GET, "month");
		} else {
			$year		= date('Y', strtotime($date));
			$month		= date('n', strtotime($date));
		}

		if($mode == "view"){
			$action = "?M0401U";

			$addWhere = " AND d.idx = ". $m_id ." AND d.companyIdx = ". $companyIdx;

			$dealViewRow = $this->dealViewByidx($year, $addWhere);
			$dealViewRow->next();
			$goodsNum = explode("|", $dealViewRow->get('dealNum'));
		} else {
			$action = "?M0401P";
			$small = 0;
			$medium = 0;
			$large = 0;
			$buyDay = date('Ym', time());

			$goodsNum = array();
			$dealViewRow = new L_ListSet();
		}

		//판매처 정보 다 가져오기
		$companyArr = $M_UnitPage->getCompanyArr(''," and level = 31 ");

		include_once $PAGE_PATH . '/OndealRV.html';
	}

	function ondealInsertProc() {
		global $MENU_ID, $P_ACTION, $M_FUNC, $M_HTML, $PAGE_PATH;
		global $db;
		
		$this->InsertOnDealData();
		
		M_JS::Go_URL('/?'. $MENU_ID . 'S', "등록되었습니다.");	
	}

	function ondealUpdateProc() {
		global $MENU_ID, $P_ACTION, $M_FUNC, $M_HTML, $PAGE_PATH;
		global $db;
		
		$m_id			= $M_FUNC->M_Filter(POST, "m_id");
		$companyIdx		= $M_FUNC->M_Filter(POST, "companyIdx");
		$date			= $M_FUNC->M_Filter(POST, "date");
		
		$this->UpdateOnDealData();
		
		M_JS::Go_URL('/?'. $MENU_ID . 'V&m_id='.$m_id.'&companyIdx='.$companyIdx.'&date='.$date, "수정되었습니다.");	
	}

	function Delivery(){
		global $PAGE_PATH, $MENU_ID, $P_ACTION, $PAGE;
		global $M_FUNC, $M_HTML;
		
		$searchDelivery = $M_FUNC->M_Filter(GET, "searchDelivery");
		$searchTerm = $M_FUNC->M_Filter(GET, "searchTerm") == "" ? 1 : $M_FUNC->M_Filter(GET, "searchTerm");
		$searchCompany = $M_FUNC->M_Filter(GET, "searchCompany") == "" ? 0 : $M_FUNC->M_Filter(GET, "searchCompany");
		$date = $M_FUNC->M_Filter(GET, "date") == "" ? date('Ymd', $this->TermArr[$searchTerm][0]) : $M_FUNC->M_Filter(GET, "date");
		
		//table 년도 가져와야한다. 나중에 만약 2019, 2020 가져올때도 나중에 만들기(조인해서 가져오기)
		$year = substr($date, 0, 4);

		//date가 있을땐 $this->searchTerm에 11 => '특정일 : 2019-05-20 ' 넣기
		$this->searchTerm[11]	 = '사용자 지정일 : '. date('Y-m-d', strtotime($date)) .' ~ '. date('Y-m-d', strtotime($date));
		$this->TermArr[11]		 = array(strtotime($date), strtotime($date));
		
		//배송중인걸 확인하는거므로 오늘 발송한거는 제외
		$addWhere = "";
		//$addWhere = " AND date <> ". date('Ymd', time()) ." ";
		if($searchTerm != 7){
			if($searchTerm == 4) {
				$addWhere .= " AND date <> ". date('Ymd', time()) ." ";
			} 
			
			$addWhere .= " AND onoff = 'On' AND date between ". date('Ymd', $this->TermArr[$searchTerm][0]) ." AND ". date('Ymd', $this->TermArr[$searchTerm][1]);
		} else {
			$addWhere .= " AND onoff = 'On' AND date between ". date('Ymd', $this->TermArr[$searchTerm][0]) ." AND ". date('Ymt', $this->prev_month);
			
			$year = date('Y', $this->TermArr[$searchTerm][0]);
		}

		switch($searchDelivery){
			case 0 : $addWhere	.= " "; break;
			case 1 : $addWhere	.= " AND dstart > 0 AND dend = 0 "; break;
			case 4 : $addWhere	.= " AND dstart = 0 AND dend = 0 "; break;
			case 11 : $addWhere .= " AND dstart > 0 AND dend > 0 "; break;
		}

		if($searchCompany != 0){
			$addWhere .= " AND companyIdx = ". $searchCompany;
		}

		//온라인 판매처 가져오기
		include_once COMMON_CLASS . "/class.account.php";
		$M_ACCOUNT = new M_ACCOUNT;
		$companyArr = $M_ACCOUNT->getCompanyArr(" AND onoff = 'On' ");
		
		$table = "Deal_".$year;
		$deliveryRow = $this->getDeliveryList($table, $addWhere);
		
		include_once $PAGE_PATH . '/delivery.html';
	}

	function checkList($onoff = "on"){
		global $MENU_ID, $P_ACTION, $PAGE_PATH, $PAGE;
		global $M_FUNC, $M_HTML, $M_SIGNUP, $M_UnitPage;

		if($_SESSION['Level'] != 1){
			$M_SIGNUP->noExistPage();
		}

		$date		= $M_FUNC->M_Filter(GET, "date");
		if($date == ""){
			$year		= $M_FUNC->M_Filter(GET, "year");
			$month		= $M_FUNC->M_Filter(GET, "month");
		} else {
			$year		= date('Y', strtotime($date));
			$month		= date('n', strtotime($date));
		}
		
		//유한실업_최 제외시키기
		$addWhere = " AND d.onoff ='Off' AND d.date = ". $date ." AND d.companyIdx <> 41 ";
		$dealDayRow = $this->dealViewByDay($year, $addWhere);

		//판매처 정보 다 가져오기
		$companyArr = $M_UnitPage->getCompanyArr(array(0=>'판매처 선택')," and level = 11 ");

		include_once $PAGE_PATH . '/dealDayList.html';
	}

	function adList(){
		global $MENU_ID, $P_ACTION, $PAGE_PATH, $PAGE;
		global $M_FUNC, $M_HTML, $M_SIGNUP, $M_UnitPage;
		
		$searchCash		= $M_FUNC->M_Filter(GET, "searchCash");
		$searchCompany	= $M_FUNC->M_Filter(GET, "searchCompany");
		$searchTerm		= $M_FUNC->M_Filter(GET, "searchTerm");
		$PAGE			= $M_FUNC->M_Filter(GET, 'PAGE');
		if($searchTerm == '')		$searchTerm = 4;
		if($PAGE == '')				$PAGE = 1;

		include_once ADMIN_CLASS_PATH . '/class.accountPage.php';
		$M_AccountPage = new M_AccountPage;
		
		$cashArr = array(0=>'전체', 2=>'정산', 3=>'광고비');
		//검색을 위한 거래처 가져오기, 온라인 개별거래 제외
		$companyArr = $M_AccountPage->getCompanyArr(" AND idx not in (42) AND onoff = 'On' ");
		
		$addWhere = " AND onoff = 'On' ";
		if($searchCash == 0){
			$addWhere .= " AND cashType in (2, 3) ";
		} else if($searchCash == 2){
			$addWhere .= " AND cashType in (1, 2) ";
		} else {
			$addWhere .= " AND cashType = ". $searchCash;
		}
		if($searchCompany != 0){
			$addWhere .= " AND companyIdx = ".$searchCompany;
		}
		$addWhere .= " AND date between ". date('Ymd', strtotime($this->TermArr[$searchTerm][0])) ." AND ". date('Ymd', strtotime($this->TermArr[$searchTerm][1]))." ";

		$URLParam = "&searchTerm=".$searchTerm;
		
		$cashRow	= $M_AccountPage->getCashList($total_page, $NO, $cnt_no, $addWhere, " date desc ", $limit);

		include_once $PAGE_PATH . '/adList.html';
	}

	function afterTheTransaction(){
		global $PAGE_PATH, $MENU_ID, $P_ACTION;
		global $M_HTML, $M_FUNC, $PAGE;
		
		$searchCompany	= $M_FUNC->M_Filter(GET, 'searchCompany');
		$year		= $M_FUNC->M_Filter(GET, 'year');

		include_once ADMIN_CLASS_PATH . '/class.accountPage.php';
		$M_AccountPage = new M_AccountPage;
		
		//검색을 위한 거래처 가져오기, 온라인 개별거래 제외
		$companyArr = $M_AccountPage->getCompanyArr(" AND onoff = 'On' ");
		$companyArr[0] = "온라인 선택";
		$companyArr[1] = "전체";
		ksort($companyArr);

		//온라인 거래처별 수수료
		$commissionArr = $M_AccountPage->getCompanyByCommission();

		if($searchCompany && $year){
			if($searchCompany == 1) $addWhere = " AND onoff = 'On' AND dend > 0 AND receive = 0 ";
			else $addWhere = " AND onoff = 'On' AND companyIdx = ".$searchCompany." AND dend > 0 AND receive = 0 ";

			$transactionRow = $this->getDealByOnline($total_page, $NO, $cnt_no, $addWhere, " date asc ", $limit);
		} else {
			$transactionRow = new L_ListSet();
		}

		$URLParam = "&year=".$year."&searchCompany=".$searchCompany;

		include_once $PAGE_PATH . '/afterTheTransaction.html';
	}

	function DelPrcieByMonth(){
		global $PAGE_PATH;
		global $M_HTML, $M_FUNC;
		
		$searchYear = $M_FUNC->M_Filter(GET, "searchYear") == "" ? date('Y', time()) : $M_FUNC->M_Filter(GET, "searchYear");

		$deliveryRow = $this->getDelPriceByMonth($searchYear);

		include_once $PAGE_PATH . '/delPriceByMonth.html';
	}

	function uploadExcel(){
		global $PAGE_PATH, $MENU_ID, $P_ACTION;
		global $M_FUNC, $M_HTML, $M_UnitPage, $db;

		$gubun = $M_FUNC->M_Filter(GET, 'gubun');

		//판매처 정보 다 가져오기
		$companyArr = $M_UnitPage->getCompanyArr(array()," and level = 31 and idx not in (42) ");

		if($gubun == 'read'){
			include_once $PAGE_PATH . '/excel_read.php';
			M_JS::Go_URL('/?'. $MENU_ID."I", "대량등록 되었습니다.");
		} else {
			include_once $PAGE_PATH . '/OndealRV_excel.html';
		}
	}

	function readExcel(){
		global $MENU_ID, $P_ACTION, $M_FUNC, $M_HTML, $PAGE_PATH;
		
		ini_set("memory_limit" , -1);
		ini_set('max_execution_time',300);

		$fileData	=	$this->readExcel();
		echo "<pre>";
		print_r($fileData);
		exit;
		$this->insertExcelData();
		
		M_JS::Go_URL('/?'. $MENU_ID."I", "대량등록 되었습니다.");
	}

	function Upload(){
		global $PAGE_PATH;

		include_once $PAGE_PATH . '/excel_read.php';
	}

	function ReadDelivery(){
		global $PAGE_PATH, $MENU_ID;
		global $M_FUNC, $M_HTML, $M_UnitPage, $db;

		$gubun = $M_FUNC->M_Filter(GET, 'gubun');

		//판매처 정보 다 가져오기
		$companyArr = $M_UnitPage->getCompanyArr(array()," and level = 31 and idx not in (42) ");

		if($gubun == 'read'){
			include_once $PAGE_PATH . '/delivery_read.php';
			M_JS::Go_URL('/', "배송완료 처리되었습니다.");
		} else {
			include_once $PAGE_PATH . '/OndealRV_excel.html';
		}
	}

	function TEST(){
		global $PAGE_PATH, $MENU_ID, $P_ACTION;
		global $M_HTML, $M_FUNC, $PAGE;
		
		$test = $this->del();

		include_once $PAGE_PATH . '/test.html';
	}

	function deliveryResult(){
		global $PAGE_PATH, $MENU_ID, $P_ACTION;
		global $M_HTML, $M_FUNC, $PAGE;

		$searchYear		= $M_FUNC->M_Filter(GET, "searchYear");
		$searchMonth	= $M_FUNC->M_Filter(GET, "searchMonth");

		if($searchYear == '') $searchYear = date('Y', time());
		if($searchMonth == '') $searchMonth = date('n', strtotime('-1 month'));
		$queryMonth		= $searchMonth < 10 ? "0".$searchMonth : $searchMonth;
		
		$addWhere = " AND onoff = 'On' AND status = 1 AND date like '".$searchYear.$queryMonth."%' ";
		$order = " date asc ";
		$deliveryRow	= $this->getDeliveryResult($addWhere, $order);
		//위의 해당 월 업체별 합계에 나머지 회사도 빈데이터로 추가

		include_once $PAGE_PATH . '/deliveryResult.html';
	}

	function deliverySetting(){
		global $PAGE_PATH, $MENU_ID, $P_ACTION;
		global $M_HTML, $M_FUNC, $PAGE;

		$time = date('H', time());
		$dayOfweek = date('N', time());
		if($dayOfweek == 6) {
			$date = date('Ymd', strtotime('+2 day', time()));
		} elseif ($dayOfweek == 7){
			$date = date('Ymd', strtotime('+1 day', time()));
		} else {
			if($time > 17) $date = date('Ymd', strtotime('+1 day', time()));
			else $date = date('Ymd', time());
		}

		$addWhere = " AND onoff = 'On' AND status = 1 AND date = ".$date." AND dstart = 0 AND priceKinds like '%|||' ";
		$deliveryRow	= $this->getBeforeShipping($addWhere);

		include_once $PAGE_PATH . '/deliverySetting.html';
	}

	function deliveryUpdate(){
		global $MENU_ID, $P_ACTION, $M_FUNC, $M_HTML, $PAGE_PATH;
		global $db;

		$this->UpdateDeliveryData();
		
		M_JS::Go_URL('/?M0403&searchDelivery=0&searchTerm=11&date='.date('Ymd', time()), "수정되었습니다.");	
	}

	function dealUpdateByStatus(){
		global $PAGE_PATH, $MENU_ID, $P_ACTION, $PAGE;
		global $M_FUNC, $M_HTML;
		
		$searchUser = $M_FUNC->M_Filter(GET, "searchUser");
		$searchUserName = $M_FUNC->M_Filter(GET, "searchUserName");
		$searchYear = $M_FUNC->M_Filter(GET, "searchYear") == "" ? date('Y', time()) : $M_FUNC->M_Filter(GET, "searchYear");
		
		$addWhere = " ";
		//검색전에는 데이터 안가져오기
		if($searchUserName == ""){
			$dealRow = new L_ListSet();			
		} else {
			if($searchUser == 'all'){
				$addWhere .= " AND ( buyer like '%".$searchUserName."%' OR recipient like '%".$searchUserName."%' ) ";
			} elseif($searchUser == 'recipient') {
				$addWhere .= " AND recipient like '%".$searchUserName."%' ";
			} else {
				$addWhere .= " AND buyer like '%".$searchUserName."%' ";
			}			
			
			$table = "Deal_".$searchYear;
			$dealRow = $this->getDeliveryList($table, $addWhere);
		}
				
		include_once $PAGE_PATH . '/dealUpdateByStatus.html';
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




	
	function companyUpdateProc() {
		global $MENU_ID, $P_ACTION, $M_FUNC, $M_HTML, $PAGE_PATH;
		global $db;
		
		$m_id = $M_FUNC->M_Filter(POST, 'm_id');
		$companyIdx = $M_FUNC->M_Filter(POST, 'companyIdx');
		$addr = $M_FUNC->M_Filter(POST, 'addr');
		$tel = $M_FUNC->M_Filter(POST, 'tel');
		$email = $M_FUNC->M_Filter(POST, 'email');
		$status = $M_FUNC->M_Filter(POST, 'status');
		
		$data = array(
			'addr'		=> $addr,
			'tel'		=> $tel,
			'email'		=> $email,
			'status'	=> $status
		);

		$msg = '계정을 수정하였습니다.';
		$this->UpdateCompanyData($companyIdx, $data);

		M_JS::Go_URL('/?'. $MENU_ID . 'V&m_id=' . $m_id , $msg);
	}

}

?>