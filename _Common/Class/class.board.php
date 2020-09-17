<?php

class M_BOARD {
	// Constructor

	function __construct() {

	}

	// Destructor
	function __destruct() {

	}

	function InsertBoardData(){
		global $M_FUNC;
		global $db;
		
		$itemLength1	= $M_FUNC->M_Filter(POST, "itemLength1");
		$itemLength2	= $M_FUNC->M_Filter(POST, "itemLength2");
		$subject		= $M_FUNC->M_Filter(POST, 'subject');
		$content		= $M_FUNC->M_Filter(POST, "content");

		if($itemLength2 == 0){
			$item = (int)$itemLength1."0";
		} else {
			$item = (int)$itemLength2;
		}
		
		$data = array(
			'item'					=> $item,
			'subject'				=> $subject,
			'content'				=> $content,
			'type'					=> 1,
			'accountIdx'			=> $_SESSION['U_idx'],
			'accountName'			=> $_SESSION['accountName'],
			'status'				=> 1,
			'regUnixtime'			=> time(),
		);

		$db->insert("Board_Info", $data);

	}

	function getBoardList(&$total_page, &$NO, &$cnt_no, $addWhere = null, $order = null, $limit = null){
		global $db;
		global $PAGE;
		
		if($PAGE < 1) $PAGE = 1 ;
		$start_row = ($PAGE - 1) * C_LIST_CNT2;
		$where = " WHERE 1 = 1 AND status <> 9 ";
		
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
			$limitQuery = " LIMIT ". $start_row .", ". C_LIST_CNT2;
		}
		
		$query = " SELECT idx, item, subject, content, regUnixtime, editUnixtime, accountIdx, status, edate "
					. " FROM Board_Info "
					. $where
					. $orderQuery
					. $limitQuery;
		$row = $db->getListSet($query);

		$cnt_no = $db->cnt("Board_Info", $where);
		$total_page = (int)(($cnt_no - 1) / C_LIST_CNT2) + 1;
		$NO = $cnt_no - $start_row;
		
		return $row;
	}

	function getBoardByIdx($m_id){
		global $db;

		$query = " SELECT * "
					." FROM Board_Info "
					." WHERE idx = ". $m_id;
		$row = $db->getListSet($query);

		return $row;
	}

	function UpdateBoardData(){
		global $db;
		global $M_FUNC;

		$m_id = $M_FUNC->M_Filter(POST, "m_id");
		$subject = $M_FUNC->M_Filter(POST, 'subject');
		$content = $M_FUNC->M_Filter(POST, 'content');
		$status = $M_FUNC->M_Filter(POST, 'status');

		$data = array(
			'subject'	=> $subject,
			'content'	=> $content,
			'status'	=> $status,
		);

		if($status == 22){
			$data['edate'] = time();
		} else {
			$data['edate'] = 0;
		}

		$db->update("Board_Info", $data, "Where idx = ". $m_id);
	}

	function DeleteBoardData(){
		global $db;
		global $M_FUNC;

		$m_id = $M_FUNC->M_Filter(GET, "m_id");
		
		$data = array(
			'status'	=> 9
		);

		$db->update("Request_Info", $data, "Where idx = ". $m_id);
	}

	function InsertNoticeData(){
		global $M_FUNC;
		global $db;
		
		$subject		= $M_FUNC->M_Filter(POST, 'subject');
		$content		= $M_FUNC->M_Filter(POST, "content");
		
		$data = array(
			'item'					=> 0,
			'subject'				=> $subject,
			'content'				=> $content,
			'type'					=> 3,
			'accountIdx'			=> $_SESSION['U_idx'],
			'accountName'			=> $_SESSION['accountName'],
			'status'				=> 1,
			'regUnixtime'			=> time(),
		);

		$db->insert("Board_Info", $data);

	}
}
?>