<?php
/*********************************************************************
*    Description	:	Class for crumb
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2010. 08. 23
*    Have a nice day, Good Luck to you ^^/
*********************************************************************/
class M_CRUMB {
	function __construct() {	
	}

	function __destruct() {
	}

	function getCrumb() {
		$crumb = base64_encode(substr(CRUMB_KEY, 0, 7) . md5(uniqid(rand())) . substr(CRUMB_KEY, -8));
		return $crumb;
	}

	function checkCrumb($crumb) {
		global $M_JS;

		$d_crumb = base64_decode($crumb);
		$d_crumb_head = substr($d_crumb, 0, 7);
		$d_crumb_tail = substr($d_crumb, -8);

		if ($d_crumb_head == substr(CRUMB_KEY, 0, 7) && $d_crumb_tail == substr(CRUMB_KEY, -8)) {
			//return true;
		} else {
			$M_JS->back("잘못된 접근입니다.");
		}
	}
} //*** End of Class
?>