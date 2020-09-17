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

		$txmax = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_txmax"];
		$txavg = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_txavg"];
		$txmin = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_txmin"];

		$txmax_pps = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_txmax_pps"];
		$txavg_pps = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_txavg_pps"];
		$txmin_pps = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_txmin_pps"];

		$tx_max = "";
		$tx_min = "";
		$tx_avg = "";
		$tx_max_pps = "";
		$tx_min_pps = "";
		$tx_avg_pps = "";
		
		$max_time_result = "";

		foreach($txmax as $key => $value){
			$tx_max .= $value['1'] . "|"; 
			//$time .=  $value['0'] / 1000;
			$max_time_result .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}

		foreach($txavg as $key => $value){
			$tx_avg .= $value['1'] . "|"; 
			//$time .=  $value['0'] / 1000;
			$avg_time_result .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}

		foreach($txmin as $key => $value){
			$tx_min .= $value['1'] . "|"; 
			//$time .=  $value['0'] / 1000;
			$min_time_result .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}


		foreach($txmax_pps as $key => $value){
			$tx_max_pps .= $value['1'] . "|"; 
			//$time .=  $value['0'] / 1000;
			$max_time_result_pps .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}

		foreach($txmin_pps as $key => $value){
			$tx_min_pps .= $value['1'] . "|"; 
			//$time .=  $value['0'] / 1000;
			$avg_time_result_pps .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}

		foreach($txavg_pps as $key => $value){
			$tx_avg_pps .= $value['1'] . "|"; 
			//$time .=  $value['0'] / 1000;
			$min_time_result_pps .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}

		$result_array["mntrDataAverage_tx_bps"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['2']['mntrDataAverage'],2);
		$result_array["mntrDataMax_tx_bps"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['2']['mntrDataMax'],2);
		$result_array["mntrDataMin_tx_bps"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['2']['mntrDataMin'],2);
	
		$result_array["mntrDataAverage_tx_pps"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['3']['mntrDataAverage'],2);
		$result_array["mntrDataMax_tx_pps"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['3']['mntrDataMax'],2);
		$result_array["mntrDataMin_tx_pps"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['3']['mntrDataMin'],2);
		
		$result_array["tx_max"] = $tx_max;
		$result_array["tx_min"] = $tx_min;
		$result_array["tx_avg"] = $tx_avg;
		
		$result_array["tx_max_pps"] = $tx_max_pps;
		$result_array["tx_min_pps"] = $tx_min_pps;
		$result_array["tx_avg_pps"] = $tx_avg_pps;

		$result_array["max_datetime"] =$max_time_result;

		$result_array["metrics"] = $metrics;
		$result_array["period"] = $period;
	
		//print_r($result_array);exit;
echo json_encode($result_array);

exit();

?>