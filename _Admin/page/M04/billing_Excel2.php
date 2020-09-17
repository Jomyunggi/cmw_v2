<?php
	$date = date('Ymd_His', time());  
	header( "Content-type: application/vnd.ms-excel; charset=euc-kr");  
	header( "Content-Disposition: attachment; filename = billing_".$date.".xls" );   
	header( "Content-Description: PHP4 Generated Data" );

	@include_once "../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
		
	$serviceIdx = $M_FUNC->M_Filter(GET, 'serviceIdx');
	$dateYmd = $M_FUNC->M_Filter(GET, 'dateYmd');
	$startdate = substr($dateYmd, 0, 4) .".". substr($dateYmd, 4, 2) .".01";
	$enddate = substr($dateYmd, 0, 4) .".". substr($dateYmd, 4, 2) .".31";
	$q_startdate = $dateYmd."01";
	$q_enddate = $dateYmd."31";
	$today = date('Y.m.d', time());
	
	$title_kr = "청구 내역";
	$filename = "Billing" . "_" . date("YmdHis") .".xlsx";
	
	@include_once ADMIN_CLASS_PATH . "/class.accountPage.php";
	$M_ACCOUNT = new M_AccountPage;
	@include_once ADMIN_CLASS_PATH . "/class.billingPage.php";
	$M_BILLING = new M_BillingPage;

	//서비스명 가져오기
	$serviceName = $M_ACCOUNT->getServiceInfoByIdx($serviceIdx, 'serviceName');

	//총 청구내역 가져오기
	$addWhere = " AND r.dateYmd like '".$dateYmd."%' and r.serviceIdx = ". $serviceIdx;
	$group =  " serviceIdx ";
	$totalUsingRow = $M_BILLING->getReportDataByVmDayList($cnt, $addWhere,$addOrder,$addLimit, $group);
	$totalUsingRow->next();
	$total_price = $totalUsingRow->get('total_price') + intval($totalUsingRow->get('total_price')/10);

	//요금 상세내역 가져오기
	$column1 = " select sum(cpu) as sumCpu, sum(memory) as sumMemory, sum(disk) as sumDisk ";
	$query = " from Service_Info s "
			."		left join VM_Account_Link va on s.keyAccountIdx = va.accountIdx "
			."		left join VM_Info v on va.vmIdx = v.idx "
			." where s.idx = ".$serviceIdx." and ((v.`status` = 1 and  v_startDate <= ".$q_enddate.") OR (v.`status` in (4, 99) and v_endDate >= ".$q_startdate." and v_startDate <= ".$q_enddate."))";
	$row = $db->getListSet($column1.$query);
	$row->next();

	//cpu, memory, disk 각각에 대한 총량의 금액
	$addWhere = " and r.dateYmd between ".$q_startdate." and ".$q_enddate." and r.serviceIdx = ".$serviceIdx;
	$detailUsingRow = $M_BILLING->getReportDataByVmDayList($cnt, $addWhere, $order, $limit, "serviceIdx");
	$detailUsingRow->next();

	//이용중인 서버대수 가져오기
	$column2 = " select cpu, memory, count(cpu) as cnt";
	$group = " group by cpu, memory "
			. " order by cpu, memory ";
	$usingServer = $db->getListSet($column2.$query.$group);


	$table = "<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">";
	/*$table .="<style> "
			."	#tr_color {background-color:#f5f5f5; font-weight:bold;} "
			."	#td_left {text-align:left; padding-left:20px;} "
			."	#td_right {text-align:right; padding-right:20px;} "
			."	#td_center {text-align:center;} "
			."</style> ";*/

	$table .="<table>"
			."	<tr>"
			."		<td colspan=\"2\" style=\"font-size:13px; text-align:center; font-weight:bold; background-color:#F1F1FA;\">"
			."			".substr($dateYmd, 0, 4)." 년 ".substr($dateYmd, 4, 2)." 월 Sk Ondemand 서비스 사용요금"
			."		</td>"
			."	</tr>"
			."</table>"
			;
	

	$table_1 = "<table>"
			 . "	<tr>"
			 . "		<td colspan='4'>■ 고객정보</td>"
			 . "	</tr>"
			 . "</table>"
			 . "<table border=1>"
			 . "	<tr id=\"tr_color\">"
			 . "		<td id=\"td_left\">서비스명</td>"
	 		 . "		<td id=\"td_right\">".$serviceName."</td>"
			 . "		<td id=\"td_left\">고객정보</td>"
			 . "		<td id=\"td_right\">(주)게임빌컴투스플랫폼</td>"
			 . "	</tr>"
			 . "	<tr>"
			 . "		<td id=\"td_left\">청구서 작성일</td>"
			 . "		<td id=\"td_right\">". $today ."</td>"
			 . "		<td id=\"td_left\">사업자담당</td>"
			 . "		<td id=\"td_right\">홍길동</td>"
			 . "	</tr>"
			 . "	<tr>"
			 . "		<td id=\"td_left\">사용기간</td>"
			 . "		<td id=\"td_right\">". $startdate ." ~ ". $enddate ."</td>"
			 . "		<td id=\"td_left\">개발사담당</td>"
			 . "		<td id=\"td_right\">홍길동</td>"
			 . "	</tr>"
			 . "</table>"
			 ;
	/*
	<table>
		<tr>
			<td colspan='4'>■ 총 청구내역</td>
		</tr>
	</table>
	<table border=1>
		<tr id="tr_color">
			<td id="td_center">기본요금</td>
			<td id="td_center">부가가치세(VAT)</td>
			<td colspan='2' id="td_center">납부하실 금액</td>
		</tr>
		<tr>
			<td id="td_center"><?php echo number_format($totalUsingRow->get('total_price')); ?> 원</td>
			<td id="td_center"><?php echo number_format(intval($totalUsingRow->get('total_price')/10)); ?> 원</td>
			<td colspan='2' id="td_center"><?php echo number_format($total_price); ?> 원</td>
		</tr>
	</table>

	<br/>

	<table>
		<tr>
			<td colspan='4'>■ 요금 상세내역</td>
		</tr>
	</table>
	<table border=1>
		<tr id="tr_color">
			<td colspan='2' id="td_center">항목명</td>
			<td id="td_center">사용내역</td>
			<td id="td_center">금액</td>
		</tr>
		<tr>
			<td rowspan='3' id="td_center">기본료(A)</td>
			<td id="td_center">CPU</td>
			<td id="td_center"><?php echo number_format($row->get('sumCpu')); ?></td>
			<td id="td_center"><?php echo number_format($detailUsingRow->get('cpu_price')); ?> 원</td>
		</tr>
		<tr>
			<td id="td_center">MEMORY</td>
			<td id="td_center"><?php echo number_format($row->get('sumMemory')); ?></td>
			<td id="td_center"><?php echo number_format($detailUsingRow->get('memory_price')); ?> 원</td>
		</tr>
		<tr>
			<td id="td_center">DISK</td>
			<td id="td_center"><?php echo number_format($row->get('sumDisk')); ?></td>
			<td id="td_center"><?php echo number_format($detailUsingRow->get('disk_price')); ?> 원</td>
		</tr>
		<tr id="tr_color">
			<td colspan='3' id="td_center">소계</td>
			<td id="td_center"><?php echo number_format($detailUsingRow->get('total_price')); ?> 원</td>
		</tr>
		<tr>
			<td id="td_center">세금 및 기타(B)</td>
			<td id="td_center">부가가치세(VAT)</td>
			<td id="td_center">10%</td>
			<td id="td_center"><?php echo number_format(intval($detailUsingRow->get('total_price')/10)); ?> 원</td>
		</tr>
		<tr id="tr_color">
			<td colspan='3' id="td_center">총 합계(A)+(B)</td>
			<td id="td_center"><?php echo number_format($total_price); ?> 원</td>
		</tr>
	</table>

	<br/>

	<table>
		<tr>
			<td colspan='4'>■ 이용중인 서버대수</td>
		</tr>
	</table>
	<table border=1>
		<tr id="tr_color">
			<td id="td_center">CPU</td>
			<td id="td_center">MEMORY</td>
			<td id="td_center">대수</td>
			<td id="td_center">비교</td>
		</tr>
		<?php 
			if($usingServer->size() > 0){
				$sumCnt = 0;
				for($i=0; $i<$usingServer->size(); $i++){
					$usingServer->next();
					$sumCnt = $sumCnt + $usingServer->get('cnt');
		?>
		<tr>
			<td id="td_center"><?php echo $usingServer->get('cpu'); ?> CPU</td>
			<td id="td_center"><?php echo $usingServer->get('memory'); ?> GB</td>
			<td id="td_center"><?php echo $usingServer->get('cnt'); ?> 대</td>
			<td id="td_center"></td>
		</tr>
		<?php
				}
			} else {
		?>
		<tr>
			<td colspan='4' id="td_center">해당 데이터가 없습니다.</td>
		</tr>
		<?php
			}
		?>
		<tr id='tr_color'>
			<td colspan='2' id="td_center">합계</td>
			<td id="td_center"><?php echo $sumCnt; ?> 대</td>
			<td id="td_center"></td>
		</tr>
	</table>

	<br/>

	<table>
		<tr>
			<td colspan='4' rowspan='6'>
				<img src="http://222.239.14.169/images/Monthly_Revenue.png">
			</td>
		<tr>
	</table>
	*/
	
	//tmpfile은 디렉토리가 존재하지 않기 때문에
	//기본 임시 디렉토리에 파일이 생성됩니다.
	$tmpfile = tempnam(sys_get_temp_dir(), 'html');
	file_put_contents($tmpfile, $table.$table_1);
	
	include_once COMMON_CLASS . '/PHPExcel_1.8.0/Classes/PHPExcel.php';
	$objPHPExcel     = new PHPExcel();
	$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');
	$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);
	$objPHPExcel->getProperties()->setTitle($title_kr); 
	//unlink($tmpfile);

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8'); // header for .xlxs file
	header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
	header('Cache-Control: max-age=0');


	$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$writer->save('php://output');
	exit;
