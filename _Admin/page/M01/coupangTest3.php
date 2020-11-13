<?php
date_default_timezone_set("GMT+0");

$datetime = date("ymd").'T'.date("His").'Z';
$method = "GET";
$path = "/v2/providers/openapi/apis/api/v4/vendors/A00180903/returnRequests";
$query = "createdAtFrom=2020-08-20&createdAtTo=2020-09-19&status=UC";

$message = $datetime.$method.$path.$query;

//replace with your own accessKey
$ACCESS_KEY = "6e334d01-f595-4c89-8be4-af02c75ac3a3";
//replace with your own secretKey
$SECRET_KEY = "8df08de6ebd28fe2e631c8755e25cff9a9a34157";
$algorithm = "HmacSHA256";

$signature = hash_hmac('sha256', $message, $SECRET_KEY);

$authorization  = "CEA algorithm=HmacSHA256, access-key=".$ACCESS_KEY.", signed-date=".$datetime.", signature=".$signature;

$url = 'https://api-gateway.coupang.com'.$path.'?'.$query;

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:  application/json;charset=UTF-8", "Authorization:".$authorization, "X-EXTENDED-TIMEOUT:90000"));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

echo($httpcode);

echo($result);
?>