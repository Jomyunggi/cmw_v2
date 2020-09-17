<?php
	/*********************************************************************
	*    Description	:	Class for User page
	*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
	*    Date			:	2012. 05. 15
	*    Have a nice day, Good Luck to you ^^/
	*********************************************************************/
	class M_UNIT {
		function __construct() {
					
		}

		function __destruct() {
			
		}

		function lpad($str, $len, $padstr){
			$tmpStr = '';
			for($i=0; $i<$len; $i++){
				$tmpStr .= $padstr;
			}

			return substr($tmpStr.$str, -$len);
		}
		
		function getGoodsList(&$total_page, &$NO, &$cnt_no, $addWhere = null, $order = null, $limit = null){
			global $db, $M_FUNC;
			global $PAGE;
			
			if($PAGE < 1) $PAGE = 1 ;
			$start_row = ($PAGE - 1) * C_LIST_CNT5;
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
				$limitQuery = " LIMIT ". $start_row .", ". C_LIST_CNT5;
			}

			$query = " SELECT g.*, c.code "
						. " FROM Goods_Info g "
						." INNER JOIN Company_Info c on g.companyIdx = c.idx "
						. $where
						. $orderQuery
						. $limitQuery;
			$row = $db->getListSet($query);

			$cnt_no = $db->cnt("Goods_Info g", $where);
			$total_page = (int)(($cnt_no - 1) / C_LIST_CNT5) + 1;
			$NO = $cnt_no - $start_row;
			
			return $row;		
		}

		function getGoodsInfo($addWhere){
			global $db;

			$query = " select * "
					." from Goods_Info "
					." where 1 = 1 "
					.$addWhere;
			$row = $db->getListSet($query);
			$row->next();

			return $row;
		}

		function getCompanyArr($companyArr, $addWhere){
			global $db;
			
			if(is_array($companyArr)){
				$arr = $companyArr;
			} else {
				$arr = array();
			}

			$query = " select idx, companyName "
					." from Company_Info "
					." where status = 1 "
					.$addWhere
					." order by idx asc ";
			$row = $db->getListSet($query);
			

			for($i=0; $i<$row->size(); $i++){
				$row->next();

				$arr[$row->get('idx')] = $row->get('companyName');
			}

			return $arr;
		}

		function InsertGoodsData(){
			global $db, $M_FUNC, $M_CRUMB, $M_JS, $_SESSION, $M_FILE, $M_MEMBER;
			global $MENU_ID, $SearchName;
			
			//goods 거래처별 상품가격 등록
			$level = $M_FUNC->M_Filter(POST, "level");
			if($level != 1){
				$message = "잘못된 경로로 들어온 경우입니다.\\n다시 등록하시기 바랍니다.";
				$url = "/?M0201L";
				$M_JS->Go_URL($url, $message, "parent");
				exit;
			}

			//거래처 배열 가져오기
			$companyArr = $this->getCompanyArr(array()," and level = 11 ");
			$companyIdx = $M_FUNC->M_Filter(POST, "companyIdx");
			$companyName = $companyArr[$companyIdx];
			
			//상품별 단가 가져오기
			$goodsPrice = "";
			$goodsMax = sizeof($this->goodsNum_Arr);
			for($i=0; $i<$goodsMax; $i++){
				$price = $M_FUNC->M_Filter(POST, $this->goodsNum_Arr[$i]['num']);
				$goodsPrice .= $price == "" ? 0 : $price;
				if($i != $goodsMax-1) $goodsPrice .= "|";
			}
			
			$data = array(
				'companyIdx'	=>	$companyIdx,
				'companyName'	=>	$companyName,
				'goodsPrice'	=>	$goodsPrice,
				'status'		=>	1,
				'regUnixtime'	=>	time()
			);

			$db->insert("Goods_Info", $data);		
		}

		function UpdateGoodsData($companyIdx, $data) {
			global $db;

			if($companyIdx == "" || $companyIdx == 0) {
				return;
			}

			$where = " WHERE companyIdx=" . $companyIdx;
			$db->update("Goods_Info", $data, $where);
		}
	} //*** End of Class
?>