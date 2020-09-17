<?php
/*********************************************************************
*    Description	:	Include for Query String Processing
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2012. 05. 15
*    Have a nice day, Good Luck to you ^^/
*********************************************************************/
$MIN = $_SERVER["QUERY_STRING"];

if ($MIN) {
	$MIN_arr = explode("&", $MIN);
	$MENU_ID = substr($MIN_arr[0], 0, 5);
	$MENU_1DEPTH = (int)substr($MIN_arr[0], 1, 2);
	$MENU_2DEPTH = (int)substr($MIN_arr[0], 3, 2);
	$P_ACTION = substr($MIN_arr[0], 5, 1);	
}
else {
	$MENU_ID = null;
	$MENU_1DEPTH = null;
	$MENU_2DEPTH = null;
	$P_ACTION = null;
}
/***** Setting Request variables *****/
$PAGE = $M_FUNC->M_Filter(GET, "PAGE");
$searchType = $M_FUNC->M_Filter(GET, "searchType");
$searchTxt = $M_FUNC->M_Filter(GET, "searchTxt");

/***** Init Variables *****/
if (!$PAGE || $PAGE < 1) { $PAGE = 1; }
?>