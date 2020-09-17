<?php
	
	if(!class_exists('M_CALCULATE')) {
		include_once COMMON_CLASS . '/class.calculate.php';
	}

	class M_CalculatePage extends M_CALCULATE {
		// Constructor

		function __construct() {
			parent::__construct();
		}

		// Destructor
		function __destruct() {

		}

		function lpad($str, $len, $padstr){
			$tmpStr = '';
			for($i=0; $i<$len; $i++){
				$tmpStr .= $padstr;
			}

			return substr($tmpStr.$str, -$len);
		}
		
		/**********************
		//UnitCost 정보 가져오기
		**********************/
		function getUnicost(){
			global $db;
						
			$costArr = array();
			$costQuery = " select * from UnitCost_Info ";
			$costRow = $db->getListSet($costQuery);
			$costRow->next();

			$costArr['cpu'][1]			= $costRow->get('cpu_1year');
			$costArr['cpu'][3]			= $costRow->get('cpu_3year');
			$costArr['memory'][1]		= $costRow->get('memory_1year');
			$costArr['memory'][3]		= $costRow->get('memory_3year');
			$costArr['disk'][1]			= $costRow->get('disk_1year');
			$costArr['disk'][3]			= $costRow->get('disk_3year');
			$costArr['OS'][1]			= $costRow->get('OS');
			$costArr['mssql'][1]		= 0;
			$costArr['mssql'][4]		= $costRow->get('mssql_4_year');
			$costArr['mssql'][8]		= $costRow->get('mssql_8_year');
			$costArr['mssql'][12]		= $costRow->get('mssql_12_year');
			$costArr['lbsS'][1]		  = $costRow->get('lbsS_1year');
			$costArr['lbsS'][3]		  = $costRow->get('lbsS_3year');
			$costArr['lbsM'][1]		  = $costRow->get('lbsM_1year');
			$costArr['lbsM'][3]		  = $costRow->get('lbsM_3year');
			$costArr['lbsL'][1]		  = $costRow->get('lbsL_1year');
			$costArr['lbsL'][3]		  = $costRow->get('lbsL_3year');
			$costArr['ssl'][1]		  = $costRow->get('ssl_1year');
			$costArr['ssl'][3]		  = $costRow->get('ssl_3year');
			$costArr['officialIP'][1] = $costRow->get('officialIP_1year');
			$costArr['officialIP'][3] = $costRow->get('officialIP_3year');

			return $costArr;
		}

		/************************************************************
		//해당 VM별 serviceIdx, companyIdx, businessIdx 배열로 담아놓기
		************************************************************/
		function getVMRelation_Info(){
			global $db;

			$vmByIdx_arr = array();
			$query = ' SELECT ai.businessIdx, ai.companyIdx, ai.roleIdx as serviceIdx, va.vmIdx '
					.' FROM VM_Account_Link va '
					.'	INNER JOIN AccountRole_Info ai on va.accountIdx = ai.accountIdx '
					;
			$row = $db->getListSet($query);
			for($i=0; $i<$row->size(); $i++){
				$row->next();
				
				$vmByIdx_arr[$row->get('vmIdx')] = array(
					'businessIdx'	=>	$row->get('businessIdx'),
					'companyIdx'	=>	$row->get('companyIdx'),
					'serviceIdx'	=>	$row->get('serviceIdx')
				);
			}

			return $vmByIdx_arr;
		}

		/*********************************************************************
		//OS, MSSQL은 월 계산이므로 해당 기간안에 OS, MSSQL이 측정됬는지 확인 여부
		*********************************************************************/
		function monthly_calculate($vmIdx, $startdate){
			global $db;
			
			$returnYN = 0;
			$query = " SELECT "
					."		r.vmIdx "
					."		, IFNULL(sum(r.OS_price), 0) as OS_price "
					."		, IFNULL(sum(r.mssql_price), 0) as mssql_price "
					." FROM ReportDataByVmDay r "
					." WHERE r.status = 1 and left(r.dateYmd, 6) = ".substr($startdate, 0,6)." and r.vmIdx = " . $vmIdx
					;
			$Mrow = $db->getListSet($query);
			
			if($Mrow->size() > 0){
				for($i=0; $i<$Mrow->size(); $i++){
					$Mrow->next();
					
					if($Mrow->get('OS_price') == 0){
						$returnYN += 1;
					} 
					if($Mrow->get('mssql_price') == 0){
						$returnYN += 2;
					} 
				}
			} else {
				$returnYN += 3;
			}
			
			return $returnYN;
		}

		/*****************************************************************
		//ip, lbs은 월 계산이므로 해당 기간안에 ip, lbs가 측정됬는지 확인 여부
		*****************************************************************/
		function monthly_calculateByService($serviceIdx, $startdate){
			global $db;
			
			$returnYN = 0;
			$query = " select r.serviceIdx, IFNULL(sum(r.ip_price),0) as ip_price, IFNULL(sum(r.loadbalancer_price),0) as loadbalancer_price "
					." from ReportDataByServiceDay r "
					." where r.`status` = 1 and left(r.dateYmd, 6) = ".substr($startdate, 0,6)." and r.serviceIdx = ". $serviceIdx
					." group by r.serviceIdx "
					;
			$serviceRow = $db->getListSet($query);
			
			if($serviceRow->size() > 0){		
				for($i=0; $i<$serviceRow->size(); $i++){
					$serviceRow->next();
					
					if($serviceRow->get('ip_price') == 0){
						$returnYN += 1;
					} 
					if($serviceRow->get('loadbalancer_price') == 0){
						$returnYN += 2;
					} 
				}
			} else {
				$returnYN += 3;
			}
			
			return $returnYN;
		}

		function VmPost_Processing($vmIdx){
			global $db, $M_JS;

			@include_once ADMIN_CLASS_PATH .'/class.vmPage_v2.php';
			$M_VmPage = new M_VmPage;

			//등록된 vmIdx를 가지고 VM정보를 다 가져온다.
			$vmRow = $M_VmPage->getVmInfoByIdx($vmIdx);
			$vmRow->next();

			//vmw정보 배열로 담기
			if($vmRow->size() > 0){
				$data = array(
					'vmIdx' => $vmRow->get('idx'),
					'vmType' => $vmRow->get('vmType'),
					'vmName' => $vmRow->get('vmName'),
					'cpu' => $vmRow->get('cpu'),
					'memory' => $vmRow->get('memory'),
					'disk' => $vmRow->get('disk'),
					'disk_os' => $vmRow->get('disk_os'),
					'disk_data' => $vmRow->get('disk_data'),
					'contract_c' => $vmRow->get('contract_c'),
					'contract_d' => $vmRow->get('contract_d'),
					'v_startDate' => $vmRow->get('v_startDate'),
					'v_endDate' => $vmRow->get('v_endDate'),
					'status' => $vmRow->get('status'),
					'OS' => $vmRow->get('OS'),
					'OS_check' => $vmRow->get('OS_check'),
					'dbUse' => $vmRow->get('dbUse'),
					'dbName' => $vmRow->get('dbName'),
					'DBMS' => $vmRow->get('DBMS'),
					'db_startDate' => $vmRow->get('db_startDate'),
				);
			}

			$startdate = $data['v_startDate'];
			$enddate = date('Ymd', strtotime('1 day ago'));
			
			/***************************************************************
			//Vm 서비스 기간만큼 Daily로 vm insert 후 정산내용 delete 후 재정산
			***************************************************************/
			if(count($data) > 0){
				//VMDataByDay에 기간에 맞춰 없으면 일자별로 Insert 후 return true, 있으면 return false;
				$recalculateYN = $this->insertVmDataByDay($vmIdx, $startdate, $enddate, $data);

				if($recalculateYN){
					// 일자별 해당 서비스 VM대수 insert 시키기
					$this->stackForServerCnt($vmIdx, $startdate, $enddate);

					//ReportDataByVmDay 기간에 맞게 delete
					$this->deleteForReportDataByVmDay($startdate, $enddate);

					//ReportDataByVmDay 삭제 후 그 기간에 맞게 재정산
					$this->calculateForVmDataByDay($startdate, $enddate);
				} else {
					$msg = 'VM:'.$data['vmName'].'은 일자별로 쌓인 데이터가 있습니다.\n관리자에게 문의하십시오.';
					$M_JS->replace_URL('?M0201', $msg, '');
					exit;
				}
			}
		}
		
		/*****************************************
			Vm 등록이므로 해당 일수의 대해 모든 데이터를 삭제할 필요가
			없으므로 해당되는 VM 한개에 대해서만 처리해준다.(속도면에서)
		******************************************/
		function VmPost_Processing_v2($vmIdx){
			global $db, $M_JS;

			@include_once ADMIN_CLASS_PATH .'/class.vmPage_v2.php';
			$M_VmPage = new M_VmPage;

			//등록된 vmIdx를 가지고 VM정보를 다 가져온다.
			$vmRow = $M_VmPage->getVmInfoByIdx($vmIdx);
			$vmRow->next();

			//vmw정보 배열로 담기
			if($vmRow->size() > 0){
				$data = array(
					'vmIdx' => $vmRow->get('idx'),
					'vmType' => $vmRow->get('vmType'),
					'vmName' => $vmRow->get('vmName'),
					'cpu' => $vmRow->get('cpu'),
					'memory' => $vmRow->get('memory'),
					'disk' => $vmRow->get('disk'),
					'disk_os' => $vmRow->get('disk_os'),
					'disk_data' => $vmRow->get('disk_data'),
					'contract_c' => $vmRow->get('contract_c'),
					'contract_d' => $vmRow->get('contract_d'),
					'v_startDate' => $vmRow->get('v_startDate'),
					'v_endDate' => $vmRow->get('v_endDate'),
					'status' => $vmRow->get('status'),
					'OS' => $vmRow->get('OS'),
					'OS_check' => $vmRow->get('OS_check'),
					'dbUse' => $vmRow->get('dbUse'),
					'dbName' => $vmRow->get('dbName'),
					'DBMS' => $vmRow->get('DBMS'),
					'db_startDate' => $vmRow->get('db_startDate'),
				);
			}

			$startdate = $data['v_startDate'];
			$enddate = date('Ymd', strtotime('1 day ago'));
			
			/***************************************************************
			//Vm 서비스 기간만큼 Daily로 vm insert 후 정산내용 delete 후 재정산
			***************************************************************/
			if(count($data) > 0){
				//VMDataByDay에 기간에 맞춰 없으면 일자별로 Insert 후 return true, 있으면 return false;
				$recalculateYN = $this->insertVmDataByDay($vmIdx, $startdate, $enddate, $data);

				if($recalculateYN){
					// 일자별 해당 서비스 VM대수 insert 시키기
					$this->stackForServerCnt($vmIdx, $startdate, $enddate);

					//ReportDataByVmDay 삭제 후 그 기간에 맞게 재정산
					$this->calculateForVmDataByDay_v2($startdate, $enddate, $vmIdx);
				} else {
					$msg = 'VM:'.$data['vmName'].'은 일자별로 쌓인 데이터가 있습니다.\n관리자에게 문의하십시오.';
					$M_JS->replace_URL('?M0201', $msg, '');
					exit;
				}
			}
		}

		function monthlyCalculateByServiceOption($serviceIdx, $startdate){
			global $db;
			
			$this_Month = strtotime(date('Ymd', time()));
			$first_Month = strtotime($startdate);
			
			$return_Arr = array();
			for($dd=$first_Month; $dd<=$this_Month; $dd=strtotime('first day of +1 months', $dd)){
				$dateYmd = date('Ymd', $dd);
				
				$query = " select r.serviceIdx, IFNULL(sum(r.ip_price),0) as ip_price, IFNULL(sum(r.loadbalancer_price),0) as loadbalancer_price "
						." from ReportDataByServiceDay r "
						." where r.`status` = 1 and left(r.dateYmd, 6) = ".substr($dateYmd, 0,6)." and r.serviceIdx = ". $serviceIdx
						." group by r.serviceIdx "
						;
				$serviceRow = $db->getListSet($query);
				
				if($serviceRow->size() > 0){		
					for($i=0; $i<$serviceRow->size(); $i++){
						$serviceRow->next();
						
						$return_Arr[$serviceIdx][substr($dateYmd, 0,6)]['ip_price'] = $serviceRow->get('ip_price');
						$return_Arr[$serviceIdx][substr($dateYmd, 0,6)]['loadbalancer_price'] = $serviceRow->get('loadbalancer_price');
					}
				} else {
					$return_Arr[$serviceIdx][substr($dateYmd, 0,6)]['ip_price'] = 0;
					$return_Arr[$serviceIdx][substr($dateYmd, 0,6)]['loadbalancer_price'] = 0;
				}
			}
			
			return $return_Arr;
		}

		function calculateForServiceByAdditionalOption($insertData){
			$calculateData_Arr = array();
			for($i=0; $i<count($insertData); $i++){
				$ipcontract = $insertData[$i]['ip_contract'];
				$ips = explode(",", $insertData[$i]['ip']) != '' ? explode(",", $insertData[$i]['ip']) : array();
				$ip_startdates = str_replace("-", "", explode(",", $insertData[$i]['ip_startDate']));
				$ip_enddates = str_replace("-", "", explode(",", $insertData[$i]['ip_endDate']));
				$lbs_contract = $insertData[$i]['balance_contract'];
				$lbss = $insertData[$i]['loadbalancer'] != '' ? explode(",", $insertData[$i]['loadbalancer']) : array();
				$lbs_startdate = str_replace("-", "", explode(",", $insertData[$i]['loadBalancer_startDate']));
				$lbs_enddate = str_replace("-", "", explode(",", $insertData[$i]['loadBalancer_endDate']));

				for($j=0; $j<count($ips); $j++){
					if($ip_startdates[$j] != 0 && $ip_startdates[$j] <= $this->lpad($ip_enddates[$j], 8, 9)){
						$calculateData_Arr[$ip_startdates[$j]]['serviceIdx'] = $insertData[$i]['serviceIdx'];
						$calculateData_Arr[$ip_startdates[$j]]['ip_contract'] = $ipcontract;
						$calculateData_Arr[$ip_startdates[$j]]['ip'] .= $ips[$j].",";
						$calculateData_Arr[$ip_startdates[$j]]['ip_enddate'] .= $ip_enddates[$j].",";
					}
				}
				
				for($k=0; $k<count($lbss); $k++){
					if($lbs_startdate[$k] != 0 && $lbs_startdate[$k] <= $this->lpad($lbs_enddate[$k], 8, 9)){
						$calculateData_Arr[$lbs_startdate[$k]]['serviceIdx'] = $insertData[$i]['serviceIdx'];
						$calculateData_Arr[$lbs_startdate[$k]]['lbs_contract'] = $lbs_contract;
						$calculateData_Arr[$lbs_startdate[$k]]['loadbalancer'] .= $lbss[$k].",";
						$calculateData_Arr[$lbs_startdate[$k]]['lbs_enddate'] .= $lbs_enddate[$k].",";
					}
				}

				if($ips[0] != "" || $lbss[0] != ""){
					$calculateData_Arr = $this->nextMonthData($calculateData_Arr, $insertData[$i]);
				}
			}

			$this->insertDataByServiceOption($calculateData_Arr);
		}

		function nextMonthData($calcdata, $data){
			$ips = explode(",", $data['ip']);
			$ip_startdates = str_replace("-", "", explode(",", $data['ip_startDate']));
			$ip_enddates = str_replace("-", "", explode(",", $data['ip_endDate']));
			$lbss = explode(",", $data['loadbalancer']);
			$lbs_startdate = str_replace("-", "", explode(",", $data['loadBalancer_startDate']));
			$lbs_enddate = str_replace("-", "", explode(",", $data['loadBalancer_endDate']));

			$dateArr = array();
			$dateArr['IP'] = $ip_startdates;
			$dateArr['LBS'] = $lbs_startdate;
			$dateArr2 = array();
			$dateArr2['IP'] = $ip_enddates;
			$dateArr2['LBS'] = $lbs_enddate;

			$datetime2 = new DateTime(date('Ymd', time()));
			foreach($dateArr as $key => $arr){
				foreach($arr as $k => $v){
					$start = new DateTime($v);

					if(($datetime2->format('Y') - $start->format('Y')) == 0){
						$diffMonth = $datetime2->format('m') - $start->format('m');
					} else {
						$diffMonth = ($datetime2->format('Y') - $start->format('Y'))*12 + ($datetime2->format('m') - $start->format('m'));
					}

					for($j=1; $j<=$diffMonth; $j++){
						$diff_dateYmd = date('Ymd', strtotime('first day of '.$j.' months', strtotime($dateArr[$key][$k])));
						if($diff_dateYmd <= $this->lpad($dateArr2[$key][$k], 8, 9)){
							$calcdata[$diff_dateYmd]['serviceIdx']		= $data['serviceIdx'];
							if($key == 'IP'){
								$calcdata[$diff_dateYmd]['ip_contract']	= $data['ip_contract'];
								$calcdata[$diff_dateYmd]['ip']				.= $ips[$k].",";
								$calcdata[$diff_dateYmd]['ip_enddate']	.= $dateArr2[$key][$k].",";
							} else {
								$calcdata[$diff_dateYmd]['lbs_contract']	= $data['balance_contract'];
								$calcdata[$diff_dateYmd]['loadbalancer']	.= $lbss[$k].",";
								$calcdata[$diff_dateYmd]['lbs_enddate']	.= $dateArr2[$key][$k].",";
							}
						}
					}
				}
			} 

			return $calcdata;	
		}

		function changeVMInfoByStatus($vmIdx, $status, $startdate=null, $enddate=null){
			
			//중지, 삭제인 경우에는 서버대수 다운, Daily VmInfo 삭제, 정산된 데이터 삭제
			if($status != 1){
				//VMDataServerCnt 서버대수 조정
				$this->popForServerCnt($vmIdx, $enddate);

				//VMDataByDay 종료일 이후 데이터 삭제
				$this->deleteVMDataByDay($vmIdx, $enddate);
			} 
			// 사용인 경우에는 서버대수 업, Daily VmInfo 쌓기
			else {
				$yesterday		= date('Ymd', strtotime('-1day', time()));

				$this->pushForServerCnt($vmIdx, $startdate);
				$this->insertDataByVMDataByDay_v2($vmIdx, $startdate, $yesterday);
			}
		}
	}

?>