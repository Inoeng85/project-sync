<?php
function make_button($class,$value_button,$button_type,$button_width,$bttn_id,$onclick,$style_div,$style_button,$bttn_name=null,$event=null,$disabled=null){
	$class_name = get_class_css($class);

	$button_div_width = $button_width-20;

	//id button same with name button
	if($bttn_id != null){
		$id_div = "id=\"".$bttn_id."_div\"";
		$name_div = "name=\"".$bttn_id."_div\"";
		$id_span = "id=\"".$bttn_id."_span\"";
		$name_span = "name=\"".$bttn_id."_span\"";
		$id_button = "id=\"".$bttn_id."\"";
		$name_button = "name=\"".$bttn_id."\"";
		if($bttn_name != null){
			$name_div = "name=\"".$bttn_name."_div\"";
			$name_span = "name=\"".$bttn_name."_span\"";
			$name_button = "name=\"".$bttn_name."\"";
		}
	}

	if($onclick != null){
		$onclick = "onclick=\"$onclick\"";
	}
	else{
		$onclick = "";
	}

	$button = "<div class=\"".$class_name."-first\" $id_div $name_div style=\"width: ".$button_div_width."px; $style_div\" $disabled >";
		$button .= "<span class=\"".$class_name."-end\" $id_span $name_span $disabled >";
			$button .= "<input $id_button $name_button type=\"$button_type\" value=\"$value_button\" class=\"$class_name\" style=\"width: ".$button_width."px; $style_button\" $onclick $event $disabled />";
		$button .= "</span>";
	$button .= "</div>";

	return $button;
}

function get_class_css($class){
	switch($class) {
		case "grey":
			$class_name = "grey-button-large";
		break;

		case "light_grey":
			$class_name = "lighter-grey-button-large";
		break;

		case "green":
			$class_name = "green-button-large";
		break;

		case "red":
			$class_name = "red-button-large";
		break;

		case "orange":
			$class_name = "orange-button-large";
		break;

		case "grey_small":
			$class_name = "grey-button-small";
		break;

		case "light_grey_small":
			$class_name = "lighter-grey-button-small";
		break;

		case "green_small":
			$class_name = "green-button-small";
		break;

		case "red_small":
			$class_name = "red-button-small";
		break;

		case "orange_small":
			$class_name = "orange-button-small";
		break;

		case "display":
			$class_name = "display-button";
		break;

		default:
			$class_name = "grey-button-small";
	}

	return $class_name;
}


function make_check_box($id, $name, $value, $label=null, $checked, $onclick, $style=null, $style_span=null){
	//id same with name
	if($id != null){
		$id_checkbox = "id=\"".$id."\"";
		$name_checkbox = "name=\"".$id."\"";
		if($name != null){
			$name_checkbox = "name=\"".$name."\"";
		}
	}

	if($onclick != null){
		$onclick = "onclick=\"$onclick\"";
	}
	else{
		$onclick = "";
	}

	if($label !=null){
		$label = "<label style='margin-left: 5px;'>$label</label>";
	}
	
	//$check_box = "<span style='padding: 5px; $style_span'><input $name_checkbox $id_checkbox value='$value' type='checkbox' safari=1 $checked $onclick $style /><label style='margin-left: 5px;'>$label</label></span>";
	$check_box = "<span style='padding: 5px; $style_span'><input $name_checkbox $id_checkbox value='$value' type='checkbox' safari=1 $checked $onclick $style />$label</span>";
	return $check_box;
}

function make_radio_button($id, $name, $value, $label, $checked, $onclick, $style=null){
	//id same with name
	if($id != null){
		$id_radio = "id=\"".$id."\"";
		$name_radio = "name=\"".$id."\"";
		if($name != null){
			$name_radio = "name=\"".$name."\"";
		}
	}

	if($onclick != null){
		$onclick = "onclick=\"$onclick\"";
	}
	else{
		$onclick = "";
	}

	$radio = "<span style='padding: 5px;'><input $name_radio $id_radio value='$value' type='radio' safari=1 $checked $onclick $style /><label style='margin-left: 5px;'>$label</label></span>";

	return $radio;
}

function make_select_box($type, $id, $name, $style, $onclick, $onchange=null){
	//id same with name
	if($id != null){
		$id_select = "id=\"".$id."\"";
		$name_select = "name=\"".$id."\"";
		if($name != null){
			$name_select = "name=\"".$name."\"";
		}
	}

	if($type != "single"){
		$type_checkbox = "multiple=multiple";
	}

	$select = select_javascript($type, $id, $onclick,$onchange);
	$select .= "<select $name_select $id_select $type_checkbox $style>";

	return $select;
}

function select_javascript($type, $id, $onclick, $onchange){
	if($onclick != null){
		$onclicked = "click: function(event, ui){
			$onclick;
		}";
	}
	
	if($type == "multi"){
		$javascript = "
		<script>
		$(function(){
			$('#$id').multiselect({
				selectedList: 2, // 0-based index
				noneSelectedText: 'Select an Option',
				$onclicked
			});
		});
		</script>";
	}
	elseif($type == "multi_filter"){
		$javascript = "
		<script>
		$(function(){
			$('#$id').multiselect({
				$onclicked
			}).multiselectfilter();
		});
		</script>";
	}
	elseif($type == "single_filter"){
		$javascript = "
		<script>
		$(function(){
		$('#$id').multiselect({
				multiple: false,
				//header: 'Select an option',
				//noneSelectedText: 'Select an Option',
				selectedList: 2,
				$onclicked
			}).multiselectfilter();
		});
		</script>";
	}
	elseif($type == "single"){
		if($onchange!=null){
			$javascript = "
			<script>
			$(function(){
				$('#$id').multiselect({
					multiple: false,
					selectedList: 1,
					change: function() {alert('Change')}
				}).multiselectfilter();

			});
			</script>";
		}
		else{
			$javascript = "
			<script>
				$(function(){
				$('#$id').multiselect({
						multiple: false,
						selectedList: 1,
						$onclicked
					}).multiselectfilter();
				});
			</script>";
		}
	}

	return $javascript;
}

?>