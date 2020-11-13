<?php
echo "here";

date_default_timezone_set("GMT+0");

$datetime = date("ymd").'T'.date("His").'Z';
$method = "GET";
//replace with your own vendorId
$path = "/v2/providers/openapi/apis/api/v4/vendors/A00180903/ordersheets";
$query = "createdAtFrom=2020-09-17&createdAtTo=2020-09-19&maxPerPage=2&status=ACCEPT";

$message = $datetime.$method.$path.$query;

//replace with your own accessKey
$accesskey = "6e334d01-f595-4c89-8be4-af02c75ac3a3";
//replace with your own secretKey
$secretkey = "8df08de6ebd28fe2e631c8755e25cff9a9a34157";

$algorithm = "HmacSHA256";

$signature = hash_hmac('sha256', $message, $secretkey);

$authorization  = "CEA algorithm=HmacSHA256, access-key=".$accesskey.", signed-date=".$datetime.", signature=".$signature;

//replace prod url when you need
$url = 'https://api-gateway.coupang.com'.$path.'?'.$query;=

$curl = curl_init();        
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:  application/json;charset=UTF-8", "Authorization:".$authorization));	
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$result = curl_exec($curl);
curl_close($curl);

echo($httpcode);
echo($result);

?>