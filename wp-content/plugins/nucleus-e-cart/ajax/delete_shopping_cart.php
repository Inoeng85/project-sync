<?php
$ABSPATH = base64_decode($_POST['ABSPATH']);
include_once($ABSPATH."wp-config.php");

$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die (mysql_error());
mysql_select_db(DB_NAME, $conn);

$unique_id = $_POST['unique_id'];
$ip_address = $_POST['ip_address'];
$product_id = $_POST['product_id'];
$customer_id = $_POST['customer_id'];

mysql_query("DELETE FROM wp_ecart_shopping_cart WHERE product_id='$product_id' AND ((unique_id='$unique_id' AND ip_address='$ip_address') or customer_id='$customer_id') ");

$qry_show = mysql_query("SELECT * from wp_ecart_shopping_cart WHERE ((unique_id='$unique_id' AND ip_address='$ip_address') or customer_id='$customer_id') ");
if(mysql_num_rows($qry_show) == 0)
{
	echo "NO_DATA";
}
else
{
	echo "DATA_EXIST";
}

?>