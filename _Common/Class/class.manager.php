<?php
class M_MANAGER {
	function __construct() {

	}

	function __destruct() {
		
	}

	function getBoardData(){
		global $db;

		$query = " SELECT * "
				." FROM Board_Info "
				." WHERE status <> 99 "
				." ORDER BY "
				." 	FIELD(status, 1, 2) DESC, "
				."  status ASC "
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function getBoardByIdx($idx){
		global $db;

		$query = " SELECT * "
				." FROM Board_Info "
				." WHERE status <> 99 "
				." AND idx = ".$idx
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function ChangeBoardData($mode){
		global $db, $M_FUNC;
		
		$category		= $M_FUNC->M_Filter(POST, "category");
		$subject		= $M_FUNC->M_Filter(POST, 'subject');
		$content		= $M_FUNC->M_Filter(POST, "content");

		$data = array(
			'category'				=> $category,
			'subject'				=> $subject,
			'content'				=> $content
		);

		if($mode == 'insert'){
			$data['accountIdx'] = $_SESSION['U_idx'];
			$data['accountName'] = $_SESSION['accountName'];
			$data['status'] = 1;
			$data['regUnixtime'] = time();

			$db->insert("Board_Info", $data);
		} elseif ($mode == 'update') {
			$m_id = $M_FUNC->M_Filter(POST, 'm_id');
			$status = $M_FUNC->M_Filter(POST, 'status');
				
			$data['status'] = $status;
			unset($data['category']);
			
			$db->update("Board_Info", $data, " WHERE idx = ".$m_id);
		} else {
			echo $m_id = $M_FUNC->M_Filter(GET, 'm_id');
			$delData['status'] = 9;

			$db->update("Board_Info", $delData, " WHERE idx = ".$m_id);
		}
	}

	function getGoodsData(){
		global $db;

		$query = " SELECT * "
				." FROM Goods_Info "
				." WHERE status <> 99 "
				." ORDER BY category asc, rollType asc, length asc, gName asc, count asc "
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function ChangeGoodsData($mode){
		global $db, $M_FUNC;
		
		$category	= $M_FUNC->M_Filter(POST, "category");
		$rollType	= $M_FUNC->M_Filter(POST, 'rollType');
		$length		= $M_FUNC->M_Filter(POST, "length");
		$count		= $M_FUNC->M_Filter(POST, "count");
		$gName		= $M_FUNC->M_Filter(POST, 'gName');
		$cost		= $M_FUNC->M_Filter(POST, "cost");
		$price		= $M_FUNC->M_Filter(POST, "price");

		$data = array(
			'category'		=> $category,
			'rollType'		=> $rollType,
			'length'		=> $length,
			'count'			=> $count,
			'gName'			=> $gName,
			'cost'			=> $cost,
			'price'			=> $price
		);

		if($mode == 'insert'){
			$data['status'] = 1;
			$data['regUnixtime'] = time();

			$db->insert("Goods_Info", $data);
		} elseif ($mode == 'update') {
			$m_id = $M_FUNC->M_Filter(POST, 'm_id');
			$status = $M_FUNC->M_Filter(POST, 'status');
			
			$data['status'] = $status;
			unset($data['category']);
			unset($data['gName']);

			$db->update("Goods_Info", $data, " WHERE idx = ".$m_id);
		} else {
			$m_id = $M_FUNC->M_Filter(GET, 'm_id');
			$delData['status'] = 9;

			$db->update("Goods_Info", $delData, " WHERE idx = ".$m_id);
		}
	}

	function ChangeMaterialData($mode){
		global $db, $M_FUNC;
		
		$division	= $M_FUNC->M_Filter(POST, "division");
		$category	= $M_FUNC->M_Filter(POST, "category");
		$subject	= $M_FUNC->M_Filter(POST, 'subject');
		$standard	= $M_FUNC->M_Filter(POST, 'standard');
		$detail		= $M_FUNC->M_Filter(POST, 'detail');
		$cost		= $M_FUNC->M_Filter(POST, "cost");
		$vat		= $M_FUNC->M_Filter(POST, "vat");
		if(!$vat){
			$vat = 0;
		}

		$data = array(
			'division'		=> $division,
			'category'		=> $category,
			'subject'		=> $subject,
			'standard'			=> $standard,
			'detail'			=> $detail,
			'cost'			=> $cost,
			'vat'			=> $vat
		);

		if($mode == 'insert'){
			$data['status'] = 1;
			$data['regUnixtime'] = time();

			$db->insert("Material_Info", $data);
		} elseif ($mode == 'update') {
			$m_id = $M_FUNC->M_Filter(POST, 'm_id');
			$status = $M_FUNC->M_Filter(POST, 'status');
			
			$data['status'] = $status;

			$db->update("Material_Info", $data, " WHERE idx = ".$m_id);
		} else {
			$m_id = $M_FUNC->M_Filter(GET, 'm_id');
			$delData['status'] = 9;

			$db->update("Material_Info", $delData, " WHERE idx = ".$m_id);
		}
	}

	function getGoodsByIdx($idx){
		global $db;

		$query = " SELECT * "
				." FROM Goods_Info "
				." WHERE status <> 9 "
				." AND idx = ".$idx
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function getCompanyData(&$total_page, &$NO, &$cnt_no, $addWhere = null, $order = null, $limit = null){
		global $db, $M_FUNC;
		global $PAGE;
		
		if($PAGE < 1) $PAGE = 1 ;
		$start_row = ($PAGE - 1) * C_LIST_CNT3;
		$where = " WHERE 1 = 1 ";
		
		if ($addWhere) 
		{
			$where .= $addWhere;
		}

		if ($order == "") 
		{
			$orderQuery = " ORDER BY idx DESC ";
		}
		else 
		{
			$orderQuery = " ORDER BY ". $order;
		}
		
		if ($limit == "NO") 
		{
			$limitQuery = "";
		} 
		else 
		{
			$limitQuery = " LIMIT ". $start_row .", ". C_LIST_CNT3;
		}

		
		$query = " SELECT * "
					. " FROM Company_Info "
					. $where
					. $orderQuery
					. $limitQuery;
		$row = $db->getListSet($query);

		$cnt_no = $db->cnt(" Company_Info ", $where);
		$total_page = (int)(($cnt_no - 1) / C_LIST_CNT3) + 1;
		$NO = $cnt_no - $start_row;
		
		return $row;		
	}

	
	function ChangeCompanyData($mode){
		global $db, $M_FUNC;
		
		$level			= $M_FUNC->M_Filter(POST, "level");
		$license1		= $M_FUNC->M_Filter(POST, 'license1');
		$license2		= $M_FUNC->M_Filter(POST, "license2");
		$license3		= $M_FUNC->M_Filter(POST, "license3");
		$companyName	= $M_FUNC->M_Filter(POST, 'companyName');
		$senior			= $M_FUNC->M_Filter(POST, "senior");
		$addr			= $M_FUNC->M_Filter(POST, "addr");
		$tel1			= $M_FUNC->M_Filter(POST, "tel1");
		$tel2			= $M_FUNC->M_Filter(POST, "tel2");
		$tel3			= $M_FUNC->M_Filter(POST, "tel3");
		$fax1			= $M_FUNC->M_Filter(POST, "fax1");
		$fax2			= $M_FUNC->M_Filter(POST, "fax2");
		$fax3			= $M_FUNC->M_Filter(POST, "fax3");
		$email			= $M_FUNC->M_Filter(POST, "email");
		$taxYN			= $M_FUNC->M_Filter(POST, "taxYN");
		$fees			= $M_FUNC->M_Filter(POST, "fees");

		if($license1 == "")	$license = "";
		else{
			if($level == 4){
				$license = $license1;
			} else {
				$license = $license1."-".$license2."-".$license3;
			}
		}

		if($tel1 == "")	$tel = "";
		else	$tel = $tel1."-".$tel2."-".$tel3;
			
		if($fax1 == "") $fax = "";
		else	$fax = $fax1."-".$fax2."-".$fax3;

		if($taxYN == "") $taxYN = 0;
		else	$taxYN = $taxYN;

		if($fees == "") $fees = 0;
		else	$fees = $fees;
		
		$data = array(
			'companyName'	=> $companyName,
			'license'		=> $license,
			'addr'			=> $addr,
			'tel'			=> $tel,
			'fax'			=> $fax,
			'senior'		=> $senior,
			'email'			=> $email,
			'level'			=> $level,
			'taxYN'			=> $taxYN,
			'fees'			=> $fees
		);

		if($mode == 'insert'){
			$data['status'] = 1;
			$data['regUnixtime'] = time();

			$db->insert("Company_Info", $data);
		} elseif ($mode == 'update') {
			$m_id = $M_FUNC->M_Filter(POST, 'm_id');

			$db->update("Company_Info", $data, " WHERE idx = ".$m_id);
		} else {
			$m_id = $M_FUNC->M_Filter(GET, 'm_id');
			$delData['status'] = 9;

			$db->update("Company_Info", $delData, " WHERE idx = ".$m_id);
		}
	}

	function getCompanyByIdx($idx){
		global $db;

		$query = " SELECT * "
				." FROM Company_Info "
				." WHERE status <> 9 "
				." AND idx = ".$idx
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function getUnitPriceByIdx($idx){
		global $db;

		$query = " SELECT * "
				." FROM UnitPrice_Info "
				." WHERE 1 =1  "
				." AND companyIdx = ".$idx
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function ChangeUnitPriceData($mode){
		global $db, $M_FUNC;
		
		$companyIdx		= $M_FUNC->M_Filter(POST, "companyIdx");
		$companyName	= $M_FUNC->M_Filter(POST, "companyName");
		$goodsIdx		= $_POST['goodsIdx'];
		$price			= $_POST['price'];

		$checkArr = $this->checkUnitPrice($companyIdx);
		
		for($i=0; $i<count($goodsIdx); $i++){
			
			
			if(in_array($goodsIdx[$i], $checkArr)){
				//거래처에 해당되는 상품Idx가 있으면 update
				$data = array(
					'price' => str_replace(",", "", $price[$i])
				);

				$where = " WHERE companyIdx = ". $companyIdx ." AND goodsIdx = ". $goodsIdx[$i];

				$db->update('UnitPrice_Info', $data, $where);
				$key = array_search($goodsIdx[$i], $checkArr); //배열에 키를 알아오고
				unset($checkArr[$key]);
			} else {
				//거래처에 해당되는 상품Idx가 없으면 insert
				$data = array(
					'companyIdx'	=> $companyIdx,
					'companyName'	=> $companyName,
					'goodsIdx'		=> $goodsIdx[$i],
					'price'			=> str_replace(",", "", $price[$i]),
					'regUnixtime'	=> time()
					
				);

				$db->insert('UnitPrice_Info', $data);
			}
		}

		if(count($checkArr) > 0){
			foreach($checkArr as $key => $value){
				
				$where = " WHERE companyIdx = ". $companyIdx ." AND goodsIdx = ". $checkArr[$key];
				$db->delete('UnitPrice_Info', $where);
			}
		}
	}

	function checkUnitPrice($cIdx){
		global $db;

		$query = " SELECT idx, goodsIdx "
				." FROM UnitPrice_Info "
				." WHERE 1=1 "
				." AND companyIdx = ". $cIdx
				;
		$row = $db->getListSet($query);
		
		$arr = array();
		for($i=0; $i<$row->size(); $i++){
			$row->next();
			
			array_push($arr, $row->get('goodsIdx'));
		}

		return $arr;
	}

}
?>