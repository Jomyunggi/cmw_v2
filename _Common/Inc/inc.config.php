<?php
/*********************************************************************
*    Description	:	Setting headers, define variables
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2015. 09. 15
*    Have a nice day, Good Luck to you ^^/
*********************************************************************/
session_cache_expire(180);
session_start();

/***** Header Setting *****/
header("Expires: -1");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: text/html; charset=utf-8'); 
header("P3P: CP='ALL IND DSP COR ADM CONo CUR CUSo IVAo IVDo PSA PSD TAI TELo OUR SAMo CNT COM INT NAV ONL PHY PRE PUR UNI'");

/***** Define Path *****/
if($_SERVER["HTTP_HOST"] == "cmw.local.jmg"){
	define("ROOT_PATH",							"C:/Users/aodrl/Desktop/Project/CMW");
} else if($_SERVER["HTTP_HOST"] == "cmw_v2.local.jmg") {
	define("ROOT_PATH", "C:/Users/aodrl/Desktop/Project/CMW_V2");
} else {
	define("ROOT_PATH", "D:/Project/cmw");
}

define("FILEPATH",								ROOT_PATH ."/_FileData");
define("COMMON_INCLUDE",				"Inc");
define("COMMON_INCLUDE_DIR"	,		"Inc");
define("COMMON_CLASS",					"Class");

define("WWW_PAGE_DIR",				"page");
define("JS_DIR",					"/js");
define("CSS_DIR",				"/css");
define("IMG_DIR",				"/images");

define("PAGE_COMMON_DIR",					"/page/common");
define("AJAX_COMMON_DIR",					"/page/ajax/common");

define("ADMIN_ROOT_DIR",				"_Admin");
define("ADMIN_CLASS_PATH",			ROOT_PATH . "/" . ADMIN_ROOT_DIR . "/class" );
define("ADMIN_PAGE_DIR",				"page");
define("ADMIN_PAGE_PATH",			ROOT_PATH ."/". ADMIN_ROOT_DIR ."/". ADMIN_PAGE_DIR);
define("ADMIN_AJAX_PATH",			"/page/ajax");


/**********excel file location**********/
define("FILE_LOCATION", "C:\Users\aodrl\Desktop\Project\CMW\_Admin\out\\");


/********** DB **********/
if($_SERVER["HTTP_HOST"] == "cmw.local.jmg"){
define("DB_TEST_HOST",								"localhost");
define("DB_TEST_NAME",								"cmw");
define("DB_TEST_USER",								"cmw");
define("DB_TEST_PASS",								"dj1rn2wh3#");
define("DB_HOST",						"");
define("DB_NAME",						"");
define("DB_USER",						"");
define("DB_PASS",						"");
} elseif ($_SERVER["HTTP_HOST"] == "cmw_v2.local.jmg") {
define("DB_TEST_HOST",								"localhost");
define("DB_TEST_NAME",								"cmw_v2");
define("DB_TEST_USER",								"cmw");
define("DB_TEST_PASS",								"dj1rn2wh3#");
define("DB_HOST",						"");
define("DB_NAME",						"");
define("DB_USER",						"");
define("DB_PASS",						"");
}

/***** Define Table Variables *****/
define("ACCOUNT_TABLE",					"Account_Info");
define("ACCOUNTROLE_TALBE",				"AccountRole_Info");
define("BOARD_TABLE",					"Board_Info");
define("COMPANY_TABLE",					"Company_Info");
define("DEAL_TABLE",					"Deal_");
define("GOODS_TABLE",					"Goods_Info");

/***** Define Encript Key *****/
define("ENC_KEY",								"hv4rQMG#fAcElNSoPXN5z-ku.FomZ[ofdfNY?SU#k~C{");

/***** List Paging Variables *****/
define("C_LIST_CNT",							10);
define("C_LIST_CNT2",							20);
define("C_LIST_CNT25",							25);
define("C_LIST_CNT3",						30);
define("C_LIST_CNT4",						40);
define("C_LIST_CNT5",						50);
define("C_LIST_CNT10",						100);
define("C_PAGE_CNT",						10);

/***** Define ETC Variables *****/
define("CRUMB_KEY",							"chcjswodlalsdyd");
?>