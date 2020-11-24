<?php
/*********************************************************************
*    Description	:	Class for HTML tag 
*    Developer	:	Min (min@minstory.kr / 010.3597.2794)
*    Date			:	2009. 03. 23

*    Have a nice day, Good Luck to you ^^/
*********************************************************************/

class M_HTML {
	function __construct() {
		
	}

	function __destruct() {
		
	}

	function body($action = 'begin') {
		if ($action == 'begin') {
			return "<body>";
		}
		else {
			return "</body></html>";
		}
	}

	function beginTable($id = '', $width = '100%', $border = '0', $class = '', $cellpadding = 0, $cellspacing = 0) {
		return "<table id=\"". $id ."\" width=\"". $width ."\" border=\"". $border ."\" cellpadding=\"". $cellpadding ."\" cellspacing=\"". $cellspacing ."\" class=\"". $class ."\">";
	}

	function endTable() {
		return "</table>";
	}

	function openDiv($id = '', $class = '', $style = '') {
		return "<div id=\"". $id ."\" class=\"". $class ."\" style=\"". $style ."\">";
	}

	function closeDiv() {
		return "</div>";
	}
	
	function b_Form($name, $action, $method='post', $upload=0, $onSubmit="", $target = "", $style = "") {
		if ($upload == 1) {
			$upload_str = "enctype=\"multipart/form-data\"";
		}
		else {
			$upload_str = "";
		}
		if ($onSubmit) {
			$onSubmit_str = "onSubmit=\"return ". $onSubmit ."\"";
		}
		else {
			$onSubmit_str = "";
		}
		if ($target) {
			$target_str = "TARGET=\"". $target ."\"";
		}
		else {
			$target_str = "";
		}
		$HTML = "<FORM ID=\"". $name ."\" NAME=\"". $name ."\" ACTION=\"". $action ."\" METHOD=\"". $method ."\" ". $target_str ." ". $upload_str ." ". $onSubmit_str ." ". $style .">";
		return $HTML;
	}
	function e_Form() {
		$HTML = "</form>";
		return $HTML;
	}
	
	function input_Mobile($type, $name, $default_value='', $class='', $style='', $add_event='', $onBlurEvent = '', $onFocusEvent = '') {
		if (!$class) {
			$class = "min_input_out";
		}
		if (!$onBlurEvent) {
			$onBlur_str = " onBlur=\"this.className='min_input_out';\"";
		} else {
			$onBlur_str = " onBlur=\"this.className='min_input_out'; ". $onBlurEvent ."\"";
		}

		if (!$onFocusEvent) {
			$onFocus_str = " onFocus=\"this.className='min_input_over';\"";
		} else {
			$onFocus_str = " onFocus=\"this.className='min_input_over'; ". $onFocusEvent ."\"";
		}
		$HTML = "<input type=\"". $type ."\" id=\"". $name ."\" name=\"". $name ."\" value=\"". $default_value ."\" class=\"". $class ."\" style=\"". $style ."\" ". $onFocus_str ." ". $onBlur_str ." ". $add_event ." />";

		return $HTML;
	}
	
	function input_Text($name, $default_value='', $class='', $style='', $add_event='', $onBlurEvent = '', $onFocusEvent = '') {
		if (!$class) {
			$class = "min_input_out";
		}
		if (!$onBlurEvent) {
			$onBlur_str = " onBlur=\"this.className='min_input_out';\"";
		} else {
			$onBlur_str = " onBlur=\"this.className='min_input_out'; ". $onBlurEvent ."\"";
		}

		if (!$onFocusEvent) {
			$onFocus_str = " onFocus=\"this.className='min_input_over';\"";
		} else {
			$onFocus_str = " onFocus=\"this.className='min_input_over'; ". $onFocusEvent ."\"";
		}
		$HTML = "<input type=\"text\" id=\"". $name ."\" name=\"". $name ."\" value=\"". $default_value ."\" class=\"". $class ."\" style=\"". $style ."\" ". $onFocus_str ." ". $onBlur_str ." ". $add_event ." />";

		return $HTML;
	}

