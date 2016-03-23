<?php
if(isset($_POST))
{
	$ABSPATH = base64_decode($_POST['ABSPATH']);
	include_once($ABSPATH."wp-config.php");

	$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die (mysql_error());
	mysql_select_db(DB_NAME, $conn);
	
	include(base64_decode($_POST['ECART_DIR'])."forms/admin.class.php");
	$admin = new admin;
	
	switch($_POST['action'])
	{
		case "insert_new_category":
			$admin->insert_new_category();
		break;	
		
		case "update_new_category":
			$admin->update_new_category();
		break;	
		
		case "delete_new_category":
			$admin->delete_new_category();
		break;
		
		case "view_subcategory":
			$admin->view_subcategory();
		break;
		
		case "assign_subcategory":
			$admin->assign_subcategory();
		break;
	}
}
?>