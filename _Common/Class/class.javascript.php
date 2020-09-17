<?php
/*********************************************************************
*    Description	:	Class for javascript
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2010. 04. 20
*    Have a nice day, Good Luck to you ^^/
*********************************************************************/
class M_JS {

	function __construct() {	
	}

	function __destruct() {
	}
	
	function Go_URL($url = '', $msg = '', $target = '') {
		if ( $msg != '' ) echo "<script>alert('$msg');</script>";
		if ( $url != '') {
			if ($target) {
				echo "<script>". $target .".location.href='$url';</script>";
			}
			else {
				echo "<script>location.href='$url';</script>";
			}
		}
		else echo "<script>history.back(-1);</script>";
		exit;
	}

	function reload($target) {
		if ($target == "opener") {
			echo "<script>opener.location.href=opener.location.href;</script>";
		}
	}

	function replace_URL($url = '', $msg = '', $target = '') {
		if ( $msg != '' ) echo "<script>alert('$msg');</script>";
		if ($target) {
			echo "<script>". $target .".location.replace('$url');</script>";
		}
		else {
			echo "<script>location.replace('$url');</script>";
		}
	}

	function reload_URL($msg = '', $target = '') {
		if ( $msg != '' ) echo "<script>alert('$msg');</script>";
		if ($target) {
			echo "<script>". $target .".location.reload();</script>";
		}
		else {
			echo "<script>location.reload();</script>";
		}
	}

	function back($msg = '') {
		M_JS::Go_URL('', $msg);
	}

	function alert($msg = '') {
		echo "<script language='javascript'>alert('". $msg ."');</script>";
	}

	function close($msg = '') {
		echo "<script language='javascript'>";
		if ($msg) { echo "alert('". $msg ."'); "; }
		echo "self.close();</script>";
	}

	function JS_Include($file_path) {
		echo "<script language='javascript' src=". $file_path ."></script>";
	}

	function executeJS ($str) {
		echo "<script language='javascript'>". $str ."</script>";
	}

	function jsChangeBgColor($over, $out) {
		echo " onmouseover=\"this.style.backgroundColor='$over';\" onMouseOut=\"this.style.backgroundColor='$out';return true;\"";
	}

	function openPopup ($url, $popName, $width, $height, $position = 1, $etcSet = '', $useOption = "Y") {
		echo "<script language='javascript'>";
		echo "var url = '". $url ."';";
		if ($useOption == "N") {
			echo "window.open(url, '". $popName ."');";
		} else {
			if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE 8")) { $height += 20; }
			$options = "'width=". $width .", height=". $height;
			if ($position == 5) {
				echo "var left_p = (screen.width - ". $width .") / 2;";
				echo "var top_p = (screen.height - ". $height .") / 2;";
				$options .= "top='+top_p+', left='+left_p+'";
			}
			if ($etcSet) {
				$options .= ", ". $etcSet;
			}
			$options .= "'";
			
			echo "window.open(url, '". $popName ."', ". $options .");";
		}
		echo "</script>";
	}

	function setTimeout($function, $time) {
		echo "<script language='javascript'>";
		echo "setTimeout('". $function ."', ". $time .");";
		echo "</script>";
	}
}
?>