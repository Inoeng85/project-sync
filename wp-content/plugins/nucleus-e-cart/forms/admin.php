<?php
include_once("admin.class.php");
$admin = new admin;

switch($_GET['sec'])
{
	case "save_setting":
		$admin->save_setting();
	break;
	
	case "form_setting":
		$admin->form_setting();
	break;
	
	case "refresh_setting":
		$admin->refresh_setting();
	break;

	case "product_list_content_management":
		$admin->product_list_content_management();
	break;

	case "update_product_list_content_management":
		$admin->update_product_list_content_management();
	break;
	
	case "email_order_content_management":
		$admin->email_order_content_management();
	break;

	case "update_email_order_content_management":
		$admin->update_email_order_content_management();
	break;
	
	case "order_success_content_management":
		$admin->order_success_content_management();
	break;

	case "update_order_success_content_management":
		$admin->update_order_success_content_management();
	break;
	
	case "category_management":
		$admin->category_management();
	break;
	
	case "link_management":
		$admin->link_management();
	break;
	
	case "process_link_management":
		$admin->process_link_management();
	break;

	case "register_customer":
		$admin->register_customer();
	break;

	case "process_customer_register":
		$admin->process_customer_register();
	break;

	default:
		$admin->home_admin();

}



?>