?>

<?php
	$date = date('Ymd_His', time());  
	header( "Content-type: application/vnd.ms-excel; charset=euc-kr");  
	header( "Content-Disposition: attachment; filename = billing_".$date.".xls" );   
	header( "Content-Description: PHP4 Generated Data" );

	@include_once "../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	@include_once ADMIN_CLASS_PATH . "/class.dealPage.php";
	$M_DealPage = new M_DealPage;

	$deliveryRow = $M_DealPage->excel_delivery();
?>
<meta http-equiv="Content-Type" content="application/vnd.ms-excel; charset=utf-8">
<style>
	/*
	#tr_color {font-weight:bold;}
	#td_left {text-align:left; padding-left:20px;}
	#td_right {text-align:right; padding-right:20px;}
	#td_center {text-align:center;}
	*/
	table {
	  background-color: transparent;
	  border-spacing: 0;
	  border-collapse: collapse;
	}
	tbody {
		border-color: inherit;
	}
	tr {
		border-color: inherit;
	}
	.table-bordered {
	  border: 1px solid black;
	}
	.table-bordered > thead > tr > th,
	.table-bordered > tbody > tr > th,
	.table-bordered > tfoot > tr > th,
	.table-bordered > thead > tr > td,
	.table-bordered > tbody > tr > td,
	.table-bordered > tfoot > tr > td {
	  border: 1px solid black;
	}
	
