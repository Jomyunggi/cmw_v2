<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	include_once COMMON_CLASS . '/PHPExcel_1.8.0/Classes/PHPExcel.php';
	print_r($_POST);
	print_r($_FILES);exit;
	$objPHPExcel = new PHPExcel();

	// 엑셀 데이터를 담을 배열을 선언한다.
	$allData = array();

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$file_dir = "D:\Project\cmw\_Admin\out\\";
	$filename = iconv("UTF-8", "EUC-KR", $_FILES['excelFile']['name']);

	try {

		// 업로드한 PHP 파일을 읽어온다.
		$objPHPExcel = PHPExcel_IOFactory::load($file_dir.$filename);
		$sheetsCount = $objPHPExcel -> getSheetCount();

		// 시트Sheet별로 읽기
		for($sheet = 0; $sheet < $sheetsCount; $sheet++) {

			  $objPHPExcel -> setActiveSheetIndex($sheet);
			  $activesheet = $objPHPExcel -> getActiveSheet();
			  $highestRow = $activesheet -> getHighestRow();             // 마지막 행
			  $highestColumn = $activesheet -> getHighestColumn();    // 마지막 컬럼

			  // 한줄읽기
			  for($row = 1; $row <= $highestRow; $row++) {

				// $rowData가 한줄의 데이터를 셀별로 배열처리 된다.
				$rowData = $activesheet -> rangeToArray("A" . $row . ":" . $highestColumn . $row, NULL, TRUE, FALSE);

				// $rowData에 들어가는 값은 계속 초기화 되기때문에 값을 담을 새로운 배열을 선안하고 담는다.
				$allData[$row] = $rowData[0];
			  }
		}
	} catch(exception $exception) {
		echo $exception;
	}

	echo "<pre>";
	print_r($allData[1]);
	
	//$companyIdx = $M_FUNC->M_Filter(GET, "companyIdx");
	/*
		[0] => 번호
		[1] => 묶음배송번호
		[2] => 주문번호
		[3] => 택배사
		[4] => 운송장번호
		[5] => 분리배송 Y/N
		[6] => 분리배송 출고예정일
		[7] => 주문시 출고예정일
		[8] => 출고일(발송일)
		[9] => 주문일
		[10] => 등록상품명
		[11] => 등록옵션명
		[12] => 노출상품명(옵션명)
		[13] => 노출상품ID
		[14] => 옵션ID
		[15] => 최초등록옵션명
		[16] => 업체상품코드
		[17] => 바코드
		[18] => 결제액
		[19] => 배송비구분
		[20] => 배송비
		[21] => 도서산간 추가배송비
		[22] => 구매수(수량)
		[23] => 옵션판매가(판매단가)
		[24] => 구매자
		[25] => 구매자이메일
		[26] => 구매자전화번호
		[27] => 수취인이름
		[28] => 수취인전화번호
		[29] => 우편번호
		[30] => 수취인 주소
		[31] => 배송메세지
		[32] => 상품별 추가메시지
		[33] => 주문자 추가메시지
		[34] => 배송완료일
		[35] => 구매확정일자
		[36] => 개인통관번호(PCCC)
		[37] => 통관용구매자전화번호
		[38] => 기타
		[39] => 결제위치
	*/

	$time = date('H', time());
	$dayOfweek = date('N', time());
	if($dayOfweek == 6) {
		$date = date('Ymd', strtotime('+2 day', time()));
	} elseif ($dayOfweek == 7){
		$date = date('Ymd', strtotime('+1 day', time()));
	} else {
		if($time > 17) $date = date('Ymd', strtotime('+1 day', time()));
		else $date = date('Ymd', time());
	}

	switch($companyIdx){
		case 34 :
			foreach($allData as $key => $value){
			print_r($key);
			/*
				$data = array(
					'date'		=> date('Y-m-d',strtotime($date)),
					'time'		=> date(
					*/

			}
			break;
		case 35 :
			break;
		case 36 :
			break;
		case 37 :
			break;
		case 38 :
			break;
		case 39 :
			break;
		case 40 :
			break;
		default :
			foreach($allData as $num => $arr){
			/*
				$data = array(
					'date'		=> date('Y-m-d', strtotime($date)),
					'time'		=> date('YmdHi', strtotime($arr[9])),

					*/
			}
			break;
	}

?>