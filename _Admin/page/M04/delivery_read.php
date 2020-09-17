<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	include_once COMMON_CLASS . '/PHPExcel_1.8.0/Classes/PHPExcel.php';
	include_once COMMON_CLASS . "/class.function.php";
	include_once COMMON_CLASS . "/class.db.php";
	
	$db			 = new M_DB("TEST");
	$M_FUNC		 = new M_FUNCTION;
	$objPHPExcel = new PHPExcel();

	// 엑셀 데이터를 담을 배열을 선언한다.
	$allData = array();

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$file_dir = "C:\Users\aodrl\Desktop\Project\CMW\_Admin\out\\";
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
		//echo $exception;
		M_JS::Go_URL('/?M0401E', "엑셀 읽는데 실패하였습니다.\\n out 경로에 파일 존재하는지 확인하십시오.");
	}

	$companyIdx = $M_FUNC->M_Filter(POST, "companyIdx");

	$num = 0;
	$queryNum = 0;
	$recipientArr = "";
	switch($companyIdx){
		case 34 :
			/**************************
				쿠팡
				[9] => 주문일
				[10] => 등록상품명
				[11] => 등록옵션명
				[16] => 업체상품코드
				[18] => 결제액
				[19] => 배송비구분
				[20] => 배송비
				[21] => 도서산간 추가배송비
				[22] => 구매수(수량)
				[23] => 옵션판매가(판매단가)
				[24] => 구매자
				[27] => 수취인이름
				[28] => 수취인전화번호
				[30] => 수취인 주소
				[31] => 배송메세지
			***************************/
			$cnt = count($allData) - 1;
			foreach($allData as $key => $arr){
				if($key == 1) continue;
				$queryNum++;

				$recipientArr .= "'".$arr[27]."'";
				if($num < $cnt) {
					if($cnt == 1) continue;
					if($num == $cnt-1) continue;
					$recipientArr .= ", ";
				}
				$num++;
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
	}

	$query = " SELECT idx "
			." FROM Deal_".date("Y", time())
			." WHERE status = 1 AND companyIdx = ".$companyIdx
			."	AND date <> ".date('Ymd', time())." AND dstart > 0 AND dend = 0 "
			;
	if($queryNum > 0){
			$query .= "	AND recipient not in (".$recipientArr.") ";
	}
	$row = $db->getListSet($query);

	for($i=0; $i<$row->size(); $i++){
		$row->next();
		
		$arr = array('dend' => strtotime(date('Ymd', time())));
		$db->update("Deal_".date("Y", time()), $arr, " WHERE idx = ".$row->get('idx'));
	}
?>