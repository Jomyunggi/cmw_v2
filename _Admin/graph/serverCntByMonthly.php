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
	$year = date('Y', strtotime('1 month ago', time()));
	$month = date('m', strtotime('1 month ago', time()));

	$startdate = $year.sprintf('%02d', $month)."01";
	$enddate = $year.sprintf('%02d', $month)."31";

	$data = array();

	$addWhere = '';
	if($serviceIdx) $addWhere .= " AND serviceIdx = ". $serviceIdx;
	
	$qeury = " SELECT dateYmd, serviceIdx, serverCnt "
			." FROM VMDataServerCnt "
			." where 1 = 1 and status = 1 "
			." and dateYmd between ".$startdate." and ".$enddate
			. $addWhere
			." order by serviceIdx asc, dateYmd asc "
			;
	$row = mysql_query($qeury, $db_conn);

	while($result = mysql_fetch_array($row)){
		$dateYmd = substr($result[0], 6, 2);

		$data[$result[1]][intval($dateYmd)] = intval($result[2]);
	}
	
	echo json_encode($data);
?>