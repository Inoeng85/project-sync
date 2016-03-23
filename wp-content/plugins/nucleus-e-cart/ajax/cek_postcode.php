<?php
$ABSPATH = base64_decode($_POST['ABSPATH']);
include_once($ABSPATH."wp-config.php");

$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die (mysql_error());

mysql_select_db(DB_NAME, $conn);

$postcode = $_POST['postcode'];

$qry_cek_postcode = mysql_query("select * from wp_ecart_postcode where postcode=\"$postcode\" ");
if(mysql_num_rows($qry_cek_postcode) == 0)
echo "not_match";
else
echo "match";

?>