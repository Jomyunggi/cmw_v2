<?php
/*********************************************************************
*    Description	:	Class for common PHP functions
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2011. 07. 06
*    Have a nice day, Good Luck to you ^^/
*********************************************************************/
class M_FUNCTION {
	function __construct() {
		$this->protocol_arr = array (
			"http://"		=> "http://",
			"https://"	=> "https://",
		);
	}

	function __destruct() {
		
	}
	
	/***** Get Single URL *****/
	function getSingleURL($url) {
		$url = str_replace("http://", "", $url);
		$url = str_replace("www.", "", $url);
		$url = str_replace("[", "", $url);
		$url = str_replace("]", "", $url);
		$url_arr = explode("/", $url);
		$url = $url_arr[0];

		return $url;
	}

	function php_fn_utf8_to_array($str){
		$re_arr = array(); $re_icount = 0;
		for($i=0,$m=strlen($str);$i<$m;$i++){
			$ch = ord($str{$i});
			if($ch<128){$re_arr[$re_icount++]=substr($str,$i,1);}  
			else if($ch<224){$re_arr[$re_icount++]=substr($str,$i,2);$i+=1;}  
			else if($ch<240){$re_arr[$re_icount++]=substr($str,$i,3);$i+=2;}  
			else if($ch<248){$re_arr[$re_icount++]=substr($str,$i,4);$i+=3;}  
		}
		return $re_arr;
	}
	
	function php_fn_utf8_substr($str,$start,$length=NULL){ 
		return implode('',array_slice($this->php_fn_utf8_to_array($str),$start,$length)); 
	} 
	
	function php_fn_utf8_strlen($str){ 
		return count($this->php_fn_utf8_to_array($str)); 
	}

	function cutStr_utf8($str, $maxlen, $tail='..') { 
	  $len = $this->php_fn_utf8_strlen($str);
	  if ($len<= $maxlen || !$maxlen)   return $str;
	  $str = $this->php_fn_utf8_substr($str,0,$maxlen+$this->php_fn_utf8_strlen($tail));
	  return $str.=$tail;
	} 

	function pagingJumper($page, $totalPage, $pageURL, $URLParam = '') {
		global $MENU_ID, $P_ACTION;

		$LSP=(int)(($page - 1) / C_PAGE_CNT + 1);
		$startpage=($LSP - 1) * C_PAGE_CNT + 1;
		$endpage=$LSP * C_PAGE_CNT ;

		if ($endpage > $totalPage) $endpage=$totalPage;	

		$page_return ="";

		
		//***** 페이지 앞부분
		$link_page = $startpage - C_PAGE_CNT;
		if ($link_page < 1) { $link_page = 1; }
		$page_return = "<img src='". IMG_DIR ."/l_prev.png' align='absmiddle' style='margin-right:5px;' onClick=\"location.href='". $pageURL ."?". $MENU_ID . $P_ACTION. "&PAGE=1&". $URLParam ."';\" class=\"pointer\" />";
		$page_return .= "<img src='". IMG_DIR ."/prev.png' align='absmiddle' style='margin-right:10px;' onClick=\"location.href='". $pageURL ."?". $MENU_ID . $P_ACTION. "&PAGE=". $link_page ."&". $URLParam ."';\" class=\"pointer\"/>";


		// 페이지 번호
		for ($np=$startpage; $np<=$endpage; $np++) {
			if ($np != $page) { 
				$page_return .= "<a href='". $pageURL ."?". $MENU_ID . $P_ACTION ."&PAGE=". $np ."&". $URLParam ."' class='page_link'>$np</a>";
			} else {
				$page_return .= "<span class='page_link_sel'>$np</span>";
			}
			if ($np < $endpage) {
				$page_return .= " . ";
			}
		}

		// 페이지 뒷부분
		$link_page = $LSP * C_PAGE_CNT + 1;
		if ($link_page > $totalPage) { $link_page = $totalPage; }
		$page_return .= "<img src='". IMG_DIR ."/l_next.png' align='absmiddle' style='margin-left:10px;' onClick=\"location.href='". $pageURL ."?". $MENU_ID . $P_ACTION. "&PAGE=". $link_page ."&". $URLParam ."';\" class=\"pointer\" />";
		$page_return .= "<img src='". IMG_DIR ."/next.png' align='absmiddle' style='margin-left:5px;' onClick=\"location.href='". $pageURL ."?". $MENU_ID . $P_ACTION. "&PAGE=". $totalPage ."&". $URLParam ."';\" class=\"pointer\" />";

		return $page_return;
	}

