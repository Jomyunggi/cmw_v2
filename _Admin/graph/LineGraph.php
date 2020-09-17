<?php
	$width = $_POST['img_width'];
	$height = $_POST['img_height'];
	$key = $_POST['key'];
	$value = $_POST['value'];
	$data = $value;	

	$image = imagecreatetruecolor($width, $height);
	
	$black = imagecolorallocate($image, 0x00, 0x00, 0x00);
	$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
	$navy = imagecolorallocate($image, 0x00, 0x00, 0x80);
	$gray = imagecolorallocate($image, 0xC0, 0xC0, 0xC0);
	$red = imagecolorallocate($image, 255, 0, 0);

	//서버대수에 따른 라인잡는 부분을 처리하기 위한
	$lastline = 0;
	if(max($data) < 5){
		$maxval = 4;
		$decimal = 1;
	} else {
		$maxval = max($data) - (substr(max($data), -1) - 9);
		$decimal = (int)(($maxval+1) / 5);
	}

	//레이아웃
	//$maxval = max($data) + 4;	//배열중 max 값 가져오기
	$nval = sizeof($data);	//배열의 크기
	$vmargin = 20;
	$hmargin = 28;
	$base = floor(($width - $hmargin) / $nval); 
	$ysize = $height - 2 * $vmargin;
	$xsize = $nval * $base;
	
	//배경색 흰색으로 
	imagefill($image, 0, 0, $white);

	//제목
	$titlefont = 4;
	$title = "Number of Servers in ".date('F', strtotime('1 day ago', time()));
	//폰트 넓이 값 얻는다.
	$txtsz = imagefontwidth($titlefont) * strlen($title);
	$xpos = (int)($hmargin + ($xsize - $txtsz)/2);
	$xpos = max(1, $xpos);
	$ypos = 3;
	//글쓰기 (제목)
	imagestring($image, $titlefont, $xpos, $ypos, $title, $black);
	
	//y labels and grid lines
	// y 격자 선, 격자 선에 해당되는 값
	$labelfont = 2;
	$ngrid = $maxval; //number of grid lines
	$dydat = $maxval / $ngrid;
	$dypix = $ysize / ($ngrid +1);
	
	for($i=0; $i<=($ngrid + 1); $i++){
		$ydat = (int)($i*$dydat);
		$ypos = $vmargin + $ysize - (int)($i*$dypix);
		$txtsz = imagefontwidth($labelfont) * strlen($ydat);
		//폰트 높이 값 얻는다.
		$txtht = imagefontheight($labelfont);
		$xpos = (int)(($hmargin - $txtsz) /2);
		$xpos = max(1, $xpos);

		if($i == ($ngrid + 1)){
			$firstYpos = $ypos;
		}

		if($i % $decimal == 0){
			imagestring($image, $labelfont, $xpos, $ypos-(int)($txtht/2), $ydat, $black);
		}

		if(!($i==0) && !($i > $ngrid)){
			//선 긋기
			if($i % $decimal == 0){
				imageline($image, $hmargin, $ypos, $hmargin + $xsize, $ypos, $gray);
			}
		}
	}

	//columns and x labaels
	$padding = 3;
	$yscale = $ysize / (($ngrid +1) * $dydat);
	
	for($i=0; list($xval, $yval) = each($data); $i++){
		// vertical columns
		$ymax = $vmargin + $ysize;
		$ymin = $ymax - (int)($yval * $yscale);
		$xmax = $hmargin + ($i + 1) * $base - $padding;
		$xmin = $hmargin + $i * $base + $padding;

		$location['x'][$i] = $xmin+6;
		$location['y'][$i] = $ymin;

		//내부가 채워진 원,호를 그린다.
		imagefilledarc($image, $xmin+6 , $ymin, 5, 5, 0, 0, 0xFF0000, IMG_ARC_PIE);

		if($i != 0){
			imageline($image, $location['x'][$i-1], $location['y'][$i-1], $location['x'][$i], $location['y'][$i], $red);
		}

		//x labels
		if($i == 0 || $xval % 3 == 1){
			$txtsz = imagefontwidth($labelfont) * strlen($xval);
			$xpos = $xmin + (int)(($base - $txtsz) /2);
			$xpos = max($xmin, $xpos);
			$ypos = $ymax + 3;
			imagestring($image, $labalfont, $xpos, $ypos, $xval, $black);
		}
	}

	//테두리 프레임, 사각형 그리기
	imagerectangle($image, $hmargin, $firstYpos+$lastline, $hmargin + $xsize, $vmargin + $ysize, $black);
	 
	$date = mktime(0,0,0, date('m'), 1, date('Y'));
	$prev_month = strtotime("-1 month", $date);

	//이미지 생성하여 파일로 저장
	//header("Content-type: image/png");
	//$path = "D:/Project/gamevil_jmg/_Admin/img_test/".date('Ym', $prev_month)."_".$key."_server.png";
	$path = "/home/gamebill/www/_Admin/images/billing_image/".date('Ym', $prev_month)."_".$key."_server.png";
	imagepng($image, $path);
	//생성된 이미지 객체를 메모리에서 해제한다.
	imagedestroy($image);
?>