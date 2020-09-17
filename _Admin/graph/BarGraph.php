<?php
	$width = $_POST['img_width'];
	$height = $_POST['img_height'];
	$key = $_POST['key'];	//serviceIdx
	$value = $_POST['value']; //월별 금액
	
	$data = $value;
	
	//인자값으로 받은 사이즈로 빈 이미지를 생성한다.
	$image = imagecreate($width, $height);

	//색지정, white에 대한 값을 가져옴(0), navy(1), black(2), gray(3)
	//imagecolorallocate : 주어진 이미지에 사용될 RGB값을 지정한다.
	$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
	$navy = imagecolorallocate($image, 0x00, 0x00, 0x80);
	$black = imagecolorallocate($image, 0x00, 0x00, 0x00);
	$gray = imagecolorallocate($image, 0xC0, 0xC0, 0xC0);

	//레이아웃
	$maxval = max($data);	//배열중 max 값 가져오기
	$nval = sizeof($data);	//배열의 크기
	$vmargin = 20;
	$hmargin = 55;
	$margin = 10;
	$base = floor(($width - $hmargin) / $nval); 
	$ysize = $height - 2 * $vmargin;
	$xsize = $nval * $base;

	//제목
	$titlefont = 3;
	$title = "Last 4 Month Usage";
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
	$ngrid = 4;
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
		imagestring($image, $labelfont, $xpos, $ypos-(int)($txtht/2), number_format($ydat), $black);

		if(!($i==0) && !($i > $ngrid)){
			//선 긋기
			imageline($image, $hmargin + $margin, $ypos, $hmargin + $xsize - $margin, $ypos, $gray);
		}
	}

	//서비스별 월별 요금에서 최근 4개월 내용중 3개월 이하일 경우 넓이 조절
	if(count($data) < 4){
		$fill_width = 45;
	} else $fill_width = 30;

	//columns and x labaels
	$padding = 3;
	$yscale = $ysize / (($ngrid +1) * $dydat);
	
	for($i=0; list($xval, $yval) = each($data); $i++){
		// vertical columns
		$ymax = $vmargin + $ysize;
		$ymin = $ymax - (int)($yval * $yscale);
		$xmax = $hmargin + ($i + 1) * $base - $padding;
		$xmin = $hmargin + $i * $base + $padding;
		//내부가 채워진 사각형을 그린다.
		imagefilledrectangle($image, $xmin+$fill_width, $ymin, $xmax-$fill_width, $ymax, $navy);

		//x labels
		$txtsz = imagefontwidth($labelfont) * strlen($xval);
		$xpos = $xmin + (int)(($base - $txtsz) /2);
		$xpos = max($xmin, $xpos);
		$ypos = $ymax + 3;
		imagestring($image, $labelfont, $xpos, $ypos, date('Y.m', strtotime($xval)), $black);

		imagestring($image, $labelfont, $xpos-5, $ymin-15, number_format($yval), $black);
	}

	//plot frame
	//테두리 프레임, 사각형 그리기
	imagerectangle($image, $hmargin+$margin, $vmargin, $hmargin + $xsize-$margin, $vmargin + $ysize, $black);

	$date = array_flip($data);

	//이미지 생성하여 파일로 저장
	//header("Content-type: image/png");
	$path = "/home/gamebill/www/_Admin/images/billing_image/".max($date)."_".$key."_price.png";
	//PNG 파일로 저장한다.
	imagePNG($image, $path);
	//imagePNG($image, $path);
	//생성된 이미지 객체를 메모리에서 해제한다.
	imagedestroy($image);

?>