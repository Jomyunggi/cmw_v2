<?php
class M_REPORT {
	function __construct() {

	}

	function __destruct() {
		
	}

	function getCompanyArr($companyArr, $addWhere){
		global $db;

		if(is_array($companyArr)){
			$arr = $companyArr;
		} else {
			$arr = array();
		}

		$query = " select idx, companyName "
				." from Company_Info "
				." where status = 1 "
				.$addWhere
				." order by idx asc ";
		$row = $db->getListSet($query);
		

		for($i=0; $i<$row->size(); $i++){
			$row->next();

			$arr[$row->get('idx')] = $row->get('companyName');
		}

		return $arr;
	}

	function getReportBySQL($select, $from, $where, $group, $having, $order){
		global $db;

		$query = " SELECT ".$select
				." FROM " .$from
				." WHERE " .$where
				;
		if($group != '') $query += " GROUP BY " .$group;
		if($having != '') $query += " HAVING " .$group;
		if($order != '') $query += " ORDER BY " .$group;

	

		$row = $db->getListSet($query);

		return $row;
	}
}
?>