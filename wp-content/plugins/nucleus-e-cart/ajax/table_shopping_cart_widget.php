<?php
$ABSPATH = base64_decode($_GET['ABSPATH']);

include_once($ABSPATH."wp-config.php");

define('ECART_LINK', base64_decode($_GET['ECART_LINK']));

$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die (mysql_error());
mysql_select_db(DB_NAME, $conn);

$arr = array();
$unique_id = $_GET['unique_id'];
$ip_address = $_GET['ip_address'];
$customer_id = $_GET['customer_id'];

$show_tax = check_show_tax();

if($_SESSION['customer_id'] == "")
	$sql = "SELECT * FROM wp_ecart_shopping_cart a, wp_ecart_product b where a.unique_id='$unique_id' and b.product_id=a.product_id ORDER BY b.product_name asc";
else
	$sql = "SELECT * FROM wp_ecart_shopping_cart a, wp_ecart_product b where ((a.unique_id='$unique_id') or a.customer_id='".$_SESSION['customer_id']."' ) and b.product_id=a.product_id ORDER BY b.product_name asc";

$qry = mysql_query($sql);
if(mysql_num_rows($qry) == 0)
{
	$table = "Shopping Cart is empty<br><br>";	
}
else
{
	$table = "<table class='shopping_cart_widget' width='100%'>";		
	$total = 0;
	$no = 0;
	
	while($data = mysql_fetch_array($qry))
	{
		$product_id = $data['product_id'];

		if($show_tax == "Y")
			$price = $data['price_inc_tax'];
		else
			$price = $data['price_exc_tax']; 

		$quantity = $data['quantity'];			
		$subtotal_raw = $quantity * $price;
		$total += $subtotal_raw;
		
		$price = number_format($price, 2, '.', ',');
		$subtotal = number_format($subtotal_raw, 2, '.', ',');
		
		$price = "$price<input type='hidden' id='price_$product_id' name='price_$product_id' value='$price'>";
		$subtotal = "<span id='span_subtotal_$product_id'>$subtotal</span>";
		$subtotal .= "<input type='hidden' id='subtotal_$product_id' value='$subtotal_raw'>";
		
		if($no%2 == 0)
			$tr_shopping_cart = "tr_shopping_cart_ganjil";
		else
			$tr_shopping_cart = "tr_shopping_cart_genap";
		
		$no++;
				
		$qty = $data['quantity'];
		$table .= "<tr id='tr_$product_id' class='$tr_shopping_cart'><td>$qty</td><td>".$data['product_amount']."</td><td>".$data['product_name']."</td><td>$".$subtotal."</td><td><a onclick=\"confirm_delete('$product_id')\">Remove</a></td></tr>";

	}			
	$table .= "</table>";
	$table .= "<a href='".ECART_LINK."ecart=shopping_cart' class='ecart_link'>Edit Cart</a>";
	$table .= "<br>&nbsp;";
	//$table .= "<br>&nbsp;";
	$table .= "<div>To complete your purchase click</div>";
	$table .= "&nbsp;";
	$table .= "<input type='button' onclick=\"window.location='".ECART_LINK."ecart=checkout'\" value='Proceed to Checkout' style='padding:0; height:20px'>";
}			
			
function check_show_tax()
{
	$sql = mysql_query("SELECT config_value FROM wp_ecart_config where config_name='show_tax'");
	$results = mysql_fetch_array($sql);
	
	if($results['config_value'] == "N")
		return "N";
	else
		return "Y";
}

$arr['shopping_cart_widget'][] = $table;
echo json_encode($arr);

?>			
			