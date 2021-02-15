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

				$arr[$row->get('idx')] = $row->get('gName')." ".$row->get('rollType')." ".$row->get('count');
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

		$goodsIdx		= $M_FUNC->M_Filter(POST, "goodsIdx");
		$count			= $M_FUNC->M_Filter(POST, 'count');
		$size			= $M_FUNC->M_Filter(POST, "size");
		$csPercent		= $M_FUNC->M_Filter(POST, "csPercent");

		$data = array(
			'goodsIdx'		=> $goodsIdx,
			'count'			=> $count,
			'size'			=> $size,
			'csPercent'		=> $csPercent
		);

		if($mode == 'insert'){
			$data['status'] = 1;
			$data['regUnixtime'] = time();

			//택배별로 insert할때 각 온라인 사이트별 다 넣어준다
			$companyArr = $this->getCompanyByOn('idx', 'idx');
			unset($companyArr[0]);

			$db->insert("Delivery_Info", $data);
			$dIdx = $db->getInsertID();

			foreach($companyArr as $key => $value){
				$R_data = array(
					'cIdx'			=> $key,
					'dIdx'			=> $dIdx,
					'salePrice'		=> 0,
					'revenue'		=>	0,
					'regUnixtime'	=> time(),
					'status'		=> 1
				);

				$db->insert('Revenue_Info', $R_data);
			}
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

	function getCompanyByOn($key, $value){
		global $db;

		$query = " SELECT idx, companyName, fees, license "
				." FROM Company_Info "
				." WHERE status = 1 AND level = 4 "
				." ORDER BY license * 1 asc "
				;
		$row = $db->getListSet($query);

		$arr[0] = '전체선택';
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			$arr[$row->get($key)] = $row->get($value);
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

	function getFinalSales($addWhere){
		global $db;

		$query = " SELECT d.count, d.size, g.category, g.rollType, g.gName, g.count as gCount, g.cost, g.price, d.idx as dIdx, r.revenue,  r.salePrice, d.csPercent "
				." FROM Delivery_Info d "
				."	LEFT JOIN Goods_Info g ON d.goodsIdx = g.idx "
				."	LEFT JOIN Revenue_Info r on r.dIdx = d.idx "
				." WHERE d.status = 1 "
				.$addWhere
				." ORDER BY "
				."	g.rollType ASC, "
				."	(case when category = 1 then g.gName END) ASC, "
				."	g.count, "
				."	(case when category = 2 then g.gName END) ASC, "
				."	g.cost "
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function getD_GoodsByArr($addWhere, $key){
		global $db;

		$query = " SELECT d.count, d.size, g.category, g.rollType, g.gName, g.count as gCount, g.cost, g.price, d.idx as dIdx, r.revenue "
				." FROM Delivery_Info d "
				."	LEFT JOIN Goods_Info g ON d.goodsIdx = g.idx "
				."	LEFT JOIN Revenue_Info r on r.dIdx = d.idx "
				." WHERE d.status = 1 "
				.$addWhere
				." ORDER BY g.category asc, g.rollType asc, g.cost asc, g.gName, d.count "
				;
		$row = $db->getListSet($query);

		$arr = array();
		
		for($i=0; $i<$row->size(); $i++){
			$row->next();
			
			if($key == 'th'){
				$arr[$row->get('dIdx')] = $row->get('gName')." ".$row->get('rollType')."롤 ".$row->get('count')."개";
			} else {
				$arr[$row->get('dIdx')] = $row->get('revenue');
			}
		}
		
		return $arr;
	}

	function ChangeRevenueData($cIdx){
		global $db;
		global $MENU_ID, $P_ACTION;
		
		$arr = $_POST;
		unset($arr['cIdx']);
		
		foreach($arr as $key => $value){
			$ar = explode("|", $key);
			$dIdx = $ar[2];
			$revenue = $value;

			if($revenue == "") continue;
			
			if($ar[1] == 'I'){
				$data = array(
					'cIdx'			=> $cIdx,
					'dIdx'			=> $dIdx,
					'revenue'		=> $revenue,
					'status'		=> 1,
					'regUnixtime'	=> time()
				);

				$db->insert("Revenue_Info", $data);
			} else {
				$data = array(
					'revenue'		=> $revenue
				);

				$db->update("Revenue_Info", $data, " WHERE cIdx = ".$cIdx." AND dIdx = ".$dIdx);
			}
		}	
	}

	function getRollType(){
		global $db;

		$query = " SELECT category, rollType "
				." FROM Goods_Info "
				." WHere 1 = 1 "
				." GROUP BY rollType "
				." order by category "
				;
		$row = $db->getListSet($query);
		
		$arr = array();
		$tmp = 0;
		if($row->size() >0){
			for($i=0; $i<$row->size(); $i++){
				$row->next();
				
				if($tmp != $row->get('category')){
					$arr[$row->get('category')][0] = '전체선택';
				}
				$arr[$row->get('category')][$row->get('rollType')] = $row->get('rollType');
				$tmp = $row->get('category');
			}
		} 

		return $arr;
	}
}
?>