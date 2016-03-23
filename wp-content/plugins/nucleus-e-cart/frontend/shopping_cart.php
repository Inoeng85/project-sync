<?php
include_once("ecart.class.php");
$ecart = new ecart;
switch($_GET['shopping_cart'])
{
	default:
		$ecart->shopping_cart();
}
?>
