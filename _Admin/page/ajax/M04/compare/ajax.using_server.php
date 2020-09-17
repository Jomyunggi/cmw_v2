<?php
	@include_once "../../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	$serviceIdx = $M_FUNC->M_Filter(GET, 'serviceIdx');
	$year = $M_FUNC->M_Filter(GET, 'year');
	$month = $M_FUNC->M_Filter(GET, 'month');
	$startdate = $year.sprintf('%02d', $month)."01";
	$enddate = $year.sprintf('%02d', $month)."31";

	$result_array = array();
	
	//cpu, memory, disk 각각에 대한 총량을 가져오는 부분
	$query = " select cpu, memory, count(cpu) as cnt "
			." from Service_Info s "
			."		left join VM_Account_Link va on s.keyAccountIdx = va.accountIdx "
			."		left join VM_Info v on va.vmIdx = v.idx "
			." where s.idx = ".$serviceIdx." and ((v.`status` = 1 and  v_startDate <= ".$enddate.") OR (v.`status` in (4, 99) and v_endDate >= ".$startdate." and v_startDate <= ".$enddate."))"
			." group by cpu, memory "
			." order by cpu, memory ";
	$row = $db->getListSet($query);
	
	if($row->size() > 0){
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			$result_array[$i]['cpu']	= $row->get('cpu');
			$result_array[$i]['memory']	= $row->get('memory');
			$result_array[$i]['cnt']	= $row->get('cnt');
		}
	}

	echo json_encode($result_array);

?>