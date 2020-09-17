<?php
@include_once 'Inc/inc.include.php';

$file_name	 = $M_FUNC->M_Filter(POST, 'fileName');
$output_dir = FILEPATH . '/tmpData/';

$remove_file = $output_dir . $file_name;

if(file_exists($remove_file)) {
	if(unlink($remove_file)) {
		echo 'OK';
	} else {
		echo 'FAIL';
	}
} else {
	echo 'FAIL';
}

exit();
?>