<?php
$ABSPATH = base64_decode($_POST['ABSPATH']);
include_once($ABSPATH."wp-config.php");

$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die (mysql_error());
mysql_select_db(DB_NAME, $conn);

$unique_id = $_POST['unique_id'];
$ip_address = $_POST['ip_address'];
$arr_product_id = $_POST['arr_product_id'];
		
foreach($arr_product_id as $product_id)
{
	$quantity = $_POST['qty_'.$product_id];
	
	mysql_query("UPDATE wp_ecart_shopping_cart SET quantity='$quantity' WHERE product_id='$product_id' AND unique_id='$unique_id' ");
	
}

?>