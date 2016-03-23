<?php
include_once("ecart.class.php");
$ecart = new ecart;
switch($_GET['product_special'])
{
	default:
		$ecart->product_special();
}
?>