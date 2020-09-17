<?php
	/*********************************************************************
	*    Description	:	Class for User page
	*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
	*    Date			:	2012. 05. 15
	*    Have a nice day, Good Luck to you ^^/
	*********************************************************************/
	class M_PATTERN {
		function __construct() {
			$this->hour_Arr = array(
				0		=> "0",
				1		=> "1",
				2		=> "2",
				3		=> "3",
				4		=> "4",
				5		=> "5",
				6		=> "6",
				7		=> "7",
				8		=> "8",
				9		=> "9",
				10		=> "10",
				11		=> "11",
				12		=> "12",
				13		=> "13",
				14		=> "14",
				15		=> "15",
				16		=> "16",
				17		=> "17",
				18		=> "18",
				19		=> "19",
				20		=> "20",
				21		=> "21",
				22		=> "22",
				23		=> "23",
			);

			$this->minType_Arr = array(1,2,3,4,5,6);

			$this->min_Arr = array(
				1	=> "00 ~ 10",
				2	=> "10 ~ 20",
				3	=> "20 ~ 30",
				4	=> "30 ~ 40",
				5	=> "40 ~ 50",
				6	=> "50 ~ 60",
			);

			$this->pattern_arr = array(
				1		=> "패턴 1",
				2		=> "패턴 2",
				3		=> "패턴 3",
				4		=> "패턴 4",
				5		=> "패턴 5",
			);

			$this->companyTable = array(
				5 => 'Pattern_gamevil',
				6 => 'Pattern_com2us',
				7 => 'Pattern_gcp'
			);
		}

		function __destruct() {
			
		}

		function getPatternInfoByIdx($table, $addWhere = ""){
			global $db_pattern;

			$query = 
				 '	SELECT * '
				.'	FROM  '.$table
				.'	WHERE 1=1 '
				.$addWhere
				//.'		AND status <> 99 '
				;

			$row = $db_pattern->getListSet($query);
			
			return $row;
		}

		function getPatternList($table,$addWhere = ""){
			global $db_pattern, $M_FUNC;
			global $PAGE;
			
			$where = " WHERE 1 = 1 ";
			if ($addWhere) 
			{
				$where .= $addWhere;
			}
			
			$query = " SELECT * "
						. " FROM ".$table
						. $where;
			$row = $db_pattern->getListSet($query);
			
			return $row;		
		}

		function getPatternInfoArr($table,$addWhere=''){
			global $db_pattern;

			$query = " SELECT * FROM ".$table
					." WHERE 1 = 1 "
					. $addWhere
					;
			$row = $db_pattern->getListSet($query);
			
			$result = array();
			for($i=0; $i<$row->size(); $i++){
				$row->next();

				$result[$row->get('idx')]['hour'] = $row->get('hour');
				$result[$row->get('idx')]['minType'] = $row->get('minType');
			}

			return $result;
		}

		function getNetworkDataBycompanyIdx($m_id, $where){
			global $db_pattern;

			$query = " SELECT * "
						." FROM ". $this->companyTable[$m_id]
						." WHERE 1=1 "
						. $where
						." ORDER BY hour asc, minType asc "
						;
			$row = $db_pattern->getListSet($query);
			
			return $row;
		}
	} //*** End of Class
?>