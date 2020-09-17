<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	$mode = $M_FUNC->M_Filter(POST, 'mode');
	$accountIdx = $M_FUNC->M_Filter(POST, 'accountIdx');
	$ip_contract = $M_FUNC->M_Filter(POST, "ip_contract");
	$ip = $_POST['ip'];
	$ip_startDate = $M_FUNC->M_Filter(POST, "ip_startDate");
	$ip_endDate = $M_FUNC->M_Filter(POST, "ip_endDate");
	/*$ip2_startDate = $M_FUNC->M_Filter(POST, "ip2_startDate");
	$ip2_endDate = $M_FUNC->M_Filter(POST, "ip2_endDate");
	$ip3_startDate = $M_FUNC->M_Filter(POST, "ip3_startDate");
	$ip3_endDate = $M_FUNC->M_Filter(POST, "ip3_endDate");
	$ip4_startDate = $M_FUNC->M_Filter(POST, "ip4_startDate");
	$ip4_endDate = $M_FUNC->M_Filter(POST, "ip4_endDate");
	$ip5_startDate = $M_FUNC->M_Filter(POST, "ip5_startDate");
	$ip5_endDate = $M_FUNC->M_Filter(POST, "ip5_endDate");*/
	$balance_contract = $M_FUNC->M_Filter(POST, "balance_contract");
	$loadBalancer = $M_FUNC->M_Filter(POST, "loadBalancer");
	$loadBalancer_startDate = $M_FUNC->M_Filter(POST, "loadBalancer_startDate");
	$loadBalancer_endDate = $M_FUNC->M_Filter(POST, "loadBalancer_endDate");

	if($ip_endDate == ''){
		$ip_endDate = '0';
	}

	$query = " SELECT * from AccountRole_Info Where level = 8 AND accountIdx = " .$accountIdx;
	$row = $db->getListSet($query);
	$row->next();
	
	if($mode == "regist"){
		if($row->get('roleIdx') > 0){
			$data = array(
				'serviceIdx'	=>	$row->get('roleIdx'),
				'ip_contract'	=>	$ip_contract,
				'ip'			=>	$ip,
				'ip_startDate'	=>	$ip_startDate,
				'ip_endDate'	=>	$ip_endDate,
				/*'ip2_startDate'	=>	substr("-", "", $ip2_startDate),
				'ip2_endDate'	=>	substr("-", "", $ip2_endDate),
				'ip3_startDate'	=>	substr("-", "", $ip3_startDate),
				'ip3_endDate'	=>	substr("-", "", $ip3_endDate),
				'ip4_startDate'	=>	substr("-", "", $ip4_startDate),
				'ip4_endDate'	=>	substr("-", "", $ip4_endDate),
				'ip5_startDate'	=>	substr("-", "", $ip5_startDate),
				'ip5_endDate'	=>	substr("-", "", $ip5_endDate),*/
				'balance_contract'	=>	$balance_contract,
				'loadbalancer'	=>	$loadBalancer,
				'regUnixtime'	=>	time(),
				'loadBalancer_startDate'	=> $loadBalancer_startDate,
				'loadBalancer_endDate'	=>	$loadBalancer_endDate,
			);

			$db->insert('ServiceByAdditionalOption', $data);

			$data['dateYmd'] = date('Ymd', time());

			//history
			$db->insert('ServiceByAdditionalOption_History', $data);

			$idx = $db->getLastIdx('idx', 'ServiceByAdditionalOption');

			$link_data = array(
				'serviceIdx'	=> $row->get('roleIdx'),
				'roleIdx'		=> $idx
			);

			$db->insert('Service_AdditionalOption_Link', $link_data);			
			
			echo "success";
		} else {
			echo 'fail';
		}
	}
?>