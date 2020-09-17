<?php
	@include_once "../../common/setCommonPath.php";
	@include_once "Inc/inc.include.php";

	@include_once COMMON_CLASS . "/class.account.php";
	$M_ACCOUNT = new M_ACCOUNT;
	
	$mode =  $_REQUEST['mode'];
	$pageCnt =  $M_FUNC->M_Filter(GET, 'pageCnt');
	$page =  $M_FUNC->M_Filter(GET, 'page');
	$list_cnt =  $M_FUNC->M_Filter(GET, 'list_cnt');
	$level =  $M_FUNC->M_Filter(GET, 'level');
	$searchType = $M_FUNC->M_Filter(GET, 'searchType');
	$searchTxt = $M_FUNC->M_Filter(GET, 'searchTxt');
	$sType = $_REQUEST['sType'];
	
	if($mode == "member_list") {

		if($page < 1) $page = 1 ;
		if($list_cnt < 1) $list_cnt = 10 ;

		$start_row = ($page - 1) * $list_cnt;
		$addOrder = " ORDER BY level asc ";
		$addWhere = " AND status <> 99 ";
		
		if($level > 0){
			$addWhere .= " and level = ".$level;
		}

		if($_SESSION['Level'] != 1 && $_SESSION['Level'] < 8){
			$where = "";
			if($_SESSION['Level'] == 2){
				$where .= " AND businessIdx = ".$_SESSION['businessIdx'];
			} elseif($_SESSION['Level'] == 4){
				$where .= " AND businessIdx = ".$_SESSION['businessIdx']." AND companyIdx = ".$_SESSION['companyIdx'];
			}

			$roleRow = $M_ACCOUNT->getAccountByRoleIdx($where);
			
			if($roleRow->size() > 0){
				$addWhere .= " and idx in ( ";
				for($i=0; $i<$roleRow->size(); $i++){
					$roleRow->next();
					
					$addWhere .= $roleRow->get('accountIdx');
					if($i < $roleRow->size() -1){
						$addWhere .= ", ";
					}
				}
				$addWhere .= " ) ";
			}
		}

		if($searchTxt != '') {
			switch($searchType) {
				case '1' : $addWhere .= ' AND accountID LIKE \'%' . $searchTxt . '%\' '; break;
				case '2' : $addWhere .= ' AND accountName LIKE \'%' . $searchTxt . '%\' '; break;
				default	 : $addWhere .= ' AND ( accountID LIKE \'%' . $searchTxt . '%\' OR accountName LIKE \'%' . $searchTxt . '%\' ) '; break;
			}
		}
		
		$addLimit = " LIMIT " . $start_row .", ". $list_cnt;
		
		$result = array();
		$company_arr = array();
		$row = $M_ACCOUNT->getAccountList($addWhere, $addOrder, $addLimit);
		$totalRow = $M_ACCOUNT->getAccountList($addWhere);

		if($row->size()>0){
			
			for($i=0;$i<$row->size();$i++){
				$row->next();	
				$no = $totalRow->size() - $i - (($page - 1) * $list_cnt);

				$regUnixtime = date('Y-m-d H:i:s',$row->get("regUnixtime"));
				
				$company_arr[$row->get("companyIdx")] = $M_ACCOUNT->getCompanyInfoByIdx($row->get("companyIdx"), "companyName");

				if($company_arr[$row->get("companyIdx")] == null || $company_arr[$row->get("companyIdx")] == ""){
					$company_arr[$row->get("companyIdx")] = "-";
				}

				$num = ($i + 1) + ($list_cnt * ($page-1));
				
				$result[$no]['num'] = $num;
				$result[$no]['idx'] = $row->get('idx');
				$result[$no]['id'] = $row->get('accountID');
				$result[$no]['name'] = $row->get('accountName');
				$result[$no]['companyName'] = $company_arr[$row->get("companyIdx")];
				$result[$no]['date'] = $regUnixtime;
				$result[$no]['total_page'] = $totalRow->size()/$list_cnt;
				$result[$no]['level']	= $M_ACCOUNT->User_level[$row->get('level')];
			}
			
		}
		echo json_encode($result);

	}else if($mode == 'member_delete') {

		$idx_arr = $_POST['idx'];
		$MENU_ID = $_POST['MENU_ID'];

		if(is_array($idx_arr)) {
			$addWhere = ' AND idx IN (' . implode(', ', $idx_arr) . ')';
			$M_ACCOUNT->deleteAccountDataList($addWhere);
		} else {
			$addWhere2 = " AND status <> 99 AND account_idx = " . $idx_arr;
			$getAccountMatchingInfoCount = $M_ACCOUNT->getAccountMatchingInfoCount($addWhere2);
			if($getAccountMatchingInfoCount > 0){
				echo "fail";exit;
			}
			$M_ACCOUNT->deleteAccountDataByIdx($idx_arr);
		}

		$accountRow = $M_ACCOUNT->getAccountList(' AND idx IN (' . implode(', ', $idx_arr) . ')', "", "");
		$accountArr = '';
		for($i=0; $i<$accountRow->size(); $i++){
			$accountRow->next();
			
			$accountArr .= $accountRow->get('accountName')."(".$accountRow->get('idx').")";
			if($i != ($accountRow->size() -1)) $accountArr .= ", ";
		}

		$M_FUNC->recordActionLog('D', $MENU_ID, '', "[".$accountArr."] 계정 삭제");

		echo "success";
	}else if($mode == "member_setion5_insert"){
			$idx		= $_POST["idx"];
			$companyName		= isset($_POST["companyName"]) ? $_POST["companyName"] : "";
			$tel1				= isset($_POST["tel1"]) ? $_POST["tel1"] : "";
			$tel2				= isset($_POST["tel2"]) ? $_POST["tel2"] : "";
			$tel3				= isset($_POST["tel3"]) ? $_POST["tel3"] : "";
			$businessNo1			= isset($_POST["businessNo1"]) ? $_POST["businessNo1"] : "";
			$businessNo2			= isset($_POST["businessNo2"]) ? $_POST["businessNo2"] : "";
			$businessNo3			= isset($_POST["businessNo3"]) ? $_POST["businessNo3"] : "";
			$firmName			= isset($_POST["firmName"]) ? $_POST["firmName"] : "";
			$presidentName		= isset($_POST["presidentName"]) ? $_POST["presidentName"] : "";
			$address			= isset($_POST["address"]) ? $_POST["address"] : "";
			$businessCondition	= isset($_POST["businessCondition"]) ? $_POST["businessCondition"] : "";
			$service			= isset($_POST["service"]) ? $_POST["service"] : "";
			$email				= isset($_POST["email"]) ? $_POST["email"] : "";
			$accountHolder		= isset($_POST["accountHolder"]) ? $_POST["accountHolder"] : "";
			$accountBank		= isset($_POST["accountBank"]) ? $_POST["accountBank"] : "";
			$accountNumber		= isset($_POST["accountNumber"]) ? $_POST["accountNumber"] : "";
			$companyID			= isset($_POST["companyID"]) ? $_POST["companyID"] : 0;
			$fileNameList		= isset($_POST["fileNameList"]) ? $_POST["fileNameList"] : "";
			$filePathList		= isset($_POST["filePathList"]) ? $_POST["filePathList"] : "";

			
			$tel = $tel1.$tel2.$tel3;
			$businessNo = $businessNo1.$businessNo2.$businessNo3;

			$data = array(
				'UI_idx'					=> $idx,
				'companyName'		=> $companyName,
				'tel'						=> $tel,
				'businessNo'			=> $businessNo,
				'firmName'				=> $firmName,
				'presidentName'		=> $presidentName,
				'address'				=> $address,
				'businessCondition'	=> $businessCondition,
				'service'					=> $service,
				'email'					=> $email,
				'accountHolder'		=> $accountHolder,
				'accountBank'			=> $accountBank,
				'accountNumber'		=> $accountNumber,
			);
			
			$addWhere = " AND idx = ".$idx." ";
			$account_row=$M_ACCOUNT->getUserInfo($addWhere,"","");
			$account_row->next();

			if($account_row->get('sType')==1){
				$MENU_ID=COMPANY_code;
			}else{
				$MENU_ID=COMPANYSKB_code;
			}

			if($companyID == 0){
				$companyID = $M_ACCOUNT->insertCompanyInfoByData($data);
				$M_ACCOUNT->insertLog($_SESSION['U_idx'], $pageURL, $companyID . ' 번 기업정보 등록 ', $companyID,$MENU_ID);
			}else{
				$data['idx'] = $companyID;
				$M_ACCOUNT->updateCompanyInfoByData($data); 
				$M_ACCOUNT->insertLog($_SESSION['U_idx'], $pageURL, $companyID . ' 번 기업정보 수정 ', $companyID,$MENU_ID);
			}

			
			$M_FILE->insertFileArrData($MENU_ID, $companyID, "", $fileNameList, $filePathList);

			echo "success";
	}
	else if($mode == "member_setion5_view"){
			$idx		= $_POST["idx"];
			
			$companyRowView = $M_ACCOUNT->getCompanyInfoByUser($idx);
			
			if($companyRowView->get("tel") == "" || $companyRowView->get("tel") == 0){
				$tel1 = "";
				$tel2 = "";
				$tel3 = "";
			} else {
				$tel1 = substr($companyRowView->get("tel"),0,3);
				$tel2 = substr($companyRowView->get("tel"),3,4);
				$tel3 = substr($companyRowView->get("tel"),7,4);
			}
			
			if($companyRowView->get("businessNo") == "" || $companyRowView->get("businessNo") == 0){
				$businessNo1 = "";
				$businessNo2 = "";
				$businessNo3 = "";
			} else {
				$businessNo1 = substr($companyRowView->get("businessNo"),0,3);
				$businessNo2 = substr($companyRowView->get("businessNo"),3,2);
				$businessNo3 = substr($companyRowView->get("businessNo"),5,6);	
			}
		
			$file_idx = "";
			$fileName_origin = "";

			if($companyRowView->size() > 0){
				$resultView["companyName"]	=	$companyRowView->get("companyName");
				$resultView["tel1"]	=	$tel1;
				$resultView["tel2"]	=	$tel2;
				$resultView["tel3"]	=	$tel3;
				$resultView["businessNo1"]	=	$businessNo1;
				$resultView["businessNo2"]	=	$businessNo2;
				$resultView["businessNo3"]	=	$businessNo3;
				$resultView["firmName"]	=	$companyRowView->get("firmName");
				$resultView["presidentName"]	=	$companyRowView->get("presidentName");
				$resultView["address"]	=	$companyRowView->get("address");
				$resultView["businessCondition"]	=	$companyRowView->get("businessCondition");
				$resultView["service"]	=	$companyRowView->get("service");
				$resultView["email"]	=	$companyRowView->get("email");
				$resultView["accountHolder"]	=	$companyRowView->get("accountHolder");
				$resultView["accountBank"]	=	$companyRowView->get("accountBank");
				$resultView["accountNumber"]	=	$companyRowView->get("accountNumber");
				$resultView["companyID"]		= $companyRowView->get("idx");

				$addWhere = " AND idx = ".$idx." ";
				$account_row=$M_ACCOUNT->getUserInfo($addWhere,"","");
				$account_row->next();

				if($account_row->get('sType')==1){
					$MENU_ID=COMPANY_code;
				}else{
					$MENU_ID=COMPANYSKB_code;
				}

				$fileRow = $M_FILE->getFileList($MENU_ID, $companyRowView->get("idx"));
				if($fileRow->size()>0){
					for($i=0; $i<$fileRow->size(); $i++) {
						$fileRow->next();
						$file_idx .= $fileRow->get("idx");
						$fileName_origin .= $i + 1 . " 번: ";
						$fileName_origin .= "<a href = '/page/common/download.php?id=".$fileRow->get('idx')."'>".$fileRow->get('fileName_origin')."</a>";
						$fileName_origin .= "&nbsp;&nbsp;<a href='javascript:file_delete(".$fileRow->get("idx").",".$companyRowView->get("idx").",\"".$MENU_ID."\")'>";
						$fileName_origin .= "<img src='".IMG_DIR."/ico_d.png' style='vertical-align:middle;'></a>";
						if($i < $fileRow->size() - 1) { $fileName_origin .= " <br> "; }
					}
					$fileName_origin .= " &nbsp;&nbsp; / x버튼을 누르면 삭제됩니다.";
				}
				$resultView["file_idx"]	= $file_idx;
				$resultView["file_name"]	= $fileName_origin;	
			}
			echo json_encode($resultView);
	} else if($mode == "member_pass"){
		$u_idx = $_POST["u_idx"];
		$addWhere = " AND idx = ".$u_idx." ";
		$user_info=$M_ACCOUNT->getUserInfo($addWhere,"","");
		$user_info->next();
		if($user_info->get('sType')==1){
			$MENU_ID=USER_code;
		} else {
			$MENU_ID=USERSKB_code;
		}
		$M_ACCOUNT->update_pw($u_idx, $_POST["new_pw"]);
		$M_ACCOUNT->insertLog($_SESSION['U_idx'], $pageURL, $u_idx.'번 유저 비밀번호 변경 ', $u_idx ,$MENU_ID);
		echo "success";
	} else if($mode == "getSalesTeamIdx"){
		$parent = $_POST["parent"];
		//print_r($parent);exit;
		$result_row = array();

		$addWhere = " AND CI_idx = " .$parent;

		$row = $M_ACCOUNT->getSalesTeamInfo($addWhere);

		if($row->size()>0){
			for($i=0;$i<$row->size();$i++){
				$row->next();

				$result_row[$i]['teamName'] = $row->get('teamName');
				$result_row[$i]['idx'] = $row->get('idx');
			}
		}
		echo json_encode($result_row);
	}
	exit();
?>