<?php
	ini_set('include_path', realpath(dirname(__FILE__) ."/../_Common"));
	@include "Inc/inc.include.php";

	/* More complex example, with variables. */
	class foo
	{
		var $foo;
		var $bar;

		function foo()
		{
			$this->foo = 'Foo';
			$this->bar = array('Bar1', 'Bar2', 'Bar3');
		}
	}

	$foo = new foo();
	$name = 'MyName';
	print_r($foo);

	

	exit;
	$db = new M_DB();
	$db_pattern = new M_DB("PATTERN");

	$company_table = array(
		5 => 'Pattern_gamevil',
		6 => 'Pattern_com2us',
		7 => 'Pattern_gcp'
	);
	
	$dateYmd = $_SERVER['argv'][1] == "" ? date('Ymd', strtotime('1 day ago')) : $_SERVER['argv'][1];
	$companyIdx = $_SERVER['argv'][2] == "" ? 0 : $_SERVER['argv'][2];
	$week = date('N', strtotime($dateYmd));
	$hour = date('G', strtotime($dateYmd));
	$minute = date('i', strtotime($dateYmd));
	if($minute >= 0 && $minute <= 10){
		$minType = 1;
	} else if($minute > 10 && $minute <= 20){
		$minType = 2;
	} else if($minute > 20 && $minute <= 30){
		$minType = 3;
	} else if($minute > 30 && $minute <= 40){
		$minType = 4;
	} else if($minute > 40 && $minute <= 50){
		$minType = 5;
	} else {
		$minType = 6;
	}

	$query = " SELECT * "
				." FROM Company_Info "
				." WHERE status = 1 ";
	$row = $db->getListSet($query);
	
	$company_Arr = array();
	if($companyIdx == 0){
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			array_push($company_Arr, $row->get('idx'));
		}
	} else {
		array_push($company_Arr, $companyIdx);
	}

	//전날자 데이터 패턴 확인 후 다른패턴으로 오늘자 등록될수 있도록
	if(date('Ym', strtotime("1 day ago", strtotime($dateYmd))) < 201601){
		$dateTable = 201601;
	} else {
		$dateTable = date('Ym', strtotime("1 day ago", strtotime($dateYmd)));
	}
	$companyPattern_arr = array();
	$query = " SELECT count(*) as cnt, companyIdx, pattern "
				." FROM NetworkData_".$dateTable
				." WHERE dateYmd = ". date('Ymd', strtotime("1 day ago", strtotime($dateYmd)))
				." GROUP by companyIdx, pattern ";exit;
	$row = $db_pattern->getListSet($query);
	if($row->size() > 0){
		for($i=0; $i<$row->size(); $i++){
			$row->next();
	
			$companyPattern_arr[$row->get('companyIdx')] = $row->get('pattern');
		}
	}

	echo "here";
	print_r($companyPattern_arr);
	exit
	;
	

	$companyPattern_arr = array();
	for($i=0; $i<count($company_Arr); $i++){
		if($week < 6){
			$checkPattern = mt_rand(1,5);
		} else {
			$checkPattern = mt_rand(4,5);
		}
		

		$query = " SELECT count(*) as cnt "
					." FROM NetworkData_".$dateTable
					." WHERE companyIdx = ". $company_Arr[$i]
					."		AND dateYmd = ". date('Ymd', strtotime("1 day ago", strtotime($dateYmd)))
					."		AND pattern = ". $checkPattern;
		$row = $db_pattern->getListSet($query);

		if($row->size() > 0){
			if($week < 6){
				if($checkPattern < 5){
					$pattern = $checkPattern + 1;
				} else {
					$pattern = $checkPattern -1;
				}
			} else {
				if($checkPattern == 4){
					$pattern = 5;
				} else {
					$pattern = 4;
				}
			}
		} else {
			$pattern = $checkPattern;
		}

		$companyPattern_arr[$company_Arr[$i]]['pattern'] = $pattern;
	}

	//해당 오늘자 등록된 패턴이 있는지 확인

	$network_Arr = array();
	for($i=0; $i<count($company_Arr); $i++){
		$query = " SELECT * "
					." FROM ". $company_table[$company_Arr[$i]]
					." WHERE pattern = ". $companyPattern_arr[$company_Arr[$i]]['pattern'];
		$row = $db_pattern->getListSet($query);
		
		for($j=0; $j<$row->size(); $j++){
			$row->next();

			$data = array(
				'companyIdx'	=> $company_Arr[$i],
				'pattern'			=>	 $companyPattern_arr[$company_Arr[$i]]['pattern'],
				'dateYmd'		=> $dateYmd,
				'hour'				=>	 $row->get('hour'),
				'minType'		=> $row->get('minType'),
				'Tx'				=>	 $row->get('Tx'),
				'Rx'				=>	 $row->get('Rx')
			);
			$network_Arr[$company_Arr[$i]][$j] = $data;
		}
	}

	foreach($network_Arr as $key => $arr){
		$companyIdx = $key;
		for($i=0; $i<count($arr); $i++){
			$db_pattern->insert('NetworkData_'.date('Ym', strtotime($dateYmd)), $arr[$i]);
		}
	}

	echo "success";
?>