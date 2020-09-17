<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	include_once ADMIN_CLASS_PATH .'/class.calculatePage.php';
	$M_Calculate = new M_CalculatePage;

	include_once ADMIN_CLASS_PATH .'/class.vmPage_v2.php';
	$M_Vm = new M_VmPage;

	$serviceIdx = $M_FUNC->M_Filter(POST, 'serviceIdx');
	$serviceName = $M_FUNC->M_Filter(POST, 'serviceName');
	$m_id			= $M_FUNC->M_Filter(POST, 'm_id');
	$type			= $M_FUNC->M_Filter(POST, 'type');
	$startdate	= $M_FUNC->M_Filter(POST, 'startdate');

	$query = " SELECT * "
				." FROM ServiceByAdditionalOption "
				." WHERE idx = ". $m_id;
	$row = $db->getListSet($query);
	$row->next();

	if($type == "P"){
		$cnt = count(explode(",", $row->get('ip')));
		$start = explode(",", $row->get('ip_startDate'));
	} else {
		$cnt = count(explode(",", $row->get('loadbalancer')));
		$start = explode(",", $row->get('loadBalancer_startDate'));
	}

	if($cnt == 1){
		//1개이면 delete
		$db->delete("ServiceByAdditionalOption", "where idx = ". $m_id);
	} else {
		//1개 이상이면 update
		$checkYN = 0;
		$power = 0;
		$count = '';
		$date_value = '';
		for($i=0; $i<$cnt; $i++){
			if($startdate == $start[$i]){
				if($checkYN){
					$count .= pow(2, $power).",";
					$date_value .= $start[$i].",";
					$power++;
				} else {
					$checkYN++;
				}
			} else {
				$count .= pow(2, $power).",";
				$date_value .= $start[$i].",";
				$power++;
			}
		}

		if($type == "P"){
			$data = array(
				'ip'	=>	substr($count, 0, -1),
				'ip_startDate' => substr($date_value, 0, -1)
			);
		} else {
			$data = array(
				'loadbalancer'	=>	substr($count, 0, -1),
				'loadBalancer_startDate' => substr($date_value, 0, -1)
			);
		}
		$db->update("ServiceByAdditionalOption", $data, "where idx = ". $m_id);
	}

	//서비스에 정산된 IP, LBS 데이터 DELETE
	$delete_where = " WHERE serviceIdx = ". $serviceIdx;
	$M_Calculate->deleteReportDataByService($delete_where);
	
	//update된 serviceOption값으로 재정산
	$serviceOptionRow = $M_Vm->getAdditionalOptionData($serviceIdx);
	$M_Calculate->calculateForServiceByAdditionalOption($serviceOptionRow->toArray());

	echo "OK";	
?>