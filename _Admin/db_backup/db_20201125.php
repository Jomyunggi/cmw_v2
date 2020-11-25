-- --------------------------------------------------------
-- 호스트:                          127.0.0.1
-- 서버 버전:                        5.7.17-log - MySQL Community Server (GPL)
-- 서버 OS:                        Win64
-- HeidiSQL 버전:                  11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- cmw_v2 데이터베이스 구조 내보내기
CREATE DATABASE IF NOT EXISTS `cmw_v2` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `cmw_v2`;

-- 테이블 cmw_v2.account_info 구조 내보내기
CREATE TABLE IF NOT EXISTS `account_info` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `accountID` varchar(100) NOT NULL COMMENT '계정ID',
  `accountPW` varchar(255) NOT NULL COMMENT '계정PW',
  `accountName` varchar(100) NOT NULL COMMENT '계정이름',
  `regUnixtime` int(11) NOT NULL DEFAULT '0',
  `status` int(4) NOT NULL DEFAULT '1' COMMENT '상태 1: 사용, 4:중지, 9:삭제',
  PRIMARY KEY (`idx`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- 테이블 데이터 cmw_v2.account_info:~0 rows (대략적) 내보내기
/*!40000 ALTER TABLE `account_info` DISABLE KEYS */;
INSERT IGNORE INTO `account_info` (`idx`, `accountID`, `accountPW`, `accountName`, `regUnixtime`, `status`) VALUES
	(1, 'shim', '7a0d42341d7d97ef1a065f295638a5e8', '쉼', 1555000000, 1);
/*!40000 ALTER TABLE `account_info` ENABLE KEYS */;

-- 테이블 cmw_v2.board_info 구조 내보내기
CREATE TABLE IF NOT EXISTS `board_info` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `accountIdx` int(11) NOT NULL COMMENT '계정ID',
  `accountName` varchar(100) NOT NULL COMMENT '계정이름',
  `category` int(4) NOT NULL COMMENT '카테고리',
  `subject` varchar(255) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `regUnixtime` int(11) NOT NULL DEFAULT '0',
  `status` int(4) NOT NULL DEFAULT '1' COMMENT '상태',
  PRIMARY KEY (`idx`) USING BTREE,
  KEY `accountIdx` (`accountIdx`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- 테이블 데이터 cmw_v2.board_info:~12 rows (대략적) 내보내기
/*!40000 ALTER TABLE `board_info` DISABLE KEYS */;
INSERT IGNORE INTO `board_info` (`idx`, `accountIdx`, `accountName`, `category`, `subject`, `content`, `regUnixtime`, `status`) VALUES
	(1, 1, '쉼', 2, '[인증]스마트스토어 오늘자 정산이 2억이 넘었습니다.', 'https://www.i-boss.co.kr/ab-1486505-34209', 1600845625, 1),
	(2, 1, '쉼', 2, '대행사를 찾고 있다면?', 'https://www.i-boss.co.kr/ab-7553', 1600845976, 1),
	(3, 1, '쉼', 2, '인천 초보 디지털 마케팅 독서스터디', 'https://www.i-boss.co.kr/ab-1486526-176', 1600846323, 1),
	(4, 1, '쉼', 2, '마케터를 위한 독서모임 모집합니다', '마케터를 위한 독서모임 모집합니다', 1600846442, 1),
	(5, 1, '쉼', 2, '키워드광고 체크리스트', 'https://www.i-boss.co.kr/ab-6141-7558', 1600846797, 1),
	(6, 1, '쉼', 2, '키워드광고 광고대행사 바로알기', 'https://www.i-boss.co.kr/ab-6141-6760', 1600846822, 1),
	(7, 1, '쉼', 2, '쇼핑몰 스마트스토어 상품명 작명소', 'https://blog.naver.com/meet011/222096730696', 1600912045, 1),
	(8, 1, '쉼', 2, '대량으로 키워드를 네이버 키워드 검색량 조회수를 알 수 있는 앱이 있네요~ 마피아넷', 'https://www.i-boss.co.kr/ab-1486505-30683', 1601881288, 1),
	(9, 1, '쉼', 2, '광고 효율 극대화를 위한 키워드 광고 노하우 41개', 'https://www.i-boss.co.kr/ab-6141-40002', 1601881454, 1),
	(10, 1, '쉼', 2, '브랜드 노출을 늘리면 브랜딩이 된다?', 'https://www.i-boss.co.kr/ab-74668-409', 1601967538, 1),
	(11, 1, '쉼', 2, '광고 - 매출최적화작업', '문제&#13;&#10;&#9;캠페인 한개당 상품 많을수록 좋다&#13;&#10;&#9;&#13;&#10;&#13;&#10;광고 효율up 상품을 넣어야지 더 최적화가 잘된다&#13;&#10;부스터업효과&#13;&#10;&#13;&#10;&#13;&#10;&#13;&#10;상품 중복이되면 피해를 본다.&#13;&#10;', 1602056437, 1),
	(12, 1, '쉼', 4, '금이동 위치에 3pl', 'https://cafe.naver.com/soho/2352861', 1602125564, 1);
/*!40000 ALTER TABLE `board_info` ENABLE KEYS */;

-- 테이블 cmw_v2.company_info 구조 내보내기
CREATE TABLE IF NOT EXISTS `company_info` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `companyName` varchar(100) NOT NULL COMMENT '회사명',
  `license` varchar(100) DEFAULT NULL COMMENT '사업자번호',
  `addr` varchar(255) DEFAULT NULL COMMENT '주소',
  `tel` varchar(100) DEFAULT NULL COMMENT '전화번호',
  `fax` varchar(100) DEFAULT NULL COMMENT '팩스',
  `senior` varchar(100) DEFAULT NULL COMMENT '대표자명',
  `email` varchar(100) DEFAULT NULL COMMENT '이메일',
  `level` int(4) NOT NULL COMMENT '레벨',
  `taxYN` int(4) NOT NULL COMMENT '세금여부 0, 1로 구분',
  `fees` float NOT NULL DEFAULT '0',
  `status` int(4) NOT NULL DEFAULT '1' COMMENT '상태',
  `regUnixtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idx`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

-- 테이블 데이터 cmw_v2.company_info:~37 rows (대략적) 내보내기
/*!40000 ALTER TABLE `company_info` DISABLE KEYS */;
INSERT IGNORE INTO `company_info` (`idx`, `companyName`, `license`, `addr`, `tel`, `fax`, `senior`, `email`, `level`, `taxYN`, `fees`, `status`, `regUnixtime`) VALUES
	(3, '유한 SG', '426-71-00065', '경기도 시흥시 하중로 117번길 10-11', '', '', '김정식', 'yhsg0717@naver.com', 1, 1, 0, 1, 1600230946),
	(4, '유한실업', '134-81-27446', '경기도 안산시 단원구 화정천동로2안길 11', '', '', '권혁조', 'ansanyuhan@hanmail.net', 1, 1, 0, 1, 1600231014),
	(5, '종합상사', '134-01-70427', '경기도 안산시 단원구 고잔동 605-1 대진빌라', '', '', '이한규', 'smile2550@hanmail.net', 1, 1, 0, 1, 1600231093),
	(6, '농업회사법인 천수 주식회사', '751-81-01334', '경기도 하남시 대성로181번길 128, 가동 2호(춘궁동)', '', '', '전천수', '', 1, 1, 0, 1, 1600231163),
	(7, '충무산업', '312-12-23480', '충남 천안시 동남구 용원3길 22번지', '', '', '오용재', '3121223480@bestbill.co.kr', 1, 1, 0, 1, 1600231215),
	(8, '태일상회', '113-16-69285', '서울시 구로구 고척로30길 26', '', '', '태근호', 'tp321@daum.net', 1, 1, 0, 1, 1600231244),
	(9, '영인물류', '140-81-61828', '', '', '', '박영순', 'ys10166@yahoo.co.kr', 1, 1, 0, 1, 1600231295),
	(10, '휘랑유통', '514-49-00298', '경기도 안양시 만안구 얀양로248번길 44', '', '', '이재훈', 'fog69@daum.net', 1, 1, 0, 1, 1600231325),
	(11, '경기화장지', '123-16-42473', '', '', '', '서원', 'sow7756@naver.com', 1, 1, 0, 1, 1600231360),
	(12, '에이스종합유통', '134-06-17413', '', '', '', '이영종', 'dwr666@naver.com', 1, 1, 0, 1, 1600231394),
	(13, '에이 하나', '130-17-84274', '경기도 김포시 통진읍 김포대로 2385번길 67-133', '1588-3489-', '', '강윤성', 'ahana1991@naver.com', 1, 1, 0, 1, 1600231447),
	(14, '주식회사 농심라면천국', '778-81-00820', '경기도 광명시 광명로 371', '', '', '한기석', 'klsok1478@daum.net', 1, 1, 0, 1, 1600231482),
	(15, '유천종합화장지', '134-19-06764', '', '', '', '송기홍', 'yooochun@naver.com', 1, 1, 0, 1, 1600231518),
	(16, '에코미스트존', '123-21-87488', '', '', '', '홍순덕', 'ecomist7142@naver.com', 1, 1, 0, 1, 1600231558),
	(17, '나라상사', '889-28-00386', '경기도 안산시 단원구 부부로4길 14, 1동 502호', '', '', '배영숙', 'tlstnrylove@hanmail.net', 1, 1, 0, 1, 1600231590),
	(18, '베스트바이', '134-19-06764', '', '', '', '서경숙', 'bestbuy6@naver.com', 1, 1, 0, 1, 1600231618),
	(19, '우신킴벌리', '113-81-71493', '', '', '', '권철오', 'ykmanone@hanmail.net', 1, 1, 0, 1, 1600231646),
	(20, '유한아저씨', '', '', '', '', '유한아저씨', '', 2, 0, 0, 1, 1600231670),
	(21, '장호용', '', '', '', '', '장호용', '', 2, 0, 0, 1, 1600231693),
	(22, '프라임에스엔지', '131-01-32176', '인천시 연수구 송도과학로27번길 55', '', '', '김경민', 'soola1234@naver.com', 1, 1, 0, 1, 1600231720),
	(23, '술술', '424-11-00065', '경기도 광명시 충현로 1, 1층(소하동)', '', '', '김영오', '', 1, 1, 0, 1, 1600231741),
	(24, '주한상사', '363-58-00182', '인천광역시 동구 금곡로 55-1, 1층(금곡동)', '', '', '정주한', '76juhan@naver.com', 1, 1, 0, 1, 1600231765),
	(25, '필그린', '117-03-55920', '', '', '', '이득혁', '', 1, 1, 0, 1, 1600231824),
	(26, '태건상사', '122-24-87604', '', '', '', '최준석', 'hellochoi2@daum.net', 1, 1, 0, 1, 1600231854),
	(27, '두꺼비 식품', '575-15-01147', '경기도 안산시 상록구 장하동 211-3', '', '', '남상희', 'stan818@naver.com', 1, 1, 0, 1, 1600231902),
	(28, '유한)최이사님', '', '', '', '', '유한)최이사님', '', 2, 0, 0, 1, 1600231929),
	(29, '쌍용상사', '139-09-71150', '인천광역시 남동구 예술로360번길 26', '', '', '이애용', 'puredabong@hanmail.net', 1, 1, 0, 1, 1600231958),
	(30, '안산장갑', '134-15-60533', '경기 안산 원곡 820-11', '', '', '박재원', 'jjtoto@hanmail.net', 1, 1, 0, 1, 1600231988),
	(31, '약장수', '', '', '', '', '약장수', '', 2, 0, 0, 1, 1600232007),
	(32, '에스씨(SC)푸드', '112-36-37319', '인천광역시 서구 북항로193번길 17(원창동)', '', '', '최승철', 'csc9082@hanmail.net', 1, 1, 0, 1, 1600232045),
	(33, '행복한세상', '209-06-42477', '', '', '', '김태완', 'twkem69@chol.com', 1, 1, 0, 1, 1600232085),
	(34, '온라인판매', '', '', '', '', '조명기', '', 2, 0, 0, 1, 1600232118),
	(35, '쿠팡', '8.58', '', '', '', '', '', 4, 0, 10, 1, 1605247384),
	(36, '티몬', '12', '', '', '', '', '', 4, 0, 12, 1, 1605247574),
	(37, '위메프', '11', '', '', '', '', '', 4, 0, 12, 1, 1605247601),
	(38, '11번가', '10', '', '', '', '', '', 4, 0, 10, 1, 1605247617),
	(39, '옥션', '9', '', '', '', '', '', 4, 0, 10, 1, 1605247630),
	(40, '지마켓', '9', '', '', '', '', '', 4, 0, 10, 1, 1605247639),
	(41, '스마트스토어', '5.72', '', '', '', '', '', 4, 0, 10, 1, 1605247661),
	(42, '롯데ON', '6.91', '', '', '', '', '', 4, 0, 10, 1, 1605248284);
/*!40000 ALTER TABLE `company_info` ENABLE KEYS */;

-- 테이블 cmw_v2.delivery_info 구조 내보내기
CREATE TABLE IF NOT EXISTS `delivery_info` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `goodsIdx` int(11) NOT NULL DEFAULT '0' COMMENT '상품번호',
  `count` int(4) NOT NULL DEFAULT '0' COMMENT '수량',
  `size` int(4) NOT NULL DEFAULT '1' COMMENT '1:소, 2:중, 3:대',
  `revenue_hope` float NOT NULL DEFAULT '0',
  `regUnixtime` int(11) NOT NULL DEFAULT '0',
  `status` int(4) NOT NULL DEFAULT '1' COMMENT '상태값',
  PRIMARY KEY (`idx`) USING BTREE,
  KEY `goodsIdx` (`goodsIdx`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- 테이블 데이터 cmw_v2.delivery_info:~19 rows (대략적) 내보내기
/*!40000 ALTER TABLE `delivery_info` DISABLE KEYS */;
INSERT IGNORE INTO `delivery_info` (`idx`, `goodsIdx`, `count`, `size`, `revenue_hope`, `regUnixtime`, `status`) VALUES
	(1, 3, 10, 3, 16.2, 1605239395, 1),
	(2, 4, 4, 3, 17.33, 1605244327, 1),
	(3, 5, 3, 2, 17.79, 1605244335, 1),
	(4, 6, 10, 3, 16.21, 1605244342, 1),
	(5, 7, 3, 2, 16.5, 1605244361, 1),
	(6, 8, 3, 3, 17.79, 1605244367, 1),
	(7, 9, 3, 3, 17.79, 1605244373, 1),
	(8, 11, 3, 3, 17.33, 1605244380, 1),
	(9, 12, 3, 3, 17.4, 1605244387, 1),
	(10, 13, 1, 2, 18.09, 1605244394, 1),
	(11, 14, 1, 2, 18.09, 1605244399, 1),
	(12, 15, 1, 2, 18.09, 1605244406, 1),
	(13, 16, 1, 2, 18.09, 1605244412, 1),
	(14, 17, 1, 2, 17.6, 1605244419, 1),
	(15, 18, 1, 2, 17.6, 1605244425, 1),
	(16, 19, 1, 2, 17.6, 1605244429, 1),
	(17, 20, 1, 2, 17.6, 1605244435, 1),
	(18, 21, 1, 2, 15.5, 1605244445, 1),
	(19, 22, 1, 2, 14.87, 1605244451, 1);
/*!40000 ALTER TABLE `delivery_info` ENABLE KEYS */;

-- 테이블 cmw_v2.goods_info 구조 내보내기
CREATE TABLE IF NOT EXISTS `goods_info` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(4) NOT NULL DEFAULT '0' COMMENT '카테고리',
  `rollType` int(4) NOT NULL DEFAULT '0' COMMENT '롤타입',
  `length` int(4) NOT NULL DEFAULT '0' COMMENT '길이',
  `gName` varchar(100) DEFAULT NULL COMMENT '상품명',
  `count` int(4) DEFAULT '0' COMMENT '수량',
  `cost` int(4) DEFAULT '0' COMMENT '원가',
  `price` int(4) NOT NULL DEFAULT '0' COMMENT '판매가',
  `status` int(4) NOT NULL DEFAULT '1' COMMENT '상태',
  `regUnixtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idx`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- 테이블 데이터 cmw_v2.goods_info:~17 rows (대략적) 내보내기
/*!40000 ALTER TABLE `goods_info` DISABLE KEYS */;
INSERT IGNORE INTO `goods_info` (`idx`, `category`, `rollType`, `length`, `gName`, `count`, `cost`, `price`, `status`, `regUnixtime`) VALUES
	(3, 1, 10, 26, '새피아', 10, 19680, 23000, 1, 1600232833),
	(4, 1, 24, 24, '새피아', 4, 15435, 20000, 1, 1600232939),
	(5, 1, 30, 17, '순수', 3, 10918, 14500, 1, 1600233018),
	(6, 1, 10, 26, '땡큐', 10, 20180, 23500, 1, 1600233054),
	(7, 1, 30, 16, '땡큐', 3, 10500, 14500, 1, 1600233112),
	(8, 1, 30, 22, '자연', 3, 13120, 17500, 1, 1600233153),
	(9, 1, 30, 22, '꿈집', 3, 13120, 17500, 1, 1600233166),
	(10, 1, 30, 24, '꽃지꿈집', 3, 15074, 19300, 1, 1600233198),
	(11, 1, 30, 24, '황토', 3, 15432, 20000, 1, 1600233231),
	(12, 1, 30, 26, '데코꿈집', 3, 16111, 20600, 1, 1600233264),
	(13, 2, 16, 150, '고급점보롤150', 1, 11127, 15000, 1, 1600233339),
	(14, 2, 16, 180, '고급점보롤180', 1, 12795, 17500, 1, 1600233363),
	(15, 2, 16, 200, '고급점보롤200', 1, 14963, 18700, 1, 1600233465),
	(16, 2, 16, 300, '고급점보롤300', 1, 21207, 27000, 1, 1600233486),
	(17, 2, 16, 150, '꽃지점보롤150', 1, 11660, 16000, 1, 1600233511),
	(18, 2, 16, 180, '꽃지점보롤180', 1, 13418, 18500, 1, 1600233536),
	(19, 2, 16, 200, '꽃지점보롤200', 1, 15710, 19800, 1, 1600233574),
	(20, 2, 16, 300, '꽃지점보롤300', 1, 21993, 27700, 1, 1600233607),
	(21, 4, 100, 100, '네프킨', 1, 9000, 11500, 1, 1600233702),
	(22, 8, 50, 100, '핸드타월', 1, 14500, 20300, 1, 1600233771);
/*!40000 ALTER TABLE `goods_info` ENABLE KEYS */;

-- 테이블 cmw_v2.unitprice_info 구조 내보내기
CREATE TABLE IF NOT EXISTS `unitprice_info` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `companyIdx` int(11) NOT NULL COMMENT '거래처ID',
  `goodsIdx` int(11) NOT NULL DEFAULT '0' COMMENT '상품번호',
  `companyName` varchar(100) NOT NULL COMMENT '거래처명',
  `price` int(4) NOT NULL COMMENT '가격',
  `regUnixtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idx`) USING BTREE,
  KEY `companyIdx` (`companyIdx`) USING BTREE,
  KEY `goodsIdx` (`goodsIdx`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8;

-- 테이블 데이터 cmw_v2.unitprice_info:~126 rows (대략적) 내보내기
/*!40000 ALTER TABLE `unitprice_info` DISABLE KEYS */;
INSERT IGNORE INTO `unitprice_info` (`idx`, `companyIdx`, `goodsIdx`, `companyName`, `price`, `regUnixtime`) VALUES
	(3, 3, 3, '유한 SG', 23000, 1600233984),
	(4, 3, 6, '유한 SG', 23500, 1600233984),
	(5, 3, 7, '유한 SG', 14000, 1600233984),
	(6, 3, 5, '유한 SG', 14500, 1600233984),
	(7, 3, 8, '유한 SG', 17000, 1600233984),
	(8, 3, 9, '유한 SG', 17000, 1600233984),
	(9, 3, 10, '유한 SG', 18500, 1600233984),
	(10, 3, 11, '유한 SG', 19500, 1600233984),
	(11, 3, 12, '유한 SG', 21000, 1600233984),
	(12, 3, 13, '유한 SG', 14500, 1600234076),
	(13, 3, 14, '유한 SG', 17500, 1600234076),
	(14, 3, 21, '유한 SG', 11500, 1600234097),
	(15, 3, 22, '유한 SG', 20000, 1600234097),
	(16, 4, 3, '유한실업', 23000, 1600234455),
	(17, 4, 5, '유한실업', 14500, 1600234455),
	(18, 4, 9, '유한실업', 16000, 1600234455),
	(19, 4, 13, '유한실업', 14500, 1600234455),
	(20, 4, 18, '유한실업', 18000, 1600234455),
	(21, 5, 3, '종합상사', 23000, 1600234514),
	(22, 5, 4, '종합상사', 19700, 1600234514),
	(23, 5, 9, '종합상사', 17000, 1600234514),
	(24, 5, 11, '종합상사', 17000, 1600234514),
	(25, 6, 8, '농업회사법인 천수 주식회사', 17000, 1600234545),
	(26, 6, 13, '농업회사법인 천수 주식회사', 14500, 1600234545),
	(27, 6, 14, '농업회사법인 천수 주식회사', 18000, 1600234545),
	(28, 7, 6, '충무산업', 23000, 1600234580),
	(29, 7, 11, '충무산업', 16500, 1600234580),
	(30, 8, 6, '태일상회', 23500, 1600234611),
	(31, 8, 7, '태일상회', 13500, 1600234611),
	(32, 8, 14, '태일상회', 17500, 1600234611),
	(33, 9, 3, '영인물류', 23000, 1600234691),
	(34, 9, 6, '영인물류', 23500, 1600234691),
	(35, 9, 7, '영인물류', 13000, 1600234691),
	(36, 9, 5, '영인물류', 14500, 1600234691),
	(37, 9, 8, '영인물류', 17500, 1600234691),
	(38, 9, 13, '영인물류', 15000, 1600234691),
	(39, 9, 14, '영인물류', 17500, 1600234691),
	(40, 9, 17, '영인물류', 16000, 1600234691),
	(41, 9, 21, '영인물류', 12000, 1600234691),
	(42, 9, 22, '영인물류', 20000, 1600234691),
	(43, 10, 3, '휘랑유통', 23000, 1600234723),
	(44, 10, 8, '휘랑유통', 17500, 1600234723),
	(45, 10, 14, '휘랑유통', 17000, 1600234723),
	(46, 11, 3, '경기화장지', 23000, 1600234779),
	(47, 11, 5, '경기화장지', 14500, 1600234779),
	(48, 11, 8, '경기화장지', 17500, 1600234779),
	(49, 11, 11, '경기화장지', 20000, 1600234779),
	(50, 11, 13, '경기화장지', 14500, 1600234779),
	(51, 11, 14, '경기화장지', 17500, 1600234779),
	(52, 11, 22, '경기화장지', 19000, 1600234779),
	(53, 12, 3, '에이스종합유통', 23000, 1600234831),
	(54, 12, 4, '에이스종합유통', 19700, 1600234831),
	(55, 12, 9, '에이스종합유통', 18400, 1600234831),
	(56, 12, 19, '에이스종합유통', 21000, 1600234831),
	(57, 13, 5, '에이 하나', 14000, 1600234868),
	(58, 13, 8, '에이 하나', 17500, 1600234868),
	(59, 13, 12, '에이 하나', 20000, 1600234868),
	(60, 14, 14, '주식회사 농심라면천국', 17000, 1600234916),
	(61, 14, 18, '주식회사 농심라면천국', 17500, 1600234916),
	(62, 14, 21, '주식회사 농심라면천국', 11000, 1600234916),
	(63, 14, 22, '주식회사 농심라면천국', 17500, 1600234916),
	(64, 15, 3, '유천종합화장지', 23000, 1600234955),
	(65, 15, 4, '유천종합화장지', 19700, 1600234955),
	(66, 15, 5, '유천종합화장지', 14500, 1600234955),
	(67, 15, 8, '유천종합화장지', 17500, 1600234955),
	(68, 16, 3, '에코미스트존', 23500, 1600234977),
	(69, 16, 14, '에코미스트존', 18000, 1600234977),
	(70, 17, 3, '나라상사', 23000, 1600235070),
	(71, 17, 6, '나라상사', 23500, 1600235070),
	(72, 17, 4, '나라상사', 19700, 1600235070),
	(73, 17, 7, '나라상사', 14500, 1600235070),
	(74, 17, 5, '나라상사', 14500, 1600235070),
	(75, 17, 9, '나라상사', 17000, 1600235070),
	(76, 17, 13, '나라상사', 15000, 1600235070),
	(77, 17, 14, '나라상사', 17500, 1600235070),
	(78, 17, 19, '나라상사', 20000, 1600235070),
	(79, 17, 21, '나라상사', 11000, 1600235070),
	(80, 17, 22, '나라상사', 19000, 1600235071),
	(81, 18, 5, '베스트바이', 13000, 1600235101),
	(82, 18, 13, '베스트바이', 14000, 1600235101),
	(83, 18, 17, '베스트바이', 15000, 1600235101),
	(84, 19, 3, '우신킴벌리', 23000, 1600235138),
	(85, 19, 11, '우신킴벌리', 20000, 1600235138),
	(86, 19, 18, '우신킴벌리', 17000, 1600235138),
	(87, 20, 3, '유한아저씨', 23000, 1600235235),
	(88, 20, 6, '유한아저씨', 23500, 1600235235),
	(89, 20, 5, '유한아저씨', 14500, 1600235235),
	(90, 20, 8, '유한아저씨', 17500, 1600235235),
	(91, 20, 9, '유한아저씨', 18400, 1600235235),
	(92, 20, 10, '유한아저씨', 18500, 1600235235),
	(93, 20, 11, '유한아저씨', 21000, 1600235235),
	(94, 20, 13, '유한아저씨', 15000, 1600235235),
	(95, 20, 14, '유한아저씨', 18000, 1600235235),
	(96, 20, 17, '유한아저씨', 16000, 1600235235),
	(97, 20, 18, '유한아저씨', 18000, 1600235235),
	(98, 20, 22, '유한아저씨', 20000, 1600235235),
	(99, 21, 3, '장호용', 23000, 1600235273),
	(100, 21, 4, '장호용', 19700, 1600235273),
	(101, 21, 5, '장호용', 14500, 1600235273),
	(102, 21, 9, '장호용', 18400, 1600235273),
	(103, 22, 14, '프라임에스엔지', 17500, 1600235291),
	(104, 23, 14, '술술', 23100, 1600235308),
	(105, 23, 21, '술술', 12600, 1600235326),
	(106, 23, 22, '술술', 21900, 1600235326),
	(107, 24, 3, '주한상사', 23000, 1600235335),
	(108, 25, 5, '필그린', 14500, 1600235377),
	(109, 25, 9, '필그린', 18000, 1600235377),
	(110, 25, 14, '필그린', 18000, 1600235377),
	(111, 25, 16, '필그린', 28000, 1600235377),
	(112, 26, 3, '태건상사', 23000, 1600235422),
	(113, 26, 5, '태건상사', 14500, 1600235422),
	(114, 26, 17, '태건상사', 15500, 1600235422),
	(115, 26, 18, '태건상사', 18000, 1600235422),
	(116, 27, 3, '두꺼비 식품', 23500, 1600235452),
	(117, 27, 4, '두꺼비 식품', 21000, 1600235452),
	(118, 27, 14, '두꺼비 식품', 18000, 1600235452),
	(119, 28, 5, '유한)최이사님', 14500, 1600235488),
	(120, 28, 7, '유한)최이사님', 14500, 1600235488),
	(121, 29, 3, '쌍용상사', 23500, 1600235506),
	(122, 30, 3, '안산장갑', 23500, 1600235518),
	(123, 31, 5, '약장수', 13500, 1600235556),
	(124, 32, 13, '에스씨(SC)푸드', 15000, 1600235576),
	(125, 32, 14, '에스씨(SC)푸드', 18000, 1600235576),
	(126, 33, 10, '행복한세상', 22000, 1600235598),
	(127, 33, 19, '행복한세상', 23000, 1600235598),
	(128, 34, 3, '온라인판매', 2300, 1600235745),
	(129, 34, 4, '온라인판매', 19700, 1600235745),
	(130, 34, 5, '온라인판매', 14500, 1600235745),
	(131, 34, 8, '온라인판매', 17500, 1600235745),
	(132, 34, 9, '온라인판매', 17500, 1600235745),
	(133, 34, 10, '온라인판매', 19000, 1600235745),
	(134, 34, 11, '온라인판매', 20000, 1600235745),
	(135, 34, 12, '온라인판매', 21000, 1600235745),
	(136, 34, 13, '온라인판매', 14500, 1600235745),
	(137, 34, 14, '온라인판매', 16800, 1600235745),
	(138, 34, 15, '온라인판매', 18150, 1600235745),
	(139, 34, 16, '온라인판매', 25000, 1600235745),
	(140, 34, 17, '온라인판매', 15800, 1600235745),
	(141, 34, 18, '온라인판매', 17500, 1600235745),
	(142, 34, 19, '온라인판매', 18900, 1600235745),
	(143, 34, 20, '온라인판매', 26000, 1600235745);
/*!40000 ALTER TABLE `unitprice_info` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
