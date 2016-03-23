<?php
include_once("ecart.class.php");
$ecart = new ecart;
switch($_GET['customer_profile'])
{
	default:
		$ecart->form_edit_profile();
}
?>