	function pagingJumper2($page, $totalPage, $pageURL, $URLParam = '') {
		global $MENU_ID, $P_ACTION;

		$LSP=(int)(($page - 1) / C_PAGE_CNT + 1);
		$startpage=($LSP - 1) * C_PAGE_CNT + 1;
		$endpage=$LSP * C_PAGE_CNT ;

		if ($endpage > $totalPage) $endpage=$totalPage;

		$url = $pageURL . '?' . $MENU_ID . $P_ACTION . $URLParam; 

		$page_return = '';

		$page_return .= '	<table>';
		$page_return .= '		<tr>';
		
		//***** 페이지 앞부분
		$link_page = $startpage - C_PAGE_CNT;
		if ($link_page < 1) { $link_page = 1; }

		$page_return .= '			<td style="width:30%"></td>';
		$page_return .= '			<td style="width:35px;"><a href="' . $url . '&PAGE=1"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></td>';
		$page_return .= '			<td style="width:5px;"></td>';
		$page_return .= '			<td style="width:35px;"><a href="' . $url . '&PAGE=' . $link_page . '"><i class="fa fa-angle-left" aria-hidden="true"></i></a></td>';

		// 페이지 번호
		$page_return .= '			<td class="num">';
		for ($np=$startpage; $np<=$endpage; $np++) {
			if ($np != $page) { 
				$page_return .= '<span><a href="' . $url . '&PAGE=' . $np . '">' . $np . '</a></span>';
			} else {
				$page_return .= '<span class="b">' . $np . '</span>';
			}
		}
		$page_return .= '			</td>';

		// 페이지 뒷부분
		$link_page = $LSP * C_PAGE_CNT + 1;
		if ($link_page > $totalPage) { $link_page = $totalPage; }

		$page_return .= '			<td style="width:35px;"><a href="' . $url . '&PAGE=' . $link_page . '"><i class="fa fa-angle-right" aria-hidden="true"></i></a></td>';
		$page_return .= '			<td style="width:5px;"></td>';
		$page_return .= '			<td style="width:35px;"><a href="' . $url . '&PAGE=' . $totalPage . '"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></td>';
		$page_return .= '			<td style="width:30%"></td>';

		$page_return .= '		</tr>';
		$page_return .= "</table>";

		return $page_return;
	}

	function pagingJumper3($page, $totalPage, $pageURL, $URLParam = '', $margin_size="30%") {
		global $MENU_ID, $P_ACTION;

		$LSP=(int)(($page - 1) / C_PAGE_CNT + 1);
		$startpage=($LSP - 1) * C_PAGE_CNT + 1;
		$endpage=$LSP * C_PAGE_CNT ;

		if ($endpage > $totalPage) $endpage=$totalPage;

		$url = $pageURL . '?' . $MENU_ID . $P_ACTION . $URLParam; 

		$page_return = '';

		$page_return .= '	<table>';
		$page_return .= '		<tr>';
		
		//***** 페이지 앞부분
		$link_page = $startpage - C_PAGE_CNT;
		if ($link_page < 1) { $link_page = 1; }

		$page_return .= '			<td style="width:' . $margin_size . '"></td>';
		$page_return .= '			<td style="width:35px;"><a href="' . $url . '&PAGE=1"><div class="l_prev_btn"><img src="' .  IMG_DIR . '/l_prev.png"></div></a></td>';
		$page_return .= '			<td style="width:5px;"></td>';
		$page_return .= '			<td style="width:35px;"><a href="' . $url . '&PAGE=' . $link_page . '"><div class="prev_btn"><img src="' . IMG_DIR . '/prev.png"></div></a></td>';

		// 페이지 번호
		$page_return .= '			<td class="num">';
		for ($np=$startpage; $np<=$endpage; $np++) {
			if ($np != $page) { 
				$page_return .= '<span><a href="' . $url . '&PAGE=' . $np . '">' . $np . '</a></span>';
			} else {
				$page_return .= '<span class="b">' . $np . '</span>';
			}
		}
		$page_return .= '			</td>';

		// 페이지 뒷부분
		$link_page = $LSP * C_PAGE_CNT + 1;
		if ($link_page > $totalPage) { $link_page = $totalPage; }

		$page_return .= '			<td style="width:35px;"><a href="' . $url . '&PAGE=' . $link_page . '"><div class="next_btn"><img src="' .  IMG_DIR . '/next.png"></div></a></td>';
		$page_return .= '			<td style="width:5px;"></td>';
		$page_return .= '			<td style="width:35px;"><a href="' . $url . '&PAGE=' . $totalPage . '"><div class="l_next_btn"><img src="' . IMG_DIR . '/l_next.png"></div></a></td>';
		$page_return .= '			<td style="width:' . $margin_size . '"></td>';

		$page_return .= '		</tr>';
		$page_return .= "</table>";

		return $page_return;
	}

