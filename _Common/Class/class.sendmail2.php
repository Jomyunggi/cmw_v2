<?php

class M_SMTP {
    var $host;
    var $fp;
    var $self;
    var $lastmsg;
    var $parts;
    var $error;
    var $debug;
    var $charset;
    var $ctype;


	function alarm_account_send($m_id, $type) {
		global $db, $M_FUNC, $M_ACCOUNT;

	}

	function dailyBudget($CI_idx){
		global $db, $M_FUNC, $M_ACCOUNT;
		
		$query = " SELECT * "
				." FROM Campaign_Info "
				." WHERE idx = ".$CI_idx;

		$row = $db->getListSet($query);
		$row->next();

		$this->alarm_send($row->get("advertiserIdx"), "BU4"); ////// 메일링, 실 서버스시 주석 풀것.
	}

	/*
	 type 정의
		'BU1' : 잔여예산부족 알림
		'BU2' : 충전예산 일정잔액 도달
		'BU4' : 일 예산 도달 시
		'EX1' : 계정 승인
		'EX2' : 캠페인 승인
		'EX4' : 소재 승인
		'EX8' : 영역 승인
		'PA1' : 무통장입금 신청
		'PA2' : 무통장입금 결과
		'ET1' : 비밀번호 변경
		'ET2' : 담당자정보 변경
		'ET4' : 대행사 정보 변경
		'ET8' : 뉴스레터 / 공지사항
		'ET16' : 심야시간 정보수신 동의
		'BU' : 대행사 알림위임 예산알림   -> BU1 / BU2 / BU4  (1)
		'EX' : 대행사 알림위임 검수결과   -> EX1 / EX2 / EX4 / EX8 (2)
		'PA' : 대행사 알림위임 결제알림   -> PA1 / PA2 (4)
		'ET' : 대행사 알림위임 기타알림   -> ET1 / ET2 / ET4 / ET8 / ET16 (8)
	*/
	function alarm_examination_send($data, $type, $fromMail = 'service@mklaud.com') {
		global $db, $M_FUNC, $M_ACCOUNT;

		foreach($data as $key=>$value) {
			// $key : 광고주번호
			// $value : 승인 or 반려 리스트

			// approveFlg   0, 7, 8 : 승인대기  / 1 : 승인완료 / 9 : 보류
			$accountIdx = $M_ACCOUNT->getAdvertiserInfoByIdx($key, 'keyAccountIdx');
			$advertiserIdx = $key;

			// Alarm ON/OFF Check And Get User Info
			$alarmInfo = $this->getAlarmInfo($advertiserIdx, $accountIdx, $type);

			if($alarmInfo['is_alarm']) {

				if($alarmInfo['is_sms'] || $alarmInfo['is_mail']) {

					include_once ADMIN_CLASS_PATH . '/class.adPage.php';
					$AdPage	 = new M_AdPage;

					$result_list = array();

					if($type === 'EX2') {
						// 캠페인 검수
						$row = $AdPage->getCampaignRow($total_page, $NO, $cnt_no, ' AND idx IN (' . implode(', ', $value) . ') ', null, 'NO');

						if($row->size() > 0) {
							for($i=0;$i<$row->size();$i++) {
								$row->next();
								$result_list[$i]['campaignName'] = $row->get('campaignName');
								$result_list[$i]['campaignPeriod'] = date('Y-m-d', strtotime($row->get('c_startDate'))) . ' ~ ' . date('Y-m-d', strtotime($row->get('c_endDate')));
								$result_list[$i]['campaignRegdate'] = date('Y-m-d H:i:s', $row->get('regUnixtime'));
							}
						} else {
							return;
						}
						
					} else if($type === 'EX4') {
						$row = $AdPage->getItemRow($total_page, $NO, $cnt_no,' AND idx IN (' . implode(', ', $value) . ') ', null, 'NO');

						if($row->size() > 0 ){
							for($i=0; $i<$row->size(); $i++){
								$row->next();
								$itemCount = $AdPage->getItemCountDataByCI_idx($value, $row->get("CI_idx"));

								$result_list[$i]['advertiserName'] = $M_ACCOUNT->getAdvertiserInfoByIdx($key, 'advertiserName');
								$result_list[$i]['s_accountID'] = $M_ACCOUNT->getAccountInfoByIdx($accountIdx, 's_accountID');
								$result_list[$i]['campaignName'] = $AdPage->getCampaignDataByIdx($row->get("CI_idx"), 'campaignName');
								$result_list[$i]['registItem'] = $itemCount;
								$result_list[$i]['Completion'] = $AdPage->getItemCountDataByCI_idx($value, $row->get("CI_idx"), " and approveFlg = 1 ");
								//$result_list[$i]['StandBy'] = $AdPage->getItemCountDataByCI_idx($value, $row->get("CI_idx"), " where approveFlg in (0, 8) and status = 1 ");
								$result_list[$i]['Companion'] = $AdPage->getItemCountDataByCI_idx($value, $row->get("CI_idx"), " and approveFlg in ( 0, 8, 9 ) ");
							}
						} else {
							return;
						}

					} else {
						return;
					}
					
					if(count($result_list) <= 0) {
						return;
					}

					$alarm_contents = $this->getAlarmContents($type, $advertiserIdx, $accountIdx, $alarmInfo, $result_list);

					if($alarmInfo['is_sms']) {
						if($M_FUNC->isNull($alarm_contents['sms_msg'])) {
						} else {
							// $this->sendSMS($alarm_contents['sms_msg'], $alarmInfo['userPhone']);  ////// SMS, 실 서버스시 주석 풀것.
						}
					}

					if($alarmInfo['is_mail']) {
						if($M_FUNC->isNull($alarm_contents['mail_title']) || $M_FUNC->isNull($alarm_contents['mail_contents'])) {
						} else {
							$this->send($alarmInfo['userEmail'], $fromMail, $alarm_contents['mail_title'], $alarm_contents['mail_contents']); ////// 메일링, 실 서버스시 주석 풀것.
						}
					}
				}
			}
		}
	}
	
