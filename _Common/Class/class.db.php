<?php
/*********************************************************************
*    Description	:	Class for DB Functions
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2011. 07. 06
*    Have a nice day, Good Luck to you ^^/
*********************************************************************/
class M_DB {

	private $db_host, $db_name, $db_user, $db_pwd, $db_conn;
	
	function __construct($s=null) {
		
		if($s == 'TEST'){
			$this->db_host		= DB_TEST_HOST;
			$this->db_name		= DB_TEST_NAME;
			$this->db_user		= DB_TEST_USER;
			$this->db_pwd		= DB_TEST_PASS;
		} else {
			$this->db_host		= DB_HOST;
			$this->db_name		= DB_NAME;
			$this->db_user		= DB_USER;
			$this->db_pwd		= DB_PASS;
		}
		
		$charset = "utf-8";
		
		$this->db_conn		= @mysql_connect($this->db_host, $this->db_user, $this->db_pwd) or die("Can't Connecting Database ". mysql_error());
		@mysql_select_db($this->db_name, $this->db_conn);
		
		mysql_query('set names utf8');

		if ($charset == "euc-kr") {
			mysql_query('set names euckr');
		}
	}

	function __destruct() {
		global $db;
		$this->disconnect();
		unset($db);
	}

	function disconnect() {
		//@mysql_close($this->db_conn) or die('MySQL 접속 닫기 에러 : ' . mysql_error());
	}

	function execute ($sql) {
		$sql			= trim($sql);
		$result		= mysql_query( $sql, $this->db_conn ) or die("에러 발생 : " .  mysql_error() . " <br><br>SQL문 : <br>" . $sql);				
		return $result;
	}

	function select ($field, $table, $where) {
		$sql			= "Select $field from $table $where";
		$result		= $this->execute($sql);
		return $result;
	}

	function get_rows_Record ($sql) {
		$result	=	mysql_query($sql, $this->db_conn) or die("에러 발생 : " .  mysql_error() . " <br><br>SQL문 : <br>" . $sql);
		$row		=	mysql_num_rows($result);
		return $row;
	}

	function object ($field, $table, $where) {
		$sql			= "Select $field from $table $where";
		$result		= $this->execute($sql);
		$row			= @mysql_fetch_object($result);
		return $row;
	}
	

	function cnt ($table, $where) {
		$sql			= "Select count(*) from $table $where";
		$result		= $this->execute($sql);
		$row			=  @mysql_fetch_row($result);
		if( $row[0] ) { return $row[0]; } else { return 0;}
	}

	function row ($field, $table, $where) {
		$sql			= "Select $field from $table $where ";
		$result		= mysql_query($sql, $this->db_conn) or die("에러 발생 : " .  mysql_error() . " <br>" . $sql);
		$row			= @mysql_num_rows($result);
		return $row;
	}		

	function sum ($field, $table, $where) {
		$sql			= "Select sum($field) from $table $where";
		$result		= $this->execute($sql);
		$row			=  @mysql_fetch_row($result);
		if( $row[0] ) { return $row[0]; } else { return 0;}
	}

	function insert ( $table, $data ) {
		while ( list($key, $val) = each($data) ) {
			$fields .= "$key, ";
			//now() 는 '' 사이에있으면 작동하지 않는다.
			if ( $val == 'now()' ) $values .= "$val, ";
			else $values .= "'" . addslashes($val) . "', ";
		}
		$fields = substr($fields, 0, -2);
		$values = substr($values, 0, -2);

		$sql			= "insert into $table ($fields) values ($values)";
		if($this->execute($sql)) { return true; } else { return false; }
	}

	function update ( $table, $data, $where ) {
		if ( is_array($data) ){
			$sets = "";
			while ( list($key, $val) = each($data) ) {
				//now() 는 '' 사이에있으면 작동하지 않는다.
				if ( $val == 'now()' ) $sets .= "$key = $val, ";
				else $sets .= "$key = '" . addslashes($val) . "', ";
			}
			$sets = substr($sets, 0, -2);
		}else{
			$sets = $data;
		}

		echo $sql				= "update $table set $sets $where";

		if($this->execute($sql)) { return true; } else { return false; }
	}
	
