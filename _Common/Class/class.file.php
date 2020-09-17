<?php
/*********************************************************************
*    Description	:	Class for File Controll 
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2012. 10. 25

*    Have a nice day, Good Luck to you ^^/
*********************************************************************/

class M_FILE {
	function __construct() {
		
	}

	function __destruct() {
		
	}

	/****************************************
	*	파일이 이미지인지 체크
	*****************************************/
	function isImage( $srcimg ) {
		if(is_file($srcimg)) {
			$image_info = @getimagesize($srcimg);

			switch ($image_info['mime']) {
				case 'image/gif': return true; break;
				case 'image/jpeg': return true; break;
				case 'image/png': return true; break;
				case 'image/bmp': return true; break;
				default : return false; break;
			}
		} else {
			return false;
		}
	}

	function deleteFile($filePath) {
		@unlink($filePath);
	}

	function resize_img_upload($img, $width, $height, $new_width, $new_height, $file_dir){
		$origin_file = $file_dir ."/". $img;
		$img2 = $new_width."_".$img;

		include_once('Class/class.phpthumbnail.php');
		$thumb = new M_phpthumbnail;
		$thumb -> Thumbsize = 120;
		$thumb -> Thumblocation = $file_dir . "/";
		//$thumb -> Thumbprefix = $new_width.'_';
		$thumb -> Thumbsaveas = 'png';
		$thumb -> Thumbfilename = $img2;
		//$thumb -> Cropimage = array(2,0,20,20,35,35);
		$thumb -> Cropimage = array(3,0,0,0,0,0);
		$thumb -> Createthumb($origin_file, 'file');

		/*
		$resize_width = $new_width;
		$resize_height = $new_height;
		$thumbnail_type = 'ratio';
		
		if($resize_width > 0 && $width >= $resize_width) $width_per = $resize_width / $width;
		else $width_per = $width / $resize_width;

		if($resize_height>0 && $height >= $resize_height) $height_per = $resize_height / $height;
		else $height_per = $height / $resize_height;

		if($thumbnail_type == 'ratio') {
			if($width_per>$height_per) $per = $height_per;
			else $per = $width_per;
			$resize_width = $width * $per;
			$resize_height = $height * $per;
		} else {
			if($width_per < $height_per) $per = $height_per;
			else $per = $width_per;
		}

		if(!$per) $per = 1;

		$new_width = (int)($width * $per);
		$new_height = (int)($height * $per);

		
		if($thumbnail_type == 'crop') {
			$x = (int)($resize_width/2 - $new_width/2);
			$y = (int)($resize_height/2 - $new_height/2);
		} else {
			$x = 0;
			$y = 0;
		}

		*/
		//echo $x . " || ". $y; exit;
		/*
		if($width >= $height) {
			$swidth=$size;
			$sheight=@round(($size/$width)*$height);
		} else {
			$sheight=$size;
			$swidth=@round(($size/$height)*$width);
		}
		*/
		//$img2 = $new_width."_".$img;

		//M_File::resizeimage($resize_width,$resize_height,"$file_dir/$img2", "$file_dir/$img", $x, $y);

		return $img2;
	}

