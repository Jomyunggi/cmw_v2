<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	include_once ADMIN_CLASS_PATH . '/class.reportPage.php';
	$M_ReportPage = new M_ReportPage;

	$companyIdx = $M_FUNC->M_Filter(POST, 'companyIdx');
	$searchTerm = $M_FUNC->M_Filter(POST, 'searchTerm');
	$optionN	= $M_FUNC->M_Filter(POST, 'optionN');
	$keyword	= $M_FUNC->M_Filter(POST, 'keyword');

	switch($companyIdx){
		case 35 : $table = "Coupang_Report"; break;
	}
	
	$addWhere = " AND keyword = '".$keyword."' ";
	if($M_ReportPage->searchTerm[1][$searchTerm][0] != 0){
		$addWhere .= " AND date between ".date('Ymd', $M_ReportPage->searchTerm[1][$searchTerm][0])." AND ".date('Ymd', $M_ReportPage->searchTerm[1][$searchTerm][1])." ";
	}
	
	$column = " date, optionN, keyword, view, click, cpc, salesCnt, salesCnt_D, salesCnt_I, salesPrice, salesPrice_D, salesPrice_I, salesPrice / cpc * 100 as roas ";
	$order = " ORDER BY date desc ";

	$query = " SELECT ". $column
			." FROM ".$table
			." WHERE optionN = '".$optionN."' "
			. $addWhere
			. $order
			;	

	$row = $db->getListSet($query);
	
	$arr = array();
	if($row->size() > 0){
		$NO = $row->size();
		$view_T = 0;
		$click_T = 0;
		$cpc_T = 0;
		$cnt_T = 0;
		$price_T = 0;
		for($i=0;$i<$row->size();$i++){
			$row->next();
			
			$view_T += $row->get('view');
			$click_T += $row->get('click');
			$cpc_T += $row->get('cpc');
			$cnt_T += $row->get('salesCnt');
			$price_T += $row->get('salesPrice');
			
			$click_1cost = number_format($row->get('cpc') / $row->get('click'));
			$ctr = $row->get('click') / $row->get('view') * 100;
			$cvr = $row->get('salesCnt') / $row->get('click') * 100;
			$roas = $row->get('salesPrice') / $row->get('cpc') * 100;

			$arr[$NO]['date']			= date('Y/m/d', strtotime($row->get('date')));
			$arr[$NO]['view']			= number_format($row->get('view'));
			$arr[$NO]['click']		= number_format($row->get('click'));
			$arr[$NO]['ctr']			= number_format($ctr, 2)."%";
			$arr[$NO]['cpc']			= number_format($row->get('cpc'));
			$arr[$NO]['click_1cost']	= number_format($click_1cost);
			$arr[$NO]['salesCnt']		= number_format($row->get('salesCnt'));
			$arr[$NO]['salesPrice']	= number_format($row->get('salesPrice'));
			$arr[$NO]['cvr']			= number_format($cvr, 2)."%";
			$arr[$NO]['roas']			= number_format($roas, 2)."%";

			$NO--;
		}

		$arr[$row->size()+1]['view']		= number_format($view_T);
		$arr[$row->size()+1]['click']		= number_format($click_T);
		$arr[$row->size()+1]['ctr']			= number_format($click_T / $view_T * 100, 2)."%";
		$arr[$row->size()+1]['cpc']			= number_format($cpc_T);
		$arr[$row->size()+1]['click_1cost']	= number_format(($cpc_T/$click_T));
		$arr[$row->size()+1]['salesCnt']	= number_format($cnt_T);
		$arr[$row->size()+1]['salesPrice']	= number_format($price_T);
		$arr[$row->size()+1]['cvr']			= number_format($cnt_T / $click_T * 100, 2)."%";
		$arr[$row->size()+1]['roas']		= number_format($price_T/$cpc_T * 100, 1)."%";		
	}

	sort($arr);

	echo "<pre>";
	print_r($arr);
	exit;

	echo json_encode($arr);
?>