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

	function getReportByCampaign($companyIdx, $searchTerm){
		global $db;

		switch($companyIdx){
			case 35 : $table = "Coupang_Report"; break;
		}
		
		$addWhere = '';
		if($this->searchTerm[1][$searchTerm][0] != 0){
			$addWhere .= " AND date between ".date('Ymd', $this->searchTerm[1][$searchTerm][0])." AND ".date('Ymd', $this->searchTerm[1][$searchTerm][1]);
		}

		$query = " SELECT campaign, sum(view) as view, sum(click) as click, sum(cpc) as cpc, sum(salesCnt) as salesCnt, "
				." sum(salesCnt_D) as salesCnt_D, sum(salesCnt_I) as salesCnt_I, sum(salesPrice) as salesPrice,  sum(salesPrice_D) as salesPrice_D, sum(salesPrice_I) as salesPrice_I "
				." FROM ".$table
				." WHERE 1 = 1 "
				. $addWhere
				." GROUP BY campaign "
				;	

		$row = $db->getListSet($query);

		return $row;
	}

	function getReportByGoods($companyIdx, $searchTerm, $campaign){
		global $db;

		switch($companyIdx){
			case 35 : $table = "Coupang_Report"; break;
		}

		$addWhere = '';
		if($this->searchTerm[1][$searchTerm][0] != 0){
			$addWhere .= " AND date between ".date('Ymd', $this->searchTerm[1][$searchTerm][0])." AND ".date('Ymd', $this->searchTerm[1][$searchTerm][1]);
		}

		$query = " SELECT campaign, goodsName, optionN, sum(view) as view, sum(click) as click, sum(cpc) as cpc, sum(salesCnt) as salesCnt, "
				." sum(salesCnt_D) as salesCnt_D, sum(salesCnt_I) as salesCnt_I, sum(salesPrice) as salesPrice,  sum(salesPrice_D) as salesPrice_D, sum(salesPrice_I) as salesPrice_I "
				." FROM ".$table
				." WHERE campaign = '".$campaign."' "
				. $addWhere
				." GROUP BY goodsName "
				;	

		$row = $db->getListSet($query);

		return $row;
	}

	function getReportByKeyword($companyIdx, $searchTerm, $campaign, $optionN){
		global $db;

		switch($companyIdx){
			case 35 : $table = "Coupang_Report"; break;
		}
		
		$addWhere = '';
		if($this->searchTerm[1][$searchTerm][0] != 0){
			$addWhere .= " AND date between ".date('Ymd', $this->searchTerm[1][$searchTerm][0])." AND ".date('Ymd', $this->searchTerm[1][$searchTerm][1]);
		} 
		if($campaign != ''){
			$addWhere .= " AND campaign = '".$campaign."'";
		}

		//검색일 일수로 전환
		$dateDifference = abs($this->searchTerm[1][$searchTerm][1] - $this->searchTerm[1][$searchTerm][0]);
		$differ_day = $dateDifference / (60 * 60 * 24) ;

		$query = " SELECT campaign, goodsName, optionN, keyword, sum(view) as view, sum(click) as click, sum(cpc) as cpc, sum(salesCnt) as salesCnt, "
				." sum(salesCnt_D) as salesCnt_D, sum(salesCnt_I) as salesCnt_I, sum(salesPrice) as salesPrice,  sum(salesPrice_D) as salesPrice_D, sum(salesPrice_I) as salesPrice_I "
				." , sum(salesPrice) / sum(cpc) * 100 as roas "
				." FROM ".$table
				." WHERE optionN = '".$optionN."' "
				. $addWhere
				." GROUP BY keyword "
				." HAVING click > 0 AND view > ". $differ_day
				." ORDER BY roas desc, view asc"
				;	

		$row = $db->getListSet($query);

		return $row;
	}

	function getReportByDaily($companyIdx, $searchTerm, $campaign, $optionN, $keyword){ 
		global $db;

		switch($companyIdx){
			case 35 : $table = "Coupang_Report"; break;
		}

		$addWhere = '';
		if($this->searchTerm[1][$searchTerm][0] != 0){
			$addWhere .= " AND date between ".date('Ymd', $this->searchTerm[1][$searchTerm][0])." AND ".date('Ymd', $this->searchTerm[1][$searchTerm][1]);
		}

		//검색일 일수로 전환
		$dateDifference = abs($this->searchTerm[1][$searchTerm][1] - $this->searchTerm[1][$searchTerm][0]);
		$differ_day = $dateDifference / (60 * 60 *24) ;

		$query = " SELECT date, campaign, goodsName, optionN, keyword, sum(view) as view, sum(click) as click, sum(cpc) as cpc, sum(salesCnt) as salesCnt, "
				." sum(salesCnt_D) as salesCnt_D, sum(salesCnt_I) as salesCnt_I, sum(salesPrice) as salesPrice,  sum(salesPrice_D) as salesPrice_D, sum(salesPrice_I) as salesPrice_I "
				." FROM ".$table
				." WHERE campaign = '".$campaign."' AND optionN = '".$optionN."' AND keyword = '".$keyword."' "
				. $addWhere
				." GROUP BY date "
				." ORDER BY date asc "
				;	

		$row = $db->getListSet($query);

		return $row;
	}

	function getOption_goodsName($companyIdx){
		global $db;

		switch($companyIdx){
			case 35 : $table = "Coupang_Report"; break;
			default : $table = "Coupang_Report"; break;
		}

		$query = " SELECT optionN, goodsName "
				." FROM ".$table
				." WHERE 1 = 1 "
				." GROUP BY optionN "
				." ORDER BY click desc "
				;
		$row = $db->getListSet($query);
		
		$arr = array(0=>'상품옵션을 선택해주세요');
		if($row->size() > 0){
			for($i=0; $i<$row->size(); $i++){
				$row->next();

				$arr[$row->get('optionN')] = $row->get('goodsName');
			}
		}

		return $arr;
	}

	function getKeywordByOption($companyIdx, $searchTerm, $optionN, $keyword, $roas, $group, $having){
		global $db;
		
		//검색일 일수로 전환
		if($searchTerm == 9){
			$dateDifference = abs(time() - strtotime('20191120'));
			$differ_day = round($dateDifference / (60 * 60 *24), 0) ;
		} else {
			$dateDifference = abs($this->searchTerm[1][$searchTerm][1] - $this->searchTerm[1][$searchTerm][0]);
			$differ_day = $dateDifference / (60 * 60 *24) ;
		}

		switch($companyIdx){
			case 35 : $table = "Coupang_Report"; break;
		}
		
		$addWhere = '';
		if($this->searchTerm[1][$searchTerm][0] != 0){
			$addWhere .= " AND date between ".date('Ymd', $this->searchTerm[1][$searchTerm][0])." AND ".date('Ymd', $this->searchTerm[1][$searchTerm][1])." ";
		}
		$column = " optionN, keyword, sum(view) as view, sum(click) as click, sum(cpc) as cpc, sum(salesCnt) as salesCnt, "
				." sum(salesCnt_D) as salesCnt_D, sum(salesCnt_I) as salesCnt_I, sum(salesPrice) as salesPrice,  sum(salesPrice_D) as salesPrice_D, sum(salesPrice_I) as salesPrice_I "
				." , sum(salesCnt) / sum(click) * 100 as cvr "
				." , sum(salesPrice) / sum(cpc) * 100 as roas "
				." , case when sum(view) < ". $differ_day." then 1 else 2 end orderRate "
				;

		$order = " ORDER BY orderRate desc "
				." , (case when sum(view) >= ". $differ_day." then sum(salesCnt) / sum(click) * 100 end) desc "
				." , (case when sum(view) < ". $differ_day." then sum(view) end) desc "
				;

		if($keyword !=''){
			$column = " date, optionN, keyword, view, click, cpc, salesCnt, salesCnt_D, salesCnt_I, salesPrice, salesPrice_D, salesPrice_I, salesPrice / cpc * 100 as roas ";
			$addWhere .= " AND keyword = '".$keyword."' ";
			$order = " ORDER BY date desc ";
		}

		switch($roas){
			case 0 : $addWhere .= ""; break;
			case 1 : $addWhere .= " AND roas > 0 "; break;
			case 2 : $addWhere .= " AND roas = 0 "; break;
		}

		$query = " SELECT ". $column
				." FROM ".$table
				." WHERE optionN = '".$optionN."' "
				. $addWhere
				. $group
				. $having
				. $order
				;	

		$row = $db->getListSet($query);

		return $row;
	}
}
?>