	/*
	 * m_id : 계정 혹은 광고주번호
	 * type : 알림 타입
	 * isAccount : m_id의 계정번호 여부 (계정번호이면 true / 광고주번호이면 false)
	 * fromMail : 보내는 메일주소
	 * 승인메일 데이터 : array(15=>array(1,2,3), 16=>(5,6,7))
	 *                       array(광고주번호=>(승인데이터들), 광고주번호=>(승인데이터들))
	 */
	function alarm_send($m_id,  $type, $isAccount = false, $fromMail = 'service@mklaud.com') {
		global $db, $M_FUNC, $M_ACCOUNT;

		if($type === 'PA2') {
			// m_id : mklaud_cash 번호
			$cashInfo = $M_ACCOUNT->getReceiptInfoByIdx($m_id);
			if($cashInfo->size() > 0) {
				$advertiserIdx = $cashInfo->get('AdvertiserIdx');
				$accountIdx = $M_ACCOUNT->getAdvertiserInfoByIdx($advertiserIdx, 'keyAccountIdx');
			} else {
				return;
			}
		} else {
			$cashInfo = new L_ListSet();
			if($isAccount) { 
				// m_id : 계정번호
				$accountIdx = $m_id;
				$advertiserIdx = $M_ACCOUNT->getAdvertiserInfoByAccount($m_id, 'idx');
			} else {
				// m_id : 광고주번호
				$accountIdx = $M_ACCOUNT->getAdvertiserInfoByIdx($m_id, 'keyAccountIdx');
				$advertiserIdx = $m_id;
			}
		}

		// Alarm ON/OFF Check And Get User Info
		if($type != "DA1"){
			$alarmInfo = $this->getAlarmInfo($advertiserIdx, $accountIdx, $type);
		} else {
			$alarmInfo = $this->getUserInfo($accountIdx);
			$alarmInfo['is_alarm'] = true;
		}
		
		if($alarmInfo['is_alarm']) {

			if($alarmInfo['is_sms'] || $alarmInfo['is_mail']) {
				$alarm_contents = $this->getAlarmContents($type, $advertiserIdx, $accountIdx, $alarmInfo, $cashInfo);
				
				if($alarmInfo['is_sms']) {
					if($M_FUNC->isNull($alarm_contents['sms_msg'])) {
					} else {
						// $this->sendSMS($alarm_contents['sms_msg'], $alarmInfo['userPhone']);  ////// SMS, 실 서버스시 주석 풀것.
					}
				}

				if($alarmInfo['is_mail']) {
					if($M_FUNC->isNull($alarm_contents['mail_title']) || $M_FUNC->isNull($alarm_contents['mail_contents'])) {
					} else {
						$this->send($alarmInfo['userEmail'], $fromMail, $alarm_contents['mail_title'], $alarm_contents['mail_contents']); ////// 메일링, 실 서버스시 주석 풀것.$alarmInfo['userEmail']
					}
				}
			}
		}
	}

