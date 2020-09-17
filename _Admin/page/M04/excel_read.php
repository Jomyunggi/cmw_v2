<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	include_once COMMON_CLASS . '/PHPExcel_1.8.0/Classes/PHPExcel.php';

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

	//회사명가져오기
	$companyArr = $M_UnitPage->getCompanyArr("", " AND idx = ". $companyIdx);
	
	$insertData = array();
	$num = 0;
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
			foreach($allData as $key => $arr){
				if($key == 1) continue;
				
				$sumDprice = (int)str_replace(",", "", $arr[20])+(int)str_replace(",", "", $arr[21]);
				$insertData[$num] = array(
					'date'			=> date('Ymd', strtotime($date)),		//출고일
					'time'			=> date('YmdHi', strtotime($arr[9])),	//결제일
					'companyIdx'	=> $companyIdx,
					'companyName'	=> $companyArr[$companyIdx],
					'deal'			=> $arr[10]." ".$arr[11],
					'dealNum'		=> $arr[22],
					'buyer'			=> $arr[24],
					'recipient'		=> $arr[27],
					'tel'			=> $arr[28],
					'addr'			=> $arr[30],
					'deliveryTxt'	=> $arr[31],
					'price'			=> $arr[18],
					'dprice'		=> $sumDprice
				);
				$num++;
			}
			break;
		case 35 :
			/**************************
				티몬
				[20] => 주문일, 결제완료일
				[15] => 등록상품명
				[16] => 등록옵션명
				[18] => 수량
				[19] => 결제액
				[41] => 배송비구분
				[42] => 배송비
				[12] => 구매자
				[22] => 수취인이름
				[24] => 수취인전화번호
				[25] => 수취인 주소
				[27] => 배송메세지
			***************************/
			foreach($allData as $key => $arr){
				if($key == 1) continue;
				else {
					if($arr[0] == "") break;
				}
				
				if($arr[16] == "") $deal = $arr[15]." ".$arr[18];
				else $deal = $arr[16];

				$insertData[$num] = array(
					'date'			=> date('Ymd', strtotime($date)),		//출고일
					'time'			=> date('YmdHi', strtotime($arr[20])),	//결제일
					'companyIdx'	=> $companyIdx,
					'companyName'	=> $companyArr[$companyIdx],
					'deal'			=> $deal,
					'dealNum'		=> $arr[18],
					'buyer'			=> $arr[12],
					'recipient'		=> $arr[22],
					'tel'			=> $arr[24],
					'addr'			=> $arr[25],
					'deliveryTxt'	=> $arr[27],
					'price'			=> $arr[19],
					'dprice'		=> $arr[42]
				);
				$num++;
			}
			break;
		case 36 :
			break;
		case 37 :
			/**************************
				11번가
				[1] => 결제일시
				[2] => 상품명
				[3] => 옵션
				[4] => 수량
				[5] => 주문금액
				[6] => 배송비
				[7] => 구매자
				[8] => 수취인
				[9] => 휴대폰번호
				[10] => 주소
				[11] => 배송메시지
				[12] => 전화번호
			***************************/
			foreach($allData as $key => $arr){
				if($key == 1 || $key == 2) continue;
				if($arr[1] == "") continue;
				
				$insertData[$num] = array(
					'date'			=> date('Ymd', strtotime($date)),		//출고일
					'time'			=> date('YmdHi', strtotime($arr[1])),	//결제일
					'companyIdx'	=> $companyIdx,
					'companyName'	=> $companyArr[$companyIdx],
					'deal'			=> $arr[3] == "" ? $arr[2] : $arr[3],
					'dealNum'		=> $arr[4],
					'buyer'			=> $arr[7],
					'recipient'		=> $arr[8],
					'tel'			=> $arr[9],
					'addr'			=> $arr[10],
					'deliveryTxt'	=> $arr[11],
					'price'			=> $arr[5],
					'dprice'		=> $arr[6]
				);
				$num++;
			}
			break;
		case 38 :
			break;
		case 39 :
			break;
		case 40 :
			/**************************
				네이버스토어
				[8] => 구매자명
				[10] => 수취인명
				[14] => 결제일
				[16] => 상품명
				[18] => 옵션명
				[20] => 수량
				[22] => 상품가격
				[25] => 상품별 총 주문금액
				[34] => 배송비합계
				[40] => 수취인연락처1
				[41] => 수취인연락처2
				[42] => 배송지
				[45] => 배송메세지
				[57] => 주문일시
			***************************/
			foreach($allData as $key => $arr){
				if($key < 3) continue;

				if($arr[18] == "") $deal = $arr[16];
				else $deal = $arr[18];

				$insertData[$num] = array(
					'date'			=> date('Ymd', strtotime($date)),		//출고일
					'time'			=> date('YmdHi', Exl2phpTime($arr[57])),	//결제일
					'companyIdx'	=> $companyIdx,
					'companyName'	=> $companyArr[$companyIdx],
					'deal'			=> $deal,
					'dealNum'		=> $arr[20],
					'buyer'			=> $arr[8],
					'recipient'		=> $arr[10],
					'tel'			=> $arr[40],
					'addr'			=> $arr[42],
					'deliveryTxt'	=> $arr[45],
					'price'			=> $arr[25],
					'dprice'		=> $arr[34]
				);
				$num++;
			}
			break;
	}

	//insert data를 정리된 데이터로 insert하기
	for($i=0; $i<count($insertData); $i++){
		$deal = $insertData[$i]['deal'];
		if(strlen($insertData[$i]['deal']) > 60){
			$deal = strcut_utf8($insertData[$i]['deal'], 30);
		}

		$data = array(
			'date'			=> $insertData[$i]['date'],	//출고일
			'time'			=> $insertData[$i]['time'],	//결제일
			'companyIdx'	=> $insertData[$i]['companyIdx'],
			'companyName'	=> $insertData[$i]['companyName'],
			'dealType'		=> 1,
			'onoff'			=> 'On',
			'taxYN'			=> 'Y',
			'deal'			=> $deal,
			'dealNum'		=> $insertData[$i]['dealNum'],
			'deliveryYN'	=> "Y",
			'calculateDate'	=> 0,
			'depositYN'		=> 'N',
			'status'		=> 1,
			'buyer'			=> $insertData[$i]['buyer'],
			'recipient'		=> $insertData[$i]['recipient'],
			'tel'			=> $insertData[$i]['tel'],
			'addr'			=> $insertData[$i]['addr'],
			'deliveryTxt'	=> $insertData[$i]['deliveryTxt'],
			'priceKinds'	=> $insertData[$i]['price']."|".$insertData[$i]['dprice']."|||",
			'regUnixtime'	=> time()
		);

		$sumPrice = (int)str_replace(",", "", $insertData[$i]['price'])+(int)str_replace(",", "", $insertData[$i]['dprice']);
		$sumData = array(
			'month'			=> date('m', strtotime($insertData[$i]['time'])),
			'day'			=> date('d', strtotime($insertData[$i]['time'])),
			'companyIdx'	=> $companyIdx,
			'companyName'	=> $companyArr[$companyIdx],
			'PriceSum'		=> $sumPrice,
			'onoff'			=> 'On',
			'taxYN'			=> 'Y',
			'regUnixtime'	=> time()
		);		

		//혹시 파일을 잘못 올렸을 경우 확인 후 insert 정지시키기
		$query = " SELECT recipient "
				." FROM Deal_".date('Y', strtotime($insertData[$i]['time']))
				." WHERE time = '".$data['time']."' AND recipient = '".$data['recipient']."' "
				." AND deal = '".$data['deal']."' "
				;
		$row = $db->getListSet($query);

		if($row->size() > 0){
			M_JS::Go_URL('/?M0401E', "이전에 등록된 데이터입니다.\\n다시한번 확인해 주세요.");
			exit;
		}
		
		//Deal_2020 거래명세표 등록후 DealSum_2020 등록
		$db->insert("Deal_".date('Y', strtotime($insertData[$i]['time'])), $data);
		$db->insert("DealSum_".date('Y', strtotime($insertData[$i]['time'])), $sumData);
	}

	function strcut_utf8($str, $len) {
      $checkmb=false ;
      $tail = '...' ; // 문장끝에 ... 을 붙이거나 다른 걸로 바꿔도 된다
	  preg_match_all('/[\xE0-\xFF][\x80-\xFF]{2}|./', $str, $match);
	  $m = $match[0];
	  $slen = strlen($str); // length of source string
	  $tlen = strlen($tail); // length of tail string
	  $mlen = count($m); // length of matched characters
	  
	  if ($slen <= $len) return $str;
	  if (!$checkmb && $mlen <= $len) return $str;
	  
	  $ret = array();
	  $count = 0;

	  for($i=0; $i < $len; $i++){
		  $count += ($checkmb && strlen($m[$i]) > 1)?2:1;
		  if ($count + $tlen > $len) break;
		  $ret[] = $m[$i];
	  }
	  
	  return join('', $ret).$tail;
	}

	function Exl2phpTime($tRes, $dFormat='1900'){
		if($dFormat == '1904') $fixRes = 24107.375;
		else $fixRes = 25569.375;

		return intval((($tRes - $fixRes) * 86400));
	}
?>