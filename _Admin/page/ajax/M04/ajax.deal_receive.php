<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";
	
	$year		= $M_FUNC->M_Filter(POST, 'year');
	$idx		= $_POST['idx'];
	$price		= $_POST['price'];
	$discount	= $_POST['discount'];
	$companyIdx	= $M_FUNC->M_Filter(POST, 'companyIdx');

	include_once ADMIN_CLASS_PATH . '/class.accountPage.php';
	$M_AccountPage = new M_AccountPage;

	//온라인 거래처별 수수료
	$commissionArr = $M_AccountPage->getCompanyByCommission();
	
	$cnt = count($idx);
	if($cnt){
		//정산시킬 deal idx가 넘어왔으니깐 정산액으로 update하기
		for($i=0; $i<$cnt; $i++){
			if($discount[$i] == "")	$discount_price = 0;
			else	$discount_price = (int)$discount[$i];
			
			//반품,교환,취소인 경우 0원으로 처리하기 때문에 정산을 하려면 1원이라도 넣어줘야한다.
			if($price[$i] == 0 || $price[$i] == "") $price[$i] = 1;

			//정산금액 측정하기		ceil 올림, round 반올림, floor 내림
			$commission = floor($price[$i] * $commissionArr[$companyIdx]/100);
			$receive = ceil($price[$i]-$commission-$discount_price);
			$data = array(
				'receive'	=> $receive,
				'discount'	=> $discount_price
			);
			$where = " WHERE companyIdx = ". $companyIdx ." AND idx = ". $idx[$i];
			$db->update(DEAL_TABLE.$year, $data, $where);
		}
		echo "success";
	} else {
		echo "failed";
	}	
?>