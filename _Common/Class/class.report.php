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
}
?>