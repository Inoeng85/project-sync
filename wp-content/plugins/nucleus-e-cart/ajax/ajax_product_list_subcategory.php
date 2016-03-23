<?php
$ABSPATH = base64_decode($_GET['ABSPATH']);
include_once($ABSPATH."wp-config.php");

include_once(".../define_file.php");
include_once(ECART_DIR."functions/common_function.php");

define('ECART_LINK', base64_decode($_GET['ECART_LINK']));

$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die (mysql_error());
mysql_select_db(DB_NAME, $conn);

$arr = array();

$show_tax = check_show_tax();

$product_category_id = $_GET['product_category_id'];
$product_type_id = $_GET['product_type_id'];

if($product_type_id == 0 || $product_type_id == "")
{
	$query_product_type = "SELECT product_type_id FROM wp_ecart_product_category_type_link WHERE product_category_id='$product_category_id'";
}
else
{
	$query_product_type = "$product_type_id";
}

$qry_all_product = mysql_query("SELECT * FROM wp_ecart_product WHERE product_type_id IN ($query_product_type) AND inactive <> 'Y' ORDER BY product_name");
$num_all_product = mysql_num_rows($qry_all_product);

$no = 0;
$qry_product = mysql_query("SELECT * FROM wp_ecart_product WHERE product_type_id IN ($query_product_type) AND inactive <> 'Y' ORDER BY product_name LIMIT 20 ");
while($result = mysql_fetch_array($qry_product))
{
	$product_id = $result['product_id'];
	
	$link_image = $result['product_image'];
			
	//$product_image = "<a href='$link_image' class='MagicThumb' rel='image-size: fit-screen; buttons-display:close' onclick='return false;'>";
	$product_image = "<a href='$link_image' title='".$result['product_name']."' rel='lightbox' onclick='return false;'>";
	$product_image .= "<img src='$link_image' width='50' style='display:block'>";
	$product_image .= "</a>";
	
	
	if($show_tax == "Y")
	{
		$selling_price = $result['selling_price_inc_tax'];
		$special_selling_price = $result['special_selling_price_inc_tax'];	
	}
	else
	{
		$selling_price = $result['selling_price_exc_tax'];
		$special_selling_price = $result['special_selling_price_exc_tax'];
	}
	
	if($result['product_on_special'] == 0)
	{
		$price = "$".number_format($selling_price, 2, '.', ',');
		$price .= "<input type='hidden' name='price_inc_tax_$product_id' value='".$result['selling_price_inc_tax']."'>";
		$price .= "<input type='hidden' name='price_exc_tax_$product_id' value='".$result['selling_price_exc_tax']."'>";
	}
	else
	{
		$price = "<del>$".number_format($selling_price, 2, '.', ',')."</del> " . "<b>$".number_format($special_selling_price, 2, '.', ',')."</b>";
		$price .= "<input type='hidden' name='price_inc_tax_$product_id' value='".$result['special_selling_price_inc_tax']."'>";
		$price .= "<input type='hidden' name='price_exc_tax_$product_id' value='".$result['special_selling_price_exc_tax']."'>";
	}
	
	$product_name = "<a href='".ECART_LINK."ecart=product_detail&product_id=$product_id' class='ecart_link'>".$result['product_name']."</a>";
	
	if($no%2 == 0)
		$tr_product_list = "tr_product_list_ganjil";
	else
		$tr_product_list = "tr_product_list_genap";
		
	$qty = "<input type='text' name='qty_$product_id' style='width:50px; text-align:center'>";
	$qty .= "<input type='hidden' name='arr_product_id[]' value='$product_id'>";
	
	$product_attribute = "";
	$qry_product_attribute = mysql_query("SELECT * FROM wp_ecart_product_attribute WHERE product_id='$product_id' ");
	while($data_product_attribute = mysql_fetch_array($qry_product_attribute))
	{
		$attribute_type = $data_product_attribute['attribute_type'];
		$attribute_value = $data_product_attribute['attribute_value'];	
		
		$product_attribute .= show_product_attribute($attribute_type, $attribute_value);
	}
	
	$tr .=  "<tr class='tr_product_list $tr_product_list' id='$no'><td>".$product_image."</td><td>".$qty."</td><td>".$price."</td><td>".$result['product_amount']."</td><td>".$product_attribute."</td><td>".$product_name."</td></tr>";

	$no++;
}


if(mysql_num_rows($qry_product) == 0)
{
	$table .= "<br>There is no product on this category";
}
else
{
	$trigger_load = $no-1;
	
	$table .= "<form method='POST'>";
        $table .= "<input type='submit' value='' name='add_to_cart' class='button_add_to_cart_top'>";
	$table .= "<table class='table_product'>";
	$table .= "<tr><td>Pic</td><td>Quantity</td><td>Price</td><td>Amount</td><td>&nbsp;</td><td>Product</td></tr>";
	$table .= $tr;
	$table .= "</table>";
	//$table .= "<input type='submit' value='Add To Cart' name='add_to_cart'>";
	$table .= "<div class='div_add_to_cart'><input type='submit' value='' name='add_to_cart' class='button_add_to_cart'></div>";
	$table .= "<input type='hidden' value='$num_all_product' id='num_all_product'>";
	$table .= "<input type='hidden' value='$trigger_load' id='trigger_load'>";
	$table .= "<input type='hidden' value='$product_category_id' id='product_category_id'>";
	$table .= "</form>";
	
}

function check_show_tax()
{
	$sql = mysql_query("SELECT config_value FROM wp_ecart_config where config_name='show_tax'");
	$results = mysql_fetch_array($sql);
	
	if($results['config_value'] == "N")
		$show_tax = "N";
	else
		$show_tax = "Y";
	
	if($_SESSION['customer_id'] != "")
	{
		$qry = mysql_query("SELECT * FROM wp_ecart_customer_address WHERE customer_id='".$_SESSION['customer_id']."' ");
		$data = mysql_fetch_array($qry);
		
		if($data['delivery_country'] != "Australia")
			$show_tax = "N";
	}

	return $show_tax;
	/*
	if($results['config_value'] == "N")
		return "N";
	else
		return "Y";
	*/
}


$arr['product_list'][] = $table;
echo json_encode($arr);
?>