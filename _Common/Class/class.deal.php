<?php
/*********************************************************************
*    Description	:	Class for User page
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2012. 05. 15
*    Have a nice day, Good Luck to you ^^/
*********************************************************************/
class M_DEAL {
	function __construct() {
		$this->User_search = array(
			'1' => '아이디',
			'2' => '이름',
			'3' => '관리자분류',
		);
		$this->User_level = array(
			'1' => '관리자',
			'11' => '판매처',
			'21' => '매입처',
			'31' => '인터넷',
			'41' => '택배사'
		);
		$this->DEAL_search = array(
			'0'	=> '검색분류선택',
			'1' => '거래처명'
		);
		$this->Month_array = array(
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
			12 => '12월',
		);
	}

	function __destruct() {
		
	}

	function getAccountByIdx($companyIdx){
		global $db;

		$query = " SELECT * "
				." FROM Account_Info "
				." WHERE 1 = 1 AND status = 1 AND roleIdx = ".$companyIdx
				;
		$row = $db->getListSet($query);

		$row->next();

		return $row->get('idx');
	}

	function getCompanyArr($addWhere){
		global $db;

		$query = " SELECT idx, companyName "
				." FROM Company_Info "
				." WHERE 1 = 1 AND status = 1 "
				.$addWhere
				." ORDER BY idx asc "
				;
		$row = $db->getListSet($query);
		
		$arr = array();
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			$arr[$row->get('idx')]	= $row->get('companyName');
		}

