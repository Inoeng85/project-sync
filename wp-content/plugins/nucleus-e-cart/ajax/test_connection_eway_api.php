<?php
if(isset($_POST))
{
	include(base64_decode($_POST['ECART_DIR'])."forms/admin.class.php");
	$admin = new admin;
	echo $admin->test_eway_api_connection_status();
}
?>