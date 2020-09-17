<?php
	
	if(!class_exists('M_DASH')) {
		include_once COMMON_CLASS . '/class.dash.php';
	}

	class M_DashPage extends M_DASH {
		// Constructor

		function __construct() {
			$this->itemLength1 = array(
				0	=> '1차 메뉴선택',
				9	=> '홈',
				1	=> '계정관리',
				2	=> '상품 단가표',
				3	=> '거래현황',
				4	=> '인수 & 배송',
				5	=> '세금',
				6	=> '게시판',
				10	=> '안내'
			);
			$this->itemLength2 = array(
				0	=> '2차 메뉴선택',
				99	=> '데쉬보드',
				11	=> '계정관리',
				21	=> '상품 단가표',
				31	=> '오프라인 매출',
				32	=> '오프라인 매입',
				33	=> '온라인 매출',
				34	=> '온라인 매입',
				41	=> '인수&인쇄',
				42	=> '배송현황',
				51	=> '오프라인',
				52	=> '온라인',
				61	=> '공지사항',
				62	=> 'Q&A',
				63	=> '작업내역',
				98	=> '문자알림'
			);
			parent::__construct();
		}

		// Destructor
		function __destruct() {

		}

		function dashMain(){
			global $PAGE_PATH;
			/*************************************
			***	1.온/오프라인 해당 월 매출 보여주기 *******
			***	2.배송 현황 ************************
			***	3.작업내역 *************************
			*************************************/
			$year		= date('Y', time());
			$month		= date('n', time());
			//유한실업_최 내용을 제외시키기 위한 조건
			$addWhere	= " AND companyIdx <> 41 ";
			
			/***	1.온/오프라인 해당 월 매출 보여주기 *******/
			$salesRow		= $this->getDealSumBySales($year, $addWhere." AND month = ". $month);

			/***	2. 온라인 배송현황	*****************/
			$deliveryRow	= $this->getDealByDelivery($year, $addWhere);

			/***	3. C.M.W 작업내역	*****************/
			$historyRow		= $this->getBoardByHistory(" AND type = 1 ", " limit 30 ");

			/***	4. C.M.W 공지사항	*****************/
			//$noticeRow		= $this->getBoardByHistory(" AND type = 3 ", " limit 10 ");

			/***	4. C.M.W 미수금 내역	*****************/
			$receiveRow		= $this->getDealByReceive($year);

			include_once $PAGE_PATH . '/dashMain_renewal.html';
		}
	}
?>