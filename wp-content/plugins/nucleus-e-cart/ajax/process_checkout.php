<?php
$ABSPATH = base64_decode($_POST['ABSPATH']);
include_once($ABSPATH."wp-config.php");

$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die (mysql_error());
mysql_select_db(DB_NAME, $conn);

define('ECART_LINK', base64_decode($_POST['ECART_LINK']));
define('ECART_DIR', base64_decode($_POST['ECART_DIR']));
define('ECART_URL', base64_decode($_POST['ECART_URL']));

$unique_id = $_POST['unique_id'];
$ip_address = $_POST['ip_address'];
$customer_id = $_POST['customer_id'];

$EWAY_CARDNAME = $_POST['EWAY_CARDNAME'];
$EWAY_CARDNUMBER = $_POST['EWAY_CARDNUMBER'];
$EWAY_CARDEXPIRYMONTH = $_POST['EWAY_CARDEXPIRYMONTH'];
$EWAY_CARDEXPIRYYEAR = $_POST['EWAY_CARDEXPIRYYEAR'];
$EWAY_CARDSTARTMONTH = $_POST['EWAY_CARDSTARTMONTH'];
$EWAY_CARDSTARTYEAR = $_POST['EWAY_CARDSTARTYEAR'];
$EWAY_CARDISSUENUMBER = $_POST['EWAY_CARDISSUENUMBER'];
$EWAY_CARDCVN = $_POST['EWAY_CARDCVN'];
$notes = $_POST['message'];

##########################################################################
# CUSTOMER DETAIL
$qry_customer = mysql_query("SELECT * FROM wp_ecart_customer a, wp_ecart_customer_address b WHERE a.customer_id='$customer_id' and b.customer_id=a.customer_id ");
$data_customer = mysql_fetch_array($qry_customer);
$first_name = $data_customer['first_name'];
$last_name = $data_customer['last_name'];
$email = $data_customer['email'];
$password = $data_customer['password'];
$phone = $data_customer['phone'];
$mobile_phone = $data_customer['mobile_phone'];
$fax = $data_customer['fax'];
$note = $data_customer['note'];
$TokenCustomerID = $data_customer['TokenCustomerID'];
$caverno = $data_customer['caverno'];
$mailing_address_line1 = $data_customer['mailing_address_line1'];
$mailing_address_line2 = $data_customer['mailing_address_line2'];

$Street = $mailing_address_line1;
$Street .= (!empty($mailing_address_line2) ? $mailing_address_line2 : "");

$mailing_city = $data_customer['mailing_city'];
$mailing_state = $data_customer['mailing_state'];
$mailing_postcode = $data_customer['mailing_postcode'];
$mailing_country = $data_customer['mailing_country'];

$delivery_type = $_POST['delivery_type'];

if($delivery_type == "default")
{
	$delivery_contact_name = $_POST['default_contact_name'];
	$delivery_address_line1 = $_POST['default_delivery_address_line1'];
	$delivery_address_line2 = $_POST['default_delivery_address_line2'];
	$delivery_city = $_POST['default_delivery_city'];
	$delivery_state = $_POST['default_delivery_state'];
	$delivery_postcode = $_POST['default_delivery_postcode'];
	$delivery_country = $_POST['default_delivery_country'];
}
else
{
	$delivery_contact_name = $_POST['new_contact_name'];
	$delivery_address_line1 = $_POST['new_delivery_address_line1'];
	$delivery_address_line2 = $_POST['new_delivery_address_line2'];
	$delivery_city = $_POST['new_delivery_city'];
	$delivery_state = $_POST['new_delivery_state'];
	$delivery_postcode = $_POST['new_delivery_postcode'];
	$delivery_country = $_POST['new_delivery_country'];
}

##########################################################################
############# EWAY
$qry_eway = mysql_query("SELECT * FROM wp_ecart_eway_api_config WHERE 1");
$data_eway = mysql_fetch_array($qry_eway);

$eway_connection = $data_eway['eway_connection'];
$eway_api_key = $data_eway['eway_api_key'];
$eway_password = base64_decode($data_eway['eway_password']);
$eway_CustomerID = $data_eway['eway_CustomerID'];

/*
$GLOBALS['EWAY_CONNECTION'] = $eway_connection;
$GLOBALS['EWAY_API_USERNAME'] = $eway_api_key;
$GLOBALS['EWAY_API_PASSWORD'] = $eway_password;

include_once(ECART_DIR."eway_function/Rapid3.0.php");
*/

$form_credit = $_POST['form_credit'];

