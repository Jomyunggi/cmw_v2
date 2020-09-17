<?php
/*********************************************************************
*    Description	:	Processing Login & Logout
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2011. 07. 08
*    Have a nice day, Good Luck to you ^^/
*********************************************************************/
@include_once realpath(dirname(__FILE__) . "/inc.include.php");

$ACTION = $_POST["action"];

if (!$ACTION) { $ACTION = $_GET["action"]; }

if (!$ACTION) { $M_JS->back("잘못된 요청입니다."); }

if ($ACTION == "login") {
		$M_SIGNUP->adm_loginProc();
		$M_JS->Go_URL("/", "", "parent");
}
elseif ($ACTION == "logout") {
	$M_SIGNUP->logoutProc();
	$M_JS->Go_URL("/", "", "");
}
?>