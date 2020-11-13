 <?php
    date_default_timezone_set("GMT+0");

    $datetime = date("ymd").'T'.date("His").'Z';
    $method = "GET";
    $path = "/v2/providers/travel_backbone_api/apis/ticket-api/v1/mock-up/purchasers";
    $query = "searchStartDateTime=20200906000000&searchEndDateTime=20200918235959&offset=0&limit=1000";

    $message = $datetime.$method.$path.$query;

    //replace with your own accessKey
    $accesskey = "6e334d01-f595-4c89-8be4-af02c75ac3a3";
	//replace with your own secretKey
	$secretkey = "8df08de6ebd28fe2e631c8755e25cff9a9a34157";

    $algorithm = "HmacSHA256";

    $signature = hash_hmac('sha256', $message, $secretkey);

    $authorization  = "CEA algorithm=HmacSHA256, access-key=".$accesskey.", signed-date=".$datetime.", signature=".$signature;

    //replace prod url when you need
    $url = 'https://api-gateway.coupang.com'.$path.'?'.$query;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:  application/json;charset=UTF-8", "Authorization:".$authorization));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);

    echo($result);

    ?>