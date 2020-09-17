<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	//전페이지에서 받아온 판매처 idx값
	$way	 = $M_FUNC->M_Filter(POST, 'way');
	$gubun	 = $M_FUNC->M_Filter(POST, 'gubun');
	
	if($way == "all"){
		$start		= date('Ymd', $M_FUNC->M_Filter(POST, 'start'));
		$end		= date('Ymd', $M_FUNC->M_Filter(POST, 'end'));
		$table		= " Deal_".substr($start, 0, 4);
		$where		= " WHERE dstart = 0 AND date between ". $start ." AND ". $end;
	} else {
		$dealIdx	= $M_FUNC->M_Filter(POST, 'dealIdx');
		$year		= $M_FUNC->M_Filter(POST, 'year');
		$month		= $M_FUNC->M_Filter(POST, 'month');
		$table		= "Deal_".$year;
		$where		= " WHERE idx = ". $dealIdx;
	}
	
	if($gubun == "dstart"){
		$data = array('dstart'	=> time());
		$result = 1;
	} else {
		$data = array('dend'	=> time());
		$result = 11;
	}

	$db->update($table, $data, $where);

	echo $result;
?>