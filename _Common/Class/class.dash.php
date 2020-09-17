<?php
	class M_DASH {
		function __construct() {	
		}

		function __destruct() {
		}

		function getDealSumBySales($year, $addWhere){
			global $db;

			$query = " SELECT idx, onoff, sum(PriceSum) as sum "
					." From DealSum_". $year
					." WHERE 1 = 1 "
					.$addWhere
					." Group By onoff "
					." Order by onoff desc "
					;
			$row = $db->getListSet($query);

			return $row;
		}

		function getDealByDelivery($year, $addWhere){
			global $db;

			$qeury = " SELECT date, companyName, onoff, deliveryYN, status, recipient, dstart, dend, addr "
					." FROM Deal_". $year
					." WHERE 1 = 1 AND date between ". date('Ymd', strtotime("7 day ago", time())) . " AND ". date('Ymd', time()) ." AND onoff = 'On' "
					." AND dend = 0 "
					.$addWhere
					." ORDER BY companyIdx , date "
					;
			$row = $db->getListSet($qeury);

			return $row;
		}

		function getBoardByHistory($addWhere, $limit){
			global $db;

			$query = " SELECT * "
					." FROM Board_Info "
					." WHERE status in (1,11,14,4) "
					.$addWhere
					." ORDER BY status asc "
					.$limit
					;
			$row = $db->getListSet($query);

			return $row;
		}

		function getDealByReceive($year){
			global $db;

			$query = " SELECT companyName, priceKinds "
					." FROM ".DEAL_TABLE.$year
					." WHERE onoff = 'On' AND receive = 0 "
					;
			$row = $db->getListSet($query);

			return $row;
		}
	}
?>