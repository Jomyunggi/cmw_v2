<?php
	ini_set('include_path', realpath(dirname(__FILE__) ."/../../_Common"));

	@include_once "Inc/inc.include.php";

	$db_pattern					= new M_DB("PATTERN");

	$table = $M_FUNC->M_Filter(POST, "table");
	$pattern = $M_FUNC->M_Filter(POST, "pattern");
	$p_act = $M_FUNC->M_Filter(POST, "p_act");
	$hour = $_POST['hour'];
	$minType = $_POST['minType'];
	$Tx = $_POST['Tx'];
	$Rx = $_POST['Rx'];

	if($p_act == "R"){
		for($i=0;$i<count($hour);$i++){
			$data = array(
				'pattern' => $pattern,
				'hour' => $hour[$i],
				'minType' => $minType[$i],
				'Tx'	=> $Tx[$i],
				'Rx' => $Rx[$i],
			);

			$db_pattern->insert($table, $data);
		}
		$M_JS->Go_URL('' , "등록되었습니다.");
	} else {
		$idx = $_POST['idx'];
		for($j=0;$j<count($hour);$j++){
			$udata = array(
				//'pattern' => $pattern,
				'hour' => $hour[$j],
				'minType' => $minType[$j],
				'Tx'	=> $Tx[$j],
				'Rx' => $Rx[$j],
			);
			$db_pattern->update($table, $udata, " WHERE idx = ".$idx[$j]);
		}
		$M_JS->Go_URL('' , "수정되었습니다.");
	}
?>