	function input_Text2($name, $default_value='', $class='', $style='', $add_event='', $onBlurEvent = '', $onFocusEvent = '') {

		$HTML = "<input type=\"text\" id=\"". $name ."\" name=\"". $name ."\" value=\"". $default_value ."\" class=\"". $class ."\" style=\"". $style ."\" ". $onFocus_str ." ". $onBlur_str ." ". $add_event ." />";

		return $HTML;
	}

	function input_Text3($name, $default_value='', $class='', $style='', $add_event='', $onBlurEvent = '', $onFocusEvent = '') {

		$HTML = "<input type=\"text\" id=\"". $name ."1\" name=\"". $name ."[]\" value=\"". $default_value ."\" class=\"". $class ."\" style=\"". $style ."\" ". $onFocus_str ." ". $onBlur_str ." ". $add_event ." />";

		return $HTML;
	}
	
	function input_Password($name, $default_value='', $class='', $style='', $add_event='') {
		if (!$class) {
			$class = "min_input_out";
		}
		$HTML = "<input type=\"password\" id=\"". $name ."\" name=\"". $name ."\" value=\"". $default_value ."\" class=\"". $class ."\" style=\"". $style ."\" onfocus=\"this.className='min_input_over';\" onblur=\"this.className='min_input_out';\" ". $add_event ." />";

		return $HTML;
	}
	function input_Password2($name, $default_value='', $class='', $style='', $add_event='') {

		$HTML = "<input type=\"password\" id=\"". $name ."\" name=\"". $name ."\" value=\"". $default_value ."\" class=\"". $class ."\" style=\"". $style ."\" onfocus=\"this.className='min_input_over';\" onblur=\"this.className='min_input_out';\" ". $add_event ." />";

		return $HTML;
	}

	function input_Hidden($name, $default_value='') {
		$HTML = "<input type=\"hidden\" id=\"". $name ."\" name=\"". $name ."\" value=\"". $default_value ."\" />";

		return $HTML;
	}

	function input_File($name, $default_value='', $class='', $style='', $add_event='') {
		if (!$class) {
			$class = "min_input_out";
		}
		$HTML = "<input type=\"file\" name=\"". $name ."\" value=\"". $default_value ."\" class=\"". $class ."\" style=\"". $style ."\" onfocus=\"this.className='min_input_over';\" onblur=\"this.className='min_input_out';\" ". $add_event ." />";

		return $HTML;
	}

	function input_File2($name, $default_value='', $class='') {
		$HTML = "<div class=\"filebox\">"
				."<input class=\"upload-name\" value=\"파일선택\" disabled=\"disabled\" style=\"width:300px; margin-right:5px;\">"
				."<label for=\"ex_filename\" style=\"margin-bottom:0px;\">Upload</label>"
				."<input type=\"file\" id=\"".$name."\" name=\"". $name ."\" class=\"upload-hidden\">"
				."</div>";

		return $HTML;
	}

	function input_Button($name, $value, $class='', $style='', $add_event='') {

		$HTML = "<input type=\"button\" name=\"". $name ."\" value=\"". $value ."\" class=\"". $class ."\" style=\"". $style ."\" ". $add_event ." />";

		return $HTML;
	}
	
	function input_Submit($name, $value, $class='', $style='', $add_event='') {
		if (!$class) {
			$class = "min_submit";
		}
		$HTML = "<input type=\"submit\" name=\"". $name ."\" value=\"". $value ."\" class=\"". $class ."\" style=\"". $style ."\" ". $add_event ." />";

		return $HTML;
	}
	
	function input_Image($name, $path, $width='', $height='') {
		if ($width) {
			$width = " width=\"". $width ."\" ";
		}
		if ($height) {
			$height = " height=\"". $height ."\" ";
		}
		$HTML = "<input type=\"image\" name=\"". $name ."\" SRC=\"". $path ."\" ". $width . $height ." ALIGN=\"absmiddle\" />";

		return $HTML;
	}

