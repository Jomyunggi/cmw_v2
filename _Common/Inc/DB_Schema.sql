CREATE TABLE Company_Info (
  idx int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '기업 일런변호',
  companyName varchar(100) NOT NULL COMMENT '기업명',
  address varchar(200) DEFAULT NULL COMMENT '사업장주소',
  status tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '상태',
  regUnixtime int(11) NOT NULL DEFAULT '0' COMMENT '생성일',
  outUnixtime int(11) DEFAULT '0' COMMENT '삭제일',
  Representative varchar(50) DEFAULT NULL COMMENT '대표자명',
  PRIMARY KEY (idx),
  KEY status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='기업정보';

CREATE TABLE Admin_History (
  idx int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '로그 일런변호',
  m_id int(10) unsigned NOT NULL COMMENT '유저 일련번호',
  MENU_ID char(10) NOT NULL COMMENT '메뉴 번호',
  account_ID varchar(255) NOT NULL COMMENT '회원 아이디',
  logContent varchar(1000) DEFAULT NULL COMMENT '활동내역',
  action varchar(10) NOT NULL COMMENT '상태값',
  regUnixtime int(11) NOT NULL DEFAULT '0' COMMENT '등록일',
  PRIMARY KEY (idx)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='활동로그';


CREATE TABLE File_Data (
  idx int(11) NOT NULL AUTO_INCREMENT COMMENT '파일 일련번호',
  MENU_ID varchar(30) NOT NULL COMMENT '파일을 올린 위치 일련번호',
  MENU_ID_idx varchar(30) NOT NULL COMMENT '파일을 올린 위치의 idx값',
  type varchar(10) NOT NULL DEFAULT '' COMMENT '이미지 타입(jpg,png 등)',
  fileName_origin varchar(255) NOT NULL COMMENT '실제 올린 파일명',
  fileName_conv varchar(255) NOT NULL COMMENT 'tmpData에 저장된 파일명',
  regDate_Ymd int(11) NOT NULL COMMENT 'Ymd값으로 저장되는 날짜',
  regUnixtime int(11) DEFAULT NULL COMMENT 'regUnixtime값으로 저장되는 날짜',
  PRIMARY KEY (idx)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='파일데이터';

CREATE TABLE SalesTeam_Info (
   idx int(11) NOT NULL AUTO_INCREMENT COMMENT '영업팀 일련번호',
   teamName varchar(100) NOT NULL COMMENT '영업팀명',
   CI_idx int(11) unsigned NOT NULL COMMENT '기업 일련번호',
   regUnixtime int(11) NOT NULL DEFAULT '0' COMMENT '생성일',
   outUnixtime int(11) DEFAULT '0' COMMENT '삭제일',
   status tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '상태',
   PRIMARY KEY(idx)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='영업팀정보';

CREATE TABLE Account_Info (
  idx int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '회원 일런변호',
  userID varchar(50) NOT NULL COMMENT '회원ID',
  userPW varchar(50) NOT NULL COMMENT '비밀번호',
  userName varchar(50) NOT NULL COMMENT '회원이름',
  email varchar(50) DEFAULT NULL COMMENT '추가이메일주소',
  hp int(11) DEFAULT '0' COMMENT '휴대폰번호',
  level tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '회원레벨',
  status tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '상태',
  CI_idx int(11) unsigned NOT NULL COMMENT '기업 일련번호',
  ST_idx int(11) unsigned NOT NULL COMMENT '영업팀 일련번호',
  regUnixtime int(11) NOT NULL DEFAULT '0' COMMENT '가입일',
  outUnixtime int(11) DEFAULT '0' COMMENT '탈퇴일',
  PRIMARY KEY (idx),
  UNIQUE KEY userID (userID),
  KEY level (level),
  KEY status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='회원정보';

CREATE TABLE Account_Matching_Info (
  idx int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '매칭테이블 일런변호',
  account_idx int(10) unsigned NOT NULL COMMENT '계정테이블의 일련번호',
  api_ID varchar(50) NOT NULL COMMENT '도메인ID',
  api_userID varchar(50) NOT NULL COMMENT '유저ID',
  api_userName varchar(100) NOT NULL COMMENT '유저명',
  api_domainName varchar(50) NOT NULL COMMENT '도메인명CompanyID',
  api_ein varchar(100) DEFAULT NULL COMMENT '사업자등록번호',
  api_country varchar(100) DEFAULT NULL COMMENT '국가',
  api_address varchar(200) DEFAULT NULL COMMENT '주소',
  api_number varchar(50) DEFAULT NULL COMMENT '전화번호',
  api_email varchar(50) DEFAULT NULL COMMENT '이메일',
  api_company varchar(100) DEFAULT NULL COMMENT '회사명',
  api_leader varchar(50) DEFAULT NULL COMMENT '대표자명',
  api_created datetime DEFAULT NULL COMMENT '생성일',
  regUnixtime int(11) NOT NULL DEFAULT '0' COMMENT '시작일',
  outUnixtime int(11) DEFAULT '0' COMMENT '삭제일',
  status tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '상태',
  PRIMARY KEY(idx)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='회원매칭테이블';

CREATE TABLE Account_Matching_Info (
  idx int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '매칭테이블 일런변호',
  account_idx int(10) unsigned NOT NULL COMMENT '계정테이블의 일련번호',
  api_userID varchar(50) NOT NULL COMMENT '유저ID',
  regUnixtime int(11) NOT NULL DEFAULT '0' COMMENT '시작일',
  outUnixtime int(11) DEFAULT '0' COMMENT '삭제일',
  status tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '상태',
  PRIMARY KEY(idx)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='회원매칭테이블';

CREATE TABLE Goods_Info (
  idx int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '상품 일런변호',
  goods_name varchar(100) NOT NULL COMMENT '상품이름',
  regUnixtime int(11) NOT NULL DEFAULT '0' COMMENT '시작일',
  outUnixtime int(11) DEFAULT '0' COMMENT '삭제일',
  status tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '상태',
  PRIMARY KEY(idx)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='상품테이블';

CREATE TABLE Goods_Matching_Info (
  idx int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '상품 매칭테이블 일런변호',
  goods_name varchar(100) NOT NULL COMMENT '상품이름',
  AM_api_userID varchar(50) NOT NULL COMMENT '계정 매칭테이블 아이디',
  account_idx int(10) unsigned NOT NULL COMMENT '계정테이블 일련번호',
  regUnixtime int(11) NOT NULL DEFAULT '0' COMMENT '시작일',
  outUnixtime int(11) DEFAULT '0' COMMENT '삭제일',
  status tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '상태',
  PRIMARY KEY(idx)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='상품테이블';