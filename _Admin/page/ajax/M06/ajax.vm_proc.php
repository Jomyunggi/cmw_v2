<?php
@include_once "Inc/inc.include.php";

$uuid = $M_FUNC->M_Filter(GET, 'uuid');
$type = $M_FUNC->M_Filter(GET, 'type');

$server = "222.239.14.224:3001";
$userApiKey = "743fd342e67d4c42893acc8cf9624000";
$userSecretKey = "MDg3YzExNDZmZDM3NGNkZDlmNGI3MDExZTc5MjRlYTE=";

$result_array = array();

	$requestUrl = "%2Fapi%2Fcl%2Fmonitoring%2Ftrend%2F".$uuid."%2Fcpu0%2FlastWeekTrend";

	$array_param = array(
		'userApiKey' => $userApiKey,
		'userApiUrl' => $requestUrl,
		//'userApiSignature' => $userSecretKey,
	);


	$url = "http://" . $server . "/userapi?";
	$param_map = "";
	$string = "";


	foreach($array_param as $key => $value){
		if($string != ""){ $string .= "&";}

		$string .= $key  . "=" . $value;	
	}	

	$sign = exec("java signurl '" . $string . "' '". $userSecretKey . "' ");

	$curl_url = $url . $string . "&userApiSignature=" . $sign;
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $curl_url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	// 문자열출력
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		$result = curl_exec($ch);
		curl_close($ch);

		$api_arr = json_decode($result, true);

		$api_arr_sec = $api_arr['gettrendgraphresponse']['gettrendgraph']['0'];
		//['cpu0_trendtable']['0'];
		//print_r($api_arr_sec);exit;

		foreach($api_arr_sec as $key => $value){
			//echo $key . " : " . $value . " / ";
			//echo $value['cpu0_trendtable']['0']['mntrDataAverage'];
			//print_r($key);
		}
		


echo json_encode($result_array);

?>