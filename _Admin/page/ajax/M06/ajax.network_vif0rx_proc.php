<?php
@include_once "Inc/inc.include.php";

$uuid = $M_FUNC->M_Filter(GET, 'uuid');
$type = $M_FUNC->M_Filter(GET, 'type');
$period = $M_FUNC->M_Filter(GET, 'period');
$metric_type = $M_FUNC->M_Filter(GET, 'metric');

$server = "222.239.14.224:3001";
$userApiKey = "743fd342e67d4c42893acc8cf9624000";
$userSecretKey = "MDg3YzExNDZmZDM3NGNkZDlmNGI3MDExZTc5MjRlYTE=";
$time_value = "";

	if($period == "" || $period == "lastDayTrend"){
		$period = "lastDayTrend";
		$time_value = "H:i";
	} else if($period == "lastWeekTrend"){
		$period = "lastWeekTrend";
		$time_value = "H:i";
	} else if($period == "lastMonthTrend"){
		$period = "lastMonthTrend";
		$time_value = "Y";
	} else if($period == "lastYearTrend"){
		$period = "lastYearTrend";
		$time_value = "Y";
	}

	$result_array = array();

	$requestUrl = "%2Fapi%2Fcl%2Fmonitoring%2Ftrend%2F".$uuid."%2F".$metric_type."%2F".$period."";
	
	$array_param = array(
		'userApiKey' => $userApiKey,
		'userApiUrl' => $requestUrl,
		'resourcetype' => "router",
		//'userApiSignature' => $userSecretKey,
	);

	$url = "http://" . $server . "/userapi?";
	$string = "";


	foreach($array_param as $key => $value){
		if($string != ""){ $string .= "&";}

		$string .= $key  . "=" . $value;	
	}	
		
		@include_once "../api/getApiData.php";
		
		//print_r($result_curl);exit;
		$api_arr = json_decode($result_curl,true);
		$metrics = $api_arr['gettrendgraphresponse']['gettrendgraph']['0']['metric'];
		$period = $api_arr['gettrendgraphresponse']['gettrendgraph']['0']['period'];
		//print_r($metrics);exit;

		$result_array["mntrType_tx"] = substr($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['0']['mntrType'],0,8);

		$rxmax = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_rxmax"];
		$rxavg = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_rxavg"];
		$rxmin = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_rxmin"];

		$rxmax_pps = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_rxmax_pps"];
		$rxavg_pps = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_rxavg_pps"];
		$rxmin_pps = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_rxmin_pps"];

		$rx_max = "";
		$rx_min = "";
		$rx_avg = "";
		$rx_max_pps = "";
		$rx_min_pps = "";
		$rx_avg_pps = "";
		
		$max_time_result = "";

		foreach($rxmax as $key => $value){
			$rx_max .= $value['1'] . "|"; 
			//$time .=  $value['0'] / 1000;
			$max_time_result .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}

		foreach($rxavg as $key => $value){
			$rx_avg .= $value['1'] . "|"; 
			//$time .=  $value['0'] / 1000;
			$avg_time_result .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}

		foreach($rxmin as $key => $value){
			$rx_min .= $value['1'] . "|"; 
			//$time .=  $value['0'] / 1000;
			$min_time_result .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}

		foreach($rxmax_pps as $key => $value){
			$rx_max_pps .= $value['1'] . "|"; 
			//$time .=  $value['0'] / 1000;
			$max_time_result_pps .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}

		foreach($rxmin_pps as $key => $value){
			$rx_min_pps .= $value['1'] . "|"; 
			//$time .=  $value['0'] / 1000;
			$avg_time_result_pps .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}

		foreach($rxavg_pps as $key => $value){
			$rx_avg_pps .= $value['1'] . "|"; 
			//$time .=  $value['0'] / 1000;
			$min_time_result_pps .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}

		$result_array["mntrDataAverage_rx_bps"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['0']['mntrDataAverage'],2);
		$result_array["mntrDataMax_rx_bps"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['0']['mntrDataMax'],2);
		$result_array["mntrDataMin_rx_bps"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['0']['mntrDataMin'],2);
	
		$result_array["mntrDataAverage_rx_pps"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['1']['mntrDataAverage'],2);
		$result_array["mntrDataMax_rx_pps"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['1']['mntrDataMax'],2);
		$result_array["mntrDataMin_rx_pps"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['1']['mntrDataMin'],2);
		
		$result_array["rx_max"] = $rx_max;
		$result_array["rx_min"] = $rx_min;
		$result_array["rx_avg"] = $rx_avg;
		
		$result_array["rx_max_pps"] = $rx_max_pps;
		$result_array["rx_min_pps"] = $rx_min_pps;
		$result_array["rx_avg_pps"] = $rx_avg_pps;

		$result_array["max_datetime"] =$max_time_result;

		$result_array["metrics"] = $metrics;
		$result_array["period"] = $period;
	
		//print_r($result_array);exit;
echo json_encode($result_array);

exit();

?>