	function resizeimage($rewidth,$reheight,$smallfile,$picture, $x = 0, $y = 0) {
		$picsize=getimagesize($picture);

		if($picsize[0]>$rewidth){
				$width=$picsize[0]-$rewidth;
				$aa=$width/$picsize[0];
				$picsize[0]=intval($picsize[0]-$picsize[0]*$aa);
				$picsize[1]=intval($picsize[1]-$picsize[1]*$aa);
		}

		if($picsize[1]>$reheight){
				$height=$picsize[1]-$reheight;
				$bb=$height/$picsize[1];
				$picsize[0]=intval($picsize[0]-$picsize[0]*$bb);
				$picsize[1]=intval($picsize[1]-$picsize[1]*$bb);
		}

		ini_set('memory_limit', '64M');
		$dstimg=ImageCreateTrueColor($rewidth,$reheight);
		$white = @imagecolorallocate($dstimg, 255,255,255);
		@imagefilledrectangle($dstimg,0,0,$rewidth-1,$reheight-1,$white);
		
		if($picsize[2]==1) {
//                @header("Content-Type: imgage/gif");
				$srcimg=@ImageCreateFromGIF($picture);
				ImageCopyResized($dstimg, $srcimg, $x, $y, 0,0,$rewidth,$reheight,ImageSX($srcimg),ImageSY($srcimg));
//                Imagegif($dstimg,$smallfile,76);
				Imagejpeg($dstimg,$smallfile,100);
		}
		elseif($picsize[2]==2) {
//                @header("Content-Type: images/jpeg");
				$srcimg=ImageCreateFromJPEG($picture);
//              ImageCopyResized($dstimg, $srcimg,0,0,0,0,$rewidth,$reheight,ImageSX($srcimg),ImageSY($srcimg));
				ImageCopyResampled($dstimg, $srcimg, $x, $y, 0,0,$rewidth,$reheight,ImageSX($srcimg),ImageSY($srcimg));

				Imagejpeg($dstimg,$smallfile,100);
		}
		elseif($picsize[2]==3) {
//                @header("Content-Type: images/png");
				$srcimg=ImageCreateFromPNG($picture);
				ImageCopyResized($dstimg, $srcimg, $x, $y,0,0,$rewidth,$reheight,ImageSX($srcimg),ImageSY($srcimg));
//                Imagepng($dstimg,$smallfile,76);
				Imagejpeg($dstimg,$smallfile,100);
		}
		@ImageDestroy($dstimg);
		@ImageDestroy($srcimg); 
	}

	function download($filePath, $fileName) {
		Header("Content-type: file/unknown");
		Header("Content-Length: ".(string)(filesize($filePath)));
		Header("Content-Disposition: attachment; filename=\"". $fileName ."\"");
		Header("Content-Description: PHP3 Generated Data");
		Header("Pragma: no-cache");
		Header("Expires: 0");

		$fp = fopen($filePath,'r');
		fpassthru($fp);
		fclose($fp);
	}

	function removeBracket($str) {
		$str = str_replace('["', '', $str);
		$str = str_replace('"]', '', $str);
		return $str;
	}

	function itemUploadToFTP($FTP_INFO, $filePathList, $dateYmd='') {
		//***** Declare variable *****
		if ($dateYmd) {
			$regdate = $dateYmd;
			$yearmonth = date("Ym", $regdate);
			$day = date("d", $regdate);
			$ymd = $yearmonth.$day;
		}
		else {
			$regdate = time();
			$yearmonth = date("Ym", $regdate);
			$day = date("d", $regdate);
			$ymd = $yearmonth.$day;
		}

		$FTP_SERVER	= $FTP_INFO["SERVER"];
		$FTP_USERID		= $FTP_INFO["USERID"];
		$FTP_PASSWD	= $FTP_INFO["PASSWD"];
		$FTP_PORT		= $FTP_INFO["PORT"];

		$Connect = ftp_connect($FTP_SERVER, $FTP_PORT) or die("[Error] Connection Failed from $FTP_SERVER"); 
		$LOGIN = ftp_login($Connect, $FTP_USERID, $FTP_PASSWD) or die("[Error] Authentication Failed from $FTP_SERVER to $FTP_USERID"); 
		ftp_pasv($Connect, true);

		ftp_chdir($Connect, "www/uploadFiles");
		$contents = ftp_nlist($Connect, ".");

		if(!in_array($yearmonth, $contents))
		{
			ftp_mkdir($Connect, $yearmonth);
		}

		ftp_chdir($Connect, $yearmonth);

		$filePath_array = explode(",", $filePathList);
		for ($i = 0; $i < sizeof($filePath_array); $i++) {
			$file_path = $filePath_array[$i];
			$upload_file = FILEPATH ."/tmpData/". $file_path;
			//$result = ftp_nb_put($Connect, $filePath_array[$i], $upload_file, FTP_BINARY);
			$result = ftp_put($Connect, $filePath_array[$i], $upload_file, FTP_BINARY);
		}

		ftp_quit($Connect);
		ftp_close($Connect);
	}

