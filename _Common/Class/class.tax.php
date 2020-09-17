<?php
/*********************************************************************
*    Description	:	Class for User page
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2012. 05. 15
*    Have a nice day, Good Luck to you ^^/
*********************************************************************/
class M_TAX {
	function __construct() {

	}

	function __destruct() {
		
	}

	function getTaxList($table, $addWhere, $order){
		global $db, $M_FUNC;
		global $PAGE;
		
		$query = " SELECT d.month, d.companyName, sum(d.PriceSum) as PriceSum, c.license, c.senior, c.email "
				." FROM ". $table
				." WHERE 1 = 1 "
				.$addWhere
				." GROUP BY c.idx "
				." ORDER BY ".$order
				;

		$row = $db->getListSet($query);

		return $row;
	}
}