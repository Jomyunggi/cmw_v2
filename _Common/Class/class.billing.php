<?php
	/*********************************************************************
	*    Description	:	Class for User page
	*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
	*    Date			:	2012. 05. 15
	*    Have a nice day, Good Luck to you ^^/
	*********************************************************************/
	class M_BILLING {
		function __construct() {
					
		}

		function __destruct() {
			
		}

		function getUnitCostRow($addWhere = null) {
			global  $db;

			$query = ''
				.'	SELECT *  '
				//. 'idx, cpu_1year, cpu_3year, memory_1year, memory_3year, disk_1year, disk_3year, OS, regUnixtime, editUnixtime, status '
				.'	FROM UnitCost_Info '
				.'	WHERE status <> 99' 
				. $addWhere
				.'	ORDER BY idx desc '
				.'	LIMIT 1 '
				;
			$row = $db->getListSet($query);
			return $row;
		}

		function getCountUnit() {
			global $db;

			$query = ' SELECT COUNT(*) cnt FROM UnitCost_Info WHERE 1 = 1 ';
			//echo $query;
			$row = $db->getListSet($query);
			$row->next();
			return $row->get('cnt');
		}

		function registCostData() {
			global $M_FUNC, $db;
			global $MENU_ID;

			$cpu_1year			= $M_FUNC->M_Filter(POST, 'cpu_1year'); 
			$cpu_3year			= $M_FUNC->M_Filter(POST, 'cpu_3year'); 
			$memory_1year		= $M_FUNC->M_Filter(POST, 'memory_1year'); 
			$memory_3year		= $M_FUNC->M_Filter(POST, 'memory_3year'); 
			$disk_1year			= $M_FUNC->M_Filter(POST, 'disk_1year'); 
			$disk_3year			= $M_FUNC->M_Filter(POST, 'disk_3year');
			$OS					= $M_FUNC->M_Filter(POST, 'OS');
			$mssql_4_year		= $M_FUNC->M_Filter(POST, 'mssql_4_year');
			$mssql_8_year		= $M_FUNC->M_Filter(POST, 'mssql_8_year');
			$mssql_12_year		= $M_FUNC->M_Filter(POST, 'mssql_12_year');
			$lbsS_1year			= $M_FUNC->M_Filter(POST, 'lbsS_1year');
			$lbsS_3year			= $M_FUNC->M_Filter(POST, 'lbsS_3year');
			$lbsM_1year			= $M_FUNC->M_Filter(POST, 'lbsM_1year');
			$lbsM_3year			= $M_FUNC->M_Filter(POST, 'lbsM_3year');
			$lbsL_1year			= $M_FUNC->M_Filter(POST, 'lbsL_1year');
			$lbsL_3year			= $M_FUNC->M_Filter(POST, 'lbsL_3year');
			$ssl_1year			= $M_FUNC->M_Filter(POST, 'ssl_1year');
			$ssl_3year			= $M_FUNC->M_Filter(POST, 'ssl_3year');
			$officialIP_1year	= $M_FUNC->M_Filter(POST, 'officialIP_1year');
			$officialIP_3year	= $M_FUNC->M_Filter(POST, 'officialIP_3year');

			$query = ''
				.'	INSERT INTO UnitCost_Info (cpu_1year, cpu_3year, memory_1year, memory_3year, disk_1year, disk_3year, regUnixtime, editUnixtime, OS, mssql_4_year, mssql_8_year, mssql_12_year, lbsS_1year, lbsS_3year, lbsM_1year, lbsM_3year, lbsL_1year, lbsL_3year, ssl_1year, ssl_3year, officialIP_1year, officialIP_3year) '
				.'	VALUES ( '
				.		$cpu_1year
				.'		, ' . $cpu_3year
				.'		, ' . $memory_1year
				.'		, ' . $memory_3year
				.'		, ' . $disk_1year
				.'		, ' . $disk_3year
				.'		, ' . $OS
				.'		, ' . $mssql_4_year
				.'		, ' . $mssql_8_year
				.'		, ' . $mssql_12_year
				.'		, ' . $lbsS_1year
				.'		, ' . $lbsS_3year
				.'		, ' . $lbsM_1year
				.'		, ' . $lbsM_3year
				.'		, ' . $lbsL_1year
				.'		, ' . $lbsL_3year
				.'		, ' . $ssl_1year
				.'		, ' . $ssl_3year
				.'		, ' . $officialIP_1year
				.'		, ' . $officialIP_3year
				.'		, ' . time()
				.'		, ' . time()
				.' ) '
				.'	ON DUPLICATE KEY UPDATE '
				.'		cpu_1year = ' . $cpu_1year
				.'		, cpu_3year = ' . $cpu_3year
				.'		, memory_1year = ' . $memory_1year
				.'		, memory_3year = ' . $memory_3year
				.'		, disk_1year = ' . $disk_1year
				.'		, disk_3year = ' . $disk_3year
				.'		, editUnixtime = ' . time()
				.'		, OS = '. $OS
				.'		, mssql_4_year = ' . $mssql_4_year
				.'		, mssql_8_year = ' . $mssql_8_year
				.'		, mssql_12_year = ' . $mssql_12_year
				.'		, lbsS_1year = ' . $lbsS_1year
				.'		, lbsS_3year = ' . $lbsS_3year
				.'		, lbsM_1year = ' . $lbsM_1year
				.'		, lbsM_3year = ' . $lbsM_3year
				.'		, lbsL_1year = ' . $lbsL_1year
				.'		, lbsL_3year = ' . $lbsL_3year
				.'		, ssl_1year = ' . $ssl_1year
				.'		, ssl_3year = ' . $ssl_3year
				.'		, officialIP_1year = ' . $officialIP_1year
				.'		, officialIP_3year = ' . $officialIP_3year
				;
			$db->execute($query);
			$costIdx = mysql_insert_id();

			$this->insertUnitCostHistoryData($costIdx);

			$M_FUNC->recordActionLog("P", $MENU_ID, $costIdx, "단가표 등록");
		}

		function updateCostData(){
			global  $M_FUNC, $db;
			global  $MENU_ID;
			
			$costIdx			= $M_FUNC->M_Filter(POST, 'costIdx');
			$cpu_1year			= $M_FUNC->M_Filter(POST, 'cpu_1year'); 
			$cpu_3year			= $M_FUNC->M_Filter(POST, 'cpu_3year'); 
			$memory_1year		= $M_FUNC->M_Filter(POST, 'memory_1year'); 
			$memory_3year		= $M_FUNC->M_Filter(POST, 'memory_3year'); 
			$disk_1year			= $M_FUNC->M_Filter(POST, 'disk_1year'); 
			$disk_3year			= $M_FUNC->M_Filter(POST, 'disk_3year'); 
			$OS					= $M_FUNC->M_Filter(POST, 'OS');
			$mssql_4_year		= $M_FUNC->M_Filter(POST, 'mssql_4_year');
			$mssql_8_year		= $M_FUNC->M_Filter(POST, 'mssql_8_year');
			$mssql_12_year		= $M_FUNC->M_Filter(POST, 'mssql_12_year');
			$lbsS_1year			= $M_FUNC->M_Filter(POST, 'lbsS_1year');
			$lbsS_3year			= $M_FUNC->M_Filter(POST, 'lbsS_3year');
			$lbsM_1year			= $M_FUNC->M_Filter(POST, 'lbsM_1year');
			$lbsM_3year			= $M_FUNC->M_Filter(POST, 'lbsM_3year');
			$lbsL_1year			= $M_FUNC->M_Filter(POST, 'lbsL_1year');
			$lbsL_3year			= $M_FUNC->M_Filter(POST, 'lbsL_3year');
			$ssl_1year			= $M_FUNC->M_Filter(POST, 'ssl_1year');
			$ssl_3year			= $M_FUNC->M_Filter(POST, 'ssl_3year');
			$officialIP_1year	= $M_FUNC->M_Filter(POST, 'officialIP_1year');
			$officialIP_3year	= $M_FUNC->M_Filter(POST, 'officialIP_3year');
			
			$data = array(
				'cpu_1year'		=> str_replace(',', '', $cpu_1year),
				'cpu_3year'		=> str_replace(',', '', $cpu_3year),
				'memory_1year'	=> str_replace(',', '', $memory_1year),
				'memory_3year'	=> str_replace(',', '', $memory_3year),
				'disk_1year'	=> str_replace(',', '', $disk_1year),
				'disk_3year'	=> str_replace(',', '', $disk_3year),
				'OS'			=> str_replace(',', '', $OS),
				'mssql_4_year'	=> str_replace(',', '', $mssql_4_year),
				'mssql_8_year'	=> str_replace(',', '', $mssql_8_year),
				'mssql_12_year'	=> str_replace(',', '', $mssql_12_year),
				'lbsS_1year'	=> str_replace(',', '', $lbsS_1year),
				'lbsS_3year'	=> str_replace(',', '', $lbsS_3year),
				'lbsM_1year'	=> str_replace(',', '', $lbsM_1year),
				'lbsM_3year'	=> str_replace(',', '', $lbsM_3year),
				'lbsL_1year'	=> str_replace(',', '', $lbsL_1year),
				'lbsL_3year'	=> str_replace(',', '', $lbsL_3year),
				'ssl_1year'	=> str_replace(',', '', $ssl_1year),
				'ssl_3year'	=> str_replace(',', '', $ssl_3year),
				'officialIP_1year'	=> str_replace(',', '', $officialIP_1year),
				'officialIP_3year'	=> str_replace(',', '', $officialIP_3year),
			);

			$db->update("UnitCost_Info", $data, " WHERE idx = ".$costIdx);

			$this->insertUnitCostHistoryData($costIdx);

			$M_FUNC->recordActionLog("U", $MENU_ID, $costIdx, "단가표 수정");
		}

		function insertUnitCostHistoryData($costIdx){
			global $db;

			$query = " SELECT * "
					." FROM UnitCost_Info "
					." WHERE idx = " . $costIdx
					;
			$row = $db->getListSet($query);
			$row->next();

			$data = array(
				'costIdx'				=>	$costIdx,
				'cpu_1year'			=>	$row->get('cpu_1year'),
				'cpu_3year'			=>	$row->get('cpu_3year'),
				'memory_1year'	=>	$row->get('memory_1year'),
				'memory_3year'	=>	$row->get('memory_3year'),
				'disk_1year'			=>$row->get('disk_1year'),
				'disk_3year'			=>$row->get('disk_3year'),
				'OS'					=>	$row->get('OS'),
				'mssql_4_year'		=>	$row->get('mssql_4_year'),
				'mssql_8_year'		=>	$row->get('mssql_8_year'),
				'mssql_12_year'	=>	$row->get('mssql_12_year'),
				'lbsS_1year'			=>	$row->get('lbsS_1year'),
				'lbsS_3year'			=>	$row->get('lbsS_3year'),
				'lbsM_1year'		=>	$row->get('lbsM_1year'),
				'lbsM_3year'		=>	$row->get('lbsM_3year'),
				'lbsL_1year'			=>	$row->get('lbsL_1year'),
				'lbsL_3year'			=>	$row->get('lbsL_3year'),
				'ssl_1year'			=>	$row->get('ssl_1year'),
				'ssl_3year'			=>	$row->get('ssl_3year'),
				'officialIP_1year'	=>	$row->get('officialIP_1year'),
				'officialIP_3year'	=>	$row->get('officialIP_3year'),
				'dateYmd'			=>	date('Ymd', time()),
			);

			$db->insert("UnitCost_History", $data);		
		}

		function getReportDataByVmDayList(&$cnt, $addWhere="", $order="", $limit="", $group=""){
			global $db, $M_FUNC;
			global $PAGE;
			
			if($PAGE < 1) $PAGE = 1 ;
			$start_row = ($PAGE - 1) * C_LIST_CNT;
			$where = " WHERE 1 = 1 AND r.status = 1 ";
			
			if ($addWhere) 
			{
				$where .= $addWhere;
			}

			if ($order == "") 
			{
				$orderQuery = " ORDER BY r.idx DESC ";
			}
			else 
			{
				$orderQuery = " ORDER BY r.". $order;
			}
			
			if ($limit == "NO") 
			{
				$limitQuery = "";
			} 
			else 
			{
				$limitQuery = " LIMIT ". $start_row .", ". C_LIST_CNT;
			}

			if($group != ""){
				$groupQuery = " GROUP BY r." . $group;
			}
			
			$query = " SELECT r.dateYmd, r.year, r.month, r.vmIdx, v.vmName, r.serviceIdx, r.companyIdx, r.businessIdx, sum(r.cpu_price) as cpu_price, sum(r.memory_price) as memory_price, sum(r.disk_price) as disk_price, sum(r.OS_price) as OS_price, sum(r.mssql_price) as mssql_price, sum(r.total_price) as total_price, v.v_startDate "
						. " FROM ReportDataByVmDay r "
						. "		LEFT JOIN VM_Info v ON r.vmIdx = v.idx "
						. $where
						. $groupQuery
						. $orderQuery
						. $limitQuery;
			$row = $db->getListSet($query);

			$cnt = $db->cnt("ReportDataByVmDay r", $where);
		
			return $row;		
		}

		function getReportDataByVmDayList_t(&$cnt, $addWhere="", $order="", $limit="", $group=""){
			global $db, $M_FUNC;
			global $PAGE;
			
			if($PAGE < 1) $PAGE = 1 ;
			$start_row = ($PAGE - 1) * C_LIST_CNT;
			$where = " WHERE 1 = 1 AND r.status = 1 ";
			
			if ($addWhere) 
			{
				$where .= $addWhere;
			}

			if ($order == "") 
			{
				$orderQuery = " ORDER BY r.idx DESC ";
			}
			else 
			{
				$orderQuery = " ORDER BY r.". $order;
			}
			
			if ($limit == "NO") 
			{
				$limitQuery = "";
			} 
			else 
			{
				$limitQuery = " LIMIT ". $start_row .", ". C_LIST_CNT;
			}

			if($group != ""){
				$groupQuery = " GROUP BY r." . $group;
			}
			
			$query = " SELECT r.dateYmd, r.year, r.month, r.vmIdx, v.vmName, r.serviceIdx, r.companyIdx, r.businessIdx, sum(r.cpu_price) as cpu_price, sum(r.memory_price) as memory_price, sum(r.disk_price) as disk_price, sum(r.OS_price) as OS_price, sum(r.mssql_price) as mssql_price, sum(r.total_price) as total_price "
						. " FROM ReportDataByVmDay_backup r "
						. "		LEFT JOIN VM_Info v ON r.vmIdx = v.idx "
						. $where
						. $groupQuery
						. $orderQuery
						. $limitQuery;
			$row = $db->getListSet($query);

			$cnt = $db->cnt("ReportDataByVmDay r", $where);
		
			return $row;		
		}

		function getReportDataByVmDayList2(&$cnt, $addWhere="", $order="", $limit="", $group=""){
			global $db, $M_FUNC;
			global $PAGE;
			
			if($PAGE < 1) $PAGE = 1 ;
			$start_row = ($PAGE - 1) * C_LIST_CNT;
			$where = " WHERE 1 = 1 AND r.status = 1 ";
			
			if ($addWhere) 
			{
				$where .= $addWhere;
			}

			if ($order == "") 
			{
				$orderQuery = " ORDER BY r.idx DESC ";
			}
			else 
			{
				$orderQuery = " ORDER BY r.". $order;
			}
			
			if ($limit == "NO") 
			{
				$limitQuery = "";
			} 
			else 
			{
				$limitQuery = " LIMIT ". $start_row .", ". C_LIST_CNT;
			}

			if($group != ""){
				$groupQuery = " GROUP BY r." . $group;
			}
			
			 $query = " SELECT "
					."		r.dateYmd "
					."		, c.companyName "
					."		, r.year "
					."		, r.month "
					."		, r.vmIdx "
					."		, v.vmName "
					."		, r.serviceIdx "
					."		, r.companyIdx "
					."		, r.businessIdx "
					."		, sum(r.cpu_price) as cpu_price "
					."		, sum(r.memory_price) as memory_price "
					."		, sum(r.disk_price) as disk_price "
					."		, sum(r.OS_price) as OS_price "
					."		, sum(r.mssql_price) as mssql_price "
					."		, sum(r.total_price) as total_price "
					. " FROM Company_Info c "
					."		LEFT JOIN ReportDataByVmDay r on c.idx = r.companyIdx "
					. "		LEFT JOIN VM_Info v ON r.vmIdx = v.idx "
					. $where
					." OR r.idx is null "
					. $groupQuery
					. $orderQuery
					. $limitQuery;
			$row = $db->getListSet($query);

			$cnt = $db->cnt("ReportDataByVmDay r", $where);
		
			return $row;		
		}

		function getVMDataServerCntByserviceIdx($addWhere){
			global $db;

			$qeury = " SELECT * "
					." FROM VMDataServerCnt "
					." where 1 = 1 and status = 1 "
					. $addWhere
					." order by dateYmd asc "
					;
			$row = $db->getListSet($qeury);

			return $row;
		}

		function getReportDataByServiceDay($where, $group=''){
			global $db;
			
			$query = " SELECT s.companyIdx, s.idx, sum(r.ip_price) as ip_price, sum(r.loadbalancer_price) as loadbalancer_price "
					." FROM ReportDataByServiceDay r "
					." 	 inner join Service_Info s on r.serviceIdx = s.idx "
					." WHERE r.status = 1 "
					.$where
					." GROUP BY s." . $group
					;
			$row = $db->getListSet($query);
			
			$result = array();
			if($row->size() > 0){
				for($i=0; $i<$row->size(); $i++){
					$row->next();
					
					if($group == "s.companyIdx"){
						$result[$row->get('companyIdx')]['ip_price'] = $row->get('ip_price');
						$result[$row->get('companyIdx')]['loadbalancer_price'] = $row->get('loadbalancer_price');
					} else {
						$result[$row->get($group)]['ip_price'] = $row->get('ip_price');
						$result[$row->get($group)]['loadbalancer_price'] = $row->get('loadbalancer_price');
					}
				}
			}

			return $result;
		}

		function getReportDataByServiceDay_t($where, $group=''){
			global $db;
			
			$query = " SELECT s.companyIdx, s.idx, sum(r.ip_price) as ip_price, sum(r.loadbalancer_price) as loadbalancer_price "
					." FROM ReportDataByServiceDay_backup r "
					." 	 inner join Service_Info s on r.serviceIdx = s.idx "
					." WHERE r.status = 1 "
					.$where
					." GROUP BY s." . $group
					;
			$row = $db->getListSet($query);
			
			$result = array();
			if($row->size() > 0){
				for($i=0; $i<$row->size(); $i++){
					$row->next();
					
					if($group == "s.companyIdx"){
						$result[$row->get('companyIdx')]['ip_price'] = $row->get('ip_price');
						$result[$row->get('companyIdx')]['loadbalancer_price'] = $row->get('loadbalancer_price');
					} else {
						$result[$row->get($group)]['ip_price'] = $row->get('ip_price');
						$result[$row->get($group)]['loadbalancer_price'] = $row->get('loadbalancer_price');
					}
				}
			}

			return $result;
		}

		function getReportDataByServiceDay2($where){
			global $db;
			
			$query = " SELECT s.companyIdx, s.idx, sum(r.ip_price) as ip_price, sum(r.loadbalancer_price) as loadbalancer_price "
					." FROM ReportDataByServiceDay r "
					." 	 inner join Service_Info s on r.serviceIdx = s.idx "
					." WHERE r.status = 1 "
					.$where
					." GROUP BY s.idx "
					;
			$row = $db->getListSet($query);
			
			$result = array();
			if($row->size() > 0){
				for($i=0; $i<$row->size(); $i++){
					$row->next();
					
					$result[$row->get('idx')]['ip_price'] = $row->get('ip_price');
					$result[$row->get('idx')]['loadbalancer_price'] = $row->get('loadbalancer_price');
				}
			}

			return $result;
		}

		function getReportDataByServiceDay2_t($where){
			global $db;
			
			$query = " SELECT s.companyIdx, s.idx, sum(r.ip_price) as ip_price, sum(r.loadbalancer_price) as loadbalancer_price "
					." FROM ReportDataByServiceDay_backup r "
					." 	 inner join Service_Info s on r.serviceIdx = s.idx "
					." WHERE r.status = 1 "
					.$where
					." GROUP BY s.idx "
					;
			$row = $db->getListSet($query);
			
			$result = array();
			if($row->size() > 0){
				for($i=0; $i<$row->size(); $i++){
					$row->next();
					
					$result[$row->get('idx')]['ip_price'] = $row->get('ip_price');
					$result[$row->get('idx')]['loadbalancer_price'] = $row->get('loadbalancer_price');
				}
			}

			return $result;
		}

		function getCalculateList($where){
			global $db;

			$query = ''
					.' SELECT * '
					.' FROM VMDataByDay_backup '
					.' WHERE 1=1 '
					.$where
					.' order by dateYmd desc, vmIdx asc '
					;
			$row = $db->getListSet($query);
			
			return $row;
		}

		function getCalculateList2($where, $year, $month){
			global $db;

			$result = new L_ListSet();

			$startdate = $year.$month."01";

			if($month == date('m', time())){
				$enddate = strtotime(date('Ymd', strtotime('1 day ago')));
			} else {
				$enddate = strtotime($year.$month."31");
			}

			for($dd=strtotime($startdate); $dd<=$enddate; $dd=strtotime('+1 day', $dd)){
				$dateYmd = date('Ymd', $dd);

				$query = ' SELECT dateYmd, serviceName, vmIdx, vmName, sum(cpu) cpu, sum(memory) memory, sum(disk_os) disk_os '
						.'		, sum(disk_data) disk_data, sum(DBMS) DBMS '
						.' FROM ( '
						.'	SELECT '
						.'			'.$dateYmd.' as dateYmd '
						.'			, s.serviceName ' 
						.'			, vr.vmIdx '
						.'			, v.vmName '
						.'			, case vr.cpu '
						.'				when 0 then 0 '
						.'				else '
						.'					case '
						.'						when vr.c_startdate <= '.$dateYmd.' then vr.cpu '
						.'						else 0 '
						.'					end '
						.'			end as cpu '
						.'			, case vr.memory '
						.'				when 0 then 0 '
						.'				else '
						.'					case '
						.'						when vr.m_startdate <= '.$dateYmd.' then vr.memory '
						.'						else 0 '
						.'					end '
						.'			end as memory  '
						.'			, case vr.disk_os '
						.'				when 0 then 0 '
						.'				else '
						.'					case '
						.'						when vr.o_startdate <= '.$dateYmd.' then vr.disk_os '
						.'						else 0 '
						.'					end '
						.'			end as disk_os '
						.'			, case vr.disk_data '
						.'				when 0 then 0 '
						.'				else '
						.'					case '
						.'						when vr.d_startdate <= '.$dateYmd.' then vr.disk_data '
						.'						else 0 '
						.'					end '
						.'			end as disk_data '
						.'			, case vr.dbUse '
						.'				when 0 then 0 '
						.'				else '
						.'					case '
						.'						when vr.db_startdate <= '.$dateYmd.' then vr.DBMS '
						.'						else 0 '
						.'					end '
						.'			end as DBMS '
						.'	FROM VM_Resource_Info vr '
						.'			INNER JOIN VM_Info v on vr.vmIdx = v.idx and ((v.status = 1 and v.v_startDate <= ' . $dateYmd .' ) OR (v.status in (4, 99) '
						.'				and v.v_startDate <= ' . $dateYmd .' and v.v_endDate >= ' . $dateYmd . ' )) '
						.'			INNER JOIN VM_Account_Link va on v.idx = va.vmIdx '
						.'			INNER JOIN Service_Info s on va.accountIdx = s.keyAccountIdx '
						.'	WHERE 1=1 '
						.$where
						.' ) u '
						.' group by vmIdx '
						.' having !(cpu = 0 and memory = 0 and disk_os = 0 and disk_data = 0 and DBMS = 0) '
						;
				$row = $db->getListSet($query);
				
				for($i=0; $i<$row->size(); $i++){
					$row->next();

					if($row->get('cpu') == 0 && $row->get('memory') == 0 && $row->get('disk_os') == 0 && $row->get('disk_data') == 0 && $row->get('DBMS') == 0){
						continue;
					} else {
						
						$result->addItem($row);
					}
				}
			}

			return $result;
		}

		function getVmDataByserviceName($where, $nameArr=null){
			global $db;
			
			$query = ''
					.' SELECT va.vmIdx as vmIdx, s.serviceName '
					.' FROM Service_Info s '
					.'		inner join VM_Account_Link va on s.keyAccountIdx = va.accountIdx '
					.'		inner join VM_Info v on va.vmIdx = v.idx '
					.' WHERE 1=1 '
					.$where
					;
			$row = $db->getListSet($query);
			
			if($nameArr == 'Y'){
				$result = array();
				if($row->size() > 0){
					for($i=0; $i<$row->size(); $i++){
						$row->next();
						$result[$row->get('vmIdx')] = $row->get('serviceName');
					}
				}
				return $result;
			} else {
				return $row;
			}
		}

		function getVMInfoForUsing($dateYmd){
			global $db;

			$query = " SELECT v.idx "
					." FROM VM_Info v "
					." WHERE (v.status = 1 and v.v_startDate <= " . $dateYmd."31 ) OR "
					."		 (v.status in (4, 99) and v.v_endDate >= ".$dateYmd."01 and v.v_startDate <= " . $dateYmd."31 ) ";
			$row = $db->getListSet($query);
			
			$vmArr = array();
			if($row->size() > 0){
				for($i=0; $i<$row->size(); $i++){
					$row->next();
					array_push($vmArr, $row->get('idx'));	
				}
			}

			return $vmArr;
		}

		function getVmDataByserviceIdx($dateYmd){
			global $db;
			
			$query = ''
					.' SELECT va.vmIdx as vmIdx, s.serviceName, s.idx '
					.' FROM Service_Info s '
					.'		inner join VM_Account_Link va on s.keyAccountIdx = va.accountIdx '
					.'		inner join VM_Info v on va.vmIdx = v.idx '
					." WHERE ((v.status = 1 and v.v_startDate <= " . $dateYmd."31 ) OR "
					."		 (v.status in (4, 99) and v.v_endDate >= ".$dateYmd."01 and v.v_startDate <= " . $dateYmd."31 )) "
					;
			$row = $db->getListSet($query);
			
			
			$result = array();
			if($row->size() > 0){
				for($i=0; $i<$row->size(); $i++){
					$row->next();
					if(!in_array($row->get('idx'), $result)){
						array_push($result, $row->get('idx'));
					}
				}
			}

			return $result; 
		}

		function getVMResourceByserviceIdx($serviceIdx, $dateYmd){
			global $db;

			$query = " select sum(v.cpu) as cpu, sum(v.memory) as memory, sum(v.disk_os) as disk_os, sum(v.disk_data) as disk_data, sum(v.DBMS) as sumDBMS "
						. "		, ai.roleIdx "
						. " from VM_Resource_Info v "
						. " 	left join VM_Account_Link val on v.vmIdx = val.vmIdx "
						. "		left join AccountRole_Info ai on val.accountIdx = ai.accountIdx "
						. "	where ((v.cpu > 0 or v.memory > 0 or v.disk_os > 0 or v.disk_data > 0 or v.DBMS > 0) and "
						. "		(v.c_startdate < ".$dateYmd." or v.m_startdate < ".$dateYmd." or v.o_startdate < ".$dateYmd." or v.d_startdate < ".$dateYmd." or v.db_startdate < ".$dateYmd.")) "
						. "  AND ai.roleIdx = ".$serviceIdx
						. " group by ai.roleIdx ";
			$row = $db->getListSet($query);
			
			$result = array();
			if($row->size() > 0){
				for($i = 0; $i<$row->size(); $i++){
					$row->next();

					$result[$row->get('roleIdx')]['cpu'] = $row->get('cpu');
					$result[$row->get('roleIdx')]['memory'] = $row->get('memory');
					$result[$row->get('roleIdx')]['disk'] = $row->get('disk_os') + $row->get('disk_data');
					$result[$row->get('roleIdx')]['disk_os'] = $row->get('disk_os');
					$result[$row->get('roleIdx')]['disk_data'] = $row->get('disk_data');
					$result[$row->get('roleIdx')]['DBMS'] = $row->get('DBMS');
				}
			} 

			return $result;
		}

		function getVMResourceByserviceIdx_v2($serviceIdx, $dateYmd){
			global $db;

			$query = " select sum(v.cpu) as cpu, sum(v.memory) as memory, sum(v.disk_os) as disk_os, sum(v.disk_data) as disk_data, sum(v.DBMS) as sumDBMS "
						. "		, ai.roleIdx "
						. " from VM_Resource_Info v "
						. " 	left join VM_Account_Link val on v.vmIdx = val.vmIdx "
						. "		left join AccountRole_Info ai on val.accountIdx = ai.accountIdx "
						. "	where (lpad(v.c_startdate, 8, 9) < ".$dateYmd." or lpad(v.m_startdate, 8, 9) < ".$dateYmd." or lpad(v.o_startdate, 8, 9) < ".$dateYmd." or lpad(v.d_startdate, 8, 9) < ".$dateYmd." or lpad(v.db_startdate, 8, 9) < ".$dateYmd.") "	//and (v.disk_os > 0 or v.disk_data > 0) "
						. "  AND ai.roleIdx = ".$serviceIdx
						. " group by ai.roleIdx ";
			$row = $db->getListSet($query);
			
			$result = array();
			if($row->size() > 0){
				for($i = 0; $i<$row->size(); $i++){
					$row->next();

					$result[$row->get('roleIdx')]['cpu'] = $row->get('cpu');
					$result[$row->get('roleIdx')]['memory'] = $row->get('memory');
					$result[$row->get('roleIdx')]['disk'] = $row->get('disk_os') + $row->get('disk_data');
					$result[$row->get('roleIdx')]['disk_os'] = $row->get('disk_os');
					$result[$row->get('roleIdx')]['disk_data'] = $row->get('disk_data');
					$result[$row->get('roleIdx')]['DBMS'] = $row->get('DBMS');
				}
			} 

			return $result;
		}

		function getVMInfoForFastDate($dateYmd=null, $group, $arrayYN = null){
			global $db;

			if($group){
				$group = " group by ". $group;
			}
				
			$addWhere = "";
			if($dateYmd){
				$addWhere .= " AND (v.status = 1 and v.v_startDate <= " . $dateYmd."31 ) OR "
					."		 (v.status in (4, 99) and v.v_endDate >= ".$dateYmd."01 and v.v_startDate <= " . $dateYmd."31 ) ";
			}

			$query = " SELECT va.accountIdx, ai.companyIdx, ai.businessIdx, ai.roleIdx, min(v.v_startDate) as v_startDate "
					."	, case v.v_endDate when 0 then 0 else case when min(v.v_endDate) = 0 then 0 else max(v.v_endDate) end end as v_endDate "
					." FROM VM_Account_Link va "
					."		LEFT JOIN VM_Info v on va.vmIdx = v.idx "
					."		LEFT JOIN AccountRole_Info ai on va.accountIdx = ai.accountIdx "
					." WHERE 1 = 1 "
					. $addWhere
					. $group;
			$row = $db->getListSet($query);

			if($arrayYN){
				$vmInfo = array();
				for($i=0; $i<$row->size(); $i++){
					$row->next();

					$vmInfo[$row->get('roleIdx')]['startDate'] = $row->get('v_startDate');
					$vmInfo[$row->get('roleIdx')]['endDate'] = $row->get('v_endDate');
				}
				return $vmInfo;
			} else return $row;
		}

		function getReportDataByVmDay($addWhere){
			global $db;

			$query = " SELECT * "
						." FROM ReportDataByVmDay "
						." WHERE 1 = 1 "
						. $addWhere
						;
			$row = $db->getListSet($query);

			return $row;
		}

		function getUsingServiceOption($addWhere, $priceAdd = null, $dateYmd = null){
			global $db;

			$query = " SELECT s.companyIdx, so.serviceIdx, s.serviceName, s.companyIdx, ar.businessIdx "
						." FROM ServiceByAdditionalOption so "
						."		LEFT JOIN Service_Info s on so.serviceIdx = s.idx "
						."		LEFT JOIN AccountRole_Info ar on s.keyAccountIdx = ar.accountIdx "
						." WHERE 1 = 1 "
						. $addWhere
						." GROUP BY s.companyIdx, so.serviceIdx "
						;
			$row = $db->getListSet($query);
		
			if($priceAdd){
				$result_array = array();
				$service_Arr = array();

				if($row->size() > 0){
					for($i=0; $i<$row->size(); $i++){
						$row->next();

						$result_array[$row->get('serviceIdx')]['serviceIdx'] = $row->get('serviceIdx');
						$result_array[$row->get('serviceIdx')]['serviceName'] = $row->get('serviceName');
						$result_array[$row->get('serviceIdx')]['companyIdx'] = $row->get('companyIdx');
						$result_array[$row->get('serviceIdx')]['businessIdx'] = $row->get('businessIdx');
						$result_array[$row->get('serviceIdx')]['dateYmd'] = $dateYmd;
						array_push($service_Arr, $row->get('serviceIdx'));
					}

					$query = " SELECT serviceIdx, sum(ip_price) ip_price, sum(loadbalancer_price) loadbalancer_price, sum(total_price) total_price"
								." FROM ReportDataByServiceDay "
								." WHERE serviceIdx in (". implode(",", $service_Arr) .") AND dateYmd like '".$dateYmd."%' "
								;
					$row = $db->getListSet($query);

					for($i=0; $i<$row->size(); $i++){
						$row->next();
						
						$result_array[$row->get('serviceIdx')]['ip_price'] = $row->get('ip_price');
						$result_array[$row->get('serviceIdx')]['loadbalancer_price'] = $row->get('loadbalancer_price');
						$result_array[$row->get('serviceIdx')]['total_price'] = $row->get('total_price');
					}
				}

				return $result_array;
			} else {
				return $row;
			}
		}

		function getUsingServiceOption_v2($addWhere, $result=null){
			global $db;

			$query = " Select r.serviceIdx, ai.companyIdx, ai.businessIdx, r.ip_price, r.loadbalancer_price, r.total_price "
						." FROM ReportDataByServiceDay r "
						."	LEFT JOIN AccountRole_Info ai on r.serviceIdx = ai.roleIdx and ai.level = 8 "
						." WHERE 1=1 "
						. $addWhere
						;
			$row = $db->getListSet($query);
			
			if(!$result){
				$result_array = array();
				for($i=0; $i<$row->size(); $i++){
					$row-> next();
					
					$result_array[$row->get('serviceIdx')]['serviceIdx'] = $row->get('serviceIdx');
					$result_array[$row->get('serviceIdx')]['companyIdx'] = $row->get('companyIdx');
					$result_array[$row->get('serviceIdx')]['businessIdx'] = $row->get('businessIdx');
					$result_array[$row->get('serviceIdx')]['ip_price'] = $row->get('ip_price');
					$result_array[$row->get('serviceIdx')]['loadbalancer_price'] = $row->get('loadbalancer_price');
					$result_array[$row->get('serviceIdx')]['total_price'] = $row->get('total_price');
				}

				return $result_array;
			} else {
				return $row;
			}
		}

		function arrayByIndex_Reorder($arr){
			array_multisort(array_keys($arr), array_values($arr), $arr);

			$result_array = array();
			$num = count($arr);
			foreach($arr as $key => $result){
				$result_array[$key]['num']					= $num;
				$result_array[$key]['vmIdx']				= $result['vmIdx'];
				$result_array[$key]['serviceIdx']			= $result['serviceIdx'];
				$result_array[$key]['serviceName']		= $result['serviceName'];
				$result_array[$key]['companyName']	= $result['companyName'];
				$result_array[$key]['businessName']	= $result['businessName'];
				$result_array[$key]['dateYmd']			= $result['dateYmd'];
				$result_array[$key]['service_price']		= $result['service_price'];
				$result_array[$key]['total_price']		= $result['total_price'];
				$result_array[$key]['v_startDate']		= $result['v_startDate'];
				$result_array[$key]['v_endDate']		= $result['v_endDate'];
				$result_array[$key]['total_page']		= $result['total_page'];
				$num--;
			}
			return $result_array;
		}

		function modifyContract($contract, $dateYmd){
			global $db;

			//첫번째로 VM_Info, VM_History, VMDataByDay에 약정을 변경
			$update_info = array(
				'contract_c'		=> $contract,
				'contract_d'		=> $contract
			);
			
			$db->update('VM_Info', $update_info, 'where status = 1 ');		//VM_Info는 진행중인 VM에 대해서만
			$db->update('VM_History', $update_info, 'where status = 1 and dateYmd >= '. $dateYmd);		//VM_History 변경할 날짜 이후의 진행중인 VM에 대해서만
			$db->update('VMDataByDay', $update_info, 'where status = 1 and dateYmd >= '. $dateYmd);		//VM_History 변경할 날짜 이후의 진행중인 VM에 대해서만

			//두번째로 게임별 서비스내역 부분 약정 변경, ip, lbs를 따로 각각 해주어야한다.
			$prior_contract = 4 - (int)$contract;
			$db->update('ServiceByAdditionalOption', array('ip_contract' => $contract), 'where ip_contract = '. $prior_contract);
			$db->update('ServiceByAdditionalOption', array('balance_contract' => $contract), 'where balance_contract = '. $prior_contract);

			return "YES";
		}

	} //*** End of Class	
?>