<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	$serviceIdx = $M_FUNC->M_Filter(POST, 'serviceIdx');
	$serviceName = $M_FUNC->M_Filter(POST, 'serviceName');
	$m_id			= $M_FUNC->M_Filter(POST, 'm_id');
	$type			= $M_FUNC->M_Filter(POST, 'type');
	$startdate	= $M_FUNC->M_Filter(POST, 'startdate');
	$enddate	= $M_FUNC->M_Filter(POST, 'enddate');
	$value		= $M_FUNC->M_Filter(POST, 'value');
	$order		= $M_FUNC->M_Filter(POST, 'order');

	if($enddate < $startdate){
		echo "NO";
		exit;
	}

	include_once ADMIN_CLASS_PATH .'/class.vmPage_v2.php';
	$M_Vm = new M_VmPage;

	include_once ADMIN_CLASS_PATH .'/class.calculatePage.php';
	$M_Calculate = new M_CalculatePage;

	$query = " SELECT * "
				." FROM ServiceByAdditionalOption "
				." WHERE idx = ". $m_id;
	$row = $db->getListSet($query);
	
	if($row->size() > 0){
		$data = array();
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			switch($type){
				case 'P' : 
					$ip_enddate = explode(",", $row->get('ip_endDate'));
					$ip_enddate[$order-1] = $enddate; 
					$colunmName = 'ip_endDate'; 
					$updateArr = $ip_enddate; break;
				case 'B' : 
					$lbs_enddate = explode(",", $row->get('loadBalancer_endDate'));
					$lbs_enddate[$order-1] = $enddate;
					$colunmName = 'loadBalancer_endDate';
					$updateArr = $lbs_enddate; break;
			}
		}

		for($i=0; $i<count($updateArr); $i++){
			if($updateArr[$i] != 0)	$updateArr[$i] = date('Y-m-d', strtotime($updateArr[$i]));
		}
		
		$update_Data = array(
			$colunmName => implode(",", $updateArr)
		);

		$where = " WHERE serviceIdx = ". $serviceIdx . " AND idx = ". $m_id;
		$M_Vm->updateServiceOptionDataByIdx($update_Data, $where);
		
		//서비스에 정산된 IP, LBS 데이터 DELETE
		$delete_where = " WHERE serviceIdx = ". $serviceIdx;
		$M_Calculate->deleteReportDataByService($delete_where);
		
		//update된 serviceOption값으로 재정산
		$serviceOptionRow = $M_Vm->getAdditionalOptionData($serviceIdx);
		$M_Calculate->calculateForServiceByAdditionalOption($serviceOptionRow->toArray());

		$M_FUNC->recordActionLog("S", "M0201", '', "[".$serviceName."] 추가 서비스 수정 및 재정산");
		
		echo "OK";
		exit;
	}
?>