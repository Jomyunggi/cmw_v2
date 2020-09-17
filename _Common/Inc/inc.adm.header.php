<?php include_once realpath(dirname(__FILE__) . '/inc.include.php');  ?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!--meta name="viewport" content="width=device-width, initial-scale=1"-->
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="<?php echo CSS_DIR; ?>/aux_common.css?afte11" rel="stylesheet">
    <link href="<?php echo CSS_DIR; ?>/aux_plugin.css" rel="stylesheet">
    <link href="<?php echo CSS_DIR; ?>/aux_admin.css?info" rel="stylesheet">
	<link href="<?php echo CSS_DIR; ?>/popup.css?after" rel="stylesheet">
    <link href="<?php echo CSS_DIR; ?>/jquery-ui1.11.4.min.css" rel="stylesheet">
	
	<script src="<?php echo JS_DIR; ?>/jquery-1.12.3.min.js"></script>
	<script src="<?php echo JS_DIR; ?>/jquery-ui1.11.4.min.js"></script>
	<script src="<?php echo JS_DIR; ?>/jquery.ui.datepicker.js"></script>
	<script src="<?php echo JS_DIR; ?>/popup.js?after1"></script>
	<script>
		document.onkeydown = trapRefresh;
        function trapRefresh(){
                if(event.keyCode == 116){
                        event.keyCode = 0;
                        event.cancelBubble = true;
                        event.returnValue = false;
                        document.location.reload();
                }
        }
	</script>
    <title>C.M.W & Shim v2 관리자사이트</title>
</head>

<body>