	function getAlarmContents($type, $advertiserIdx, $accountIdx, $alarmInfo, $row, $OLS = NULL) {
		global $db, $M_FUNC, $M_ACCOUNT;

		$alarm_contents = array();

		$sms_msg = '';
		$mail_title = '';
		$mail_contents = '';

		if($advertiserIdx <= 0 || $accountIdx <= 0) {
			if($OLS != "OLS"){
				return $alarm_contents;
			}
		}

		$mail_root = ROOT_PATH . '/_Admin/mail';
		$mail_html = file_get_contents($mail_root . '/mail_form_' . strtolower($type) . '.html', false);

		$accountInfo = $M_ACCOUNT->getAccountInfoByIdx($accountIdx);
		
		switch($type) {
			case 'BU1' : 
				// $sms_msg = '[엠클라우드에이피] 광고주님 잔여 예산이 부족하여 광고가 중단 되었습니다.'; 
				$sms_msg = '[엠클라우드에이피] 광고주님 잔여 예산이 부족합니다.'; 
				$mail_title = '잔여 예산이 부족합니다.';
				break;
			case 'BU2' : 
				$sms_msg = '[엠클라우드에이피] 광고주님 설정하신 예산이 소진 되었습니다.'; 
				$mail_title = '설정 예산이 소진 되었습니다.';
				break;
			case 'BU4' : 
				$sms_msg = '[엠클라우드에이피] 광고주님 설정하신 예산이 소진 되었습니다.'; 
				$mail_title = '설정 예산이 소진 되었습니다.';
				break;
			case 'DA1' : 
				$advertiserInfo = $M_ACCOUNT->getAdvertiserInfoByIdx($advertiserIdx);
				$sms_msg = '[엠클라우드에이피] 광고주님 계정이 휴면상태가 되었습니다.'; 
				$mail_title = '계정이 휴면상태가 되었습니다.';
				$mail_html = str_replace('{advertiserName}', $advertiserInfo->get('advertiserName'), $mail_html);
				break;
			case 'EX1' : 
				$advertiserInfo = $M_ACCOUNT->getAdvertiserInfoByIdx($advertiserIdx);
				$sms_msg = '[엠클라우드에이피] 계정 검수가 완료되었습니다. '; 
				$mail_title = '계정검수 결과를 알려드립니다.';
				$mail_html = str_replace('{advertiserName}', $advertiserInfo->get('advertiserName'), $mail_html);
				$mail_html = str_replace('{accountID}', $M_FUNC->decryptData($accountInfo->get('accountID')), $mail_html);
				$mail_html = str_replace('{email}', $alarmInfo['userEmail'], $mail_html);
				break;
			case 'EX2' :
				$sms_msg = '[엠클라우드에이피] 광고주님의 캠페인 검수가 완료 되었습니다.'; 
				$mail_title = '캠페인검수 결과를 알려드립니다.';
				$campaignList = '';
				foreach($row as $key=>$value) {
					$campaignList .= '<tr>';
					$campaignList .= '	<td style="width:30%; height:30px; padding-left:12px; border:3px solid #ffffff; background-color:#fafafa;">' . $value['campaignName'] . '</td>';
					$campaignList .= '	<td style="width:30%; height:30px; padding-left:12px; border:3px solid #ffffff; background-color:#fafafa;">' . $value['campaignPeriod'] . '</td>';
					$campaignList .= '	<td style="width:30%; height:30px; padding-left:12px; border:3px solid #ffffff; background-color:#fafafa;">' . $value['campaignRegdate'] . '</td>';
					$campaignList .= '<tr>';
				}
				$mail_html = str_replace('{campaignList}', $campaignList, $mail_html);
				break;
			case 'EX4' : 
				$sms_msg = '[엠클라우드에이피] 광고주님의 소재 검수가 완료 되었습니다.'; 
				$mail_title = '소재검수 결과를 알려드립니다.';
				foreach($row as $key=>$value) {
					$mail_html = str_replace('{s_accountID}', $value['s_accountID'], $mail_html);
					$mail_html = str_replace('{advertiserName}', $value['advertiserName'], $mail_html);
					$mail_html = str_replace('{campaignName}', $value['campaignName'], $mail_html);
					$mail_html = str_replace('{registItem}', $value['registItem'], $mail_html);
					$mail_html = str_replace('{Completion}', $value['Completion'], $mail_html);
					$mail_html = str_replace('{Companion}', $value['Companion'], $mail_html);
				}
				break;
			case 'EX8' : 
				$sms_msg = '[엠클라우드에이피] 매체사님의 영역 검수가 완료 되었습니다.'; 
				$mail_title = '영역검수 결과를 알려드립니다.';
				/*
				{accountID}
				{iframe}
				*/
				break;
			case 'PA1' : 
				$advertiserInfo = $M_ACCOUNT->getAdvertiserInfoByIdx($advertiserIdx);
				$cashInfo = $M_ACCOUNT->getCashInfoByIdxNType($advertiserIdx, 2);
				$advertiserName = $advertiserInfo->get('advertiserName');
				// $paytype = $cashInfo->get('paytype');
				$paytype = '무통장입금';
				$m_amount = number_format($cashInfo->get('price'));
				$time = date('Y-m-d H:i:s', $cashInfo->get('rdate'));
				$time2 = date('Y년 m월 d일 H시 i분 s초', $cashInfo->get('rdate'));
				$m_bank = $M_ACCOUNT->m_bank;
				$m_account = $M_ACCOUNT->m_account;
				$m_holder = $M_ACCOUNT->m_holder;

				$sms_msg = '광고주님 ' . $m_bank . ' ' . $m_account . ' 예금주 ' . $m_holder . ' ' . $m_amount . '원 입금바랍니다'; 
				$mail_title = '입금 요청 내역을 알려드립니다.';
				$mail_html = str_replace('{advertiserName}', $advertiserName, $mail_html);
				$mail_html = str_replace('{m_amount}', $m_amount, $mail_html);
				$mail_html = str_replace('{paytype}', $paytype, $mail_html);
				$mail_html = str_replace('{m_bank}', $m_bank, $mail_html);
				$mail_html = str_replace('{m_account}', $m_account, $mail_html);
				$mail_html = str_replace('{m_holder}', $m_holder, $mail_html);
				$mail_html = str_replace('{time}', $time, $mail_html);
				$mail_html = str_replace('{time2}', $time2, $mail_html);
				break;
			case 'PA2' : 
				$cashInfo = $row;
				$advertiserInfo = $M_ACCOUNT->getAdvertiserInfoByIdx($advertiserIdx);
				$advertiserName = $advertiserInfo->get('advertiserName');
				$paytype = '무통장입금';
				$m_amount = number_format($cashInfo->get('price'));
				$time = date('Y-m-d H:i:s', $cashInfo->get('paydate'));
				$m_bank = $M_ACCOUNT->m_bank;
				$m_account = $M_ACCOUNT->m_account;
				$m_holder = $M_ACCOUNT->m_holder;

				$sms_msg = '[엠클라우드에이피] 광고주님 ' . $m_amount . ' 원 입금이 확인되었습니다. 감사합니다'; 
				$mail_title = '입금 처리가 확인 되었습니다.';
				$mail_html = str_replace('{advertiserName}', $advertiserName, $mail_html);
				$mail_html = str_replace('{m_amount}', $m_amount, $mail_html);
				$mail_html = str_replace('{paytype}', $paytype, $mail_html);
				$mail_html = str_replace('{m_bank}', $m_bank, $mail_html);
				$mail_html = str_replace('{m_account}', $m_account, $mail_html);
				$mail_html = str_replace('{m_holder}', $m_holder, $mail_html);
				$mail_html = str_replace('{time}', $time, $mail_html);
				break;
			case 'ET1' : 
				$sms_msg = '[엠클라우드에이피] 광고주님의 비밀번호 변경이 완료 되었습니다'; 
				$mail_title = '비밀번호가 변경 되었습니다.';
				$mail_html = str_replace('{accountID}', $M_FUNC->decryptData($accountInfo->get('accountID')), $mail_html);
				if($OLS == "OLS"){
					$mail_html = str_replace('{accountPW}', $M_FUNC->M_Filter(POST, 'modifiedPassword'), $mail_html);
				} else {
					$mail_html = str_replace('{accountPW}', $M_FUNC->M_Filter(POST, 'accountPW'), $mail_html);
				}
				$mail_html = str_replace('{time}', date('Y-m-d H:i:s'), $mail_html);
				break;
			case 'ET2' : 
				$sms_msg = '[엠클라우드에이피] 광고주님의 변경요청으로 담당자 정보가 변경되었습니다'; 
				$mail_title = '담당자 정보가 변경 되었습니다.';
				$mail_html = str_replace('{accountID}', $M_FUNC->decryptData($accountInfo->get('accountID')), $mail_html);
				$mail_html = str_replace('{userName}', $M_FUNC->M_Filter(POST, 'userName'), $mail_html);
				$mail_html = str_replace('{tel}', $M_FUNC->M_Filter(POST, 'cp'), $mail_html);
				$mail_html = str_replace('{email}', $M_FUNC->M_Filter(POST, 'email'), $mail_html);
				break;
			case 'ET4' : 
				$sms_msg = '[엠클라우드에이피] 광고주님의 변경요청으로 광고 대행 정보가 변경 되었습니다'; 
				$mail_title = '광고대행 정보가 변경 되었습니다.';
				$mail_html = str_replace('{accountID}', $M_FUNC->decryptData($accountInfo->get('accountID')), $mail_html);
				$mail_html = str_replace('{time}', date('Y-m-d H:i:s'), $mail_html);
				$mail_html = str_replace('{agencyName}', $alarmInfo['agencyName'], $mail_html);
				$mail_html = str_replace('{userName}', $alarmInfo['userName'], $mail_html);
				$mail_html = str_replace('{tel}', $alarmInfo['userPhone'], $mail_html);
				$mail_html = str_replace('{email}', $alarmInfo['userEmail'], $mail_html);
				break;
		}

		$mail_contents = $mail_html;
		$mail_title =  iconv('utf-8', 'euc-kr', $mail_title);
		$mail_contents = iconv('utf-8', 'euc-kr', $mail_contents);

		$alarm_contents['sms_msg']			= $sms_msg;
		$alarm_contents['mail_title']			= $mail_title;
		$alarm_contents['mail_contents']	= $mail_contents;

		return $alarm_contents;
	}


