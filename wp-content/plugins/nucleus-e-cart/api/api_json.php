<?php
ob_start();
include_once("../define_file.php");
include_once(ABSPATH."wp-config.php");
include_once(ECART_DIR."eway_function/Rapid3.0.php");
ob_clean() ;

$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die (mysql_error());
mysql_select_db(DB_NAME, $conn);

$xml = file_get_contents('php://input');

global $request;
$request = json_decode($xml);

$company_name = $request->company_name;
$company_url = $request->company_url;
$nucleus_api_username = $request->nucleus_api_username;
$nucleus_api_password = $request->nucleus_api_password;
$nucleus_api_key = $request->nucleus_api_key;

$qry_cek = mysql_query("SELECT * FROM wp_ecart_nucleus_api_config WHERE 
																		company_name = \"$company_name\" AND
																		company_url = \"$company_url\" AND 
																		nucleus_api_username = \"$nucleus_api_username\" AND
																		nucleus_api_password = \"$nucleus_api_password\" AND
																		nucleus_api_key = \"$nucleus_api_key\"
																	") or die (mysql_error());
		
if(mysql_num_rows($qry_cek) != 0)
{
	/*
	if($request->requestMethod == "Customer")
	{
		include("customer.php");
	}
	if($request->requestMethod == "Sales")
	{
		include("sales.php");
	}
	*/
	if($request->requestMethod == "import_product_from_cloud")
	{
		include("import_product_from_cloud.php");
	}
}

?>