$create_customer = "SUCCESS";
###################################################################
#### NEW TOKEN CUSTOMER ID
if($TokenCustomerID == "" || $form_credit == "new")
{
	require_once(ECART_DIR."eway_function/lib/nusoap.php");

	if($eway_connection == "Live")
	{
		$client = new nusoap_client("https://www.eway.com.au/gateway/ManagedPaymentService/managedCreditCardPayment.asmx", false);
	}
	else
	{
		$client = new nusoap_client("https://www.eway.com.au/gateway/ManagedPaymentService/test/managedCreditCardPayment.asmx", false);
	}

	$err = $client->getError();
	if ($err) {
		echo "An Error occured when connection to eway server, Please try again!";
		$create_customer = "FAILED";
		exit();
	}

	$client->namespaces['man'] = 'https://www.eway.com.au/gateway/managedpayment';
	// set SOAP header
	$headers = "<man:eWAYHeader><man:eWAYCustomerID>" . $eway_CustomerID. "</man:eWAYCustomerID><man:Username>" . $eway_api_key . "</man:Username><man:Password>" . $eway_password . "</man:Password></man:eWAYHeader>";
	$client->setHeaders($headers);

	$requestbody = array(
        'man:Title' => "Mr.",
        'man:FirstName' => $first_name,
        'man:LastName' => $last_name,
        'man:Address' => $Street,
        'man:Suburb' => $mailing_city,
        'man:State' => $mailing_state,
        'man:Company' => "Nucleus E-Cart",
        'man:PostCode' => $mailing_postcode,
        'man:Country' => strtolower(show_country_code($mailing_country)),
        'man:Email' => "email@email.com",
        'man:Fax' => $fax,
        'man:Phone' => $phone,
        'man:Mobile' => $mobile_phone,
		'man:CustomerRef' => "Nucleus E-Cart",
        'man:JobDesc' => $_POST['JobDesc'],
        'man:Comments' => $note,
        'man:URL' => $_POST['URL'],
        'man:CCNumber' => $EWAY_CARDNUMBER,
        'man:CCNameOnCard' => $EWAY_CARDNAME,
        'man:CCExpiryMonth' => $EWAY_CARDEXPIRYMONTH,
        'man:CCExpiryYear' => $EWAY_CARDEXPIRYYEAR
    );
    $soapaction = 'https://www.eway.com.au/gateway/managedpayment/CreateCustomer';
    $result = $client->call('man:CreateCustomer', $requestbody, '', $soapaction);

	//echo $client;

	if ($client->fault)
	{
		//echo "An Error occured when connection to eway server, Please try again!";
		//echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
		echo $result['faultstring'];
		$create_customer = "FAILED";
	}
	else
	{
		$err = $client->getError();
		if ($err)
		{
			echo "An Error occured when connection to eway server, Please try again!";
			$create_customer = "FAILED";
		}
		else
		{
			$TokenCustomerID = $result;
			$create_customer = "SUCCESS";
		}
	}


	/*
	//Create RapidAPI Service
	$service = new RapidAPI();

	//Create AccessCode Request Object
	$request = new CreateAccessCodeRequest();

    $request->Customer->Title = "Mr.";
    $request->Customer->FirstName = $first_name;
    $request->Customer->LastName = $last_name;
    $request->Customer->Street1 = $Street;
    $request->Customer->City = $mailing_city;
    $request->Customer->State = $mailing_state;
    $request->Customer->PostalCode = $mailing_postcode;
    $request->Customer->Country = strtolower(show_country_code($mailing_country));
    $request->Customer->Email = $email;
    $request->Customer->Phone = $phone;
    $request->Customer->Mobile = $mobile_phone;
    $request->Customer->Fax = $fax;
	$request->Customer->Comments = $note;
	$request->Customer->CompanyName = "Nucleus E-Cart";

	$request->RedirectUrl = "http://www.nucleuslogic.com";
	$request->Method = "CreateTokenCustomer";

	$result = $service->CreateAccessCode($request);

	if(isset($result->Errors))
	{
		$create_customer = "FAILED";

		//Get Error Messages from Error Code. Error Code Mappings are in the Config.ini file
		$ErrorArray = explode(",", $result->Errors);

		$lblError = "";

		foreach ( $ErrorArray as $error )
		{
			if(isset($service->APIConfig[$error]))
				$lblError .= $service->APIConfig[$error]."\n";
			else
				$lblError .= $error;
		}

		echo $lblError;
	}
	else
	{
		$Response = $result;

		//set POST variables
		$url = $Response->FormActionURL;
		$fields = array(
					'EWAY_ACCESSCODE' => urlencode($Response->AccessCode),
					'EWAY_CARDNAME' => urlencode($EWAY_CARDNAME),
					'EWAY_CARDNUMBER' => urlencode($EWAY_CARDNUMBER),
					'EWAY_CARDEXPIRYMONTH' => urlencode($EWAY_CARDEXPIRYMONTH),
					'EWAY_CARDEXPIRYYEAR' => urlencode($EWAY_CARDEXPIRYYEAR),
					'EWAY_CARDSTARTMONTH' => urlencode($EWAY_CARDSTARTMONTH),
					'EWAY_CARDSTARTYEAR' => urlencode($EWAY_CARDSTARTYEAR),
					'EWAY_CARDISSUENUMBER' => urlencode($EWAY_CARDISSUENUMBER),
					'EWAY_CARDCVN' => urlencode($EWAY_CARDCVN)
				);

		//url-ify the data for the POST
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

		//execute post
		$output = curl_exec($ch);

		//close connection
		curl_close($ch);

		//Build request for getting the result with the access code.
		$request = new GetAccessCodeResultRequest();

		$request->AccessCode = $Response->AccessCode;

		//Call RapidAPI to get the result
		$result = $service->GetAccessCodeResult($request);

		if(
			$result->ResponseCode == "00" ||
			$result->ResponseCode == "08" ||
			$result->ResponseCode == "10" ||
			$result->ResponseCode == "11" ||
			$result->ResponseCode == "16"
		)
		{
			$AccessCode = $Response->AccessCode;
			$TokenCustomerID = $result->TokenCustomerID;

			$create_customer = "SUCCESS";
		}
		else
		{
			$create_customer = "FAILED";

			echo response_code($result->ResponseCode)."\n";

			if(isset($result->ResponseMessage))
			{
				//Get Error Messages from Error Code. Error Code Mappings are in the Config.ini file
				$ResponseMessageArray = explode(",", $result->ResponseMessage);

				$responseMessage = "";

				foreach ( $ResponseMessageArray as $message )
				{
					if(isset($service->APIConfig[$message]))
						$responseMessage .= $service->APIConfig[$message]."\n";
					else
						$responseMessage .= $message;
				}

				echo $responseMessage;
			}
		}
	}
	*/
}


