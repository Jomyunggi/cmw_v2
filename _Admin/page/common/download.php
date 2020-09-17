<?php
/*********************************************************************
*    Description	:	File Download Page
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2013. 04. 24
*    Have a nice day, Good Luck to you ^^/
*********************************************************************/

/*** Include Config & Class File ***/
@include_once "Inc/inc.include.php";

/*******************************************************
*	BODY CODE
*******************************************************/
	$m_id = $_GET["id"];

	$fileRow = $M_FILE->getFileInfoByIdx($m_id);	
	
	if ($fileRow) {
		$filePath = FILEPATH ."/tmpData/". $fileRow->get("fileName_conv");
		$fileName = $fileRow->get("fileName_origin");
	} else {
		exit;
	}
	
	$M_FILE->download($filePath, $fileName);
?>