	function getAlarmInfo($advertiserIdx, $accountIdx, $type) {
		global $db, $M_FUNC, $M_ACCOUNT;

		$result = array(
			'is_alarm'	=> false,
			'is_sms'	=> false,
			'is_mail'	=> false
		);

		if($M_FUNC->isNull($advertiserIdx)){
			return $result;
		}
	
		if($M_FUNC->isNull($accountIdx)){
			return $result;
		}

		if($M_FUNC->isNull($type)) {
			return $result;
		}

		$alarm_key = '';
		$search_key	= strval(preg_replace('/[^A-Z]*/s', '', $type));
		$alarm_column	= intval(preg_replace('/[^0-9]*/s', '', $type));

		foreach($M_ACCOUNT->alarm as $key=>$value) {
			if(strtoupper(substr($key, 0, 2)) === $search_key) {
				$alarm_key = $key;
				break;
			}
		}

		if($M_FUNC->isNull($alarm_key)) {
			return $result;
		}

		$table_column = str_replace('_alarm', '', $alarm_key);
		$alarm_info = $M_ACCOUNT->getAlarmInfoByIdx($advertiserIdx);
		
		if($alarm_info){
			if($alarm_column & intval($alarm_info->get($table_column))) {
				// 알람 ON
				$result['is_alarm'] = true;

				$agencyInfo = new L_ListSet();
				$advertiserInfo = $M_ACCOUNT->getAdvertiserInfoByIdx($advertiserIdx);
				if($advertiserInfo->get('agencyIdx') > 0) {
					// 대행사 위임 알림 체크
					$alarm_type = 0;
					
					switch($search_key) {
						case 'BU' : $alarm_type = 1; break;
						case 'EX' : $alarm_type = 2; break;
						case 'PA' : $alarm_type = 4; break;
						case 'ET' : $alarm_type = 8; break;
					}

					if($alarm_type & intval($alarm_info->get('agency'))) { 
						// 대행함
						$agencyInfo = $M_ACCOUNT->getAgencyInfoByIdx($advertiserInfo->get('agencyIdx'));
						$result['agencyName'] = $agencyInfo->get('agencyName');
						
						$userInfo = $this->getUserInfo($agencyInfo->get('keyAccountIdx'));
					} else {
						// 대행 안함
						if($type === "PA1"){
							$userInfo = $this->getMKCashInfo($advertiserIdx, $accountIdx);
						} else {
							$userInfo = $this->getUserInfo($accountIdx);
						}
					}

				} else {
					// 대행사 없음
					if($type === "PA1"){
						$userInfo = $this->getMKCashInfo($advertiserIdx, $accountIdx);
					} else {
						$userInfo = $this->getUserInfo($accountIdx);
					}
				}

				foreach($userInfo as $key=>$value) {
					$result[$key] = $value;
				}

				return $result;
			} else {
				// 알람 OFF
				return $result;
			}
		} else {
			// 알람 OFF
			return $result;
		}
	}


