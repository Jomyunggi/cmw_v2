<?php
/*********************************************************************
*    Description	:	Setting headers, define variables
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2015. 09. 15
*    Have a nice day, Good Luck to you ^^/
*********************************************************************/
session_start();

/***** Header Setting *****/
header("Expires: -1");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: text/html; charset=utf-8'); 
header("P3P: CP='ALL IND DSP COR ADM CONo CUR CUSo IVAo IVDo PSA PSD TAI TELo OUR SAMo CNT COM INT NAV ONL PHY PRE PUR UNI'");

/***** Define Site Info. *****/
define("SITE_TITLE",							"ONDEMAND");
define("ADMIN_DOMAIN",					"http://gamey.sand.wich.kr");
define("IMG_DOMAIN",						"http://img.sandwich.com");
define("FILE_DOMAIN",						"http://gamey.file.wich.kr");
//define("AD_DOMAIN",						"http://sand.sandwich.com");
define("DEV_DOMAIN",						"http://dev.sandwich.com");

/***** Define Path *****/
define("ROOT_PATH",							"/home/ondemand");
define("FILEPATH",								ROOT_PATH ."/_FileData");
define("XML_DIR",								"/home/ondemand/_www_emato/xml");

define("COMMON_INCLUDE",				"Inc");
define("COMMON_INCLUDE_DIR"	,		"Inc");
define("COMMON_CLASS",					"Class");

define("WWW_PAGE_DIR",				"page");
define("JS_DIR",					"/js");
define("CSS_DIR",				"/css");
define("IMG_DIR",				"/images");

define("PAGE_COMMON_DIR",					"/page/common");
define("AJAX_COMMON_DIR",					"/page/ajax/common");

define("WWW_ROOT_DIR",				"_www_emato");
define("WWW_CLASS_DIR",			WWW_ROOT_DIR . "/Class" );
define("WWW_PAGE_PATH",			ROOT_PATH ."/". WWW_ROOT_DIR ."/". WWW_PAGE_DIR);

define("SKB_ROOT_DIR", "_Ondemand_SKB");
define("SKB_CLASS_DIR", SKB_ROOT_DIR . "/Class");
define("SKB_PAGE_DIR", "page");
define("SKB_PAGE_PATH", ROOT_PATH ."/". SKB_ROOT_DIR ."/". SKB_PAGE_DIR);

define("ADMIN_ROOT_DIR",				"_Admin");
define("ADMIN_CLASS_PATH",			ROOT_PATH . "/" . ADMIN_ROOT_DIR . "/class" );
define("ADMIN_PAGE_DIR",				"page");
define("ADMIN_PAGE_PATH",			ROOT_PATH ."/". ADMIN_ROOT_DIR ."/". ADMIN_PAGE_DIR);
define("ADMIN_AJAX_PATH",			"/page/ajax");


/********** DB **********/
define("DB_HOST",								"localhost");
define("DB_NAME",								"notice");
define("DB_USER",								"ondemand_emato");
define("DB_PASS",								"dhselapsemWkd!!!");

/***** Define Table Variables *****/
define("USER_TABLE",					"User_Info");
define("ADMIN_TABLE",					"Admin_History");
define("FAQ_TABLE",						"FAQ_Info");
define("FILE_TABLE",					"File_Data");
define("NOTICE_TABLE",					"Notice_Info");
define("PRODUCT_TABLE",					"Product_Info");
define("QNA_TABLE",						"QNA_Info");
define("SERVICE_COMMENT_TABLE",					"Service_Comment_Info");
define("SERVICE_TABLE",					"Service_Info");
define("COMPANY_TABLE",					"Company_Info");

/***** Define Encript Key *****/
define("ENC_KEY",								"hv4rQMG#fAcElNSoPXN5z-ku.FomZ[ofdfNY?SU#k~C{");

/***** List Paging Variables *****/
define("C_LIST_CNT",							20);
define("C_LIST_CNT2",						10);
define("C_LIST_CNT3",						5);
define("C_PAGE_CNT",						10);

/***** Define ETC Variables *****/
define("CRUMB_KEY",							"chcjswodlalsdyd");

/***** 결제모듈 Variables *****/
define("CST_MID", "sandwich");
define("LGD_MERTKEY", "d980adeb305a5079c2a53973cac1ec5d");

/***** 코드값 *****/
define("공지사항코드", "Notice_code");
define("FAQ코드", "FAQ_code");
define("1대1문의코드", "QNA_code");
define("기업정보코드", "COMPANY_code");
define("서비스코드", "SERVICE_code");
define("유저코드", "USER_code");
define("관라자코드", "ADMIN_code");

define("SKB_공지사항코드", "NoticeSKB_code");
define("SKB_FAQ코드", "FAQSKB_code");
define("SKB_1대1문의코드", "QNASKB_code");
define("SKB_기업정보코드", "COMPANYSKB_code");
define("SKB_서비스코드", "SERVICESKB_code");
define("SKB_유저코드", "USERSKB_code");
define("SKB_관라자코드", "ADMINSKB_code");

$Level_Info = array (
	9 => "관리자",
	1 => "일반",
);
?>