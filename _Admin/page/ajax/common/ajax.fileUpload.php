<?php
/*********************************************************************
*	Description	:	Ajax upload Files
*	Developer		:	Min (min@minstory.kr / 010.3597.2794)
*	Date				:	2014. 06. 13
*	Have a nice day, Good Luck to you ^^/
*********************************************************************/
@include_once 'Inc/inc.include.php';

$MENU_ID = $M_FUNC->M_Filter(POST, 'MENU_ID');
$regdate	= time();
$dateYM	= date('Ym', $regdate);
$dateD		= date('d', $regdate);
$dateYmd = $dateYM.$dateD;

$output_dir = FILEPATH .'/tmpData/';
/*
if(!is_dir(FILEPATH .'/'. $MENU_ID)) {
	mkdir(FILEPATH .'/'. $MENU_ID, 0755);
	chown(FILEPATH .'/'. $MENU_ID, 'nginx');
}
if(!is_dir(FILEPATH .'/'. $MENU_ID .'/'. $dateYM)) {
	mkdir(FILEPATH .'/'. $MENU_ID .'/'. $dateYM, 0755);
	chown(FILEPATH .'/'. $MENU_ID .'/'. $dateYM, 'nginx');
}
if(!is_dir(FILEPATH .'/'. $MENU_ID .'/'. $dateYM .'/'. $dateD)){
	mkdir(FILEPATH .'/'. $MENU_ID .'/'. $dateYM .'/'. $dateD, 0755);
	chown(FILEPATH .'/'. $MENU_ID .'/'. $dateYM .'/'. $dateD, 'nginx');
}
*/

if(isset($_FILES['myfile'])) {
	$ret = array();
	$error =$_FILES['myfile']['error'];
    
	if(!is_array($_FILES["myfile"]['name'])) {
		$RandomNum  = md5(uniqid(time()));
		$ImageName = str_replace(' ','-',strtolower($_FILES['myfile']['name']));
		$ImageType = $_FILES['myfile']['type']; //"image/png", image/jpeg etc.
		$ImageExt = substr($ImageName, strrpos($ImageName, '.'));
		$ImageExt       = str_replace('.','',$ImageExt);
		$ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
		$newFileName	= $RandomNum .'.'. $ImageExt;
		move_uploaded_file($_FILES['myfile']['tmp_name'],$output_dir. $newFileName);
		$ret[]= $newFileName;

	} else {
		$fileCount = count($_FILES['myfile']['name']);
		for($i=0; $i < $fileCount; $i++) {
			$seq = $i + 1;
			$RandomNum = time();
			$ImageName = str_replace(' ','-',strtolower($_FILES['myfile']['name'][$i]));
			$ImageType = $_FILES['myfile']['type'][$i]; //"image/png", image/jpeg etc.
			$ImageExt			= substr($ImageName, strrpos($ImageName, '.'));
			$ImageExt			= str_replace('.','',$ImageExt);
			$ImageName     = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
			$newFileName	= 'M'. $seq .'_'. $regdate .'.'. $ImageExt;
			$ret[$newFileName]= $output_dir.$newFileName;
			move_uploaded_file($_FILES['myfile']['tmp_name'][$i],$output_dir.$newFileName );
		}
	}	
    echo json_encode($ret);
}


?>