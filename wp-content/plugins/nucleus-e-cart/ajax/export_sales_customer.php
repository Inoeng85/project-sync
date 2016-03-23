<?php
define('ABSPATH', base64_decode($_POST['ABSPATH']));
define('ECART_DIR', base64_decode($_POST['ECART_DIR']));
define('ECART_URL', base64_decode($_POST['ECART_URL']));

include_once(ECART_DIR."eway_function/Rapid3.0.php");

include_once(ABSPATH."wp-config.php");
$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die (mysql_error());
mysql_select_db(DB_NAME, $conn);

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

$request = new StdClass();
$request->requestMethod = "get_exist_invoice";
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

$response = Parser::XML2Obj($output);

$notincludeinvoice = $response->notIncludeInvoiceNumber;


$request_for_sales = new StdClass();
$request_for_sales->requestMethod = "export_sales_customer_from_ecart";
$request_for_sales->company_name = "$company_name";
$request_for_sales->company_url = "$company_url";
$request_for_sales->nucleus_api_username = "$nucleus_api_username";
$request_for_sales->nucleus_api_password = "$nucleus_api_password";
$request_for_sales->nucleus_api_key = "$nucleus_api_key";

$arr_in_sales = explode(",",$notincludeinvoice);
if(count($arr_in_sales) != 0)
{
	$not_in_sales = "'".implode("','", $arr_in_sales)."'";
	$qry_not_in_sales = " and a.invoice_number NOT IN ($not_in_sales)";
}

$qry_sales = mysql_query("SELECT * FROM wp_ecart_sales_order a, wp_ecart_sales_order_delivery_address b WHERE b.invoice_number=a.invoice_number $qry_not_in_sales");
while($data_sales = mysql_fetch_array($qry_sales))
{
	$request_sales = new StdClass();
	$request_sales->invoice_number = (string) $data_sales['invoice_number'];
	$request_sales->customer_id = (string) $data_sales['customer_id'];
	$request_sales->sale_date = (string) $data_sales['sale_date'];
	$request_sales->delivery_contact_name = (string) $data_sales['delivery_contact_name'];
	$request_sales->delivery_address_line1 = (string) $data_sales['delivery_address_line1'];
	$request_sales->delivery_address_line2 = (string) $data_sales['delivery_address_line2'];
	$request_sales->delivery_city = (string) $data_sales['delivery_city'];
	$request_sales->delivery_state = (string) $data_sales['delivery_state'];
	$request_sales->delivery_postcode = (string) $data_sales['delivery_postcode'];
	$request_sales->delivery_country = (string) $data_sales['delivery_country'];
	$request_sales->notes = (string) $data_sales['notes'];

	$qry_sales_detail = mysql_query("select * from wp_ecart_sales_order_detail a, wp_ecart_product b where a.invoice_number='".$data_sales['invoice_number']."' and b.product_id=a.product_id ");
	while($data_sales_detail = mysql_fetch_array($qry_sales_detail))
	{
		$request_sales_detail = new StdClass();
		$request_sales_detail->product_id = (string) $data_sales_detail['product_id'];
		$request_sales_detail->product_code = (string) $data_sales_detail['product_code'];
		$request_sales_detail->product_name = (string) trim($data_sales_detail['product_name']);
		$request_sales_detail->quantity = (string) $data_sales_detail['quantity'];
		$request_sales_detail->price_inc_tax = (string) $data_sales_detail['price_inc_tax'];
		$request_sales_detail->price_exc_tax = (string) $data_sales_detail['price_exc_tax'];
		$request_sales_detail->total_inc_tax = (string) $data_sales_detail['total_inc_tax'];
		$request_sales_detail->total_exc_tax = (string) $data_sales_detail['total_exc_tax'];
		
		$request_sales->item[] = $request_sales_detail;
	}
	
	$request_sales->customer = get_customer_detail($data_sales['customer_id']);
	
	$request_for_sales->Sales[] = $request_sales;

}

function get_customer_detail($customer_id)
{
	$qry = mysql_query("select * from wp_ecart_customer a, wp_ecart_customer_address b where b.customer_id=a.customer_id and a.customer_id='$customer_id'");
	$data = mysql_fetch_array($qry);

	$request_customer = new StdClass();
	$request_customer->customer_id = (string) $data['customer_id'];
	$request_customer->first_name = (string) $data['first_name'];
	$request_customer->last_name = (string) $data['last_name'];
	$request_customer->email = (string) $data['email'];
	$request_customer->password = (string) $data['password'];
	$request_customer->phone = (string) $data['phone'];
	$request_customer->mobile_phone = (string) $data['mobile_phone'];
	$request_customer->fax = (string) $data['fax'];
	$request_customer->hear_about_us = (string) $data['hear_about_us'];
	$request_customer->register_date = (string) $data['register_date'];
	$request_customer->note = (string) $data['note'];
	$request_customer->TokenCustomerID = (string) $data['TokenCustomerID'];
	$request_customer->caverno = (string) $data['caverno'];

	$request_customer->mailing_address_line1 = (string) $data['mailing_address_line1'];
	$request_customer->mailing_address_line2 = (string) $data['mailing_address_line2'];
	$request_customer->mailing_city = (string) $data['mailing_city'];
	$request_customer->mailing_state = (string) $data['mailing_state'];
	$request_customer->mailing_postcode = (string) $data['mailing_postcode'];
	$request_customer->mailing_country = (string) $data['mailing_country'];
	$request_customer->delivery_contact_name = (string) $data['delivery_contact_name'];
	$request_customer->delivery_address_line1 = (string) $data['delivery_address_line1'];
	$request_customer->delivery_address_line2 = (string) $data['delivery_address_line2'];
	$request_customer->delivery_city = (string) $data['delivery_city'];
	$request_customer->delivery_state = (string) $data['delivery_state'];
	$request_customer->delivery_postcode = (string) $data['delivery_postcode'];
	$request_customer->delivery_country = (string) $data['delivery_country'];

	return $request_customer;
}


$xml_data = Parser::Obj2XML($request_for_sales);

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


echo $output;

//echo "SUCCESS";
?>