	function getUserInfo($accountIdx) {
		global $db, $M_FUNC, $M_ACCOUNT;

		$result_data = array();
		$is_sms = false;
		$is_mail = false;
		$userName	= '';
		$userEmail	= '';
		$userTel		= '';
		$userPhone = '';

		$userInfo = $M_ACCOUNT->getUserInfoByAccountIdx($accountIdx);

		if($userInfo->size() > 0) {
			$userName	= $M_FUNC->decryptData($userInfo->get('userName'));
			$userEmail	= $M_FUNC->decryptData($userInfo->get('email'));
			$userTel		= $userInfo->get('tel');
			$userPhone = str_replace("-", "", $userInfo->get('cp'));

			if($M_FUNC->isNull($userPhone)) {
			} else {
				switch(strlen($userPhone)) {
					case 10 : 
						$phone_num = substr($userPhone, 0, 3) . '-' . substr($userPhone, 3, 3) . '-' . substr($userPhone, 6, 4);
						break;
					case 11 : 
						$phone_num = substr($userPhone, 0, 3) . '-' . substr($userPhone, 3, 4) . '-' . substr($userPhone, 7, 4);
						break;
					default : $phone_num = 0;
				}

				if(preg_match("/^(0[0-9]{1,3})-[0-9]{3,4}-[0-9]{4}$/i", $phone_num)) {
					$is_sms = true;
				} 
			}

			if($M_FUNC->isNull($userEmail)) {
			} else {
				if(preg_match(" /^([0-9a-zA-Z_\.-]+)@([0-9a-zA-Z_-]+)(\.[0-9a-zA-Z_-]+){1,2}$/i", $userEmail)) {
					$is_mail = true;
				} 	
			}
		} 

		$result_data['is_sms']			= $is_sms;
		$result_data['is_mail']			= $is_mail;
		$result_data['userName']	= $userName;
		$result_data['userTel']			= $userTel;
		$result_data['userPhone']	= $userPhone;
		$result_data['userEmail']		= $userEmail;

		return $result_data;
	}

