<?php
if(!class_exists('M_VM')) {
	include_once COMMON_CLASS . '/class.unit.php';
}

class M_UnitPage extends M_UNIT {
	// Constructor

	function __construct() {
		//parent::__construct();

		$this->yearArr = array(2016 => "2016년", 2017 => "2017년", 2018 => "2018년");
		$this->monthArr = array(
			1	=>	"1월",
			2	=>	"2월",
			3	=>	"3월",
			4	=>	"4월",
			5	=>	"5월",
			6	=>	"6월",
			7	=>	"7월",
			8	=>	"8월",
			9	=>	"9월",
			10	=>	"10월",
			11	=>	"11월",
			12	=>	"12월",
		);

		$this->contract_arr = array(
			1	=>	'1년',
			3	=>	'3년'
		);

		$this->loadbalance = array(
			1	=> 'Small',
			2	=> 'Middle',
			4	=> 'Large',
			8	=> 'SSL'
		);

		$this->status_arr = array(
			1	=> '사용',
			4	=> '중지',
			99 => '삭제'
		);
		$this->dbUse = array(
			0	=> '미사용',
			1	=> '사용',
		);
		$this->dbms_arr = array(
			1	=> 'MS-SQL Dev',
			4	=> 'MS-SQL Std. Edt. 4core',
			8	=> 'MS-SQL Ent. Edt. 8core',
			12	=> 'MS-SQL Ent. Edt.  12core'
		);
		$this->goodsNum_Arr = array(
			0	=> array('num' => 'g11011', 'name' => '새피아', 'roll' => 10),
			1	=> array('num' => 'g11013', 'name' => '땡큐', 'roll' => 10),
			2	=> array('num' => 'g11012', 'name' => '새피아', 'roll' => 24),
			3	=> array('num' => 'g11027', 'name' => '땡큐', 'roll' => 30),
			4	=> array('num' => 'g11021', 'name' => '순수', 'roll' => 30),
			5	=> array('num' => 'g11022', 'name' => '자연', 'roll' => 30),
			6	=> array('num' => 'g11023', 'name' => '꿈집', 'roll' => 30),
			7	=> array('num' => 'g11024', 'name' => '꽃지꿈집', 'roll' => 30),
			8	=> array('num' => 'g11025', 'name' => '황토', 'roll' => 30),
			9	=> array('num' => 'g11026', 'name' => '데코꿈집', 'roll' => 30),
			10	=> array('num' => 'g11031|150', 'name' => '고급점보롤', 'length' => '150', 'roll' => 16),
			11	=> array('num' => 'g11032|180', 'name' => '고급점보롤', 'length' => '180', 'roll' => 16),
			12	=> array('num' => 'g11033|200', 'name' => '고급점보롤', 'length' => '200', 'roll' => 16),
			13	=> array('num' => 'g11034|300', 'name' => '고급점보롤', 'length' => '300', 'roll' => 16),
			14	=> array('num' => 'g11035|160', 'name' => '꽃지점보롤', 'length' => '160', 'roll' => 16),
			15	=> array('num' => 'g11036|180', 'name' => '꽃지점보롤', 'length' => '180', 'roll' => 16),
			16	=> array('num' => 'g11037|200', 'name' => '꽃지점보롤', 'length' => '200', 'roll' => 16),
			17	=> array('num' => 'g11038|300', 'name' => '꽃지점보롤', 'length' => '300', 'roll' => 16),
			18	=> array('num' => 'g11041', 'name' => '네프킨', 'roll' => '-'),
			19	=> array('num' => 'g11043', 'name' => '핸드타월', 'roll' => '-')
		);
	}

	// Destructor
	function __destruct() {

	}
	
	function unitList(){
		global $MENU_ID, $P_ACTION, $PAGE_PATH, $PAGE;
		global $M_FUNC, $M_HTML, $M_SIGNUP;
		
		$searchTxt		= $M_FUNC->M_Filter(GET, "searchTxt");
		$PAGE			= $M_FUNC->M_Filter(GET, 'PAGE');
		if($PAGE == '') $PAGE = 1;

		$addWhere = " AND g.status <> 9 ";
		$order = " g.companyName asc ";
		
		if($searchTxt != 0) {
			$addWhere .= ' AND g.companyName LIKE \'%' . $searchTxt . '%\' '; 
		}
		
		$goodsRow	= $this->getGoodsList($total_page, $NO, $cnt_no, $addWhere, $order, $limit);

		include_once $PAGE_PATH . '/unitList.html';
	}

	function unitRV($mode = "regist"){
		global $MENU_ID, $P_ACTION, $PAGE_PATH, $PAGE;
		global $M_FUNC, $M_HTML, $M_SIGNUP;

		if($_SESSION['Level'] != 1){
			$M_SIGNUP->noExistPage();
		}

		//등록	level == 11인 판매처를 가져오기
		//수정	상품단가만 수정할 수 있게
		
		$m_id = $M_FUNC->M_Filter(GET, "m_id");
		$companyIdx = $M_FUNC->M_Filter(GET, "companyIdx");
		$companyArr = $this->getCompanyArr(array(0=>'판매처 선택')," and level = 11 ");

		if($P_ACTION == 'V') {
			$action = "?M0201U";
			$addWhere = " AND companyIdx = ". $companyIdx;
			$registButton = "수정";

			$goodsRow = $this->getGoodsInfo($addWhere);	
			$goodsArr = explode('|', $goodsRow->get('goodsPrice'));
		} else {
			$action = "?M0201P";
			$registButton = "저장";

			$goodsRow = new L_ListSet();
			$goodsArr = array();
		}

		include_once $PAGE_PATH . '/unitRV.html';
	}

	function unitInsertProc(){
		global $MENU_ID, $P_ACTION, $M_FUNC, $M_HTML, $PAGE_PATH;
		global $db;
		
		$this->InsertGoodsData();
		
		M_JS::Go_URL('/?'. $MENU_ID . 'L', "등록되었습니다.");	
	}

	function unitUpdateProc(){
		global $MENU_ID, $P_ACTION, $M_FUNC, $M_HTML, $PAGE_PATH;
		global $db;
		
		$m_id = $M_FUNC->M_Filter(POST, 'm_id');
		$companyIdx = $M_FUNC->M_Filter(POST, 'companyIdx');
		//상품별 단가 가져오기
		$goodsPrice = "";
		$goodsMax = sizeof($this->goodsNum_Arr);
		for($i=0; $i<$goodsMax; $i++){
			$price = $M_FUNC->M_Filter(POST, $this->goodsNum_Arr[$i]['num']);
			$goodsPrice .= $price == "" ? 0 : $price;
			if($i != $goodsMax-1) $goodsPrice .= "|";
		}
		
		$data = array(
			'goodsPrice'		=> $goodsPrice
		);

		$msg = '상품별 단가를 수정하였습니다.';
		$this->UpdateGoodsData($companyIdx, $data);

		M_JS::Go_URL('/?'. $MENU_ID . 'V&m_id=' . $m_id .'&companyIdx='. $companyIdx , $msg);
	}

	function getCompanyByGoodsPrice($m_id){
		global $db;

		$query = " SELECT * "
				." FROM Goods_Info "
				." WHERE status = 1 AND companyIdx = ". $m_id
				;
		$row = $db->getListSet($query);
		$row->next();
		
		return $row;
	}
}
?>