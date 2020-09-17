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
/*
$metric_array = array(
	"1" => "cpu0",
	"2" => "memory",
	"4" => "vif_0",
	"8" => "vif_2",
);
$requestUrl = "";

	foreach($metric_array as $key => $value){
		$requestUrl = "%2Fapi%2Fcl%2Fmonitoring%2Ftrend%2F".$uuid."%2F".$metric_array[$key]."%2F".$period."";
	}
*/
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
		$temp_max = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_max"];
		$temp_avg = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_avg"];
		$temp_min = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics."_min"];
		$max = "";
		$min = "";
		$avg = "";
		$max_time_result = "";
		$min_time_result = "";
		$avg_time_result = "";

		foreach($temp_max as $key => $value){
			$max .= round($value['1'],2) . "|"; 
			//$time .=  $value['0'] / 1000;
			$max_time_result .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}

		foreach($temp_avg as $key => $value){
			$avg .= round($value['1'],2) . "|"; 
			//$time .=  $value['0'] / 1000;
			$avg_time_result .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}

		foreach($temp_min as $key => $value){
			$min .= round($value['1'],2) . "|"; 
			//$time .=  $value['0'] / 1000;
			$min_time_result .= date('D, M d,'.$time_value,$value['0'] / 1000) . "|";
		}
		
		//print_r($time_result);exit;
		if($metrics == "cpu0"){
			$result_array["mntrDataAverage"] = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['0']['mntrDataAverage'] * 100;
			$result_array["mntrDataMax"] = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['0']['mntrDataMax'] * 100;
			$result_array["mntrDataMin"] = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['0']['mntrDataMin'] * 100;
		} else if($metrics == "memory"){
			$result_array["mntrDataAverage"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['0']['mntrDataAverage'],2);
			$result_array["mntrDataMax"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['0']['mntrDataMax'],2);
			$result_array["mntrDataMin"] = round($api_arr['gettrendgraphresponse']['gettrendgraph']['0'][$metrics.'_trendtable']['0']['mntrDataMin'],2);
		}

		$result_array["max"] = $max;
		$result_array["max_datetime"] =$max_time_result;

		$result_array["min"] = $min;
		$result_array["min_datetime"] =$min_time_result;

		$result_array["avg"] = $avg;
		$result_array["avg_datetime"] =$avg_time_result;

		$result_array["metrics"] = $metrics;
		$result_array["period"] = $period;
	
		//print_r($result_array);exit;
echo json_encode($result_array);

exit();

?>