<?php
include_once("ecart.class.php");
include_once(ECART_DIR."functions/common_function.php");

$ecart = new ecart;
$opt = get_option('timezone_string');

if($opt==""){
	$offset = get_option('gmt_offset'); 
	$string = timezone_convert($offset);
	date_default_timezone_set($string);
}else{
	date_default_timezone_set(get_option('timezone_string'));
}


if(isset($_SESSION['atts']))
{
 $atts = $_SESSION['atts'];
	 if($_GET['ecart'] == "product_detail")
	 {
	  $atts['view'] = "";
	  $atts['product'] = $_GET['product_id'];
	  $atts['amount_pos'] = "N";
	  $atts['image']="Y";
	  $atts['description']="Y";
	  $atts['details']="Y";
	  $atts['price']="Y";
	  $atts['pname']="Y";
	  $atts['show_attribute']="Y";
	  $atts['width']="";
	 }
	 /*echo "<pre>";
	 print_r($atts);
	 echo "</pre>";*/
}
else{
 $atts = array(
				'view' => 'list',
				'product' => '',
				'columns' => '2',
				'category' => '',
				'subcategory' => '',
				'order' => 'ASC',
				'attribute' => '',
				'attribute_value' => '',
				'show_attribute '=> 'Y',
				'width' => '250',
				'description' => 'Y',
				'details' => 'Y',
				'price' => 'Y',
				'name' => 'Y'
				);
}
	
switch($_GET['ecart'])
{
	case "product_detail":
		$ecart->modified_product_detail($atts,"2");
	break;
	
	case "process_add_cart":
		$ecart->process_add_cart();
	break;
	
	case "shopping_cart":
		$ecart->shopping_cart();
	break;
	
	case "checkout":
		$ecart->checkout();
	break;
	
	case "process_login":
		$ecart->process_login();
	break;
	
	case "process_customer_register":
		$ecart->process_customer_register();
	break;
	
	case "add_to_cart_bulk":
		$ecart->add_to_cart_bulk();
	break;
	
	case "form_edit_profile":
		$ecart->form_edit_profile();
	break;
	
	case "form_forget_password":
		$ecart->form_forget_password();
	break;
	
	case "process_form_forget_password":
		$ecart->process_form_forget_password();
	break;
	
	case "reset_password":
		$ecart->reset_password();
	break;
	
	case "action_reset_password":
		$ecart->action_reset_password();
	break;
	
	case "process_edit_profile":
		$ecart->process_edit_profile();
	break;
	
	case "change_credit_card_detail":
		$ecart->change_credit_card_detail();
	break;
	
	case "process_update_credit_card_detail":
		$ecart->process_update_credit_card_detail();
	break;
	
	case "order_success":
		$ecart->order_success();
	break;
	
	case "logout":
		$ecart->logout();
	break;
	
	default:
		$ecart->modified_product_list($atts,"2");
}


?>
