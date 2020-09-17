<?php
	ini_set("memory_limit" , -1);
	ini_set('max_execution_time',0);
	echo $file_dir = FILE_LOCATION; exit;
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
	echo $file_dir = FILE_LOCATION;
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
				//$allData[$row] = $rowData[0];
				//inserData를 만든 후 insert보다 여기에서 조건만 맞으면 바로바로 insert되도록 수정
				if($row > 1){
					//클릭수 0은 제외시켜서 등록(클릭률이 수치화 된걸로 판단하기 위해서)
					if($rowData[0][12] > 0){
						//그리고 키워드가 빈값일 때는 검색이후 상세페이지에 들어와서 함께보기 좋은 중단, 하단에 나오는 광고를 클릭해서
						//들어온 경우에 해당되는 키워드이므로 제외시켜준다.
						if($rowData[0][8] != ""){
							$data = array(
								'date'			=> $rowData[0][0],
								'campaignName'	=> $rowData[0][1],
								'itemName'		=> $rowData[0][10],
								'keyword'		=> $rowData[0][8],
								'impress'		=> $rowData[0][11],
								'click'			=> $rowData[0][12],
								'adPrice'		=> $rowData[0][13],
								'ctr'			=> $rowData[0][14]*100,
								'D_order1'		=> $rowData[0][15],
								'D_sale1'		=> $rowData[0][16],
								'D_sales1'		=> $rowData[0][17],
								'InD_order1'	=> $rowData[0][18],
								'InD_sale1'		=> $rowData[0][19],
								'InD_sales1'	=> $rowData[0][20],
								'D_order14'		=> $rowData[0][21],
								'D_sale14'		=> $rowData[0][22],
								'D_sales14'		=> $rowData[0][23],
								'InD_order14'	=> $rowData[0][24],
								'InD_sale14'	=> $rowData[0][25],
								'InD_sales14'	=> $rowData[0][26]
							);

							$db->insert("Report".substr($rowData[0][0], 0,4)."_".$tName, $data);
						}
					}
				}
			  }
		}
	} catch(exception $exception) {
		//echo $exception;
		M_JS::Go_URL('/?M0703', "엑셀 읽는데 실패하였습니다.\\n out 경로에 파일 존재하는지 확인하십시오.");
	}
?>