	function json_encode($data) {
		switch (gettype($data)) {
			case 'boolean':
				return $data?'true':'false';
			case 'integer':
			case 'double':
				return $data;
			case 'string':
				return '"'.strtr($data, array('\\'=>'\\\\','"'=>'\\"')).'"';
			case 'array':
				$rel = false; // relative array?
				$key = array_keys($data);
				foreach ($key as $v) {
					if (!is_int($v)) {
						$rel = true;
						break;
					}
				}
	 
				$arr = array();
				foreach ($data as $k=>$v) {
					$arr[] = ($rel?'"'.strtr($k, array('\\'=>'\\\\','"'=>'\\"')).'":':'').$this->json_encode($v);
				}
	 
				return $rel?'{'.join(',', $arr).'}':'['.join(',', $arr).']';
			default:
				return '""';
		}
	}

	function File_open($file) {
		$fp = fopen($file, "r"); 
		$i = 0;
		while (!feof($fp)){ 
			if ($i > 100) {
				fclose($fp);
			}
			$data .= fread($fp, 4096); 
			$i++;
		} 
		fclose($fp); 

		return $data;
	}

	function min_setcookie($name, $value, $expire, $path='/')
	{
		if (headers_sent()) {
			$cookie = $name.'='.urlencode($value).';';
			if ($expire) $cookie .= ' expires='.gmdate('D, d M Y H:i:s', $expire).' GMT';
			echo '<script language="javascript">document.cookie="'.$cookie.'";</script>';
		} else {
			setcookie($name, $value, $expire, $path);
		}
	}

	function getPath($MENU_1DEPTH) {
		$MENU = "M". substr("0". $MENU_1DEPTH, -2);
		return $MENU;
	}

	function encryptData($str) {
		$encrypted = base64_encode(substr(ENC_KEY, 0, 22) . base64_encode($str) . substr(ENC_KEY, -22));
		return trim($encrypted);
	}

	function decryptData($str) {
		$decrypted = base64_decode($str);
		$decrypted = str_replace(substr(ENC_KEY, 0, 22), "", $decrypted);
		$decrypted = str_replace(substr(ENC_KEY, -22), "", $decrypted);
		return trim(base64_decode($decrypted));
	}

