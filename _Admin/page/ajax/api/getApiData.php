<?php

//print_r($userSecretKey);exit;
//echo $string . " ------ " . $userSecretKey;
$sign = exec("java signurl '" . $string . "' '". $userSecretKey . "' ");
//print_r($sign);exit;
$curl_url = $url . $string . "&userApiSignature=" . $sign;

$ch = curl_init(); //세션을 초기화하고 curl_setopt(), curl_exec() 그리고 curl_close 함수들과 함께 사용하기 위해 CURL 핸들값을 리턴해준다.
curl_setopt($ch, CURLOPT_URL, $curl_url); // 옵션 설정. 위치 헤더가 존재하면 따라간다 // curlopt_url : 접속 할 url 정보를 설정
curl_setopt($ch, CURLOPT_HEADER, false); // 헤더 정보 설정
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	// 문자열출력
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$result_curl = curl_exec($ch);
curl_close($ch);


?>