####################################################################################################################
### validation for other necessary requirement data for running API beside tokencustomerid
if($create_customer == "SUCCESS")
{
	$validate = array(

			'eway_connection' => $eway_connection,
			'eway_api_key' => $eway_api_key,
			'eway_password' => $eway_password,
			'eway_CustomerID' => $eway_CustomerID,
			'customer_id' => $customer_id

	);
//'eway_cardname' => $EWAY_CARDNAME,
//	'eway_cardnumber' => $EWAY_CARDNUMBER,
//	'eway_cardexpirymonth' => $EWAY_CARDEXPIRYMONTH,
//	'eway_cardexpiryyear' => $EWAY_CARDEXPIRYYEAR,
//	'eway_cardcvn' => $EWAY_CARDCVN,

	//Customer detail minimal requirement validation
	foreach($validate as $key => $value)
	{
		if($value=="")
		{
			$create_customer = "FAILED";
			$keyindex = $key;
			//echo "<script>alert('$key must not empty');</script>";
			break;
		}
	}
}
####################################################################################################################


if($create_customer == "SUCCESS")
{
	$invoice_number = get_invoice_number($unique_id);
	$total_includetax = 0;
	$total_excludetax = 0;
	$taxcount = 0;
	$i=0;
	$transaction = array();
	$item = array();

	$now_date = date("Y-m-d");
	$timestamp  = date("Y-m-d H:i:s");
	$tuesday = date("Y-m-d", strtotime("+1 Tuesday"));

	if($tuesday == date("Y-m-d"))
		$tuesday = date("Y-m-d", strtotime("+2 Tuesday"));

	$sale_date = $tuesday;

	if($caverno != "")
		$EWAY_CARDCVN = base64_decode($caverno);

	$caverno = base64_encode($EWAY_CARDCVN);

	if($TokenCustomerID != "")
		$qry_TokenCustomerID = " TokenCustomerID='$TokenCustomerID', ";

	if($AccessCode != "")
		$qry_AccessCode = " AccessCode='$AccessCode', ";

	$qry_update_token = mysql_query("UPDATE wp_ecart_customer SET
																	$qry_TokenCustomerID
																	$qry_AccessCode
																	caverno='$caverno'

																WHERE customer_id='$customer_id' ");



	$qry_shop_cart = mysql_query("SELECT * FROM wp_ecart_shopping_cart a, wp_ecart_product b WHERE ((a.unique_id=\"$unique_id\") or a.customer_id=\"$customer_id\") AND b.product_id = a.product_id ORDER BY b.product_name asc");
	if(mysql_num_rows($qry_shop_cart) > 0)
	{

			mysql_query("INSERT INTO wp_ecart_sales_order 	(
			invoice_number,
			customer_id,
			sale_date,
			add_date,
			delivery_status,
			notes,
			timestamp
			)
			VALUES
			(
			\"$invoice_number\",
			\"$customer_id\",
			\"$sale_date\",
			\"$now_date\",
			\"$delivery_status\",
			\"$notes\",
			\"$timestamp\"
			)
			");

			mysql_query("INSERT INTO wp_ecart_sales_order_delivery_address	(
			invoice_number,
			delivery_contact_name,
			delivery_address_line1,
			delivery_address_line2,
			delivery_city,
			delivery_state,
			delivery_postcode,
			delivery_country
			)
			VALUES
			(
			\"$invoice_number\",
			\"$delivery_contact_name\",
			\"$delivery_address_line1\",
			\"$delivery_address_line2\",
			\"$delivery_city\",
			\"$delivery_state\",
			\"$delivery_postcode\",
			\"$delivery_country\"
			)
			");

			while($data_shop_cart = mysql_fetch_array($qry_shop_cart))
			{
				$product_id = $data_shop_cart['product_id'];
				$product_type_id = $data_shop_cart['product_type_id'];
				$product_code = $data_shop_cart['product_code'];
				$product_name = $data_shop_cart['product_name'];
				$quantity = $data_shop_cart['quantity'];
				$price_inc_tax = $data_shop_cart['price_inc_tax'];
				$price_exc_tax = $data_shop_cart['price_exc_tax'];
				$total_inc_tax = $quantity * $price_inc_tax;
				$total_exc_tax = $quantity * $price_exc_tax;

				##additional for google analytic
				$catsql = mysql_query(" select
								a.category
							from
								wp_ecart_product_category a,
								wp_ecart_product_category_type_link b
							where
								b.product_type_id = $product_type_id and
								b.product_category_id = a.product_category_id
							order by
								b.product_type_id");
				$catresult =  mysql_fetch_array($catsql);
				$category = $catresult['category'];

				$cus_namesql = mysql_query("SELECT first_name,last_name FROM wp_ecart_customer
											WHERE customer_id='$customer_id' ");
				$cus_nameresult =  mysql_fetch_array($cus_namesql);
				$customer_name = $cus_nameresult['first_name'].''.$cus_nameresult['last_name'];
				##

				mysql_query("INSERT INTO wp_ecart_sales_order_detail	(
																			invoice_number,
																			product_id,
																			quantity,
																			price_inc_tax,
																			price_exc_tax,
																			total_inc_tax,
																			total_exc_tax
																		)
																		VALUES
																		(
																			\"$invoice_number\",
																			\"$product_id\",
																			\"$quantity\",
																			\"$price_inc_tax\",
																			\"$price_exc_tax\",
																			\"$total_inc_tax\",
																			\"$total_exc_tax\"
																		)
																		");


				//google analytics

				$total_includetax = $total_includetax + $total_inc_tax;
				$total_excludetax = $total_excludetax + $total_exc_tax;
				$taxcount = $taxcount + ($total_inc_tax-$total_exc_tax);
				$item[$i]['productcode'] = $product_code;
				$item[$i]['productname'] = $product_name;
				$item[$i]['category'] = $category;
				$item[$i]['quantity'] =	number_format($quantity, 0, '.', ' ');
				if($price_inc_tax!=0)
				{
					$item[$i]['price'] = number_format($price_inc_tax, 2, '.', ' ');
				}
				else
				{
					$item[$i]['price'] = number_format($price_exc_tax, 2, '.', ' ');
				}
				$i++;
				//
			}
			//google analytic
			$transaction['transactionID']=$invoice_number;
			$transaction['customername']=$customer_name;
			if($total_includetax==0){
				$transaction['total']=$total_includetax;
			}else
			{
				$transaction['total']=$total_excludetax;
			}
			$transaction['tax']=$taxcount;
			$transaction['city']=$delivery_city;
			$transaction['state']=$delivery_state;
			$transaction['country']=$delivery_country;
			//
			mysql_query("DELETE FROM wp_ecart_trigger_invoice_number WHERE unique_id=\"$unique_id\" ");

			//mysql_query("DELETE FROM wp_ecart_shopping_cart WHERE unique_id=\"$unique_id\" and ip_address=\"$ip_address\" ");
			mysql_query("DELETE FROM wp_ecart_shopping_cart WHERE customer_id=\"$customer_id\" ");

			email_invoice($invoice_number);
			export_customer_sales_to_nucleus($invoice_number);
			$uaid = show_ecart_config( 'ua_id' );
			if($uaid!="")
			{
				$transaction = json_encode($transaction);
				$item = json_encode($item);
			}
			else {
				$transaction= "";
				$item ="";
			}

			echo "SUCCESS||$invoice_number||$uaid||$transaction||$item";
	}

}
######################################################################################## IF FAILED
else {
	echo "FAILED because $keyindex is empty";
}


function export_customer_sales_to_nucleus($invoice_number)
{
	include_once(ECART_DIR."eway_function/Rapid3.0.php");

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

	$request_for_sales = new StdClass();
	$request_for_sales->requestMethod = "export_sales_customer_from_ecart";
	$request_for_sales->company_name = "$company_name";
	$request_for_sales->company_url = "$company_url";
	$request_for_sales->nucleus_api_username = "$nucleus_api_username";
	$request_for_sales->nucleus_api_password = "$nucleus_api_password";
	$request_for_sales->nucleus_api_key = "$nucleus_api_key";

	$qry_sales = mysql_query("SELECT * FROM wp_ecart_sales_order a, wp_ecart_sales_order_delivery_address b WHERE b.invoice_number=a.invoice_number AND a.invoice_number='$invoice_number'");
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

		$qry_sales_detail = mysql_query("select * from wp_ecart_sales_order_detail a, wp_ecart_product b where a.invoice_number='".$data_sales['invoice_number']."' and b.product_id=a.product_id ORDER BY b.product_name");
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




function get_invoice_number($unique_id)
{
	$qry_show_invoice = mysql_query("SELECT * FROM wp_ecart_trigger_invoice_number where unique_id=\"$unique_id\" ");
	if(mysql_num_rows($qry_show_invoice) == 0)
	{
		mysql_query("INSERT INTO wp_ecart_trigger_invoice_number (unique_id) VALUES (\"$unique_id\") ");
		$qry_show_invoice = mysql_query("SELECT * FROM wp_ecart_trigger_invoice_number where unique_id=\"$unique_id\" ");
	}

	$data_invoice = mysql_fetch_array($qry_show_invoice);

	$trigger_invoice_number_id = $data_invoice['trigger_invoice_number_id'];

	$invoice_number = "IN".format_code($trigger_invoice_number_id);

	return $invoice_number;
}

function format_code($text)
{
	$leng= strlen($text);
	$nol=6-$leng;
	for($i=0;$i<$nol;$i++)
	{
		$nols=$nols."0";
	}
	$hasil= $nols.$text;
	return $hasil;
}

function email_invoice($invoice_number)
{
	$qry_invoice = mysql_query("SELECT * FROM wp_ecart_sales_order WHERE invoice_number='$invoice_number' ");
	$data_invoice = mysql_fetch_array($qry_invoice);
	$customer_id = $data_invoice['customer_id'];

	$qry_customer = mysql_query("SELECT * FROM wp_ecart_customer WHERE customer_id='$customer_id' ");
	$data_customer = mysql_fetch_array($qry_customer);

	$to = $data_customer['email'];
	$subject = "Order Confirmation - #".$invoice_number;
	$from = "Lilly Pilly Organics <orders@lillypillyorganics.com.au>";

	$qry_cek_content = mysql_query("SELECT * FROM wp_ecart_config WHERE config_name='email_order_content_management' ");
	$data_content = mysql_fetch_array($qry_cek_content);

	$message = nl2br($data_content['config_value']);

	$detail_order = detail_order($invoice_number);

	$message = str_replace("{order_detail}",$detail_order,$message);

	sendmail($to, $subject, $message, $from);
}

function detail_order($invoice_number)
{
	$qry_invoice = mysql_query("SELECT * FROM wp_ecart_sales_order WHERE invoice_number='$invoice_number' ");
	$data_invoice = mysql_fetch_array($qry_invoice);
	$customer_id = $data_invoice['customer_id'];

	$show_tax = check_show_tax($customer_id);

	$qry_delivery_address = mysql_query("SELECT * FROM wp_ecart_sales_order_delivery_address WHERE invoice_number='$invoice_number' ");
	$data_delivery_address = mysql_fetch_array($qry_delivery_address);

	$qry_customer = mysql_query("SELECT * FROM wp_ecart_customer WHERE customer_id='$customer_id' ");
	$data_customer = mysql_fetch_array($qry_customer);

	$first_name = $data_customer['first_name'];
	$last_name = $data_customer['last_name'];
	$address = $data_delivery_address['delivery_address_line1']." ".$data_delivery_address['delivery_address_line2'];
	$city = $data_delivery_address['delivery_city'];
	$state = $data_delivery_address['delivery_state'];
	$postcode = $data_delivery_address['delivery_postcode'];
	$phone = $data_customer['phone'];
	$mobile_phone = $data_customer['mobile_phone'];
	$email = $data_customer['email'];

	$message = "<table cellpadding='5'>";
	$message .= "<tr><td>Order ID</td><td colspan='3'>$invoice_number</td></tr>";
	$message .= "<tr><td>First Name</td><td width='290'>$first_name</td><td>Last Name</td><td>$last_name</td></tr>";
	$message .= "<tr><td>Delivery Address</td><td>$address</td><td>Suburb</td><td>$city</td></tr>";
	$message .= "<tr><td>State</td><td>$state</td><td>Postcode</td><td>$postcode</td></tr>";
	$message .= "<tr><td>Phone</td><td>$phone</td><td>Mobile</td><td>$mobile_phone</td></tr>";
	$message .= "<tr><td>Email</td><td colspan='3'>$email</td></tr>";
	$message .= "<tr><td>Payment Method</td><td colspan='3'>Credit Card</td></tr>";
	$message .= "</table>";
	$message .= "<br><br>";

	$grand_total = 0;
	$message .= "<table cellpadding='5' cellspacing='0' border='1'>";
	$message .= "<tr><td>Qty</td><td>Description</td><td>Size</td><td>Each</td><td>Sub Total</td></tr>";
	$qry_sales_order_detail = mysql_query("SELECT * FROM wp_ecart_sales_order_detail a, wp_ecart_product b WHERE invoice_number='$invoice_number' AND b.product_id=a.product_id ORDER BY b.product_name");
	while($data_sales_order_detail = mysql_fetch_array($qry_sales_order_detail))
	{
		if($show_tax == "Y")
		{
			$price = $data_sales_order_detail['price_inc_tax'];
			$total = $data_sales_order_detail['total_inc_tax'];
		}
		else
		{
			$price = $data_sales_order_detail['price_exc_tax'];
			$total = $data_sales_order_detail['total_exc_tax'];
		}

		$price_label = "$".number_format($price, 2, '.', ',');;
		$total_label = "$".number_format($total, 2, '.', ',');

		$grand_total += $total;

		$quantity = $data_sales_order_detail['quantity'];
		$product_name = $data_sales_order_detail['product_name'];
		$product_amount = $data_sales_order_detail['product_amount'];

		$message .= "<tr><td>$quantity</td><td>$product_name</td><td>$product_amount</td><td>$price_label</td><td>$total_label</td></tr>";
	}

	if($show_tax == "Y")
	{
		$total_label = "Your Order Total - incl. GST (FREE Shipping!)";
	}
	else
	{
		$total_label = "Your Order Total";
	}

	$grand_total_label = "$".number_format($grand_total, 2, '.', ',');;

	$message .= "<tr><td>&nbsp;</td><td>$total_label</td><td>&nbsp;</td><td>&nbsp;</td><td>$grand_total_label</td></tr>";
	$message .= "</table>";

	$message .= "<b>Message :</b>";
	$message .= "<br>";
	$message .= nl2br($data_invoice['notes']);

	return $message;

}

function email_invoice_old($invoice_number)
{
	$qry_invoice = mysql_query("SELECT * FROM wp_ecart_sales_order WHERE invoice_number='$invoice_number' ");
	$data_invoice = mysql_fetch_array($qry_invoice);
	$customer_id = $data_invoice['customer_id'];

	$show_tax = check_show_tax($customer_id);

	$qry_delivery_address = mysql_query("SELECT * FROM wp_ecart_sales_order_delivery_address WHERE invoice_number='$invoice_number' ");
	$data_delivery_address = mysql_fetch_array($qry_delivery_address);

	$qry_customer = mysql_query("SELECT * FROM wp_ecart_customer WHERE customer_id='$customer_id' ");
	$data_customer = mysql_fetch_array($qry_customer);

	$first_name = $data_customer['first_name'];
	$last_name = $data_customer['last_name'];
	$address = $data_delivery_address['delivery_address_line1']." ".$data_delivery_address['delivery_address_line2'];
	$city = $data_delivery_address['delivery_city'];
	$state = $data_delivery_address['delivery_state'];
	$postcode = $data_delivery_address['delivery_postcode'];
	$phone = $data_customer['phone'];
	$mobile_phone = $data_customer['mobile_phone'];
	$email = $data_customer['email'];

	$to = $data_customer['email'];
	$subject = "Order Confirmation";
	$from = "Lilly Pilly Organics <orders@lillypillyorganics.com.au>";

	$message = "<div style='border:1px solid black; width:780; padding:10px; font-family:arial'>";
	$message .= "<img src='http://lillypillyorganics.com.au/images/pagetitles/logoEmail-slogan.jpg' width='776'>";

	$message .= "<h1 style='font-family:arial'>Thank you for your order</h1>";
	$message .= "We are currently processing your order.<br>The details of your order and details for direct deposit are listed below.";
	$message .= "<br><br>";
	$message .= "Bank Account Details for Direct Deposit:";
	$message .= "<br><br>";
	$message .= "Bendigo Bank<br>";
	$message .= "BSB: 633 000<br>";
	$message .= "Account Number: 1370 88472<br>";
	$message .= "Account name: Lilly Pilly Organics<br>";
	$message .= "<br><br>";

	$message .= "<table cellpadding='5'>";
	$message .= "<tr><td>Order ID</td><td colspan='3'>$invoice_number</td></tr>";
	$message .= "<tr><td>First Name</td><td width='290'>$first_name</td><td>Last Name</td><td>$last_name</td></tr>";
	$message .= "<tr><td>Delivery Address</td><td>$address</td><td>Suburb</td><td>$city</td></tr>";
	$message .= "<tr><td>State</td><td>$state</td><td>Postcode</td><td>$postcode</td></tr>";
	$message .= "<tr><td>Phone</td><td>$phone</td><td>Mobile</td><td>$mobile_phone</td></tr>";
	$message .= "<tr><td>Email</td><td colspan='3'>$email</td></tr>";
	$message .= "<tr><td>Payment Method</td><td colspan='3'>Credit Card</td></tr>";
	$message .= "</table>";
	$message .= "<br><br>";

	$grand_total = 0;
	$message .= "<table cellpadding='5' cellspacing='0' border='1'>";
	$message .= "<tr><td>Qty</td><td>Description</td><td>Size</td><td>Each</td><td>Sub Total</td></tr>";
	$qry_sales_order_detail = mysql_query("SELECT * FROM wp_ecart_sales_order_detail a, wp_ecart_product b WHERE invoice_number='$invoice_number' AND b.product_id=a.product_id ORDER BY b.product_name");
	while($data_sales_order_detail = mysql_fetch_array($qry_sales_order_detail))
	{
		if($show_tax == "Y")
		{
			$price = $data_sales_order_detail['price_inc_tax'];
			$total = $data_sales_order_detail['total_inc_tax'];
		}
		else
		{
			$price = $data_sales_order_detail['price_exc_tax'];
			$total = $data_sales_order_detail['total_exc_tax'];
		}

		$price_label = "$".number_format($price, 2, '.', ',');;
		$total_label = "$".number_format($total, 2, '.', ',');

		$grand_total += $total;

		$quantity = $data_sales_order_detail['quantity'];
		$product_name = $data_sales_order_detail['product_name'];
		$product_amount = $data_sales_order_detail['product_amount'];

		$message .= "<tr><td>$quantity</td><td>$product_name</td><td>$product_amount</td><td>$price_label</td><td>$total_label</td></tr>";
	}

	if($show_tax == "Y")
	{
		$total_label = "Your Order Total - incl. GST (FREE Shipping!)";
	}
	else
	{
		$total_label = "Your Order Total";
	}

	$grand_total_label = "$".number_format($grand_total, 2, '.', ',');;

	$message .= "<tr><td>&nbsp;</td><td>$total_label</td><td>&nbsp;</td><td>&nbsp;</td><td>$grand_total_label</td></tr>";
	$message .= "</table>";

	$message .= "<br><br>";

	$message .= "</div>";

	sendmail($to, $subject, $message, $from);
}

function check_show_tax($customer_id)
{
	if(show_ecart_config_new('show_tax') == "N")
		$show_tax = "N";
	else
		$show_tax = "Y";

	$qry = mysql_query("SELECT * FROM wp_ecart_customer_address WHERE customer_id='".$customer_id."' ");
	$data = mysql_fetch_array($qry);

	if($data['delivery_country'] != "Australia")
		$show_tax = "N";

	return $show_tax;
}

function show_ecart_config_new($config_name)
{
	$sql = mysql_query("SELECT config_value FROM wp_ecart_config where config_name='$config_name'");
	$results = mysql_fetch_array($sql);
	return $results['config_value'];
}


function sendmail($to, $subject, $message, $from)
{
	#$to = $this->to;
	#$from = $this->from;
	#$subject = $this->subject;

	//begin of HTML message
	#$message =  $this->body;

	$headers  = "From: $from\r\n";
	$headers .= "Content-type: text/html\r\n";

	$cc_list = show_ecart_config_new('cc_list');
	$cc_stat = show_ecart_config_new('cc_stat');
	//options to send to cc+bcc
	if($cc_stat == 1){

		if(explode(',',$cc_list)==false)
		{
		   echo "<script>alert('email cc is not correctly written');</script>";
		}else
		{
			if($cc_list!="")
			{
			$headers .= "Cc: $cc_list";
			}
		}
	}
	//$headers .= "Bcc: nucleus.logic@marquantinternational.com";

	// now lets send the email.

 mail($to, $subject, $message, $headers);
}


function response_code($code)
{
	$response_code = array(
		"00" => "Transaction Approved",
		"01" => "Refer to Issuer",
		"02" => "Refer to Issuer, special",
		"03" => "No Merchant",
		"04" => "Pick Up Card",
		"05" => "Do Not Honour",
		"06" => "Error",
		"07" => "Pick Up Card, Special",
		"08" => "Honour With Identification",
		"09" => "Request In Progress",
		"10" => "Approved For Partial Amount",
		"11" => "Approved, VIP 	approved",
		"12" => "Invalid Transaction",
		"13" => "Invalid Amount",
		"14" => "Invalid Card Number",
		"15" => "No Issuer",
		"16" => "Approved, Update Track 3",
		"19" => "Re-enter Last Transaction",
		"21" => "No Action Taken",
		"22" => "Suspected Malfunction",
		"23" => "Unacceptable Transaction Fee",
		"25" => "Unable to Locate Record On File",
		"30" => "Format Error",
		"31" => "Bank Not Supported By Switch",
		"33" => "Expired Card, Capture",
		"34" => "Suspected Fraud, Retain Card",
		"35" => "Card Acceptor, Contact Acquirer, Retain Card",
		"36" => "Restricted Card, Retain Card",
		"37" => "Contact Acquirer Security Department, Retain Card",
		"38" => "PIN Tries Exceeded, Capture",
		"39" => "No Credit Account",
		"40" => "Function Not Supported",
		"41" => "Lost Card",
		"42" => "No Universal Account",
		"43" => "Stolen Card",
		"44" => "No Investment Account",
		"51" => "Insufficient Funds",
		"52" => "No Cheque Account",
		"53" => "No Savings Account",
		"54" => "Expired Card",
		"55" => "Incorrect PIN",
		"56" => "No Card Record",
		"57" => "Function Not Permitted to Cardholder",
		"58" => "Function Not Permitted to Terminal",
		"59" => "Suspected Fraud",
		"60" => "Acceptor Contact Acquirer",
		"61" => "Exceeds Withdrawal Limit",
		"62" => "Restricted Card",
		"63" => "Security Violation",
		"64" => "Original Amount Incorrect",
		"66" => "Acceptor Contact Acquirer, Security",
		"67" => "Capture Card",
		"75" => "PIN Tries Exceeded",
		"82" => "CVV Validation Error",
		"90" => "Cutoff In Progress",
		"91" => "Card Issuer Unavailable",
		"92" => "Unable To Route Transaction",
		"93" => "Cannot Complete, Violation Of The Law",
		"94" => "Duplicate Transaction",
		"96" => "System Error"
	);

	return $response_code[$code];
}



function show_country_code($country)
{
	$array_country = get_array_country();
	$key = array_search($country, $array_country);

	return $key;
}

function get_array_country()
{
	$array_country = array(
		"AU" => "Australia",
		"AP" => "Asia/Pacific Region",
		"EU" => "Europe",
		"AD" => "Andorra",
		"AE" => "United Arab Emirates",
		"AF" => "Afghanistan",
		"AG" => "Antigua and Barbuda",
		"AI" => "Anguilla",
		"AL" => "Albania",
		"AM" => "Armenia",
		"AN" => "Netherlands Antilles",
		"AO" => "Angola",
		"AQ" => "Antarctica",
		"AR" => "Argentina",
		"AS" => "American Samoa",
		"AT" => "Austria",
		"AW" => "Aruba",
		"AZ" => "Azerbaijan",
		"BA" => "Bosnia and Herzegovina",
		"BB" => "Barbados",
		"BD" => "Bangladesh",
		"BE" => "Belgium",
		"BF" => "Burkina Faso",
		"BG" => "Bulgaria",
		"BH" => "Bahrain",
		"BI" => "Burundi",
		"BJ" => "Benin",
		"BM" => "Bermuda",
		"BN" => "Brunei Darussalam",
		"BO" => "Bolivia",
		"BR" => "Brazil",
		"BS" => "Bahamas",
		"BT" => "Bhutan",
		"BV" => "Bouvet Island",
		"BW" => "Botswana",
		"BY" => "Belarus",
		"BZ" => "Belize",
		"CA" => "Canada",
		"CC" => "Cocos (Keeling) Islands",
		"CD" => "Congo, The Democratic Republic of the",
		"CF" => "Central African Republic",
		"CG" => "Congo",
		"CH" => "Switzerland",
		"CI" => "Cote D'Ivoire",
		"CK" => "Cook Islands",
		"CL" => "Chile",
		"CM" => "Cameroon",
		"CN" => "China",
		"CO" => "Colombia",
		"CR" => "Costa Rica",
		"CU" => "Cuba",
		"CV" => "Cape Verde",
		"CX" => "Christmas Island",
		"CY" => "Cyprus",
		"CZ" => "Czech Republic",
		"DE" => "Germany",
		"DJ" => "Djibouti",
		"DK" => "Denmark",
		"DM" => "Dominica",
		"DO" => "Dominican Republic",
		"DZ" => "Algeria",
		"EC" => "Ecuador",
		"EE" => "Estonia",
		"EG" => "Egypt",
		"EH" => "Western Sahara",
		"ER" => "Eritrea",
		"ES" => "Spain",
		"ET" => "Ethiopia",
		"FI" => "Finland",
		"FJ" => "Fiji",
		"FK" => "Falkland Islands (Malvinas)",
		"FM" => "Micronesia, Federated States of",
		"FO" => "Faroe Islands",
		"FR" => "France",
		"FX" => "France, Metropolitan",
		"GA" => "Gabon",
		"GB" => "United Kingdom",
		"GD" => "Grenada",
		"GE" => "Georgia",
		"GF" => "French Guiana",
		"GH" => "Ghana",
		"GI" => "Gibraltar",
		"GL" => "Greenland",
		"GM" => "Gambia",
		"GN" => "Guinea",
		"GP" => "Guadeloupe",
		"GQ" => "Equatorial Guinea",
		"GR" => "Greece",
		"GS" => "South Georgia and the South Sandwich Islands",
		"GT" => "Guatemala",
		"GU" => "Guam",
		"GW" => "Guinea-Bissau",
		"GY" => "Guyana",
		"HK" => "Hong Kong",
		"HM" => "Heard Island and McDonald Islands",
		"HN" => "Honduras",
		"HR" => "Croatia",
		"HT" => "Haiti",
		"HU" => "Hungary",
		"ID" => "Indonesia",
		"IE" => "Ireland",
		"IL" => "Israel",
		"IN" => "India",
		"IO" => "British Indian Ocean Territory",
		"IQ" => "Iraq",
		"IR" => "Iran, Islamic Republic of",
		"IS" => "Iceland",
		"IT" => "Italy",
		"JM" => "Jamaica",
		"JO" => "Jordan",
		"JP" => "Japan",
		"KE" => "Kenya",
		"KG" => "Kyrgyzstan",
		"KH" => "Cambodia",
		"KI" => "Kiribati",
		"KM" => "Comoros",
		"KN" => "Saint Kitts and Nevis",
		"KP" => "Korea, Democratic People's Republic of",
		"KR" => "Korea, Republic of",
		"KW" => "Kuwait",
		"KY" => "Cayman Islands",
		"KZ" => "Kazakstan",
		"LA" => "Lao People's Democratic Republic",
		"LB" => "Lebanon",
		"LC" => "Saint Lucia",
		"LI" => "Liechtenstein",
		"LK" => "Sri Lanka",
		"LR" => "Liberia",
		"LS" => "Lesotho",
		"LT" => "Lithuania",
		"LU" => "Luxembourg",
		"LV" => "Latvia",
		"LY" => "Libyan Arab Jamahiriya",
		"MA" => "Morocco",
		"MC" => "Monaco",
		"MD" => "Moldova, Republic of",
		"MG" => "Madagascar",
		"MH" => "Marshall Islands",
		"MK" => "Macedonia, the Former Yugoslav Republic of",
		"ML" => "Mali",
		"MM" => "Myanmar",
		"MN" => "Mongolia",
		"MO" => "Macau",
		"MP" => "Northern Mariana Islands",
		"MQ" => "Martinique",
		"MR" => "Mauritania",
		"MS" => "Montserrat",
		"MT" => "Malta",
		"MU" => "Mauritius",
		"MV" => "Maldives",
		"MW" => "Malawi",
		"MX" => "Mexico",
		"MY" => "Malaysia",
		"MZ" => "Mozambique",
		"NA" => "Namibia",
		"NC" => "New Caledonia",
		"NE" => "Niger",
		"NF" => "Norfolk Island",
		"NG" => "Nigeria",
		"NI" => "Nicaragua",
		"NL" => "Netherlands",
		"NO" => "Norway",
		"NP" => "Nepal",
		"NR" => "Nauru",
		"NU" => "Niue",
		"NZ" => "New Zealand",
		"OM" => "Oman",
		"PA" => "Panama",
		"PE" => "Peru",
		"PF" => "French Polynesia",
		"PG" => "Papua New Guinea",
		"PH" => "Philippines",
		"PK" => "Pakistan",
		"PL" => "Poland",
		"PM" => "Saint Pierre and Miquelon",
		"PN" => "Pitcairn",
		"PR" => "Puerto Rico",
		"PS" => "Palestinian Territory, Occupied",
		"PT" => "Portugal",
		"PW" => "Palau",
		"PY" => "Paraguay",
		"QA" => "Qatar",
		"RE" => "Reunion",
		"RO" => "Romania",
		"RU" => "Russian Federation",
		"RW" => "Rwanda",
		"SA" => "Saudi Arabia",
		"SB" => "Solomon Islands",
		"SC" => "Seychelles",
		"SD" => "Sudan",
		"SE" => "Sweden",
		"SG" => "Singapore",
		"SH" => "Saint Helena",
		"SI" => "Slovenia",
		"SJ" => "Svalbard and Jan Mayen",
		"SK" => "Slovakia",
		"SL" => "Sierra Leone",
		"SM" => "San Marino",
		"SN" => "Senegal",
		"SO" => "Somalia",
		"SR" => "Suriname",
		"ST" => "Sao Tome and Principe",
		"SV" => "El Salvador",
		"SY" => "Syrian Arab Republic",
		"SZ" => "Swaziland",
		"TC" => "Turks and Caicos Islands",
		"TD" => "Chad",
		"TF" => "French Southern Territories",
		"TG" => "Togo",
		"TH" => "Thailand",
		"TJ" => "Tajikistan",
		"TK" => "Tokelau",
		"TM" => "Turkmenistan",
		"TN" => "Tunisia",
		"TO" => "Tonga",
		"TP" => "East Timor",
		"TR" => "Turkey",
		"TT" => "Trinidad and Tobago",
		"TV" => "Tuvalu",
		"TW" => "Taiwan, Province of China",
		"TZ" => "Tanzania, United Republic of",
		"UA" => "Ukraine",
		"UG" => "Uganda",
		"UM" => "United States Minor Outlying Islands",
		"US" => "United States",
		"UY" => "Uruguay",
		"UZ" => "Uzbekistan",
		"VA" => "Holy See (Vatican City State)",
		"VC" => "Saint Vincent and the Grenadines",
		"VE" => "Venezuela",
		"VG" => "Virgin Islands, British",
		"VI" => "Virgin Islands, U.S.",
		"VN" => "Vietnam",
		"VU" => "Vanuatu",
		"WF" => "Wallis and Futuna",
		"WS" => "Samoa",
		"YE" => "Yemen",
		"YT" => "Mayotte",
		"YU" => "Yugoslavia",
		"ZA" => "South Africa",
		"ZM" => "Zambia",
		"ZR" => "Zaire",
		"ZW" => "Zimbabwe"
	);

	return $array_country;
}


?>