	function M_Filter($requestType, $str, $filterType = 'NO_FILTER') {
		
		/*** PHP 5.2 이상에서만 작동 가능 ***/
		if ($filterType == "INT") {
			$filter = FILTER_VALIDATE_INT;
		} elseif ($filterType == "FLOAT") {
			$filter = FILTER_VALIDATE_FLOAT;
		} elseif ($filterType == "EMAIL") {
			$filter = FILTER_VALIDATE_EMAIL;
		} elseif ($filterType == "BOOLEAN") {
			$filter = FILTER_VALIDATE_BOOLEAN;
		} elseif ($filterType == "IP") {
			$filter = FILTER_VALIDATE_IP;
		} elseif ($filterType == "URL") {
			$filter = FILTER_VALIDATE_URL;
		} else {
			$filter = FILTER_SANITIZE_SPECIAL_CHARS;
		}
	
		
		if ($requestType == POST) {
			$data = filter_input(INPUT_POST, $str, $filter);
		} else {
			$data = filter_input(INPUT_GET, $str, $filter);
		}
		

		if (ini_get('magic_quotes_gpc') && !is_array($data)) {
			$data = stripslashes($data);
		}

		if ($filterType == "INT") {
			$data = str_replace(",", "", $data);
			$data = (int)$data;
		}
		
		if ($data) {
			return $data;
		} else {
			return null;
		}
		
		
		/*
		if ($requestType == POST) {
			$data = $_POST[$str];
		} elseif ($requestType == GET) {
			$data = $_GET[$str];
		}

		if (ini_get('magic_quotes_gpc') && !is_array($data)) {
			$data = stripslashes($data);
		}

		if ($filterType == FILTER_NUMBER) {
			$data = str_replace(",", "", $data);
			$data = (int)$data;
		}
		
		if ($data) {
			//mysql_real_escape_string 여기서 한글 깨져서 잠시 보류...
			//return mysql_real_escape_string(trim($data));
			return $data;
		} else {
			return null;
		}
		*/
	}

	function viewHTML($content) {
		$content = str_replace("&#60;", "<", $content);
		$content = str_replace("&#62;", ">", $content);
		$content = str_replace("&#34;", "\"", $content);
		$content = str_replace("&#38;", "&", $content);
		$content = str_replace("&#39;", "'", $content);
		$content = str_replace("&nbsp;", " ", $content);
		$content = str_replace("&#13;&#10;", "<br />", $content);
		return $content;
	}

	function send_SMS($from, $to, $msg) {
		if ($from == "") {
			$from = "01035972794";
		}
		$smsURL = "http://sms.emato.net/sms_db_insert_linux_utf8.php";
		
		if (is_array($to)) {
			foreach($to as $key => $toNumber) {
				$param = "?User_Name=emato&Password=dnflsms&To_Phone_Num_List=". $toNumber ."&Msg=". urlencode($msg) ."&Server_IP=58.120.226.5&From_Phone_Num=". $from ."&Return_URL=NO_RETURN";
				$url = $smsURL . $param;
				$fp = fopen($url, 'r');
				fclose($fp);
			}
		} else {
			$param = "?User_Name=emato&Password=dnflsms&To_Phone_Num_List=". $to ."&Msg=". urlencode($msg) ."&Server_IP=58.120.226.5&From_Phone_Num=". $from ."&Return_URL=NO_RETURN";
			$url = $smsURL . $param;
			$fp = fopen($url, 'r');
			fclose($fp);
		}								
	}