	function textarea($name, $default_value='', $class='', $style='', $add_event='', $onblur_event = '') {
		if (!$class) {
			$class = "min_textarea_out";
		}
		if (!$onBlurEvent) {
			$onBlur_str = " onBlur=\"this.className='min_textarea_out';\"";
		} else {
			$onBlur_str = " onBlur=\"this.className='min_textarea_out'; ". $onBlurEvent ."\"";
		}

		if (!$onFocusEvent) {
			$onFocus_str = " onFocus=\"this.className='min_textarea_over';\"";
		} else {
			$onFocus_str = " onFocus=\"this.className='min_textarea_over'; ". $onFocusEvent ."\"";
		}
		
		$HTML = "<textarea id=\"". $name ."\" name=\"". $name ."\" class=\"". $class ."\" style=\"". $style ."\" ". $onFocus_str ." ". $onBlur_str ." ". $add_event ."/>". $default_value ."</textarea>";

		return $HTML;
	}

	
	/***************************************************
	*	Function name : input_Checkbox
	*	Parameter
			$name				:	Element's name		/	String or Character
			$value_arr			:	Element's values	/	Array
				- array type :
					array ($key1 => $value2, $key2 => $value2, ...)
			$split_char			:	Character for split	/	Character
			$default_value	:	Check it if this value is defined	/	Integer (in key)
	*	Return	:	HTML tag string
	***************************************************/
	function input_Radio($name, $value_arr='', $split_char='', $default_value='', $add_class = '', $add_event = '') {
		if (!$split_char) {
			$split_char = "&nbsp;&nbsp;";
		}
		$class = "";
		if($add_class != ""){
			$class = "class=".$add_class;
		}

		$i = 0;
		$HTML = "";
		if (is_array($value_arr)) {
			foreach($value_arr as $key => $value) {
				if ($key == $default_value) {
					$checked = "checked";
				}
				else {
					$checked = "";
				}
				$HTML .= "<input type=\"radio\" \"".$class."\" id=\"". $name ."\" name=\"". $name ."\" value=\"". $key ."\" ". $checked ." ". $add_event ." />". $value;
				if ($i < sizeof($value_arr)-1) {
					$HTML .= $split_char;
				}
				$i++;
			}
		}
		else {
			$HTML = "Please insert parameter array type.";
		}
		return $HTML;
	}

	function input_Radio2($name, $value_arr='', $default_value='', $add_event='', $class='', $brYn='') {

		$i = 0;
		$HTML = "";
		if (is_array($value_arr)) {
			foreach($value_arr as $key => $value) {

				if ($key == $default_value) 	$checked = 'checked="checked"';
				else										$checked = '';

				$HTML .= '<span class="'.$class.'"><input type="radio" value="' . $key . '" id="' . $name.$key . '" name="' . $name . '"' . $checked.' ' .$add_event . ' /><label for="' . $name.$key. '" style="padding:0 12px 0 5px; vertical-align:middle;">'.$value.'</label></span>';
				if($brYn != ''){
					$HTML .= "<br/>";
				}
				$i++;
			}
		}
		else {
			$HTML = "Please insert parameter array type.";
		}

		return $HTML;
	}

	function input_Radio3($name, $value_arr='', $split_char='', $default_value='', $add_class = '', $add_event = '') {
		if (!$split_char) {
			$split_char = "&nbsp;&nbsp;";
		}
		$class = "";
		if($add_class != ""){
			$class = "class=".$add_class;
		}

		$i = 0;
		$HTML = "";
		if (is_array($value_arr)) {
			foreach($value_arr as $key => $value) {
				if ($key == $default_value) {
					$checked = "checked";
				}
				else {
					$checked = "";
				}
				$HTML .= "<input type=\"radio\" \"".$class."\" id=\"". $name ."\" name=\"". $name ."1\" value=\"". $key ."\" ". $checked ." ". $add_event ." />". $value;
				if ($i < sizeof($value_arr)-1) {
					$HTML .= $split_char;
				}
				$i++;
			}
		}
		else {
			$HTML = "Please insert parameter array type.";
		}
		return $HTML;
	}