	function itemUploadToFTP2($filePathList, $dateYmd='') {
		//***** Declare variable *****
		if ($dateYmd) {
			$regdate = $dateYmd;
			$yearmonth = date("Ym", $regdate);
			$day = date("d", $regdate);
			$ymd = $yearmonth.$day;
		}
		else {
			$regdate = time();
			$yearmonth = date("Ym", $regdate);
			$day = date("d", $regdate);
			$ymd = $yearmonth.$day;
		}

		$FTP_SERVER		= "58.229.141.19";
		$FTP_USERID		= "mk_file";
		$FTP_PASSWD		= "dpazmf!#%";
		$FTP_PORT		= "21";

		$Connect = ftp_connect($FTP_SERVER, $FTP_PORT) or die("[Error] Connection Failed from $FTP_SERVER"); 
		$LOGIN = ftp_login($Connect, $FTP_USERID, $FTP_PASSWD) or die("[Error] Authentication Failed from $FTP_SERVER to $FTP_USERID"); 
		ftp_pasv($Connect, true);

		ftp_chdir($Connect, "");
		$contents = ftp_nlist($Connect, ".");

		if(!in_array($yearmonth, $contents))
		{
			ftp_mkdir($Connect, $yearmonth);
		}

		ftp_chdir($Connect, $yearmonth);

		$filePath_array = explode(",", $filePathList);
		for ($i = 0; $i < sizeof($filePath_array); $i++) {
			$file_path = $filePath_array[$i];
			$upload_file = FILEPATH ."/tmpData/". $file_path;
			//$result = ftp_nb_put($Connect, $filePath_array[$i], $upload_file, FTP_BINARY);
			$result = ftp_put($Connect, $filePath_array[$i], $upload_file, FTP_BINARY);
		}

		ftp_quit($Connect);
		ftp_close($Connect);
	}


	function convertFile($fileNameList, $filePathList, $MENU_ID, $dateYmd='') {
		//***** Declare variable *****
		if ($dateYmd) {
			$regdate = $dateYmd;
			$yearmonth = date("Ym", $regdate);
			$day = date("d", $regdate);
			$ymd = $yearmonth.$day;
		}
		else {
			$regdate = time();
			$yearmonth = date("Ym", $regdate);
			$day = date("d", $regdate);
			$ymd = $yearmonth.$day;
		}
		
		//***** Copy & Change File Name *****
		if(!is_dir(FILEPATH ."/". $MENU_ID)) {
			mkdir(FILEPATH ."/". $MENU_ID, 0755);
			chown(FILEPATH ."/". $MENU_ID, "nginx");
		}
		if(!is_dir(FILEPATH ."/". $MENU_ID ."/". $yearmonth)) {
			mkdir(FILEPATH ."/". $MENU_ID ."/". $yearmonth, 0755);
			chown(FILEPATH ."/". $MENU_ID ."/". $yearmonth, "nginx");
		}
		if(!is_dir(FILEPATH ."/". $MENU_ID ."/". $yearmonth ."/". $day)){
			mkdir(FILEPATH ."/". $MENU_ID ."/". $yearmonth ."/". $day, 0755);
			chown(FILEPATH ."/". $MENU_ID ."/". $yearmonth ."/". $day, "nginx");
		}

		$fileName_array = explode(",", $fileNameList);
		$filePath_array = explode(",", $filePathList);
		$file_txt = "";
		for ($i = 0; $i < sizeof($fileName_array); $i++) {
			$seq = $i + 1;

			$file_name = $fileName_array[$i];
			$file_path = $filePath_array[$i];
			//$upload_file = FILEPATH ."/tmpData/". $file_name;
			$upload_file = FILEPATH ."/tmpData/". $file_path;
		
			$file_name_array = explode(".", $file_name);
			$s_point = count($file_name_array) - 1;
			$upload_check = $file_name_array[$s_point];
			$filetype = strtolower($upload_check);

			//$file = "M". $seq ."_". $regdate .".". $filetype;
			$file = $file_path;
			$change_file = FILEPATH ."/". $MENU_ID ."/". $yearmonth ."/". $day ."/". $file;

			copy($upload_file, $change_file);

			if ($i == 0) {
				//***** Make Thumbnail Image *****
				if ($filetype == "gif" || $filetype == "jpg" || $filetype == "jpeg" || $filetype == "png" || $filetype == "bmp") {
					$image_size = getimagesize("$change_file");
					$width = $image_size[0];  //가로
					$height = $image_size[1]; //세로

					$thumb_width = 120;
					$thumb_height = 120;

					$file2 = $this->resize_img_upload($file, $width, $height, $thumb_width, $thumb_height, FILEPATH ."/$MENU_ID/$yearmonth/$day");
				}

				else {
					$file2 = "no_image";
				}
			}

			$file_txt .= $file_name . "::+::" . $file ."::+::". $file2 ."::+::". $ymd ."::+::". $filetype ."\n";

			$this->deleteFile($upload_file);
		}

		return $file_txt;

	}