		return $arr;
	}

	function getDealSumList(&$total_page, &$NO, &$cnt_no, $addWhere = null, $order = null, $limit = null, $groupby = null){
		global $db, $M_FUNC;
		global $PAGE;
		
		if($PAGE < 1) $PAGE = 1 ;
		$start_row = ($PAGE - 1) * C_LIST_CNT3;
		$where = " WHERE 1 = 1 ";
		$colum = "";
		
		if ($addWhere) 
		{
			$where .= $addWhere;
		}

		if ($order == "") 
		{
			$orderQuery = " ORDER BY idx DESC ";
		}
		else 
		{
			$orderQuery = " ORDER BY ". $order;
		}
		
		if ($limit == "NO") 
		{
			$limitQuery = "";
		} 
		else 
		{
			$limitQuery = " LIMIT ". $start_row .", ". C_LIST_CNT3;
		}

		if($groupby == ""){
			$groupbyQuery == "";
		} else {
			$groupbyQuery = "GROUP BY ".$groupby;
			$colum .= ", count(*) as cnt ";
		}

		//해당월 테이블을 가져오기 위한
		$searchYear		= $M_FUNC->M_Filter(GET, "searchYear");
		if($searchYear == '') $searchYear = date('Y', time());
		$table = " DealSum_".$searchYear;

		$query = " SELECT month, companyIdx, companyName, taxYN, sum(PriceSum) as price "
					. $colum
					. " FROM ".$table
					. $where
					. $groupbyQuery
					. $orderQuery
					. $limitQuery;
		$row = $db->getListSet($query);
		
		return $row;		
	}

	function priceByDay($addWhere, $groupBy){
		global $db, $M_FUNC;

		$where = " WHERE 1 = 1 ";

		//해당월 테이블을 가져오기 위한
		$searchYear		= $M_FUNC->M_Filter(GET, "searchYear");
		if($searchYear == '') $searchYear = date('Y', time());
		$table = " DealSum_".$searchYear;

		$query = " SELECT day, sum(PriceSum) as price, companyName "
				." FROM ".$table
				. $where . $addWhere
				. $groupBy;
		$row = $db->getListSet($query);
		
		return $row;
	}

	function priceByDay2($addWhere, $groupBy){
		global $db, $M_FUNC;

		$where = " WHERE 1 = 1 ";

		//해당월 테이블을 가져오기 위한
		$searchYear		= $M_FUNC->M_Filter(GET, "searchYear");
		if($searchYear == '') $searchYear = date('Y', time());
		$table = DEAL_TABLE.$searchYear;

		$query = " SELECT LEFT(time, 8) as day, MID(time, 8, 2) as time, priceKinds as price, companyName "
				." FROM ".$table
				. $where . $addWhere
				. $groupBy;
		$row = $db->getListSet($query);
		
		return $row;
	}

	function getDealList(&$total_page, &$NO, &$cnt_no, $addWhere = null, $order = null, $limit = null){
		global $db, $M_FUNC;
		global $PAGE;
		
		if($PAGE < 1) $PAGE = 1 ;
		$start_row = ($PAGE - 1) * C_LIST_CNT3;
		$where = " WHERE 1 = 1 ";
		
		if ($addWhere) 
		{
			$where .= $addWhere;
		}

		if ($order == "") 
		{
			$orderQuery = " ORDER BY idx DESC ";
		}
		else 
		{
			$orderQuery = " ORDER BY ". $order;
		}
		
		if ($limit == "NO") 
		{
			$limitQuery = "";
		} 
		else 
		{
			$limitQuery = " LIMIT ". $start_row .", ". C_LIST_CNT3;
		}

		//해당월 테이블을 가져오기 위한
		$year		= $M_FUNC->M_Filter(GET, "year");
		if($year == '') $year = date('Y', time());
		$table = DEAL_TABLE.$year;

		$query = " SELECT idx, date, deal, companyIdx, dealNum, takeover, onoff "
					. " FROM ".$table
					. $where
					. $orderQuery
					. $limitQuery;
		$row = $db->getListSet($query);

		$cnt_no = $db->cnt($table, $where);
		$total_page = (int)(($cnt_no - 1) / C_LIST_CNT) + 1;
		$NO = $cnt_no - $start_row;
		
		return $row;		
	}

	function dealViewByidx($year, $addWhere){
		global $db;

		$query = " SELECT d.idx, d.date, d.companyIdx, d.companyName, d.deal, d.dealNum, c.addr, c.tel, d.onoff, d.takeover "
				." FROM ".DEAL_TABLE.$year ." d "
				."	INNER JOIN Company_Info c on d.companyIdx = c.idx "
				." WHERE 1 = 1 "
				. $addWhere;
		$row = $db->getListSet($query);
		
		return $row;
	}

	function dealViewByDay($year, $addWhere){
		global $db;

		$query = " SELECT d.idx, d.date, d.companyIdx, d.companyName, d.deal, d.dealNum, c.addr, c.tel, d.onoff, d.takeover "
				." FROM ".DEAL_TABLE.$year ." d "
				."	INNER JOIN Company_Info c on d.companyIdx = c.idx "
				." WHERE 1 = 1 "
				. $addWhere;
		$row = $db->getListSet($query);
		
		return $row;
	}

	function InsertDealData(){
		global $db, $M_FUNC, $M_CRUMB, $M_JS, $_SESSION, $M_FILE, $M_MEMBER, $M_UnitPage;
		global $MENU_ID, $SearchName;
		
		//Deal_2019 거래명세표 등록
		//Deal_2019 분석 후 DealSum_2019 등록
		$level		= $M_FUNC->M_Filter(POST, "level");
		$p_action	= $M_FUNC->M_Filter(POST, "p_action");
		$date1		= $M_FUNC->M_Filter(POST, "date");
		//date형식 재정의
		$date		= date("Ymd",strtotime($date1));
		$year		= substr($date, 0, 4);
		$month		= substr($date, 4, 2);
		$day		= substr($date, 6, 2);
		$companyIdx = $M_FUNC->M_Filter(POST, 'companyIdx');

		if($level == ""){
			$message = "잘못된 경로로 들어온 경우입니다.\\n다시 등록하시기 바랍니다.";
			$url = "/?M0301L";
			$M_JS->Go_URL($url, $message, "parent");
			exit;
		}

		//상품별 단가, 갯수 가져오기
		$goodsNum = "";
		$goodsSum = "";
		$goodsPriceSum = 0;
		$goodsMax = sizeof($M_UnitPage->goodsNum_Arr);
		for($i=0; $i<$goodsMax; $i++){
			$num = $M_FUNC->M_Filter(POST, "num".$i);
			$price = $M_FUNC->M_Filter(POST, $M_UnitPage->goodsNum_Arr[$i]['num']);
			$goodsNum .= $num == "" ? 0 : $num;
			$goodsSum .= (int)$price * $num;
			$goodsPriceSum += (int)$price * $num;

			if($i != $goodsMax-1){
				$goodsNum	.= "|";
				$goodsSum	.= "|";
			}
		}
		
		//회사명가져오기
		$companyArr = $M_UnitPage->getCompanyArr("", " AND idx = ". $companyIdx);

		$deal_data = array(
			'date'			=> $date,
			'companyIdx'	=> $companyIdx,
			'companyName'	=> $companyArr[$companyIdx],
			'dealType'		=> 1,
			'onoff'			=> 'Off',
			'taxYN'			=> 'Y',
			'deal'			=> $goodsSum,
			'dealNum'		=> $goodsNum,
			'deliveryYN'	=> "N",
			'calculateDate'	=> 0,
			'depositYN'		=> 'N',
			'status'		=> 1,
			'regUnixtime'	=> time()
		);

		//Deal_2019 거래명세표 등록
		$db->insert(DEAL_TABLE.$year, $deal_data);
		$dealIdx = $db->getInsertID();

		$dealSum_date = array(
			'dealIdx'		=> $dealIdx,
			'companyIdx'	=> $companyIdx,
			'month'			=> $month,
			'day'			=> $day,
			'companyName'	=> $companyArr[$companyIdx],
			'PriceSum'		=> $goodsPriceSum,
			'taxYN'			=> 'Y',
			'regUnixtime'	=> time()
		);

		//Deal_2019 분석 후 DealSum_2019 등록
		$db->insert("DealSum_".$year, $dealSum_date);

		//송장내용 인수하기
		//Cash_Info에 인수하는 내용 입력
		$cash_data = array(
			'dealIdx'		=>	$dealIdx,
			'companyIdx'	=>	$companyIdx,
			'date'			=>	$date,
			'onoff'			=>	'Off',
			'cashType'		=>	1,		// 1:인수, 2:정산, 3:전미수금
			'price'			=>	$goodsPriceSum,
			'regUnixtime'	=>	time()
		);

		$db->insert("Cash_Info", $cash_data);

		//인수확인 후 Deal_20xx에서 해당데이터 인수확인 된 내용 update
		$update_data = array(
			'takeover'	=> 1
		);
		$db->update(DEAL_TABLE.$year, $update_data, " WHERE idx = ". $dealIdx ." AND companyIdx = ". $companyIdx);
	}

	function UpdateDealData(){
		global $db, $M_FUNC, $M_CRUMB, $M_JS, $_SESSION, $M_FILE, $M_MEMBER, $M_UnitPage;
		global $MENU_ID, $SearchName;
		
		//Deal_2019 deal내용, dealNum 내용 수정
		//Deal_2019 분석 후 DealSum_2019 Price값 수정
		$level		= $M_FUNC->M_Filter(POST, "level");
		$m_id		= $M_FUNC->M_Filter(POST, "m_id");
		$companyIdx	= $M_FUNC->M_Filter(POST, "companyIdx");
		$date		= $M_FUNC->M_Filter(POST, "date");
		$year		= date('Y', strtotime($date));
		$month		= date('n', strtotime($date));
		$day		= date('j', strtotime($date));

		if($level == ""){
			$message = "잘못된 경로로 들어온 경우입니다.\\n다시 등록하시기 바랍니다.";
			$url = "/?M0301S";
			$M_JS->Go_URL($url, $message, "parent");
			exit;
		}

		//상품별 단가, 갯수 가져오기
		$goodsNum = "";
		$goodsSum = "";
		$goodsPriceSum = 0;
		$goodsMax = sizeof($M_UnitPage->goodsNum_Arr);
		for($i=0; $i<$goodsMax; $i++){
			$price = $M_FUNC->M_Filter(POST, $M_UnitPage->goodsNum_Arr[$i]['num']);
			$num = $M_FUNC->M_Filter(POST, "num".$i);

			$goodsNum .= $num == "" ? 0 : $num;
			$goodsSum .= (int)$price * $num;
			$goodsPriceSum += (int)$price * $num;

			if($i != $goodsMax-1){
				$goodsNum	.= "|";
				$goodsSum	.= "|";
			}
		}

		$deal_data = array(
			'date'			=> $date,
			'deal'			=> $goodsSum,
			'dealNum'		=> $goodsNum
		);

		$dealSum_date = array(
			'PriceSum'		=> $goodsPriceSum
		);

		//Deal_2019 거래명세표 수정
		$dealWhere = " WHERE idx = ".$m_id." AND companyIdx = ".$companyIdx;
		$db->update(DEAL_TABLE.$year, $deal_data, $dealWhere);

		//Deal_2019 분석 후 DealSum_2019 수정
		if($year >= 2020 && $m_id > 2403){
			$dealSumWhere = " WHERE dealIdx = ".$m_id." AND companyIdx = ".$companyIdx." AND month = ".$month." AND day = ".$day;
		} else {
			$dealSumWhere = " WHERE companyIdx = ".$companyIdx." AND month = ".$month." AND day = ".$day;
		}
		$db->update("DealSum_".$year, $dealSum_date, $dealSumWhere);

		//Cash_Info에서 인수내용도 수정하기
		$cash_data = array(
			'price'			=>	$goodsPriceSum
		);

		$db->update("Cash_Info", $cash_data, " WHERE companyIdx = ".$companyIdx." AND dealIdx = ".$m_id." AND date = ".$date);
	}
	

	/******************************************************************************
	***		Cash_Info cashType 1:구매, 2:정산, 3:전미수금								***
	***		1. 상품 구매시 cashType 1로 등록											***
	***		2. 정산 시 cashType 2로 등록											***
	***		//3. 구매일 기준 전 미수금, 전 구매금액 합친 금액으로 구매일 기준 전미수금 등록			***
	***		//4. 정산할 경우, 정산일 기준 전 전 미수금 가져와서 정산 후 0원 또는 정산 후 금액 등록	***
	***		20200110 기준 전으로는 합산으로 전미수금, 후로는 cashType 3인 전 미수금을 가져와서	***
	******************************************************************************/
	function cashSystem($way){
		global $db;

		switch($way){
			case 1 : //구매시 전미수금 금액 산정해서 등록(1건처리)
				break;
			
			case 2 : //정산내용 등록, 이후 전미수에서 정산금액 차감 후 전미수금으로 산정해서 등록(2건 처리)
				break;

		}

		
		$query = " select c2.type, c2.idx, c1.companyIdx, c1.date, c1.price "
				." from Cash_Info c1 inner join "
				." ( "
				."		select 'prev_row' as type, max(idx) as idx from Cash_Info where companyidx = 14 and date > 20191231 and dealIdx < 151 "
				." ) c2 on c1.idx = c2.idx "
				;
	}

	function InsertTakeOverData(){
		global $M_FUNC, $db;
		global $M_UnitPage;

		//Cash_Info에 거래에 대한 금액 미수로 잡기
		//미수로 잡고 Deal_20xx에 미수로 잡았다고 takeover를 1로 변경
		$m_id			= $M_FUNC->M_Filter(POST, "m_id");
		$companyIdx		= $M_FUNC->M_Filter(POST, "companyIdx");
		$date			= $M_FUNC->M_Filter(POST, "date");
		$year			= substr($date, 0, 4);
		$onoff			= $M_FUNC->M_Filter(POST, "onoff");
		
		//상품별 단가, 갯수 가져오기
		$goodsPriceSum = 0;
		$goodsMax = sizeof($M_UnitPage->goodsNum_Arr);
		for($i=0; $i<$goodsMax; $i++){
			$price = $M_FUNC->M_Filter(POST, $M_UnitPage->goodsNum_Arr[$i]['num']);
			$num = $M_FUNC->M_Filter(POST, "num".$i);

			$goodsPriceSum += (int)$price * (int)$num;
		}
		
		//Cash_Info에 인수하는 내용 입력
		$data = array(
			'companyIdx'	=>	$companyIdx,
			'date'			=>	$date,
			'onoff'			=>	$onoff,
			'cashType'		=>	1,		// 1:인수, 2:정산
			'price'			=>	$goodsPriceSum,
			'regUnixtime'	=>	time()
		);
		$db->insert("Cash_Info", $data);

		//인수확인 후 Deal_20xx에서 해당데이터 인수확인 된 내용 update
		$deal_data = array(
			'takeover'	=> 1
		);
		$db->update("Deal_".$year, $deal_data, " WHERE idx = ". $m_id ." AND companyIdx = ". $companyIdx);
	}

	function ReceivablesByPrice($Where){
		global $db;
		
		//오늘 송장 등록 후 기준 전날까지에 대한 미수금 가져오기
		$query = " SELECT sum(price) as price, cashType "
				." FROM Cash_Info "
				." WHERE 1 = 1 "
				.$Where
				." GROUP By cashType "
				;
		$row = $db->getListSet($query);
		
		$RPrice = 0;	//미수금
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			if($row->get('cashType') == 1){
				$RPrice += 	$row->get('price');
			} else {
				$RPrice -= 	$row->get('price');
			}
		}

		return $RPrice;
	}

	function getOnDealList(&$total_page, &$NO, &$cnt_no, $addWhere = null, $order = null, $limit = null){
		global $db, $M_FUNC;
		global $PAGE;
		
		if($PAGE < 1) $PAGE = 1 ;
		$start_row = ($PAGE - 1) * C_LIST_CNT3;
		$where = " WHERE 1 = 1 ";
		
		if ($addWhere) 
		{
			$where .= $addWhere;
		}

		if ($order == "") 
		{
			$orderQuery = " ORDER BY idx DESC ";
		}
		else 
		{
			$orderQuery = " ORDER BY ". $order;
		}
		
		if ($limit == "NO") 
		{
			$limitQuery = "";
		} 
		else 
		{
			$limitQuery = " LIMIT ". $start_row .", ". C_LIST_CNT3;
		}

		//해당월 테이블을 가져오기 위한
		$year		= $M_FUNC->M_Filter(GET, "year");
		if($year == '') $year = date('Y', time());
		$table = " Deal_".$year;

		$query = " SELECT idx, date, time, deal, companyIdx, dealNum, recipient, tel, addr, tel2, deliveryTxt, buyDay, dstart, dend, priceKinds "
					. " FROM ".$table
					. $where
					. $orderQuery
					. $limitQuery;
		$row = $db->getListSet($query);

		$cnt_no = $db->cnt($table, $where);
		$total_page = (int)(($cnt_no - 1) / C_LIST_CNT) + 1;
		$NO = $cnt_no - $start_row;
		
		return $row;		
	}

	function InsertOnDealData(){
		global $db, $M_FUNC, $M_CRUMB, $M_JS, $_SESSION, $M_FILE, $M_MEMBER, $M_UnitPage;
		global $MENU_ID, $SearchName;

		//Deal_2019 거래명세표 등록
		//Deal_2019 분석 후 DealSum_2019 등록
		$level			= $M_FUNC->M_Filter(POST, "level");
		$companyIdx		= $M_FUNC->M_Filter(POST, 'companyIdx');

		$buyDay			= $M_FUNC->M_Filter(POST, 'buyDay');		//주문(결제일)		YmdHis
		$orderDay		= $M_FUNC->M_Filter(POST, 'orderDay');		//주문확인&출고일	Ymd
		$deal			= $M_FUNC->M_Filter(POST, 'deal');			//상품명
		//실제 상품명 가져오기
		$dealName		= $this->online_goods[(int)$deal];
		$dealLast		= $M_FUNC->M_Filter(POST, 'dealLast');		//나머지 상품명
		$dealNum		= $M_FUNC->M_Filter(POST, 'dealNum');		//상품 수량
		$price			= $M_FUNC->M_Filter(POST, 'price');			//금액
		$D_price		= $M_FUNC->M_Filter(POST, 'D_price');		//택배비
		$buyer			= $M_FUNC->M_Filter(POST, 'buyer');		//구매자
		$recipient		= $M_FUNC->M_Filter(POST, 'recipient');		//수취인
		$tel			= $M_FUNC->M_Filter(POST, 'tel');			//핸드폰
		$addr			= $M_FUNC->M_Filter(POST, 'addr');			//주소
		$deliveryTxt	= $M_FUNC->M_Filter(POST, 'deliveryTxt');	//배송메세지
		$small			= $M_FUNC->M_Filter(POST, 'small');			//택배실제 소사이즈
		$medium			= $M_FUNC->M_Filter(POST, 'medium');		//택배실제 중사이즈
		$large			= $M_FUNC->M_Filter(POST, 'large');			//택배실제 대사이즈

		$year		= substr($buyDay, 0, 4);
		$month		= substr($buyDay, 4, 2);
		$day		= substr($buyDay, 6, 2);

		if($level == ""){
			$message = "잘못된 경로로 들어온 경우입니다.\\n다시 등록하시기 바랍니다.";
			$url = "/?M0303S";
			$M_JS->Go_URL($url, $message, "parent");
			exit;
		}

		//회사명가져오기
		$companyArr = $M_UnitPage->getCompanyArr("", " AND idx = ". $companyIdx);

		if($dealLast == ""){
			$dealN	=	$dealName;
		} else {
			$dealN	= $dealName." ".$dealLast;
		}

		$deal_data = array(
			'date'			=> $orderDay,
			'time'			=> $buyDay,
			'companyIdx'	=> $companyIdx,
			'companyName'	=> $companyArr[$companyIdx],
			'dealType'		=> 1,
			'onoff'			=> 'On',
			'taxYN'			=> 'Y',
			'deal'			=> $dealN,
			'dealNum'		=> $dealNum,
			'deliveryYN'	=> "Y",
			'calculateDate'	=> 0,
			'depositYN'		=> 'N',
			'status'		=> 1,
			'buyer'			=> $buyer,
			'recipient'		=> $recipient,
			'tel'			=> $tel,
			'addr'			=> $addr,
			'deliveryTxt'	=> $deliveryTxt,
			'priceKinds'	=> $price."|".$D_price."|".$small."|".$medium."|".$large,
			'regUnixtime'	=> time()
		);

		$SumPrice = $price + $D_price;

		//Deal_2019 거래명세표 등록
		$db->insert("Deal_".$year, $deal_data);
		$dealIdx = $db->getInsertID();

		$dealSum_date = array(
			'dealIdx'		=> $dealIdx,
			'companyIdx'	=> $companyIdx,
			'month'			=> $month,
			'day'			=> $day,
			'companyName'	=> $companyArr[$companyIdx],
			'PriceSum'		=> $SumPrice,
			'onoff'			=> 'On',
			'taxYN'			=> 'Y',
			'regUnixtime'	=> time()
		);

		//Deal_2019 분석 후 DealSum_2019 등록
		$db->insert("DealSum_".$year, $dealSum_date);	
	}

	function UpdateOnDealData(){
		global $db, $M_FUNC, $M_CRUMB, $M_JS, $_SESSION, $M_FILE, $M_MEMBER, $M_UnitPage;
		global $MENU_ID, $SearchName;
		
		//Deal_2019 deal내용, dealNum 내용 수정
		//Deal_2019 분석 후 DealSum_2019 Price값 수정
		$level		= $M_FUNC->M_Filter(POST, "level");
		$m_id		= $M_FUNC->M_Filter(POST, "m_id");
		$companyIdx	= $M_FUNC->M_Filter(POST, "companyIdx");
		$date		= $M_FUNC->M_Filter(POST, "date");
		$year		= date('Y', strtotime($date));
		$month		= date('n', strtotime($date));
		$day		= date('j', strtotime($date));

		if($level == ""){
			$message = "잘못된 경로로 들어온 경우입니다.\\n다시 등록하시기 바랍니다.";
			$url = "/?M0301S";
			$M_JS->Go_URL($url, $message, "parent");
			exit;
		}

		//상품별 단가, 갯수 가져오기
		$goodsNum = "";
		$goodsSum = "";
		$goodsPriceSum = 0;
		$goodsMax = sizeof($M_UnitPage->goodsNum_Arr);
		for($i=0; $i<$goodsMax; $i++){
			$price = $M_FUNC->M_Filter(POST, $M_UnitPage->goodsNum_Arr[$i]['num']);
			$num = $M_FUNC->M_Filter(POST, "num".$i);

			$goodsNum .= $num == "" ? 0 : $num;
			$goodsSum .= (int)$price * (int)$num;
			$goodsPriceSum += (int)$price * (int)$num;

			if($i != $goodsMax-1){
				$goodsNum	.= "|";
				$goodsSum	.= "|";
			}
		}

		$deal_data = array(
			'date'			=> $date,
			'deal'			=> $goodsSum,
			'dealNum'		=> $goodsNum
		);

		$dealSum_date = array(
			'PriceSum'		=> $goodsPriceSum
		);
		
		//Deal_2019 거래명세표 수정
		$dealWhere = " WHERE idx = ".$m_id." AND companyIdx = ".$companyIdx;
		$db->update("Deal_".$year, $deal_data, $dealWhere);

		//Deal_2019 분석 후 DealSum_2019 수정
		$dealSumWhere = " WHERE companyIdx = ".$companyIdx." AND month = ".$month." AND day = ".$day;
		$db->update("DealSum_".$year, $dealSum_date, $dealSumWhere);	
	}

	function getDeliveryList($table, $addWhere){
		global $db;

		$query = " SELECT * "
				." FROM ". $table
				." WHERE status = 1 "
				.$addWhere
				." ORDER BY time asc "
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function getCountByCash($addWhere){
		global $db;

		$query = " SELECT count(*) as cnt "
				." FROM Cash_Info "
				." WHERE 1 = 1 "
				.$addWhere
				;
		$row = $db->getListSet($query);
		$row->next();

		return $row->get('cnt');
	}

	function getdealByDelivery($year, $month){
		global $db;

		$query = " SELECT idx, date, dealNum, status, priceKinds, companyIdx, addr "
				." FROM Deal_".$year
				." WHERE status = 1 AND onoff = 'On' AND date like '".$year.$month."%'"
				;
		$row = $db->getListSet($query);
		
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			$kindArr = explode("|", $row->get('priceKinds'));

			//제주인경우 추가운임
			$addFreight = strpos($row->get('addr'), "제주") === false ? 0 : 1;
			
			if($kindArr[2] > 0){
				$arr[$row->get('companyIdx')]['S']	+= $kindArr[2];
				$arr[$row->get('companyIdx')]['add'] += $addFreight * $kindArr[2];
			}
			if($kindArr[3] > 0){
				$arr[$row->get('companyIdx')]['M']	+= $kindArr[3];
				$arr[$row->get('companyIdx')]['add'] += $addFreight * $kindArr[3];
			}
			if($kindArr[4] > 0){
				$arr[$row->get('companyIdx')]['L']	+= $kindArr[4];
				$arr[$row->get('companyIdx')]['add'] += $addFreight * $kindArr[4];
			}
		}

		return $arr;
	}

	function getDealForTakeOver($year, $month){
		global $db;
		
		$query = " SELECT idx, date, companyIdx, companyName, deal, takeover, onoff "
				." FROM Deal_".$year
				." WHERE 1 = 1 "
				." AND onoff = 'Off' AND takeover = 0 "
				." AND date like '".$year.$month."%' "
				." ORDER BY date asc "
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function getDealByOnline(&$total_page, &$NO, &$cnt_no, $addWhere = null, $order = null, $limit = null){
		global $db, $M_FUNC;
		global $PAGE;
		
		if($PAGE < 1) $PAGE = 1 ;
		$start_row = ($PAGE - 1) * C_LIST_CNT5;
		$where = " WHERE 1 = 1 ";
		
		if ($addWhere) 
		{
			$where .= $addWhere;
		}

		if ($order == "") 
		{
			$orderQuery = " ORDER BY idx DESC ";
		}
		else 
		{
			$orderQuery = " ORDER BY ". $order;
		}
		
		if ($limit == "NO") 
		{
			$limitQuery = "";
		} 
		else 
		{
			$limitQuery = " LIMIT ". $start_row .", ". C_LIST_CNT5;
		}

		//해당월 테이블을 가져오기 위한
		$year		= $M_FUNC->M_Filter(GET, "year");
		if($year == '') $year = date('Y', time());
		$table = DEAL_TABLE.$year;

		$query = " SELECT idx, date, time, deal, companyIdx, dealNum, buyer,  recipient, tel, addr, tel2, deliveryTxt, buyDay, dstart, dend, priceKinds "
					. " FROM ".$table
					. $where
					. $orderQuery
					. $limitQuery;
		$row = $db->getListSet($query);

		$cnt_no = $db->cnt($table, $where);
		$total_page = (int)(($cnt_no - 1) / C_LIST_CNT5) + 1;
		$NO = $cnt_no - $start_row;
		
		return $row;
	}

	function getDelPriceByMonth($year){
		global $db;

		$query = " SELECT * "
				." FROM Deal_". $year
				." WHERE date like '".$year."%' ";
		$row = $db->getListSet($query);

		return $row;
	}

	function getTodayList($day){
		global $db;

		$year = date('Y', time());
		if($day){
			$day = date('Ym', time()).$day;
		} else {
			$day = date('Ymd', time());
		}

		$query = " SELECT date, deal, sum(dealNum) as sum, count(deal) as unit "
				." FROM ".DEAL_TABLE.$year
				." WHERE onoff = 'On' AND date = ".$day
				." GROUP BY deal, dealNum "
				." ORDER BY deal asc "
				;
		$row = $db->getListSet($query);

		$tmp_name = "";
		$tmp_cnt = 0;
		$arr = array();
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			if(strpos($row->get('deal'), '점보롤') === false){
				$name = $row->get('deal')." ".$row->get('sum')/$row->get('unit')."개";
				if(strpos($name, "팩") === false){
					$arr[$name]['cnt'] += $row->get('unit');
				} else {
					$arr[$name]['cnt'] += $row->get('sum');
				}
			} else {
				$arr[$row->get('deal')]['cnt'] += $row->get('sum');
			}
		}

		return $arr;
	}

	function excel_delivery(){
		global $db;

		$query = " SELECT * "
				." FROM ".DEAL_TABLE.date('Y', time())
				//." WHERE onoff = 'On' AND date = 20191126 AND dstart > 0 "
				." WHERE onoff = 'On' AND date = ".date('Ymd', time())." AND dstart = 0 "
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function del(){
		global $db;

		$query = " SELECT * "
				." FROM Deal_2019 "
				." WHERE onoff = 'On' AND date like '201911%' "	//and companyIdx = 40 
				." ORDER by time asc "
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function addRow($row){
		global $db;
		
		$addIdx = "";
		$returnRow = new L_ListSet();

		if($row->size() > 0){
			for($j=0; $j<$row->size(); $j++){
				$row->next();

				if($j != $row->size()-1){
					$addIdx .= "".$row->get('companyIdx').", ";
				} else {
					$addIdx .= "".$row->get('companyIdx');
				}

				$arr = array(
					'month'			=> $row->get('month'),
					'companyIdx'	=> $row->get('companyIdx'),
					'companyName'	=> $row->get('companyName'),
					'taxYN'			=> $row->get('taxYN'),
					'price'			=> $row->get('price'),
					'cnt'			=> $row->get('cnt')
				);

				$returnRow->add($arr);
			}
			$addIdx = " AND idx not in (". $addIdx .") ";
		} else {
			$addIdx = "";
		}
		
		$query = " SELECT idx, companyName, taxYN "
				." FROM Company_Info "
				." where onoff = 'On' and status = 1 "
				.$addIdx
				." AND level <> 31 "		//오프라인 매출 리스트에도 온라인애들이 나와서 우선 제외
				;
		$addRow = $db->getListSet($query);

		if($addRow->size() > 0){
			for($i=0; $i<$addRow->size(); $i++){
				$addRow->next();
				
				$arr = array(
					'month'			=> date('m', time()),
					'companyIdx'	=> $addRow->get('idx'),
					'companyName'	=> $addRow->get('companyName'),
					'taxYN'			=> $addRow->get('taxYN'),
					'price'			=> 0,
					'cnt'			=> 0
				);
				$returnRow->add($arr);
			}
		}

		return $returnRow;
	}

	function getDeliveryResult($addWhere = null, $order = null){
		global $db, $M_FUNC;
	
		$where = " WHERE 1 = 1 ";
		
		if ($addWhere){
			$where .= $addWhere;
		}
		
		if ($order == "") {
			$orderQuery = " ORDER BY idx DESC ";
		} else {
			$orderQuery = " ORDER BY ". $order;
		}

		//해당월 테이블을 가져오기 위한
		$year		= $M_FUNC->M_Filter(GET, "searchYear");
		if($year == '') $year = date('Y', time());
		$table = DEAL_TABLE.$year;

		$query = " SELECT idx, date, companyIdx, companyName, deal, dealNum, buyer, recipient, addr, priceKinds "
					. " FROM ".$table
					. $where
					. $orderQuery;
		$row = $db->getListSet($query);

		//row Data를 형식에 맞게 Array로 변형하여 return
		$arr = array();
		if($row->size() > 0){
			for($i=0; $i<$row->size(); $i++){
				$row->next();

				$kind = explode('|', $row->get('priceKinds'));
				$except = strpos($row->get('addr'), '제주');
				$exceptCnt = $except === false ? 0 : 1;

				$arr[$row->get('date')]['S'] += $kind[2];
				$arr[$row->get('date')]['M'] += $kind[3];
				$arr[$row->get('date')]['L'] += $kind[4];
				$arr[$row->get('date')]['except'] += $exceptCnt;
				$arr[$row->get('date')]['cnt'] += $kind[2]+$kind[3]+$kind[4];
			}
		}

		return $arr;		
	}

	function readExcel(){
		include_once COMMON_CLASS . '/PHPExcel_1.8.0/Classes/PHPExcel.php';

		$objPHPExcel = new PHPExcel();

		// 엑셀 데이터를 담을 배열을 선언한다.
		$allData = array();

		// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
		$file_dir = "D:\Project\cmw\_Admin\out\\";
		$filename = iconv("UTF-8", "EUC-KR", $_FILES['excelFile']['name']);

		try {
			// 업로드한 PHP 파일을 읽어온다.
			$objPHPExcel = PHPExcel_IOFactory::load($file_dir.$filename);
			$sheetsCount = $objPHPExcel -> getSheetCount();

			// 시트Sheet별로 읽기
			for($sheet = 0; $sheet < $sheetsCount; $sheet++) {

				  $objPHPExcel -> setActiveSheetIndex($sheet);
				  $activesheet = $objPHPExcel -> getActiveSheet();
				  $highestRow = $activesheet -> getHighestRow();             // 마지막 행
				  $highestColumn = $activesheet -> getHighestColumn();    // 마지막 컬럼

				  // 한줄읽기
				  for($row = 1; $row <= $highestRow; $row++) {

					// $rowData가 한줄의 데이터를 셀별로 배열처리 된다.
					$rowData = $activesheet -> rangeToArray("A" . $row . ":" . $highestColumn . $row, NULL, TRUE, FALSE);

					// $rowData에 들어가는 값은 계속 초기화 되기때문에 값을 담을 새로운 배열을 선안하고 담는다.
					$allData[$row] = $rowData[0];
				  }
			}
		} catch(exception $exception) {
			echo $exception;
		}

		return $allData;
	}

	function getBeforeShipping($addWhere){
		global $db;

		echo $query = " SELECT * "
				." FROM ".DEAL_TABLE.date('Y', time())
				." WHERE 1 = 1 "
				.$addWhere
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function UpdateDeliveryData(){
		global $db, $M_FUNC;

		$priceKinds			= $_POST['priceKinds'];

		$arr = array();
		foreach($priceKinds as $key => $value){
			$delArr = explode("|", $value);

			$set = " priceKinds = '".str_replace($delArr[0]."|", "", $value)."'";
			$where = ' WHERE idx = '. $delArr[0];

			$db->update(DEAL_TABLE.date('Y', time()), $set, $where);
		}
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////








	function getUserInfoByIdx($idx){
		global $db, $M_FUNC;
		$query = " SELECT * "
					. " FROM Account_Info "
					. " WHERE idx = " . $idx
					;
		$row = $db->getListSet($query);
		$row->next();
		return $row;
	}


	function getUserInfo($addWhere="",$addOrder="",$addLimit=""){
		global $db, $M_FUNC;
		
		$query = " SELECT * "
					. " FROM Account_Info "
					. " WHERE 1=1 "
					. $addWhere
					. $addOrder
					. $addLimit;

		$row = $db->getListSet($query);
		
		return $row;
	}

	function getCompanyList(&$total_page, &$NO, &$cnt_no, $addWhere = null, $order = null, $limit = null){
		global $db, $M_FUNC;
		global $PAGE;
		
		if($PAGE < 1) $PAGE = 1 ;
		$start_row = ($PAGE - 1) * C_LIST_CNT;
		$where = " WHERE 1 = 1 AND status = 1 ";
		
		if ($addWhere) 
		{
			$where .= $addWhere;
		}

		if ($order == "") 
		{
			$orderQuery = " ORDER BY idx DESC ";
		}
		else 
		{
			$orderQuery = " ORDER BY ". $order;
		}
		
		if ($limit == "NO") 
		{
			$limitQuery = "";
		} 
		else 
		{
			$limitQuery = " LIMIT ". $start_row .", ". C_LIST_CNT;
		}
		
		$query = " SELECT idx, companyName, regUnixtime, status, businessIdx "
					. " FROM Company_Info "
					. $where
					. $orderQuery
					. $limitQuery;
		$row = $db->getListSet($query);

		$cnt_no = $db->cnt("Company_Info", $where);
		$total_page = (int)(($cnt_no - 1) / C_LIST_CNT) + 1;
		$NO = $cnt_no - $start_row;
		
		return $row;		
	}

	function getAccountList2(&$total_page, &$NO, &$cnt_no, $addWhere = null, $order = null, $limit = null){
		global $db, $M_FUNC;
		global $PAGE;
		
		if($PAGE < 1) $PAGE = 1 ;
		$start_row = ($PAGE - 1) * C_LIST_CNT;
		$where = " WHERE 1 = 1 AND status = 1 ";
		
		if ($addWhere) 
		{
			$where .= $addWhere;
		}

		if ($order == "") 
		{
			$orderQuery = " ORDER BY idx DESC ";
		}
		else 
		{
			$orderQuery = " ORDER BY ". $order;
		}
		
		if ($limit == "NO") 
		{
			$limitQuery = "";
		} 
		else 
		{
			$limitQuery = " LIMIT ". $start_row .", ". C_LIST_CNT;
		}
		
		$query = " SELECT * "
					. " FROM Account_Info "
					. $where
					. $orderQuery
					. $limitQuery;
		$row = $db->getListSet($query);

		$cnt_no = $db->cnt("Account_Info", $where);
		$total_page = (int)(($cnt_no - 1) / C_LIST_CNT) + 1;
		$NO = $cnt_no - $start_row;
		
		return $row;		
	}

	function getCompanyInfo($addWhere="",$addOrder="",$addLimit=""){
		global $db, $M_FUNC;
		
		$query = " SELECT * "
					. " FROM Company_Info "
					. " WHERE 1=1 AND status <> 99 "
					. $addWhere
					. $addOrder
					. $addLimit;

		$row = $db->getListSet($query);
		
		return $row;
	}

	function getAccountInfoArr($addWhere="",$addOrder="",$addLimit=""){
		global $db, $M_FUNC;
		
		$result = array(
			0 => "서비스명 선택",
		);

		$query = " SELECT a.idx, s.serviceName "
					. " FROM Account_Info a "
					. "		LEFT JOIN Service_Info s ON a.idx = s.keyAccountIdx "
					. " WHERE 1=1 and a.status <> 99 "
					. $addWhere
					. $addOrder
					. $addLimit;

		$row = $db->getListSet($query);
		for($i=0; $i<$row->size(); $i++){
			$row->next();
			$result[$row->get('idx')] = $row->get('serviceName');
		}

		return $result;
	}

	function getCompanyInfoArr($addWhere="",$addOrder="",$addLimit=""){
		global $db, $M_FUNC;
		
		$result = array(
			0 => "고객사선택",
		);

		$query = " SELECT * "
					. " FROM Company_Info "
					. " WHERE 1=1 and status <> 99 "
					. $addWhere
					. $addOrder
					. $addLimit;

		$row = $db->getListSet($query);
		for($i=0; $i<$row->size(); $i++){
			$row->next();
			$result[$row->get('idx')] = $row->get('companyName');
		}

		return $result;
	}

	function getServiceInfoArr($addWhere="",$addOrder="",$addLimit=""){
		global $db, $M_FUNC;
		
		$result = array(
			0 => "서비스명 선택",
		);

		$query = " SELECT s.idx, s.serviceName "
				." FROM Service_Info s "
				." WHERE 1=1 and s.status <> 99 "
					. $addWhere
					. $addOrder
					. $addLimit;

		$row = $db->getListSet($query);
		for($i=0; $i<$row->size(); $i++){
			$row->next();
			$result[$row->get('idx')] = $row->get('serviceName');
		}

		return $result;
	}

	function check_ID($user_id){
		global $db, $M_FUNC;

		$query	= "SELECT accountID FROM Account_Info WHERE accountID = '".$user_id."' ";
		$row = $db->getListSet($query);
		return $row;
	}

	function check_companyName($companyName){
		global $db, $M_FUNC;

		$query	= "SELECT count(*) cnt FROM Company_Info WHERE companyName = '".$companyName."' ";
		$row = $db->getListSet($query);
		$row->next();
		return $row->get('cnt');
	}


	function check_PW($UI_idx, $user_pw){
		global $db;

		$query	= "SELECT userID FROM Account_Info WHERE idx='" . $UI_idx . "' AND userPW = '".md5($user_pw) . "'";
		$row = $db->getListSet($query);

		if($row->size() == 0) {
			return false;
		} 
		return true;
	}

	function update_pw($UI_idx, $user_pw) {
		global $db;

		$data = array(
			"userPW"		=> md5($user_pw),
		);
		$where = " WHERE idx=" . $UI_idx;
		$db->update("Account_Info", $data, $where);
	}

	function InsertAccountData2(){
		global $db, $M_FUNC, $M_CRUMB, $M_JS, $_SESSION, $M_FILE, $M_MEMBER;
		global $MENU_ID, $SearchName;

		$m_id = $M_FUNC->M_Filter(POST, 'm_id');
		$p_action = $M_FUNC->M_Filter(POST, 'p_action');
		$level = $M_FUNC->M_Filter(POST, "level");
		$companyIdx = $M_FUNC->M_Filter(POST, 'companyIdx');
		$businessIdx = $M_FUNC->M_Filter(POST, 'businessIdx');
		$businessName = $M_FUNC->M_Filter(POST, 'businessName');
		$serviceName = $M_FUNC->M_Filter(POST, 'serviceName');
		$userName = $M_FUNC->M_Filter(POST, "userName");

		if($userName == ""){
			$message = "잘못된 경로로 들어온 경우입니다.\\n다시 등록하시기 바랍니다.";
			$url = "/?M0102L";
			$M_JS->Go_URL($url, $message, "parent");
			exit;
		}
		
		if($level == 4){
			//고객사 연결되어 있는 경우 등록 못하도록
			$addWhere = " AND idx = ".$companyIdx." AND businessIdx = ".$businessIdx;
			$connectYN = $this->compareCompanyByIdx($addWhere);
			
			if(!$connectYN){
				$message = "해당 고객사가 등록되어 있습니다.\\n다시 등록하시기 바랍니다.";
				$url = " /?M0102R ";
				$M_JS->Go_URL($url, $message, "parent");
				exit;
			}
		}

		$data = array(
			'accountID'		=> $M_FUNC->M_Filter(POST, 'userID'),
			'accountPW'		=> md5($M_FUNC->M_Filter(POST, 'userPW')),
			'accountName'	=> $userName,
			'level'			=> $level,
			'regUnixtime' => time(),
		);

		if($level == 4 || $level == 8){
			$data['companyIdx'] = $companyIdx;
			if($level == 8){
				unset($date['accountID']);
				unset($date['accountPW']);
			}
		}
		
		$this->join_proc($data);
		$m_id = $db->getLastIdx("idx", "Account_Info");
		
		if($level == 1){
			$msg = '시스템관리자 계정이 등록되었습니다.';
		} else if($level == 2){
			$b_data = array(
				'businessName'	=> $businessName,
				'regUnixtime'	=> time(),
				'status'		=> 1,
				'keyAccountIdx'	=> $m_id,
			);
			$db->insert("Business_Info", $b_data);
			$roleIdx = $db->getLastIdx('idx', "Business_Info");
			$msg = '회사 계정이 등록되었습니다.';
		} else if($level == 4){
			$c_data = array(
				'keyAccountIdx' => $m_id
			);
			$db->update("Company_Info", $c_data, " WHERE idx = ".$companyIdx);
			$roleIdx = $companyIdx;
			$msg = '고객사 계정이 등록되었습니다.';
		} else {
			$s_data = array(
				'serviceName'	=> $serviceName,
				'regUnixtime'	=> time(),
				'status'		=> 1,
				'keyAccountIdx'	=> $m_id,
				'companyIdx'	=> $companyIdx,
			);
			$db->insert("Service_Info", $s_data);
			$roleIdx = $db->getLastIdx('idx', "Service_Info");
			$msg = '서비스 계정이 등록되었습니다.';
		}

		$r_data = array(
			'accountIdx'	=>	$m_id,
			'roleIdx'		=>	$roleIdx,
			'level'			=>	$level,
			'companyIdx'	=>	$companyIdx,
			'businessIdx'	=>	$businessIdx,
		);
		if($level != 1){
			$db->insert('AccountRole_Info', $r_data);
		}

		//history 데이터 남기기
		if($level == 8){
			$M_FUNC->recordActionLog('P', $MENU_ID, $m_id, $_SESSION['accountID'] . "님께서 ".$this->User_level[$level]." 계정[". $data['accountName'] . "]를 생성하였습니다. ");
		} else {
			$M_FUNC->recordActionLog('P', $MENU_ID, $m_id, $_SESSION['accountID'] . "님께서 ".$this->User_level[$level]." 계정[". $M_FUNC->M_Filter(POST, 'userID') . "]를 생성하였습니다. ");
		}
	}

	function mail_contents($text){
		$mail_root = ROOT_PATH . '/_www_emato/mail';
		$mail_html = file_get_contents($mail_root . '/mail_form_join.html', false);

		$mail_contents = str_replace('{text}', $text, $mail_html);
		$mail_contents = iconv('utf-8', 'euc-kr', $mail_contents);

		return $mail_contents;
	}
	
	function mail_contents_skb($text){
		$mail_root = ROOT_PATH . '/_Ondemand_SKB/mail';
		$mail_html = file_get_contents($mail_root . '/mail_form_join.html', false);

		$mail_contents = str_replace('{text}', $text, $mail_html);
		$mail_contents = iconv('utf-8', 'euc-kr', $mail_contents);

		return $mail_contents;
	}
	
	
	
	function deleteAccountDataByIdx($idx){
		global $db;
		
		$table = "Account_Info";
		$delete_data = array(
			'status' => 99,
		);
		$db->update($table, $delete_data, " where idx=".$idx);
	}

	function getCompanyInfoByIdx($m_id, $field = ""){
		global $db;

		$query = 
			 '	SELECT * '
			.'	FROM Company_Info '
			.'	WHERE 1=1 '
			//.'		status <> 99 '
			.'		AND idx = ' . $m_id
			;

		$row = $db->getListSet($query);
		$row->next();
		if ($field) {
			return $row->get($field);
		} else {
			return $row;
		}
	}

	function insertCompanyInfoByData($data) {
		global $M_FUNC, $MENU_ID;
		global $db;
		
		$idx = $db->getLastIdx("idx", COMPANY_TABLE) + 1;
		$data["idx"] = $idx;

		$db->insert(COMPANY_TABLE, $data);
		
		//history 데이터 남기기
		$M_FUNC->recordActionLog('P', $MENU_ID, $idx, $_SESSION['accountID'] . "님께서 고객사[". $data['companyName'] . "]를 생성하였습니다. ");

		return $idx;
	}

	function updateCompanyInfoByData($m_id, $data) {
		global $M_FUNC, $MENU_ID;
		global $db;
		
		unset($data["regUnixtime"]);
		$db->update(COMPANY_TABLE, $data, " WHERE idx=" . $m_id);

		//history 데이터 남기기
		$M_FUNC->recordActionLog('U', $MENU_ID, $m_id, $_SESSION['accountID'] . "님께서 고객사[". $data['companyName'] . "]를 수정하였습니다. ");
	}

	function deleteCompanyDataByIdx($idx){
		global $M_FUNC;
		global $db;
		
		$table = "Company_Info";
		$delete_data = array(
			'status' => 99,
		);
		$db->update($table, $delete_data, " where idx=".$idx);

		$companyName = $this->getCompanyInfoByIdx($idx, 'companyName');

		//history 데이터 남기기
		$M_FUNC->recordActionLog('D', 'M0101', '', $_SESSION['accountID'] . "님께서 고객사[". $companyName . "]를 삭제하였습니다. ");
	}

	function deleteAccountDataList($addWhere) {
		global $db;
		$where = " WHERE 1 = 1 " . $addWhere;
		$delete_data = array('status' => 99);
		$db->update("Account_Info", $delete_data, $where);
	}

	function getAdminHistoryInfo($addWhere="",$addOrder="",$addLimit=""){
		global $db, $M_FUNC;
		
		$query = " SELECT * "
					. " FROM Admin_History "
					. " WHERE 1=1 "
					. $addWhere
					. $addOrder
					. $addLimit;

		$row = $db->getListSet($query);
		
		return $row;
	}

	function getBusinessInfoArr(){
		global $db;

		$query = " SELECT * FROM Business_Info "
				." WHERE status = 1 "
				;
		$row = $db->getListSet($query);
		
		$result = array();
		$result[0] = "회사 선택";
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			$result[$row->get('idx')] = $row->get('businessName');
		}

		return $result;
	}

	function getBusinessByidx($m_id, $field=NULL){
		global $db;
		
		$query = " SELECT * FROM Business_Info "
				." WHERE idx = ".$m_id;
		$row = $db->getListSet($query);

		$row->next();

		if($field){
			return $row->get($field);
		} else {
			return $row;
		}
	}

	function compareCompanyByIdx($where){
		global $db;

		$query = " SELECT * FROM Company_Info "
				." WHERE 1 = 1 AND status = 1 "
				.$where
				;
		$row = $db->getListSet($query);
		$row->next();

		if($row->get('keyAccountIdx') != 0){
			return false;
		} else {
			return true;
		}
	}

	function getAccountByRoleIdx($addWhere){
		global $db;

		$query = " select accountIdx, level from AccountRole_Info "
				." where 1=1 "
				. $addWhere;
		$row = $db->getListSet($query);
		
		return $row;		
	}

	function getServiceInfoByIdx($m_id, $field=null){
		global $db;
		
		$query = " select * "
				." from Service_Info "
				." where idx = " . $m_id;
		$row = $db->getListSet($query);
		$row->next();
		
		if($field){
			return $row->get($field);
		} else {
			return $row;
		}
	}

	function getServiceInfoBykeyAccountIdx($m_id, $field=null){
		global $db;
		
		$query = " select * "
				." from Service_Info "
				." where 1=1 and status <> 99 "
				." AND keyAccountIdx = " . $m_id;
		$row = $db->getListSet($query);
		$row->next();

		if($field){
			return $row->get($field);
		} else {
			return $row;
		}
	}

	function getadminHistoryList(&$total_page, &$NO, &$cnt_no, $addWhere = null, $order = null, $limit = null){
		global $db, $M_FUNC;
		global $PAGE;
		
		if($PAGE < 1) $PAGE = 1 ;
		$start_row = ($PAGE - 1) * C_LIST_CNT;
		$where = " WHERE 1 = 1 ";
		
		if ($addWhere) 
		{
			$where .= $addWhere;
		}

		if ($order == "") 
		{
			$orderQuery = " ORDER BY idx DESC ";
		}
		else 
		{
			$orderQuery = " ORDER BY ". $order;
		}
		
		if ($limit == "NO") 
		{
			$limitQuery = "";
		} 
		else 
		{
			$limitQuery = " LIMIT ". $start_row .", ". C_LIST_CNT;
		}
		
		$query = " SELECT * "
					. " FROM adminHistory "
					. $where
					. $orderQuery
					. $limitQuery;
		$row = $db->getListSet($query);

		$cnt_no = $db->cnt("adminHistory", $where);
		$total_page = (int)(($cnt_no - 1) / C_LIST_CNT) + 1;
		$NO = $cnt_no - $start_row;
		
		return $row;		
	}

	function updateServiceInfoByData($m_id, $data){
		global $db;

		$db->update("Service_Info", $data, "WHERE keyAccountIdx = ". $m_id);
	}
} //*** End of Class

//insert into Deal_2019(`date`, companyIdx, companyName, dealType, onoff, taxYN, deal, deliveryYn, calculateDate, depositYN, status, recipient, tell, addr, deliveryTxt, regUnixtime) values (1555307493, 5, '태일상회', 1, 'On', 'Y', '230000|0|0|0|145000|0|0|0|0|0|0|0|0|0|0|0|0|0', 'N', 0, 'N', 1, '', '', '', '', 1555307493)
?>
