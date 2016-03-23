<?php
define('ABSPATH', base64_decode($_POST['ABSPATH']));
define('ECART_DIR', base64_decode($_POST['ECART_DIR']));
define('ECART_URL', base64_decode($_POST['ECART_URL']));

include_once(ECART_DIR."eway_function/Rapid3.0.php");

include_once(ABSPATH."wp-config.php");
$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die (mysql_error());
mysql_select_db(DB_NAME, $conn);

$URL = "http://testnucleus.com/cloud/application/app_ecart_api/api/api.php";

$sql = mysql_query("SELECT * FROM wp_ecart_nucleus_api_config where 1");
$results = mysql_fetch_array($sql);

$company_name = $results['company_name'];
$company_url = $results['company_url'];
$nucleus_api_username = $results['nucleus_api_username'];
$nucleus_api_password = $results['nucleus_api_password'];
$nucleus_api_key = $results['nucleus_api_key'];

if(strpos($company_url,"nucleuslogic.com"))
{
	$URL = "http://cloud.nucleuslogic.com/application/app_ecart_api/api/api.php";
}
else if(strpos($company_url,"testnucleus.com")) 
{
	$URL = "http://cloud.testnucleus.com/application/app_ecart_api/api/api.php";
}
else if(strpos($company_url,"localhost")) 
{
	$URL = $company_url."/application/app_ecart_api/api/api.php";
}

$request = new StdClass();
$request->requestMethod = "Product";
$request->company_name = "$company_name";
$request->company_url = "$company_url";
$request->nucleus_api_username = "$nucleus_api_username";
$request->nucleus_api_password = "$nucleus_api_password";
$request->nucleus_api_key = "$nucleus_api_key";

$xml_data = Parser::Obj2XML($request);

$ch = curl_init($URL);
//curl_setopt($ch, CURLOPT_MUTE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);
//$output = str_replace("&#13;","",$output);
//$output = replace_string($output);
//$response = Parser::XML2Obj($output);
$response = json_decode($output);

//print_r($output);
//print_r($response);

$arr_product_type = $response->Products->product_type;
if(count($arr_product_type) != 0)
{
	foreach($arr_product_type as $product_type)
	{
		$product_type_id = $product_type->product_type_id;
		$type = $product_type->type;
		
		$qry_cek_product_type = mysql_query("SELECT * FROM wp_ecart_product_type WHERE product_type_id='$product_type_id'");
		if(mysql_num_rows($qry_cek_product_type) == 0)
		{
			mysql_query("INSERT INTO wp_ecart_product_type (product_type_id, type) VALUES(\"".$product_type_id."\", \"".$type."\") ");
		}
		else
		{
			mysql_query("UPDATE wp_ecart_product_type SET type=\"".$type."\" WHERE product_type_id=\"".$product_type_id."\" ");
		}
	}
}

$arr_product = $response->Products->product;

if(count($arr_product) != 0)
{
	foreach($arr_product as $product)
	{
		$product_id = $product->product_id;
		$product_type_id = $product->product_type_id;
		$product_code = $product->product_code;
		$product_name = $product->product_name;
		$product_description = $product->product_description;
		$product_amount = $product->product_amount;
		$inactive = $product->inactive;
		$product_on_special = $product->product_on_special;
		$tax_percentage = $product->tax_percentage;
		$selling_price_inc_tax = $product->selling_price_inc_tax;
		$selling_price_exc_tax = $product->selling_price_exc_tax;
		$special_selling_price_inc_tax = $product->special_selling_price_inc_tax;
		$special_selling_price_exc_tax = $product->special_selling_price_exc_tax;
		$product_image = $product->product_image;
		
		$qry_cek_product_id = mysql_query("SELECT * FROM wp_ecart_product WHERE product_id='$product_id' ");
		if(mysql_num_rows($qry_cek_product_id) > 0)
		{
			mysql_query("UPDATE wp_ecart_product SET
														product_type_id = \"$product_type_id\",
														product_code = \"$product_code\",
														product_name = \"$product_name\",
														product_description = \"$product_description\",
														product_amount = \"$product_amount\",
														selling_price_inc_tax = \"$selling_price_inc_tax\",
														selling_price_exc_tax = \"$selling_price_exc_tax\",
														special_selling_price_inc_tax = \"$special_selling_price_inc_tax\",
														special_selling_price_exc_tax = \"$special_selling_price_exc_tax\",
														product_on_special = \"$product_on_special\",
														product_image = \"$product_image\",
														tax_percentage = \"$tax_percentage\",
														inactive = \"$inactive\"
													
													WHERE product_id = \"$product_id\"
													
													");	
		}
		else
		{
			mysql_query("INSERT INTO wp_ecart_product	(
															product_id,
															product_type_id,
															product_code,
															product_name,
															product_description,
															product_amount,
															selling_price_inc_tax,
															selling_price_exc_tax,
															special_selling_price_inc_tax,
															special_selling_price_exc_tax,
															product_on_special,
															product_image,
															tax_percentage,
															inactive
														)
														VALUES
														(
															\"".$product_id."\",
															\"".$product_type_id."\",
															\"".$product_code."\",
															\"".$product_name."\",
															\"".$product_description."\",
															\"".$product_amount."\",
															\"".$selling_price_inc_tax."\",
															\"".$selling_price_exc_tax."\",
															\"".$special_selling_price_inc_tax."\",
															\"".$special_selling_price_exc_tax."\",
															\"".$product_on_special."\",
															\"".$product_image."\",
															\"".$tax_percentage."\",
															\"".$inactive."\"
														)
														");
		}
		
		mysql_query("DELETE FROM wp_ecart_product_attribute WHERE product_id = \"$product_id\" ");
		$product_attribute = $product->product_attribute;
		
		if(count($product_attribute) !=0)
		{
			foreach($product_attribute as $attributes)
			{
				$attribute_type = $attributes->attribute_type;
				$attribute_value = $attributes->attribute_value;
				
				mysql_query("INSERT INTO wp_ecart_product_attribute (
																		product_id,
																		attribute_type,
																		attribute_value
																	)
																	VALUES
																	(
																		\"".$product_id."\",
																		\"".$attribute_type."\",
																		\"".$attribute_value."\"
																	)																
																	");
			}
		}
	}
}

/*
echo "<pre>";
print_r($arr_product);
echo "</pre>";
*/
echo "SUCCESS";
?>