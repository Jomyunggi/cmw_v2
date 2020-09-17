<?php
	@include "D:\Project\gamevil_jmg/_Common/Inc/inc.config.php";
	@include "D:\Project\gamevil_jmg/_Common/Class/class.db.php";
	
	$db = new M_DB('TEST');
	//$dateYmd = $_SERVER['argv'][1] == "" ? date('Ymd', strtotime('1 day ago')) : $_SERVER['argv'][1];
	$dateYmd = "20170202";
	//UnitCost 정보 가져오기
	$costArr = getUnicost();

	//OS, MS-SQL인 경우 한달중 1일만 사용해도 한달 요금 부과
	$query = " select r.serviceIdx, IFNULL(sum(r.ip_price),0) as ip_price, IFNULL(sum(r.loadbalancer_price),0) as loadbalancer_price "
			." from ReportDataByServiceDay r "
			." where r.`status` = 1 and left(r.dateYmd, 6) = ".substr($dateYmd, 0,6)
			." group by r.serviceIdx "
			;
	$serviceRow = $db->getListSet($query);
	
	$ip_arr = array();
	$loadbalancer_arr = array();
	if($serviceRow->size() > 0){		
		for($i=0; $i<$serviceRow->size(); $i++){
			$serviceRow->next();
			
			if($serviceRow->get('ip_price') == 0){
				$ip_arr[$serviceRow->get('serviceIdx')] = 0;
			} 
			if($serviceRow->get('loadbalancer_price') == 0){
				$loadbalancer_arr[$serviceRow->get('serviceIdx')] = 0;
			} 
		}
	} else {
		$serviceRow = getServiceInfo();
		for($i=0; $i<$serviceRow->size(); $i++){
			$serviceRow->next();
			
			$ip_arr[$serviceRow->get('idx')] = 0;
			$loadbalancer_arr[$serviceRow->get('idx')] = 0;
		}
	}

	//추가적인 서비스 배열로 담기
	$query = " SELECT s.* "
			." FROM ServiceByAdditionalOption s "
			."	INNER JOIN Service_AdditionalOption_Link sal ON s.idx = sal.roleIdx "
			;
	$row = $db->getListSet($query);

	if($row->size() > 0){
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			$ipArr = explode(",", $row->get('ip'));
			$startDate_Arr = explode(",", str_replace("-", '', $row->get('ip_startDate')));
			$endDate_Arr = explode(",", str_replace("-", '', $row->get('ip_endDate')));

			$ipContract = $row->get('ip_contract');
			$ip_unitcost = $costArr['officialIP'][$ipContract];
			$ipCnt = 0;
			if(count($ipArr) > 0){
				$ip_priceSum = 0;
				if(array_key_exists($row->get('serviceIdx'), $ip_arr)){
					for($j=0; $j<count($ipArr); $j++){
						if($dateYmd >= $startDate_Arr[$j] && $dateYmd <= lpad($endDate_Arr[$j], 8, 9)) {
							$ip_priceSum += $costArr['officialIP'][$ipContract];
							$ipCnt++;
						}
					}
				}
			}
			
			$loadbalancer = 0;
			$lbs_priceSum = 0;
			if($row->get('balance_contract') > 0){				
				$lbs = explode(",", $row->get('loadbalancer'));
				$loadBalancer_startDate = explode(",", str_replace("-", "", $row->get('loadBalancer_startDate')));
				$loadBalancer_endDate = explode(",", str_replace("-", "", $row->get('loadBalancer_endDate')));
				
				if(count($lbs) > 0){
					if(array_key_exists($row->get('serviceIdx'), $loadbalancer_arr)){
						for($k=0; $k<count($lbs); $k++){
							switch($lbs[$k]){
								case 1 : $loadbalancer = $costArr['lbsS'][$row->get('balance_contract')]; break;
								case 2 : $loadbalancer = $costArr['lbsM'][$row->get('balance_contract')]; break;
								case 4 : $loadbalancer = $costArr['lbsL'][$row->get('balance_contract')]; break;
							}
							
							if ($dateYmd >= $loadBalancer_startDate[$k] && $dateYmd <= lpad($loadBalancer_endDate[$k], 8, 9)){					
								$lbs_priceSum += $loadbalancer;
							}
						}
					}
				}
			}

			$sum = $ip_priceSum + $lbs_priceSum;
			$data_service = array(
				'serviceIdx'		=>	$row->get('serviceIdx'),
				'dateYmd'			=>  $dateYmd,
				'year'				=>	substr($dateYmd, 0, 4),
				'month'				=>	substr($dateYmd, 4, 2),
				'ip_unitcost'		=>	$ip_unitcost,
				'ipCnt'				=>	$ipCnt,
				'loadbalancer_unitcost'	=> $loadbalancer,
				'ip_price'			=> $ip_priceSum,
				'loadbalancer_price'	=> $lbs_priceSum,
				'total_price'		=> $sum,
			);
			
			$db->insert('ReportDataByServiceDay', $data_service);
		}
		
	}

	function getUnicost(){
		global $db;
					
		$costArr = array();
		$costQuery = " select * from UnitCost_Info";
		$costRow = $db->getListSet($costQuery);
		
		for($i=0; $i<$costRow->size(); $i++){
			$costRow->next();

			$costArr['cpu'][1]			= $costRow->get('cpu_1year');
			$costArr['cpu'][3]			= $costRow->get('cpu_3year');
			$costArr['memory'][1]		= $costRow->get('memory_1year');
			$costArr['memory'][3]		= $costRow->get('memory_3year');
			$costArr['disk'][1]			= $costRow->get('disk_1year');
			$costArr['disk'][3]			= $costRow->get('disk_3year');
			$costArr['OS'][0]			= 0;
			$costArr['OS'][1]			= $costRow->get('OS');
			$costArr['mssql'][0]			= 0;
			$costArr['mssql'][1]			= 0;
			$costArr['mssql'][4]			= $costRow->get('mssql_4_year');
			$costArr['mssql'][8]			= $costRow->get('mssql_8_year');
			$costArr['mssql'][12]		= $costRow->get('mssql_12_year');
			$costArr['lbsS'][1]			= $costRow->get('lbsS_1year');
			$costArr['lbsS'][3]			= $costRow->get('lbsS_3year');
			$costArr['lbsM'][1]			= $costRow->get('lbsM_1year');
			$costArr['lbsM'][3]			= $costRow->get('lbsM_3year');
			$costArr['lbsL'][1]			= $costRow->get('lbsL_1year');
			$costArr['lbsL'][3]			= $costRow->get('lbsL_3year');
			$costArr['ssl'][1]			= $costRow->get('ssl_1year');
			$costArr['ssl'][3]			= $costRow->get('ssl_3year');
			$costArr['officialIP'][1]		= $costRow->get('officialIP_1year');
			$costArr['officialIP'][3]		= $costRow->get('officialIP_3year');
		}

		return $costArr;
	}

	function getServiceInfo(){
		global $db;

		$query = " SELECT * FROM Service_Info WHERE status = 1 ";
		$row = $db->getListSet($query);

		return $row;
	}

	function lpad($str, $len, $padstr){
		$tmpStr = '';
		for($i=0; $i<$len; $i++){
			$tmpStr .= $padstr;
		}

		return substr($tmpStr.$str, -$len);
	}
?>