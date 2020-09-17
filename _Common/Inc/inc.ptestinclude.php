<?php
/*********************************************************************
*    Description	:	Include common files and Create Object
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2011. 07. 07
*    Have a nice day, Good Luck to you ^^/
*********************************************************************/
include_once realpath(dirname(__FILE__) ."/inc.ptestconfig.php");
include_once COMMON_CLASS . "/class.db.php";

include_once COMMON_CLASS . "/class.html.php";
include_once COMMON_CLASS . "/class.javascript.php";
include_once COMMON_CLASS . "/class.function.php";
include_once COMMON_CLASS . "/class.crumb.php";
include_once COMMON_CLASS . "/class.signup.php";
include_once COMMON_CLASS . "/class.file.php";

/***** Create Object ****/
$db					= new M_DB;
$M_FUNC				= new M_FUNCTION;
$M_JS				= new M_JS;
$M_SIGNUP			= new M_SIGNUP;
$M_HTML				= new M_HTML;
$M_CRUMB			= new M_CRUMB;
$M_FILE				= new M_FILE;
?>
