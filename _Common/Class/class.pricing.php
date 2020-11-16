<?php
class M_PRICING {
	function __construct() {

	}

	function __destruct() {
		
	}
	
	function getD_P_Date(){
		global $db;

		$query = " SELECT g.idx as goodsIdx, d.idx, d.count, d.size, g.gName, g.rollType "
				." FROM Delivery_Info d "
				." LEFT JOIN Goods_Info g on d.goodsIdx = g.idx "
				." WHERE d.status = 1 "
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function getGoodsData(){
		global $db;

		$query = " SELECT * "
				." FROM Goods_Info "
				." WHERE status = 1 "
				;
		$row = $db->getListSet($query);

		$arr = array();
		if($row->size() > 0){
			for($i=0; $i<$row->size(); $i++){
				$row->next();

				$arr[$row->get('idx')] = $row->get('gName')." ".$row->get('rollType');
			}
		}

		return $arr;
	}

	function getDeliberyByIdx($m_id){
		global $db;

		$query = " SELECT g.idx as goodsIdx, d.idx, d.count, d.size, g.gName, g.rollType "
				." FROM Delivery_Info d "
				." LEFT JOIN Goods_Info g on d.goodsIdx = g.idx "
				." WHERE d.status = 1 "
				." AND d.idx = ". $m_id
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function ChangeDeliveryData($mode){
		global $db;
		global $M_FUNC;

		$goodsIdx	= $M_FUNC->M_Filter(POST, "goodsIdx");
		$count		= $M_FUNC->M_Filter(POST, 'count');
		$size		= $M_FUNC->M_Filter(POST, "size");

		$data = array(
			'goodsIdx'	=> $goodsIdx,
			'count'		=> $count,
			'size'		=> $size
		);

		if($mode == 'insert'){
			$data['status'] = 1;
			$data['regUnixtime'] = time();

			$db->insert("Delivery_Info", $data);
		} elseif ($mode == 'update') {
			$m_id = $M_FUNC->M_Filter(POST, 'm_id');
			
			unset($data['goodsIdx']);

			$db->update("Delivery_Info", $data, " WHERE idx = ".$m_id);
		} else {
			$m_id = $M_FUNC->M_Filter(GET, 'm_id');
			$delData['status'] = 9;

			$db->update("Delivery_Info", $delData, " WHERE idx = ".$m_id);
		}
	}

	function getCompanyByOn(){
		global $db;

		$query = " SELECT idx, companyName "
				." FROM Company_Info "
				." WHERE status = 1 AND level = 4 "
				;
		$row = $db->getListSet($query);

		$arr[0] = '전체선택';
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			$arr[$row->get('idx')] = $row->get('companyName');
		}

		return $arr;
	}

	function getGoods(){
		global $db;

		$query = " SELECT * "
				." FROM Goods_Info "
				." WHERE status = 1 "
				;
		$row = $db->getListSet($query);

		$arr[0] = '전체선택';
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			$arr[$row->get('idx')] = $row->get('gName');
		}

		return $arr;
	}
}
?>