	function getMKCashInfo($advertiserIdx, $accountIdx) {
		global $db, $M_FUNC, $M_ACCOUNT;

		$result_data = array();
		$is_sms = false;
		$is_mail = false;
		$userName	= '';
		$userEmail	= '';
		$userTel		= '';
		$userPhone = '';

		$userInfo = $M_ACCOUNT->getUserInfoByAccountIdx($accountIdx);

		if($userInfo->size() > 0) {
			$userName	= $M_FUNC->decryptData($userInfo->get('userName'));
		}

		$MK_CashInfo = $M_ACCOUNT->getCashInfoByIdx($advertiserIdx);

		if($MK_CashInfo->size() > 0){

			$userEmail	= $MK_CashInfo->get('email');
			$userPhone = $MK_CashInfo->get('hp');

			if($M_FUNC->isNull($userPhone)) {
			} else {
				switch(strlen($userPhone)) {
					case 10 : 
						$phone_num = substr($userPhone, 0, 3) . '-' . substr($userPhone, 3, 3) . '-' . substr($userPhone, 6, 4);
						break;
					case 11 : 
						$phone_num = substr($userPhone, 0, 3) . '-' . substr($userPhone, 3, 4) . '-' . substr($userPhone, 7, 4);
						break;
					default : $phone_num = 0;
				}

				if(preg_match("/^(0[0-9]{1,3})-[0-9]{3,4}-[0-9]{4}$/i", $phone_num)) {
					$is_sms = true;
				} 
			}

			if($M_FUNC->isNull($userEmail)) {
			} else {
				if(preg_match(" /^([0-9a-zA-Z_\.-]+)@([0-9a-zA-Z_-]+)(\.[0-9a-zA-Z_-]+){1,2}$/i", $userEmail)) {
					$is_mail = true;
				} 	
			}
		} 

		$result_data['is_sms']			= $is_sms;
		$result_data['is_mail']			= $is_mail;
		$result_data['userName']	= $userName;
		$result_data['userPhone']	= $userPhone;
		$result_data['userEmail']		= $userEmail;

		return $result_data;
	}


