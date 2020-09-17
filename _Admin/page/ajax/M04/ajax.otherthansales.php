<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	$year = $M_FUNC->M_Filter(POST, 'year');
	$result_array = array();
	
	include_once ADMIN_CLASS_PATH . "/class.dealPage.php";
	$M_DealPage = new M_DealPage;

	$query = " SELECT * "
			." FROM Deal_".$year
			." WHERE status <> 1 "
			;
	$row = $db->getListSet($query);
	
	$ds = 0;
	if($row->size() > 0){
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			$arr = explode("|", $row->get('priceKinds'));
			$small = $arr[2];
			$medium = $arr[3];
			$large = $arr[4];

			if($row->get('dend') > 0){
				$ds = 11;
			} else {
				if($row->get('dstart') > 0){
					$ds = 1;
				} else {
					$ds = 4;
				}
			}

			$result_array[$i]['status']			= $M_DealPage->dstatus[$row->get('status')];
			$result_array[$i]['date']			= date('Y.m.d', strtotime($row->get('date')));
			$result_array[$i]['time']			= date('Y.m.d H:i', strtotime($row->get('time')));
			$result_array[$i]['dend']			= date('Y.m.d', $row->get('dend'));
			$result_array[$i]['DS']				= $M_DealPage->searchDelivery[$ds];
			$result_array[$i]['companyName']	= $row->get('companyName');
			$result_array[$i]['buyer']			= $row->get('buyer');
			$result_array[$i]['recipient']		= $row->get('recipient');
			$result_array[$i]['tel']			= $row->get('tel');
			$result_array[$i]['deal']			= $row->get('deal');
			$result_array[$i]['addr']			= $row->get('addr');
			$result_array[$i]['priceKinds']		= $row->get('priceKinds');
			$result_array[$i]['small']			= $small;
			$result_array[$i]['medium']			= $medium;
			$result_array[$i]['large']			= $large;
		}
	}

	echo json_encode($result_array);

?>