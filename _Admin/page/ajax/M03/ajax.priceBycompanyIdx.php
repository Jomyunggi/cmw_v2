<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	//전페이지에서 받아온 판매처 idx값
	$companyIdx = $M_FUNC->M_Filter(GET, 'companyIdx');

	$query = " SELECT goodsPrice "
			." FROM Goods_Info "
			." WHERE status = 1 AND companyIdx = ". $companyIdx
			;
	$row = $db->getListSet($query);
	$row->next();
	
	$priceBygoods = explode('|', $row->get('goodsPrice'));
		
	echo json_encode($priceBygoods);
?>