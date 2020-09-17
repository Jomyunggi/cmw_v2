<?php
class M_AD {
	function __construct() {
		$this->User_search = array(
			'1' => '아이디',
			'2' => '이름',
			'3' => '관리자분류',
		);
		$this->User_level = array(
			'1' => '관리자',
			'11' => '판매처',
			'21' => '매입처',
			'31' => '인터넷',
			'41' => '택배사'
		);
		$this->DEAL_search = array(
			'0'	=> '검색분류선택',
			'1' => '거래처명'
		);
		$this->Month_array = array(
			1 => '1월',
			2 => '2월',
			3 => '3월',
			4 => '4월',
			5 => '5월',
			6 => '6월',
			7 => '7월',
			8 => '8월',
			9 => '9월',
			10 => '10월',
			11 => '11월',
			12 => '12월',
		);
	}

	function __destruct() {
		
	}

	function getCompanyArr($addWhere){
		global $db;

		$query = " SELECT idx, companyName "
				." FROM Company_Info "
				." WHERE 1 = 1 AND status = 1 "
				.$addWhere
				." ORDER BY CompanyName asc "
				;
		$row = $db->getListSet($query);
		
		$arr = array(0 => '거래처 선택');
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			$arr[$row->get('idx')]	= $row->get('companyName');
		}

		return $arr;
	}

	function getAnalyAD($year, $addWhere){
		global $db;

		$query = " SELECT left(time, 8) as day, right(time, 4) as time, deal, dealNum "
				." FROM ". DEAL_TABLE .$year
				." WHERE 1=1 "
				.$addWhere
				." ORDER BY time asc "
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function getSalesRate($year, $addWhere){
		global $db;

		$query = " SELECT date, time, companyIdx, companyName, onoff, deal, dealNum, priceKinds "
				." FROM ". DEAL_TABLE .$year
				." WHERE status = 1 "
				.$addWhere
				;
		$row = $db->getListSet($query);

		return $row;
	}

	function getCostByCompany(){
		global $db;

		$query = " SELECT * "
				." FROM goods_Info "
				." WHERE status = 1 "
				;
		$row = $db->getListSet($query);
		
		$arr = array();
		if($row->size() > 0){
			for($i=0; $i<$row->size(); $i++){
				$row->next();

				$cost = explode("|", $row->get('goodsPrice'));

				for($k=0; $k<count($cost); $k++){
					$arr[$row->get('companyIdx')][$k] = $cost[$k];
				}
			}
		}

		return $arr;
	}

	function modifyData($row){
		global $db;

		$totalNum = 0;
		$paperNum = array();
		for($i=0; $i<$row->size(); $i++){
			$row->next();

			$goodsPriceArr = explode('|', $row->get('deal'));
			$goodsNumArr = explode('|', $row->get('dealNum'));
			for($j=0; $j<sizeof($goodsPriceArr); $j++){

				//if($j == 3 || $j == 7 || $j == 10 || $j == 17 || $j == 18) continue;

				//업체별 단가별로 판매량
				if($goodsPriceArr[$j] > 0){
					$goodPrice = (int)$goodsPriceArr[$j]/$goodsNumArr[$j];
					//각 상품별 수량합계
					if($row->get('companyIdx') == 28){
						//약장수는 단품가격으로 계산으로 나가기때문에
						if($j == 4 || $j == 9){
							$goodPrice= (int)$goodsPriceArr[$j]/($goodsNumArr[$j]/3);
							$paperNum[$j][$goodPrice] += $goodsNumArr[$j]/3;
						}
					} elseif($row->get('companyIdx') == 51){
						$paperNum[$j][$goodPrice] += (float)$goodsNumArr[$j];
					} else	$paperNum[$j][$goodPrice] += (int)$goodsNumArr[$j];
				}
			}
		}

		ksort($paperNum);
		return $paperNum;
	}

	function modifyData2($row){
		global $db;

		$arr = array();
		if($row->size() > 0){
			for($i=0; $i<$row->size(); $i++){
				$row->next();

				$deal = explode("|", $row->get('deal'));
				$num = explode("|", $row->get('dealNum'));
				$count = 0;

				for($k=0; $k<count($deal); $k++){
					if($row->get('companyIdx') == 51){
						if($k == 1)	continue;		//온라인판매처는 배송비를 떙큐로 정리하기때문에
						if($k == 4 || $k == 5 || $k == 6 || $k == 7 || $k == 8 || $k == 9) $count = $num[$k]/3;
					} else {
						$count = $num[$k];
					}

					$arr[$k]['name']	= $this->goodsName[$k];
					$arr[$k]['cost']	= $this->goodsCost[$k];
					$arr[$k]['num']		+= $count;
					$arr[$k]['totalP']	+= $deal[$k];
					$arr[$k]['costT']	+= $this->goodsCost[$k]*$count;
					$arr[$k]['diff']	+= $deal[$k] - ($this->goodsCost[$k]*$count);
				}
			}
		} 	

		return $arr;
	}
}
?>