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

	function getReportByCampaign($companyIdx, $startD, $endD){
		global $db;

		switch($companyIdx){
			case 35 : $table = "Coupang_Report"; break;
		}

		$query = " SELECT campaign, sum(view) as view, sum(click) as click, sum(cpc) as cpc, sum(salesCnt) as salesCnt, "
				." sum(salesCnt_D) as salesCnt_D, sum(salesCnt_I) as salesCnt_I, sum(salesPrice) as salesPrice,  sum(salesPrice_D) as salesPrice_D, sum(salesPrice_I) as salesPrice_I "
				." FROM ".$table
				." WHERE 1=1 "
				." GROUP BY campaign "
				;	

		$row = $db->getListSet($query);

		return $row;
	}

	function getReportByGoods($companyIdx, $startD, $endD, $campaign){
		global $db;

		switch($companyIdx){
			case 35 : $table = "Coupang_Report"; break;
		}

		$query = " SELECT campaign, sum(view) as view, sum(click) as click, sum(cpc) as cpc, sum(salesCnt) as salesCnt, "
				." sum(salesCnt_D) as salesCnt_D, sum(salesCnt_I) as salesCnt_I, sum(salesPrice) as salesPrice,  sum(salesPrice_D) as salesPrice_D, sum(salesPrice_I) as salesPrice_I "
				." FROM ".$table
				." WHERE 1=1 AND campaign = '".$campaign."' "
				." GROUP BY goodsName "
				;	

		$row = $db->getListSet($query);

		return $row;
	}
}
?>