    function M_SMTP($host="localhost") {
        /*
		if($host == "self") $this->self = true;
        else $this->host = $host;
		*/
		$this->self = false;
		$this->host = "58.120.226.9";
        $this->parts = array();
        $this->error = array();
        $this->debug = 0;
        $this->charset = "euc-kr";
        $this->ctype = "text/html";
    }

    // 디버그 모드 : 1
    function debug($n=1) {
        $this->debug = $n;
    }

    // smtp 통신을 한다.
    function dialogue($code, $cmd) {

        fputs($this->fp, $cmd."\r\n");
        $line = fgets($this->fp, 1024);
        ereg("^([0-9]+).(.*)$", $line, $data);
        $this->lastmsg = $data[0];

        if($this->debug) {
            echo htmlspecialchars($cmd)."<br>".$this->lastmsg."<br>";
            flush();
        }

        if($data[1] != $code) return false;
        return true;

    }

    //  smptp 서버에 접속을 한다.
    function smtp_connect($host) {

        if($this->debug) {
            echo "SMTP($host) Connecting...<br>";
            flush();
        }

        if(!$host) $host = $this->host;
        if(!$this->fp = fsockopen($host, 25, $errno, $errstr, 10)) {
            $this->lastmsg = "SMTP($host) 서버접속에 실패했습니다.[$errno:$errstr]";
            return false;
        }

        $line = fgets($this->fp, 1024);
        ereg("^([0-9]+).(.*)$", $line, $data);
        $this->lastmsg = $data[0];
        if($data[1] != "220") return false;

        if($this->debug) {
            echo $this->lastmsg."<br>";
            flush();
        }

        $this->dialogue(250, "HELO phpmail");
        return true;

    }

    // stmp 서버와의 접속을 끊는다.
    function smtp_close() {

        $this->dialogue(221, "QUIT");
        fclose($this->fp);
        return true;

    }

    // 메시지를 보낸다.
    function smtp_send($email, $from, $data) {

        if(!$mail_from = $this->get_email($from)) return false;
        if(!$rcpt_to = $this->get_email($email)) return false;

        if(!$this->dialogue(250, "MAIL FROM:$mail_from")) 
            $this->error[] = $email.":MAIL FROM 실패($this->lastmsg)";
        if(!$this->dialogue(250, "RCPT TO:$rcpt_to"))
            $this->error[] = $email.":RCPT TO 실패($this->lastmsg)";
        $this->dialogue(354, "DATA");

        $mime = "Message-ID: <".$this->get_message_id().">\r\n";
        $mime .= "From: $from\r\n";
        $mime .= "To: $email\r\n";

        fputs($this->fp, $mime);
        fputs($this->fp, $data);
        $this->dialogue(250, ".");

    }

    // Message ID 를 얻는다.
  function get_message_id() {
    $id = date("YmdHis",time());
    mt_srand((float) microtime() * 1000000);
    $randval = mt_rand();
    $id .= $randval."@phpmail";
    return $id;
  }

    // Boundary 값을 얻는다.
  function get_boundary() {
    $uniqchr = uniqid(time());
    $one = strtoupper($uniqchr[0]);
    $two = strtoupper(substr($uniqchr,0,8));
    $three = strtoupper(substr(strrev($uniqchr),0,8));
    return "----=_NextPart_000_000${one}_${two}.${three}";
  }