	function delete ($table, $where) {
		$sql				= "delete from $table $where";
		if($this->execute($sql)) { return true; } else { return false; }
	}
	
	function dropTable ($data) {
		$sql				= "drop table $data";
		if($this->execute($sql)) { return true; } else { return false; }
	}

	function createTable ($data) {
		$sql				= "create table $data";
		if($this->execute($sql)) { return true; } else { return false; }
	}

	function stripSlash ($st ) {
		$str				= trim($str);
		$str				= stripslashes($str);
		return $str;
	}

	function addSlash ($str) {
		$str				= trim($str);
		$str				= addslashes($str);
		if(empty( $str )) {
			$str			= "NULL";
		}
		return $str;
	}
	
	function &parseQuery($q) {
		// [d[field]] -> date_format 으로 치환
		$q = ereg_replace("\[d\[([a-zA-Z0-9_]*)\]\]", "date_format(\\1, '%Y-%m-%d %H:%i:%s') \\1", $q);
		// ....
		return $q;
	}

	function &getListSet($q) {
		if ( $q == '' ) $this->err('비어있는 질의문');
		$q =& $this->parseQuery($q);

		$l = new L_ListSet();

		$result = mysql_query( $q, $this->db_conn ) or die("에러 발생 : " .  mysql_error() . " <br><br>SQL문 : <br>" . $q);
		
		while ( $row = mysql_fetch_array($result) ) {
			$l->add($row);
		}
		mysql_free_result($result);

		return $l;
	}

	function getInsertID() {
		//$q = " SELECT ". $column ." FROM ". $table ." ORDER BY ". $column . " DESC LIMIT 1 ";
		//$result = mysql_query( $q, $this->db_conn ) or die("에러 발생 : " .  mysql_error() . " <br><br>SQL문 : <br>" . $q);
		return mysql_insert_id();
	}

	function getLastIdx($column, $table) {
		$q = " SELECT ". $column ." FROM ". $table ." ORDER BY ". $column . " DESC LIMIT 1 ";
		$result = mysql_query( $q, $this->db_conn ) or die("에러 발생 : " .  mysql_error() . " <br><br>SQL문 : <br>" . $q);
		$row			=  @mysql_fetch_row($result);
		if( $row[0] ) { return $row[0]; } else { return 0;}	
	}

}

class L_ListSet {
	var $list;
	var $rownum;

	/** 생성자 */
	function __construct() {
		$this->init();
	}

	function __destruct() {
	}

	/** 초기화 */
	function init($a = '') {
		if ( is_array($a) ) {
			$this->list = $a;
		} else {
			$this->list = array();
		}
		$this->rownum = -1;
	}

	/** 아이템 추가 */
	function add($value) {
		array_push($this->list, $value);
	}

	/** 결과 전체 사이즈 반환 */
	function size() {
		return count($this->list);
	}

	/**
	 * 리스트 커서 이동.
	 * 1 에서 시작한다.
	 */
	function next() {
		$this->rownum++;
		if ( @is_array($this->list[$this->rownum]) ) return true;
		else return false;
	}

	/**
	 * 리스트 커서를 강제 이동
	 * -1부터 시작한다. next 호출시 ++ 되기 때문이다
	 * 첫 next() 호출시 커서는 0 으로 시작되기때문
	 */
	function cursor($n) {
		if ( $n > -1 && $this->size() >= $n) {
			$this->rownum = $n - 1;
		} else if ( $this->size() < $n ) {
			die ('지정한 커서가 리스트 크기를 초과하였습니다');
		} else {
			$this->rownum = -1;
		}
	}
	function getCursor() {
		return $this->rownum + 1;
	}

	function hasMore() {
		return ($this->getCursor() < $this->size()) ? true : false;
	}

