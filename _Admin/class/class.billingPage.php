<?php

if(!class_exists('M_BILLING')) {
	include_once COMMON_CLASS . '/class.billing.php';
}

class M_BillingPage extends M_BILLING {
	// Constructor

	function __construct() {
		parent::__construct();
	}

	// Destructor
	function __destruct() {

	}

	function costList(){
		global $MENU_ID, $P_ACTION, $PAGE_PATH, $PAGE;
		global $M_FUNC, $M_HTML, $M_SIGNUP;
		global $db;

		if($_SESSION['Level'] != 1){
			$M_SIGNUP->noExistPage();
		}
		
		$unitRow = $this->getUnitCostRow();
		$unitRow->next();

		if($unitRow->size() > 0){
			$action = "/?M0401U";
		} else {
			$action = "/?M0401P";
		}
		
		include_once ADMIN_PAGE_PATH . "/" . substr($MENU_ID, 0, 3) . "/costList.html";
	}

	function costProc() {
		global $MENU_ID, $M_FUNC, $M_JS, $P_ACTION, $M_SIGNUP;
		
		if($_SESSION['Level'] != 1){
			$M_SIGNUP->noExistPage();
		}

		if($P_ACTION === 'P') {
			$count = $this->getCountUnit(); 
			if($count <=0){
				$this->registCostData();
				$msg = "등록한 데이터가 저장되었습니다.";
			}
		} else {
			$count = $this->getCountUnit(); 
			if($count == 1){
				$this->updateCostData();
				$msg = "수정되었습니다.";
			}
		}

		M_JS::Go_URL("/?". $MENU_ID . "L",  $msg, "parent");		
	}

	function calculateList(){
		global $MENU_ID, $M_FUNC, $M_JS, $P_ACTION, $M_SIGNUP;
		global $M_HTML;

		if($_SESSION['U_idx'] != 10){
			$M_SIGNUP->noExistPage();
		}

		$year = $M_FUNC->M_Filter(GET, 'year');
		$month = $M_FUNC->M_Filter(GET, 'month');
		$searchTxt = $M_FUNC->M_Filter(GET, 'searchTxt');
		$searchType = $M_FUNC->M_Filter(GET, 'searchType');
		
		if($year == ''){
			$year = date('Y', time());
		}
		if($month == ''){
			$month = date('m', time());
		}
		
		$addWhere = '';
		if($year > 0 && $month > 0){
			$addWhere .= ' AND left(dateYmd, 6) = '.$year.$month;
		}
		$where = '';
		if($searchTxt != ''){
			if($searchType == 'serviceName'){
				$where = " AND s.serviceName like '%".$searchTxt."%' ";
			} else {
				$where = " AND v.vmName like '%".$searchTxt."%' ";
			}
			$vmRow = $this->getVmDataByserviceName($where);

			if($vmRow->size() > 0){
				$addWhere .= ' AND vmIdx in ( ';
				for($i=0; $i<$vmRow->size(); $i++){
					$vmRow->next();

					$addWhere .= $vmRow->get('vmIdx');
					if($i < $vmRow->size() -1) $addWhere .= ',';
				}
				$addWhere .= ')';
			}
		}
		//서비스명을 배열로 가져옴
		$serviceRow = $this->getVmDataByserviceName($where, 'Y');

		$calculateRow = $this->getCalculateList($addWhere);

		include_once ADMIN_PAGE_PATH . "/" . substr($MENU_ID, 0, 3) . "/calculateList.html";
	}

	function calculateList2(){
		global $MENU_ID, $M_FUNC, $M_JS, $P_ACTION, $M_SIGNUP;
		global $M_HTML;

		if($_SESSION['U_idx'] != 10){
			$M_SIGNUP->noExistPage();
		}

		$year = $M_FUNC->M_Filter(GET, 'year');
		$month = $M_FUNC->M_Filter(GET, 'month');
		
		if($year == ''){
			$year = date('Y', time());
		}
		if($month == ''){
			$month = date('m', time());
		}

		include_once ADMIN_PAGE_PATH . "/" . substr($MENU_ID, 0, 3) . "/calculateList.html";
	}

	function changeCommitment(){
		global $MENU_ID, $M_FUNC, $M_JS, $P_ACTION, $M_SIGNUP, $M_HTML;

		include_once ADMIN_CLASS_PATH . '/class.vmPage_v2.php';
		$M_VmPage = new M_VmPage;


		include_once ADMIN_PAGE_PATH . "/" . substr($MENU_ID, 0, 3) . "/changeCommitment.html";
	}
}

?>