    // 첨부파일이 있을 경우 이 함수를 이용해 파일을 첨부한다.
    function attach($path, $name="", $ctype="application/octet-stream") {
        if(file_exists($path)) {
            $fp = fopen($path, "r");
            $message = fread($fp, filesize($path));
            fclose($fp);
            $this->parts[] = array ("ctype" => $ctype, "message" => $message, "name" => $name);
        } else return false;
    }

    // Multipart 메시지를 생성시킨다.
    function build_message($part) {

        $msg .= "Content-Type: ".$part['ctype'];
        if($part['name']) $msg .= "; name=\"".$part['name']."\"";
        $msg .= "\r\nContent-Transfer-Encoding: base64\r\n";
        $msg .= "Content-Disposition: attachment; filename=\"".$part['name']."\"\r\n\r\n";
        $msg .= chunk_split(base64_encode($part['message']));
        return $msg;

    }

    // SMTP에 보낼 DATA를 생성시킨다.
    function build_data($subject, $body) {

        $boundary = $this->get_boundary();
		$subject_t = "=?UTF-8?B?".base64_encode($subject)."?=";
        $mime = "Subject: $subject_t\r\n";
        $mime .= "Date: ".date ("D, j M Y H:i:s T",time())."\r\n";
        $mime .= "MIME-Version: 1.0\r\n";
        $mime .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n\r\n".
                 "This is a multi-part message in MIME format.\r\n\r\n";
		$mime .= "--".$boundary."\r\n".
             "Content-Type: ".$this->ctype."; charset=\"".$this->charset."\"\r\n".
             "Content-Transfer-Encoding: base64\r\n\r\n".
             chunk_split(base64_encode($body)).
             "\r\n\r\n--".$boundary;

        $max = count($this->parts);
        for($i=0; $i<$max; $i++) {
            $mime .= "\r\n".$this->build_message($this->parts[$i])."\r\n\r\n--".$boundary;
        }
        $mime .= "--\r\n";

        return $mime;

    }

    // MX 값을 찾는다.
    function get_mx_server($email) {
        
        if(!ereg("([\._0-9a-zA-Z-]+)@([0-9a-zA-Z-]+\.[a-zA-Z\.]+)", $email, $reg)) return false;
        getmxrr($reg[2], $host);
        if(!$host) $host[0] = $reg[2];
        return $host;

    }

    // 이메일의 형식이 맞는지 체크한다.
    function get_email($email) {
        if(!ereg("([\._0-9a-zA-Z-]+)@([0-9a-zA-Z-]+\.[a-zA-Z\.]+)", $email, $reg)) return false;
        return "<".$reg[0].">";
    }


    // 메일을 전송한다.
    function send($to, $from, $subject, $body) {
		
        if(!is_array($to)) $to = split("[,;]",$to); 
        if($this->self) {

            $data = $this->build_data($subject, $body);
            foreach($to as $email) {
                if($host = $this->get_mx_server($email)) {
                    $flag = false; $i = 0;
                    while($flag == false) {
                        if($host[$i]) {
                            $flag = $this->smtp_connect($host[$i]);
                            $i++;
                        } else break;
                    }
                    if($flag) {
                        $this->smtp_send($email, $from, $data);
                        $this->smtp_close();
                    } else {
                        $this->error[] = $email.":SMTP 접속실패";
                    }
                } else {
                    $this->error[] = $email.":형식이 잘못됨";
                }
            }

        } else {
			
            if(!$this->smtp_connect($this->host)) {
                $this->error[] = "$this->host SMTP 접속실패";
                return false;
            }
            $data = $this->build_data($subject, $body);
            foreach($to as $email) $this->smtp_send($email, $from, $data);
            $this->smtp_close();

        }
    }

	function sendSMS($msg, $toPhone) {
		$from_Phone = '0220884095';
	
		$url = 'http://sms.emato.net/sms_db_insert_linux_utf8.php?User_Name=emato&Password=dnflsms&To_Phone_Num_List=' . $toPhone . '&From_Phone_Num=' . $from_Phone . '&Msg=' . urlencode($msg) . '&Server_IP=58.120.226.5&Return_URL=NO_RETURN';

		$stat = fopen($url, 'r');
		fclose($stat);
	}

}

/*
$mail = new Smtp('self');
$mail->debug(); 
$mail->send('photon0@hanmail.net', '보내는사람', '이 메일은 정상입니다.', '정상적인 메일이니 삭제하지 마십시오.');
*/
?>