	/** 값 가져오기 */
	function &get($name) {
		//if ( $this->rownum <= 0 ) return null;
		return $this->list[$this->rownum][$name];
	}
	function &getString($name, $length = 0, $dep = '...', $search = '', $replace = '', $stripTags = 0) {
		if ( $length == 0 ) {
			$ret = (string) $this->get($name);
		} else {
			//require_once ("string.lib.php");
			//return L_String::strCut((string) $this->get($name), $length, $dep);
			if ( mb_strlen($this->get($name)) <= $length ) {
				$ret = ($stripTags ? strip_tags($this->get($name)) : $this->get($name));
			} else {
				$ret = mb_substr(($stripTags ? strip_tags($this->get($name)) : $this->get($name)), 0, $length, "euc_kr");
				if ( substr($ret, -1) == '?' ) $ret = substr($ret, 0, -1);
				$ret .= $dep;
			}
		}
		return ( $search == '' && $replace == '' ) ? $ret : str_replace($search, $replace, $ret);
	}
	function &getStringNl2Br($name) {
		return nl2br((string) $this->get($name));
	}
	function &getInt($name) {
		return (int) $this->get($name);
	}
	function &getBoolean($name) {
		$val = strtoupper($this->get($name));
		switch ( $val ) {
			case 'Y' : case '1' : case 'YES' : case 'TRUE' :
				return true;
				break;
			default :
				return false;
		}
	}
	function &getDate($name, $format = '') {
		$date = $this->get($name);
		if ( $format != '' && $date != '') {
			return (string) date($format, strtotime($date));
		} else {
			return (string) $date;
		}
	}

	function set($name, $val) {
		$this->list[$this->rownum][$name] = $val;
	}

	/** 리스트를 배열로 넘겨준다 */
	function &toArray() {
		return $this->list;
	}

	function rand() {
		$this->rownum = array_rand($this->list);
	}

	function addItem($item) {
		$item = $item->list;
		foreach($item as $value) {
			array_push($this->list, $value);
		}
	}

	// 리포트에서만 사용하는 날짜별 클릭, 노출수 합계 함수
	function sumItem($item, $column) {
		$n = $this->list;
		$i = $item->list;

		foreach($i as $key=>$value){
			$i[$key]['sViewCnt']	= intval($value['sViewCnt']);
			$i[$key]['viewCnt']		= intval($value['viewCnt']);
			$i[$key]['clickCnt']	= intval($value['clickCnt']);
		}

		$m = array_merge($n, $i);
		$r = array();

		$tmp_r = array_reduce($m, function ($a, $b) use ($column) {
			foreach($b as $key=>$value){
				if(is_numeric($key))	unset($b[$key]);
			}

			if(isset($a[$b[$column]])) {
				if(isset($a[$b[$column]]['sViewCnt']))	$a[$b[$column]]['sViewCnt'] += intval($b['sViewCnt']);
				if(isset($a[$b[$column]]['viewCnt']))		$a[$b[$column]]['viewCnt']	+= intval($b['viewCnt']);
				if(isset($a[$b[$column]]['clickCnt']))	$a[$b[$column]]['clickCnt'] += intval($b['clickCnt']);
			} else {
				$a[$b[$column]] = $b; 
			}

			return $a;
		});	

		$r_cnt = 0;
		foreach($tmp_r as $value) {
			$r[$r_cnt++] = $value;
		}

		$this->list = $r;

		return $this;
	}

	function orderItem($key, $order = "ASC") {
		$r = $this->list;		

		usort($r, function($a, $b) use ($key, $order){

			if($order == "DESC") {
				if($a[$key] < $b[$key]) {
					return 1;
				} else if($a[$key] > $b[$key]) {
					return -1;
				} else {
					return 0;
				}
			} else {
				if($a[$key] > $b[$key]) {
					return 1;
				} else if($a[$key] < $b[$key]) {
					return -1;
				} else {
					return 0;
				}
			}
		});

		$this->list = $r;

		return $this;
	}
}

?>