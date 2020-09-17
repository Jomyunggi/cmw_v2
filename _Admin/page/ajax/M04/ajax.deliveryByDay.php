<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	$date = $M_FUNC->M_Filter(POST, 'date');

	$query = " SELECT deal, dealNum, companyName, recipient, priceKinds "
			//." FROM ". DEAL_TABLE .date('Y', $date)
			." FROM Deal_".date('Y', strtotime($date))
			." WHERE onoff = 'On' AND status = 1 "
			." AND date = ". $date
			." ORDER BY recipient desc "
			;
	$row = $db->getListSet($query);
	
	$arr = array();
	if($row->size() > 0){
		$NO = $row->size();
		for($i=0;$i<$row->size();$i++){
			$row->next();

			$kinds = explode("|", $row->get('priceKinds'));
			$except = strpos($row->get('addr'), "제주");
			$exceptCnt = $except === false ? 0 : 1;
			$boxCnt = $kinds[2] + $kinds[3] + $kinds[4];
			$price = $kinds[2] * 2500 + $kinds[3] * 3000 + $kinds[4] * 3500;
			$totalP = $kinds[2] * 2500 + $kinds[3] * 3000 + $kinds[4] * 3500 + $exceptCnt * 3000;
			$exceptP = $exceptCnt * 3000;
			
			$arr[$NO]['num'] = $NO;
			$arr[$NO]['companyName'] = $row->get('companyName');
			$arr[$NO]['recipient'] = $row->get('recipient');
			$arr[$NO]['boxCnt'] = $boxCnt;
			$arr[$NO]['totalP'] = $totalP;
			$arr[$NO]['price'] = $price;
			$arr[$NO]['exceptP'] = $exceptP;
			$arr[$NO--]['dealName']	= $row->get('deal').", ".$row->get('dealNum')."개";
		}
	}

	echo json_encode($arr);
?>