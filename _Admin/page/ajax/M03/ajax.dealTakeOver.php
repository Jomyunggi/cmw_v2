<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	include_once ADMIN_CLASS_PATH . "/class.dealPage.php";
	$M_DealPage = new M_DealPage;

	//Cash_Info에 거래에 대한 금액 미수로 잡기
	//미수로 잡고 Deal_20xx에 미수로 잡았다고 takeover를 1로 변경
	$gubun			= $M_FUNC->M_Filter(POST, "gubun");
	$companyIdx		= $M_FUNC->M_Filter(POST, "companyIdx");
	$date			= $M_FUNC->M_Filter(POST, "date");
	$onoff			= $M_FUNC->M_Filter(POST, "onoff");
	$table			= $M_FUNC->M_Filter(POST, "table");
	$m_id			= $M_FUNC->M_Filter(POST, "m_id");
	
	if($gubun == "one"){
		$dealView = $M_DealPage->dealViewByidx(substr($date, 0, 4), 'AND d.idx = '.$m_id.' AND d.companyIdx = '.$companyIdx);
	} else if($gubun == "all") {
		$dealView = $M_DealPage->dealViewByidx(substr($date, 0, 4), 'AND d.companyIdx = '.$companyIdx.' AND d.takeover = 0 AND left(d.date, 6) = '.$date);
	} else {	//cAll 판매처 구분 없이 해당 월에 해당되는 인수건 인수하기
		$dealView = $M_DealPage->dealViewByidx(substr($date, 0, 4), 'AND d.onoff = "Off" AND d.takeover = 0 AND left(d.date, 6) = '.$date);
	}
	
	if($dealView->size() > 0){
		for($j=0; $j<$dealView->size(); $j++){
			$dealView->next();

			//상품별 단가, 갯수 가져오기
			$goodsPriceSum = 0;
			$goodsMax = explode("|", $dealView->get('deal'));
			for($i=0; $i<sizeof($goodsMax); $i++){
				$goodsPriceSum += (int)$goodsMax[$i];
			}
			
			//Cash_Info에 인수하는 내용 입력
			$data = array(
				'dealIdx'		=>	$dealView->get('idx'),
				'companyIdx'	=>	$dealView->get('companyIdx'),
				'date'			=>	$dealView->get('date'),
				'onoff'			=>	$onoff,
				'cashType'		=>	1,		// 1:인수, 2:정산
				'price'			=>	$goodsPriceSum,
				'regUnixtime'	=>	time()
			);

			$db->insert("Cash_Info", $data);

			//인수확인 후 Deal_20xx에서 해당데이터 인수확인 된 내용 update
			$deal_data = array(
				'takeover'	=> 1
			);
			$db->update($table, $deal_data, " WHERE idx = ". $dealView->get('idx') ." AND companyIdx = ". $dealView->get('companyIdx'));
		}

		echo "success";
	} else {
		echo "NO";
	}
?>