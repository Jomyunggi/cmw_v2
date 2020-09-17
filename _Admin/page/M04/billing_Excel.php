<?php
	@include_once "../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	include_once COMMON_CLASS . '/PHPExcel_1.8.0/Classes/PHPExcel.php';

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

	$query = " SELECT * "
			." FROM ".DEAL_TABLE.date('Y', time())
			." WHERE 1 = 1 "
			//." AND onoff = 'On' AND date = 20200309 AND dstart = 0 "
			." AND onoff = 'On' AND date = ". $date ." AND dstart = 0 "
			;
	$row = $db->getListSet($query);

	if($row->size() == 0) {
		$M_JS->Go_URL('/?M0401', "출고할 상품이 없습니다.");
		exit;
	}

	//set the desired name of the excel file
	$fileName = 'delivery_'.date('YmdHis', time());

	//create new PHPExcel object
	$objPHPExcel = new PHPExcel();

	//set document properties
	$objPHPExcel->getProperties()->setCreator('Me')->setLastModifiedBy('Me')->setTitle('My Excel Sheet')->setSubject('My Excel Sheet')->setDescription('Excel Sheet')->setKeywords('Excel Sheet')->setCategory('Me');

	//Set active sheet index to the first sheet, so Excel opens this as the first sheet
	//$objPHPExcel->setActiveSheetIndex(0);

	// Add column headers
	/*
	$objPHPExcel->getActiveSheet()
				->setCellValue('A1', '번호')
				->setCellValue('B1', '질문')
				->setCellValue('C1', '답변')
				->setCellValue('D1', '문의수')
				;
				*/
	
	$ii = 1;
	for($i=0; $i<$row->size(); $i++){
		$row->next();

		$deliveryS = explode("|", $row->get('priceKinds'));
		$S = $deliveryS[2] == "" ? 0 : $deliveryS[2];
		$M = $deliveryS[3] == "" ? 0 : $deliveryS[3];
		$L = $deliveryS[4] == "" ? 0 : $deliveryS[4];

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$ii, $row->get('recipient'));
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$ii, $row->get('tel'));
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$ii, '');
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$ii, $row->get('deal').", ".$row->get('dealNum')."개");
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$ii, '');
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$ii, $row->get('addr'));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$ii, $row->get('deliveryTxt'));
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$ii, $S);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$ii, $M);
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$ii, $L);
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$ii, "없음");
		$ii++;
	}

	//Set worksheet title
	$objPHPExcel->getActiveSheet()->setTitle($fileName);

	//save the file to the server(Excel 2007)
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('.\\'.$fileName.'.xlsx');	//저장될 파일위치


	//파일 다운로드 구현
	function mb_basename($path) { return end(explode('/', $path)); }
	function utf2euc($str) { return iconv("UTF-8", "cp949//IGNORE", $str); }
	function is_ie(){
		if(!isset($_SERVER['HTTP_USER_AGENT'])) return false;
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) return true; //IE8
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) return true; //IE11
		return false;
	}

	$filepath = '.\\'.$fileName.'.xlsx';
	$filesize = filesize($filepath);
	$filename = mb_basename($filepath);
	if(is_ie()) $filename = utf2euc($filename);

	header("Pragma:public");
	header("Expires:0");
	header("Content-Type:application/octet-stream");
	header("Content-Disposition:attachment; filename=\"$filename\"");
	header("Content-Transfer-Encoding:binary");
	header("Content-Length:$filesize");
	ob_clean();
	flush();
	readfile($filepath);

	unlink($filepath);	//생성된 excel 파일 삭제
?>