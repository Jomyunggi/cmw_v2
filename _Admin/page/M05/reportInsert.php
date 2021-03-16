<?php
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');
	ini_set('memory_limit', '-1');
	@set_time_limit(0);

	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	include_once COMMON_CLASS . '/PHPExcel_1.8.0/Classes/PHPExcel.php';
	include_once COMMON_CLASS . "/class.function.php";
	include_once COMMON_CLASS . "/class.db.php";
	
	$db			 = new M_DB();
	$M_FUNC		 = new M_FUNCTION;
	$objPHPExcel = new PHPExcel();
	
	$companyIdx = $M_FUNC->M_Filter(POST, "companyIdx");

	// 엑셀 데이터를 담을 배열을 선언한다.
	$allData = array();

	// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
	$file_dir = "C:\Users\aodrl\Desktop\Project\CMW_V2\\report\\";
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
		M_JS::Go_URL('/?M0501', "엑셀 읽는데 실패하였습니다.\\n report 경로에 파일 존재하는지 확인하십시오.");
	}
	
	$insertData = array();
	$num = 0;	
	switch($companyIdx){
		case 35 :	
			/*****	 쿠팡   *****/			
			$column_arr = array(
				0 => '날짜',
				4 => '캠페인',
				8 => '광고전환매출발생 상품명',
				9 => '광고전환매출발생 옵션ID',
				10 => '키워드',
				11 => '노출수',
				12 => '클릭수',
				13 => '광고비',
				14 => '클릭률',
				27 => '총 판매수량(14일)',
				28 => '직접 판매수량(14일)',
				29 => '간접 판매수량(14일)',
				30 => '총 전환매출액(14일)',
				31 => '직접 전환매출액(14일)',
				32 => '간접 전환매출액(14일)',
				36 => '총광고수익률(14일)',
				37 => '직접광고수익률(14일)',
				38 => '간접광고수익률(14일)'
			);
			
			foreach($allData as $key => $arr){
				if($key == 1){
					foreach($column_arr as $columnKey => $value){
						if($arr[$columnKey] != $value){
							M_JS::Go_URL('/?M0501', "기존 보고서 컬럼값이 변경된 것으로 의심됩니다.\\n 확인 후 다시 업로드해주세요.");
						}
					} 
				}
				
				if($arr[30] != 0 && $arr[13] != 0){
					$roas = number_format($arr[30] / $arr[13] * 100, 2);
					$roas = str_replace(",", "", $roas);
				} else {
					$roas = 0;
				}
				
				$insertData[$num] = array(
					'date'			=> $arr[0],
					'campaign'		=> $arr[4],	
					'goodsName'		=> $arr[8],	
					'optionN'		=> $arr[9],	
					'keyword'		=> $arr[10],	
					'view'			=> $arr[11],
					'click'			=> $arr[12],
					'cpc'			=> $arr[13], 
					'clickRate'		=> $arr[14],
					'salesCnt'		=> $arr[27],
					'salesCnt_D'	=> $arr[28],
					'salesCnt_I'	=> $arr[29],
					'salesPrice'	=> $arr[30],
					'salesPrice_D'	=> $arr[31],
					'salesPrice_I'	=> $arr[32],
					'roas'			=> $roas
				);

				if($num != 0){
					if($insertData[$num]['keyword'] != ''){
						$db->insert("Coupang_Report", $insertData[$num]);
					}
				}
				$num++;
			}
		break;
	}
?>