	function insertFileData($MENU_ID, $MENU_ID_idx, $fileType, $fileName_origin, $fileName_conv, $regDate_Ymd) {
		global $db;

		if ($fileName_origin) {
			$data = array (
				'MENU_ID'				=> $MENU_ID,
				'MENU_ID_idx'			=> $MENU_ID_idx,
				'type'					=> $fileType,
				'fileName_origin'		=> $fileName_origin,
				'fileName_conv'		=> $fileName_conv,
				'regDate_Ymd'		=> time(),
				'regUnixtime'			=> time()
			);
			$db->insert(FILE_TABLE, $data);
		}
	}

	function insertFileArrData($MENU_ID, $MENU_ID_idx, $fileType, $fileName_origin_arr, $fileName_conv_arr) {

		if($fileName_origin_arr == "" || $fileName_conv_arr == "") {
			return;
		}
		$file_name_arr = explode(",", $fileName_origin_arr);
		$fileName_conv_arr = str_replace("[", "", $fileName_conv_arr);
		$fileName_conv_arr = str_replace("]", "", $fileName_conv_arr);
		$fileName_conv_arr = str_replace('"', "", $fileName_conv_arr);
		$file_path_arr = explode(",", $fileName_conv_arr);

		if(count($file_name_arr) != count($file_path_arr)) {
			return;
		}

		for($i=0; $i<count($file_name_arr); $i++) {
			$this->insertFileData($MENU_ID, $MENU_ID_idx, $fileType, $file_name_arr[$i], $file_path_arr[$i], "", time());
		}
	}

	function getFileList($MENU_ID, $MENU_ID_idx) {
		global $db;

		$query = " SELECT * FROM File_Data WHERE MENU_ID = '". $MENU_ID ."' AND MENU_ID_idx = '". $MENU_ID_idx ."'";
		$row = $db->getListSet($query);
		return $row;
	}

	function getFileInfoByIdx($idx) {
		global $db;

		$query = " SELECT * FROM ". FILE_TABLE . " WHERE idx = '". $idx ."'";
		$row = $db->getListSet($query);
		if ($row->size() > 0) {
			$row->next();
			return $row;
		} else {
			return null;
		}
	}

	function deleteFileData2($m_id) 
	{
		global $db;

		$query = " SELECT * FROM ". FILE_TABLE . " WHERE parentIdx = '". $m_id ."'";
		$row = $db->getListSet($query);
		for($i=0;$i<$row->size();$i++)
		{
			$row->next();
			$filePath = FILEPATH ."/". $row->get("MENU_ID") ."/". date("Ym", $row->get("regUnixtime")) ."/". date("d", $row->get("regUnixtime")) ."/". $row->get("fileName_conv");
			if (is_file($filePath)) {
				$this->deleteFile($filePath);
			}
			$filePath = FILEPATH ."/". $row->get("MENU_ID") ."/". date("Ym", $row->get("regUnixtime")) ."/". date("d", $row->get("regUnixtime")) ."/". $row->get("fileName_thumb");
			if (is_file($filePath)) {
				$this->deleteFile($filePath);
			}
		}

		$where = " WHERE parentIdx = ". $m_id;
		$db->delete("File_Data", $where);
	}

	function deleteFileData($fileIdx) {
		global $db;

		$query = " SELECT * FROM ". FILE_TABLE . " WHERE idx = '". $fileIdx ."'";
		$row = $db->getListSet($query);
		$row->next();
		$filePath = FILEPATH ."/". $row->get("MENU_ID") ."/". date("Ym", $row->get("regUnixtime")) ."/". date("d", $row->get("regUnixtime")) ."/". $row->get("fileName_conv");
		if (is_file($filePath)) {
			$this->deleteFile($filePath);
		}
		$filePath = FILEPATH ."/". $row->get("MENU_ID") ."/". date("Ym", $row->get("regUnixtime")) ."/". date("d", $row->get("regUnixtime")) ."/". $row->get("fileName_thumb");
		if (is_file($filePath)) {
			$this->deleteFile($filePath);
		}
		$db->delete(FILE_TABLE, " WHERE idx = ". $fileIdx);
	}
}

?>