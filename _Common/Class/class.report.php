<?php
	/*********************************************************************
	*    Description	:	Class for User page
	*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
	*    Date			:	2012. 05. 15
	*    Have a nice day, Good Luck to you ^^/
	*********************************************************************/
	class M_REPORT {
		function __construct() {
					
		}

		function __destruct() {
			
		}

		function getCompanyArr($companyArr, $addWhere){
			global $db;
			
			if(is_array($companyArr)){
				$arr = $companyArr;
			} else {
				$arr = array();
			}

			$query = " select idx, companyName, senior "
					." from Company_Info "
					." where status = 1 "
					.$addWhere
					." order by idx asc ";
			$row = $db->getListSet($query);
			

			for($i=0; $i<$row->size(); $i++){
				$row->next();

				$arr[$row->get('senior')] = $row->get('companyName');
			}

			return $arr;
		}

		function getAnalysisOfReports($table, $term){
			global $db;

			$query = " SELECT itemName, keyword, sum(impress) as T_impress, sum(click) as T_click, sum(adPrice) as T_adPrice "
					." , sum(D_sale1) as T_D_sale1, sum(D_sales1) as T_D_sales1 "
					." FROM Report2020_". $table
					." WHERE date between ". date('Ymd', $this->TermArr[$term][0]) ." AND ". date('Ymd', $this->TermArr[$term][1])
					." GROUP BY itemName, keyword "
					." HAVING T_D_sale1 > 0 "
					." ORDER BY itemName, T_click desc"
					;
			$row = $db->getListSet($query);

			return $row;			
		}

	} //*** End of Class
?>