	function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {  
		/*  
		$interval can be:  
		yyyy - Number of full years  
		q - Number of full quarters  
		m - Number of full months  
		y - Difference between day numbers  
		(eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)  
		d - Number of full days  
		w - Number of full weekdays  
		ww - Number of full weeks  
		h - Number of full hours  
		n - Number of full minutes  
		s - Number of full seconds (default)  
		*/  

		/*
			Usage : echo datediff('w', '9 July 2003', '4 March 2004', false);
		*/
 
		if (!$using_timestamps) {  
			$datefrom = strtotime($datefrom, 0);  
			$dateto = strtotime($dateto, 0);  
		}  
		$difference = $dateto - $datefrom; // Difference in seconds  
		 
		switch($interval) {  
			case 'yyyy': // Number of full years  
				$years_difference = floor($difference / 31536000);  
				if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {  
					$years_difference--;  
				}  
				if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {  
					$years_difference++;  
				}  
				$datediff = $years_difference;  
				break;  
			   
			case "q": // Number of full quarters  
				$quarters_difference = floor($difference / 8035200);  
				while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {  
					$quarters_difference++;  
				}  
				$quarters_difference--;  
				$datediff = $quarters_difference;  
				break;  
			   
			case "m": // Number of full months  
				$months_difference = floor($difference / 2678400);  
				while (mktime((int)date("H", $datefrom), (int)date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {  
					$months_difference++;  
				}  
				$months_difference--;  
				$datediff = $months_difference;  
				break;  
			   
			case 'y': // Difference between day numbers  
				$datediff = date("z", $dateto) - date("z", $datefrom);  
				break;  
			   
			case "d": // Number of full days  
				$datediff = floor($difference / 86400);  
				break;  
			   
			case "w": // Number of full weekdays  
			   
				$days_difference = floor($difference / 86400);  
				$weeks_difference = floor($days_difference / 7); // Complete weeks  
				$first_day = date("w", $datefrom);  
				$days_remainder = floor($days_difference % 7);  
				$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?  
				if ($odd_days > 7) { // Sunday  
					$days_remainder--;  
				}  
				if ($odd_days > 6) { // Saturday  
					$days_remainder--;  
				}  
				$datediff = ($weeks_difference * 5) + $days_remainder;  
				break;  
			
			case "ww": // Number of full weeks  
				$datediff = floor($difference / 604800);  
				break;  
			   
			case "h": // Number of full hours  
				$datediff = floor($difference / 3600);  
				break;  
			   
			case "n": // Number of full minutes  
				$datediff = floor($difference / 60);  
				break;  
			   
			default: // Number of full seconds (default)   
				$datediff = $difference;  
				break;  
		}  
		return $datediff;  
	} 

	function replaceChr($str) {
		$str = str_replace("&#38;", "&", $str);
		return $str;
	}
	
	function random_number($length, $type) { 
			switch($type){ 
			case 0: 
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890'; 
			break; 
			case 1: 
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'; 
			break; 
			case 2: 
			$chars = 'abcdefghijklmnopqrstuvwxyz1234567890'; 
			break; 
			case 3: 
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
			break; 
			case 4: 
			$chars = 'abcdefghijklmnopqrstuvwxyz'; 
			break; 
			case 5: 
			$chars = '1234567890'; 
			break; 
			default: 
			return false; 
		} 
			$chars_length = (strlen($chars) - 1); 
			$string = ''; 
			for ($i = 0; $i < $length; $i = strlen($string)){ 
			$string .= $chars{rand(0, $chars_length)}; 
		} 
		return $string; 
	} 

	function isNull($object) {
		if( $object === '' || $object === null) {
			return true;
		} else {
			return false;
		}
	}

	function get_random_string($type = null, $len = 10) {
		$lowercase	= "abcdefghijklmnopqrstuvwxyz";
		$uppercase	= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$numeric		= "0123456789";
		$special		= "`~!@#$%^&*()-_=+\\|[{]};:'\",<.>/?";

		$key = "";
		$token = "";

		if($type == null) {
			$key = $lowercase.$uppercase.$numeric;
		} else {
			if(strpos($type, '09') > -1 ) $key .= $numeric;
			if(strpos($type, 'az') > -1 ) $key .= $lowercase;
			if(strpos($type, 'AZ') > -1 ) $key .= $uppercase;
			if(strpos($type, '$') > -1 )	$key .= $special;
		}

		for($i=0;$i<$len;$i++) {
			$token .= $key[mt_rand(0, strlen($key) -1)];
		}

		return $token;
	}
	
	//정상적이지 않은 방법으로 접근한 경우를 위한 dummy함수(FRONT에서 페이지 생성 시 미리 정의)
	function dummy() {
	}

	function recordActionLog($action, $MENU_ID, $m_id = 0, $memo = '') {
		global $db;

		switch($action){
			case 'P' : $action = 1; break;
			case 'U' : $action = 2; break;
			case 'D' : $action = 3; break;
			case 'S' : $action = 4; break;
			case 'L' : $action = 9; break;
			case 'E' : $action = 10; break;
			case 'P2' : $action = 11; break;
			case 'D2' : $action = 13; break;
			default : $action = 4; break;
		}

		$pType = 2;
		if ($_SESSION['Level'] == 1) {
			$pType = 1;
		}

		$data = array (
			'accountID'		=> $_SESSION["accountID"],
			'action'		=> $action,
			'MENU_ID'		=> $MENU_ID,
			'm_id'			=> $m_id === "" ? 0 : $m_id,
			'user_ip'		=> $_SERVER["REMOTE_ADDR"],
			'regUnixtime'	=> time(),
			'memo'			=> $memo,
			'pType'			=> $pType
		);
		$db->insert(ADMINHISTORY_TABLE, $data);
	}
} //** End of Class
?>