</style>
<table class="table table-bordered">
	<!--tr>
		<td>수하인</td>
		<td>핸드폰</td>
		<td>전화번호</td>
		<td>상품명</td>
		<td>우편번호</td>
		<td>주소</td>
		<td>배송메세지</td>
		<td>수량(소)</td>
		<td>수량(중)</td>
		<td>수량(대)</td>
		<td>특기사항</td>
	</tr-->
<?php
	if($deliveryRow->size() > 0){
		for($i=0; $i<$deliveryRow->size(); $i++){
			$deliveryRow->next();

			$kinds = explode("|", $deliveryRow->get('priceKinds'));
			$small = $kinds[2] == "" ? 0 : $kinds[2];
			$medium = $kinds[3] == "" ? 0 : $kinds[3];
			$large = $kinds[4] == "" ? 0 : $kinds[4];
?>
	<tr>
		<td><?php echo $deliveryRow->get('recipient');	?></td>
		<td><?php echo $deliveryRow->get('tel');	?></td>
		<td><?php echo $deliveryRow->get('tel2');	?></td>
		<td><?php echo $deliveryRow->get('deal').", ".$deliveryRow->get('dealNum')."개";	?></td>
		<td></td>
		<td><?php echo $deliveryRow->get('addr');	?></td>
		<td><?php echo $deliveryRow->get('deliveryTxt');	?></td>
		<td><?php echo $small;	?></td>
		<td><?php echo $medium;	?></td>
		<td><?php echo $large;	?></td>
		<td>없음</td>
	</tr>
<?php
		}
	}
?>
</table>