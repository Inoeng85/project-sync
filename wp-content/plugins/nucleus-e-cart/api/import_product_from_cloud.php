<?php
global $request;

$response = $request;

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

/*echo "<pre>";
print_r($arr_product);
echo "</pre>";*/
echo "SUCCESS";
?>