<?php
/*********************************************************************
*    Description	:	Class for signup
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2014. 05. 12
*    Have a nice day, Good Luck to you ^^/
*********************************************************************/
class M_SIGNUP {
	function __construct() {
	}

	function __destruct() {
	}

	function isLogin() {
		$_SESSION["U_idx"] ? $result = true : $result = false;
		return $result;
	}

	function loginPage() {
		global $M_HTML;
		@include_once ("./page/loginPage.html");
	}

	function loginCheck() {
		if (!$_SESSION["U_idx"]) {
			return false;
		}
		return true;
	}

	/*
	function loginProc() {
		global $db;
		global $M_FUNC, $M_JS;

		$userID = $M_FUNC->M_Filter(POST, "userID");
		$userPW = md5($M_FUNC->M_Filter(POST, "userPW"));
		$userID_save = $_POST["userID_save"];
		$table = "Account_Info";
		$query = " SELECT * "
				. " FROM " . $table
				. " WHERE status != 99 AND userID = '". $userID ."'";
		$account_list = $db->getListSet($query);
		
		if(is_array($userID_save) == true) {
			if(count($userID_save) > 0) {
				$userID_save = $userID_save[0];
			} else {
				$userID_save = 1;
			}
		}
		$account_list->next();

		if ($account_list->size() == 0) {
			$M_JS->alert("입력하신 \"$userID\"는 없는 아이디입니다.");
			return;
		}
		if ($account_list->get('sType')==2){
			$M_JS->alert("해당 홈페이지에서 사용할 수 없는 아이디입니다.");
		}
		else {					
			if ($account_list->get("userPW") != $userPW) {
				$M_JS->alert("비밀번호가 틀렸습니다.");
				return;
			}
			else {
				include_once COMMON_CLASS . "/class.account.php";
				$M_ACCOUNT = new M_ACCOUNT;

				$_SESSION["U_idx"]			= $account_list->get('idx');
				$_SESSION["userID"]			= $account_list->get('userID');
				$_SESSION["Level"]			= $account_list->get('level');
				$_SESSION["userName"]		= $account_list->get('userName');
				$_SESSION["uip"]			= $_SERVER["REMOTE_ADDR"];
				$_SESSION["sType"]		= $M_ACCOUNT->User_type[$account_list->get("sType")];
				if($userID_save == 1){
					$cookie_id = $userID;
					 setcookie('cookie_id',$cookie_id,time()+864000,'/'); //time()+864000 => 쿠키의 유효기간을 설정하는 부분 30일정도
				} else {
					 setcookie('cookie_id','',0,'/');
				}		
			}
		}
	}
	*/

	function adm_loginProc(){
		global $db;
		global $M_FUNC, $M_JS;

		$userID = $M_FUNC->M_Filter(POST, "userID");
		$userPW = md5($M_FUNC->M_Filter(POST, "userPW"));
		$userID_save = $_POST["userID_save"];

		$table = "Account_Info";
		$query = " SELECT * "
				. " FROM " . $table
				. " WHERE accountID = '". $userID ."'";
		$account_list = $db->getListSet($query);
		if(is_array($userID_save) == true) {
			if(count($userID_save) > 0) {
				$userID_save = $userID_save[0];
			} else {
				$userID_save = 1;
			}
		}
		$account_list->next();

		if ($account_list->size() == 0) {
			$M_JS->alert("입력하신 \"$userID\"는 없는 아이디입니다.");
			exit;
		} 
		if($account_list->get("status") == 9){
			$M_JS->alert("입력하신 \"$userID\"는 삭제 혹은 정지된 아이디입니다.");
			exit;
		}
		else {	
			if ($account_list->get("accountPW") != $userPW) {
				$M_JS->alert("비밀번호가 틀렸습니다.");
				exit;
			}
			else {
				$_SESSION["U_idx"]			= $account_list->get('idx');
				$_SESSION["accountID"]		= $account_list->get('accountID');
				$_SESSION["accountName"]	= $account_list->get('accountName');
				$_SESSION["uip"]			= $_SERVER["REMOTE_ADDR"];

				if($userID_save == 1){
					$cookie_id = $userID;
					 setcookie('cookie_id',$cookie_id,time()+864000,'/'); //time()+864000 => 쿠키의 유효기간을 설정하는 부분 30일정도
				} else {
					 setcookie('cookie_id','',0,'/');
				}
			}
		}
	}

	function logoutProc() {
		global $db;
		global $M_FUNC, $M_JS;

		/* Session */
		unset($_SESSION["U_idx"]);
		unset($_SESSION["userID"]);
		unset($_SESSION["userName"]);
		unset($_SESSION["uip"]);
	}

	function ipCheck() {
		$currentIP = $_SERVER["REMOTE_ADDR"];
		$sessionIP = $_SESSION["uip"];
		if ($currentIP != $sessionIP) {
			$this->logoutProc();
			M_JS::Go_URL("/", "", "top");
		}
	}

	function noExistPage() {
		$HTML = "<div style='margin:200px 0 0 400px; line-height:200%;'><span style='font-size:14pt; color:green;'>존재하지 않는 페이지입니다.</span><br>관리자에게 문의하세요.<br><br><u><a href=\"javascript:history.back();\">뒤로 가기</a></u></div>";
		echo $HTML;
		exit;
	}
} //*** End of Class
?>