	/***************************************************
	*	Function name : input_Checkbox
	*	Parameter
			$name				:	Element's name		/	String or Character
			$value_arr			:	Element's values	/	Array
				- array type :
					array ($key1 => $value2, $key2 => $value2, ...)
			$split_char			:	Character for split	/	Character
			$default_value	:	Check it if this value is defined	/	Array
				- array type :
					array ($key1, $key2, $key3, ...)
	*	Return	:	HTML tag string
	***************************************************/
	function input_Checkbox($name, $value_arr='', $split_char='', $default_value='', $add_event = '', $padding = 0, $class = '') {
		if (!$split_char) {
			$split_char = "&nbsp;&nbsp;";
		}
		$k = 0;
		$HTML = "";
		if (is_array($value_arr)) {
			foreach($value_arr as $key => $value) {
				$checked = "";
				if ($default_value) {
					if (is_array($default_value)) {
						for ($i = 0; $i < sizeof($default_value); $i++) {
							if ($key == $default_value[$i]) {
								$checked = "checked";
							}
						}
					} else {
						if (is_numeric($default_value)) {
							if ($key & $default_value) {
								$checked = "checked";
							}
						} else {
							if ($key == $default_value) {
								$checked = "checked";
							}
						}
					}
				}
				if($padding > 0) {
					$HTML .= '<label style="display:inline-block; width:'.$padding.'px;">';
				}
				$HTML .= "<input type=\"checkbox\" id=\"". $name ."\" name=\"". $name ."[]\" class=\"". $class ."\" value=\"". $key ."\" ". $checked ." ". $add_event ." /> ". $value;
				if ($k < sizeof($value_arr)-1) {
					$HTML .= $split_char;
				}
				if($padding > 0) {
					$HTML .= '</label>';
				}
				$k++;
			}
		}
		else {
			if ($value_arr == $default_value) {
				$checked = "checked";
			}
			else {
				$checked = "";
			}
			$HTML = "<input type=\"checkbox\" id=\"". $name ."\" name=\"". $name ."\" value=\"". $value_arr ."\" ". $checked ." ". $add_event ." />";
			//echo "Please insert parameter array type.";
		}
		return $HTML;
	}

	/***************************************************
	*	Function name : _Select
	*	Parameter
			$name				:	Element's name		/	String or Character
			$value_arr			:	Element's values	/	Array
				- array type :
					array ($key1 => $value2, $key2 => $value2, ...)
			$default_value	:	Check it if this value is defined	/	Integer (in key)
	*	Return	:	HTML tag string
	***************************************************/
	function _Select($name, $value_arr='', $default_value='', $add_event = '', $style = '') {
		if (is_array($value_arr)) {
			$HTML = "<SELECT ID=\"". $name ."\" NAME=\"". $name ."\" ". $add_event ." CLASS=\"min_selectbox\" STYLE = \"". $style ."\">";
			foreach($value_arr as $key => $value) {
				if ($key == $default_value) {
					$checked = "selected";
				}
				else {
					$checked = "";
				}
				$HTML .= "<OPTION VALUE=\"". $key ."\" ". $checked .">". $value ."</OPTION>";
			}
			$HTML .= "</SELECT>";
		}
		else {
			echo "Please insert parameter array type.";
		}
		return $HTML;
	}

	function _Select2($name, $value_arr='', $default_value='', $class = '', $add_event = '', $style = '') {
		if (is_array($value_arr)) {
			$HTML = '<select id="' . $name . '" name="' . $name . '" class="' . $class . '"'.$add_event . $style.'>';
			foreach($value_arr as $key=>$value) {
				if ($key == $default_value) $checked = ' selected="selected"';
				else $checked = '';
				$HTML .= '<option value="' . $key . '"' . $checked . '>' . $value . '</option>';
			}
			$HTML .= '</select>';
		}
		else {
			echo "Please insert parameter array type.";
		}
		return $HTML;
	}

	function iframe($id, $width, $height, $src = "about:blank;", $option = '') {
		$HTML = "<IFRAME ID=\"". $id ."\" NAME=\"". $id ."\" SRC=\"". $src ."\" WIDTH=\"". $width ."\" HEIGHT=\"". $height ."\" ". $option ." FRAMEBORDER=\"no\" BORDER=\"no\"></IFRAME>";
		return $HTML;
	}



}


?>