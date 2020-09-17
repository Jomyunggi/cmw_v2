<?php
	define("DB_TEST_HOST",						"222.239.14.169");
	define("DB_TEST_NAME",						"gamebill");
	define("DB_TEST_USER",						"gamebill");
	define("DB_TEST_PASS",						"rpdlaqlf!!!");

	$db_host		= DB_TEST_HOST;
	$db_name		= DB_TEST_NAME;
	$db_user		= DB_TEST_USER;
	$db_pwd			= DB_TEST_PASS;
	
	$charset = "utf-8";
	
	$db_conn = mysql_connect($db_host, $db_user, $db_pwd) or die("Can't Connecting Database ". mysql_error());
	@mysql_select_db($db_name, $db_conn);
	
	mysql_query('set names utf8');

	if ($charset == "euc-kr") {
		mysql_query('set names euckr');
	}

	$serviceIdx = isset($_POST['idx']) ? $_POST['idx'] : 0;

	$date_1 = mktime(0,0,0, date('m'), 1, date('Y'));
	$date_30 = mktime(0,0,0, date('m'), 30, date('Y'));
	$prev_month_1 = strtotime("-1 month", $date_1);
	$prev_month_30 = strtotime("-1 month", $date_30);

	$enddate =date('Ymd', $prev_month_30) + 1;
	$threeMonth_ago = date('Ymd', strtotime('3 month ago', $prev_month_1));
	
	$addWhere = '';
	if($serviceIdx) $addWhere .= " AND r.serviceIdx = ". $serviceIdx;

	$query = " SELECT r.dateYmd, sum(r.total_price) as total_price, r.serviceIdx "
				. " FROM ReportDataByVmDay r "
				. "		LEFT JOIN VM_Info v ON r.vmIdx = v.idx "
				. " WHERE 1=1 and r.status = 1 AND v.status = 1 AND r.dateYmd between ".$threeMonth_ago." and ".$enddate
				. $addWhere
				. " GROUP BY serviceIdx, r.year, r.month "
				;
	$row = mysql_query($query);
	
	$data = array();
	while($result = mysql_fetch_array($row, MYSQL_NUM)){
		//$data[$result[0]] = $result[1];
		$data[$result[2]][date('Ym', strtotime($result[0]))] = $result[1];
	}

	echo json_encode($data);
?>