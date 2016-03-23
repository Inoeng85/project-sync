<?php

include_once("nucleus_style.php");

if($_GET['make_button_ajax'] == "y"){
	$value = $_GET['value'];
	echo create_button($value);
}

function create_button($value){
	$onclick = "remove_sales_person('$value')";
	return make_button("red_small","Remove","button",70,null,$onclick,"float: left; margin-left: 5px; margin-right: 5px;",null);
}
?>