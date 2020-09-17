<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	$dealIdx = $M_FUNC->M_Filter(POST, 'dealIdx');
	$dstatus = $M_FUNC->M_Filter(POST, 'dstatus');
	$deliveryNum = $M_FUNC->M_Filter(POST, 'deliveryNum');
	$priceKinds = $M_FUNC->M_Filter(POST, 'priceKinds');
	$date = $M_FUNC->M_Filter(POST, 'date');
	$dstart = $M_FUNC->M_Filter(POST, 'dstart');
	$dend = $M_FUNC->M_Filter(POST, 'dend');

	$year = substr($date, 0, 4);
	$month = substr($date, 4, 2);
	$day = substr($date, 6, 2);

	//Deal_2020테이블에서는 상품 status만 변경
	//DealSum_2020테이블에서는 각 상태에 따른 가격 조정 후 PriceSum 수정
	$arr = explode("|", $priceKinds);

	$price = $arr[0];
	$Dprice = $arr[1];
	$small = $arr[2];
	$medium = $arr[3];
	$large = $arr[4];
	$priceSum = 1;
	switch($dstatus){
		case 0 : 
			$start = strlen($dstart)>8 ? $dstart : strtotime($dstart);
			$end = strlen($dend)>8 ? $dend : strtotime($dend);
			$dstatus = 0;
			$db->update('Deal_'.$year, array('dstart' => $start, 'dend' => $end), " WHERE idx = ".$dealIdx);
			break;
		case 1 :				//판매
			$db->update('Deal_'.$year, array('status' => $dstatus), " WHERE idx = ".$dealIdx);
			$db->update('DealSum_'.$year, array('status' => $dstatus, 'PriceSum' => $price), " WHERE dealIdx = ".$dealIdx." AND month = ".$month." AND day = ".$day);
			break;
		case 99 :				//환불
			//환불은 삭제와 동일한 로직이므로 삭제로직을 탈수 있도록 한다.
			//break;
		case 9 :				//삭제
			$db->update('Deal_'.$year, array('status' => $dstatus), " WHERE idx = ".$dealIdx);
			$db->update('DealSum_'.$year, array('status' => $dstatus, 'PriceSum' => 0), " WHERE dealIdx = ".$dealIdx." AND month = ".$month." AND day = ".$day);
			break;
		case 11 :				//교환
			//아직 교환이 일어나지 않은 상태이므로 반품과 똑같이 진행하고, 이후 교환이 일어나면 그 상황을 비롯해서 로직만들기
			//break;
		case 19 :				//취소
			//취소또한 반품과 동일하므로 지금은 반품 로직으로 움직이게 한다.
		case 14 :				//반품
			if($Dprice){
				$price = $price - $Dprice;
			}
			$num = $small+$medium+$large;
			$perPrice = $price/$num;
			$priceSum = $perPrice * ($num-$deliveryNum);
			$db->update('Deal_'.$year, array('status' => $dstatus), " WHERE idx = ".$dealIdx);
			$db->update('DealSum_'.$year, array('status' => $dstatus, 'PriceSum' => $priceSum), " WHERE dealIdx = ".$dealIdx." AND month = ".$month." AND day = ".$day);
			break;
	}
	
	if($priceSum < 0){
		echo "false";
	} else {
		echo $dstatus;
	}
?>