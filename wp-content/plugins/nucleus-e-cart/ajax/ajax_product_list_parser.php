<?php
$no = $_GET['no'];

$ABSPATH = base64_decode($_GET['ABSPATH']);
include_once($ABSPATH."wp-config.php");

include_once(ECART_DIR."define_file.php");
include_once(ECART_DIR."functions/common_function.php");

define('ECART_LINK', base64_decode($_GET['ECART_LINK']));

$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die (mysql_error());
mysql_select_db(DB_NAME, $conn);
$atts = $_GET['short_code_setting'];
//$atts = str_replace ('\"','"', $atts);
//$atts = preg_replace('/,\s*([\]}])/m', '$1', utf8_encode($atts));
//$atts = json_decode($atts,true);
//$ser =  $_GET['short_code_setting'];
## setting variable
if(is_array($atts))
{
	if($atts['view']!="" && $atts['view'] =="list" || $atts['view'] == "grid")
	{
		$modview = $atts['view'];
	}
	else
	{
		$modview = "list";
	}
		if($atts['columns']!="" && $atts['columns'] != 0)
		{
		 $modcolumns = (int)$atts['columns'];
		}
		else
		{
		 $modcolumns = 1;
		}
			$modshowcategory = $atts['showcategory'];
			$modcategory = $atts['category'];
			$modsubcategory = $atts['subcategory'];
			$modorders = $atts['order'];
			$modordersby = $atts['orderby'];
			$modimage = $atts['image'];
			$moddescription = $atts['description'];
			$moddetails = $atts['details'];
			$modprice = $atts['price'];
			$modattribute = $atts['attribute'];
			$modattributevalue = $atts['attribute_value'];
			$modshowattribute = $atts['show_attribute'];
			$modname = $atts['pname'];
}
else
{
	//echo $atts.'ok';
	$modview = "list";
	$modcolumns = 1;
	$modshowcategory = "Y";
	$modcategory = "";
	$modsubcategory = "";
	$modorders = "";
	$modordersby = "";
	$modimage = "Y";
	$moddescription = "Y";
	$moddetails = "Y";
	$modprice = "Y";
	$modattribute = '';
	$modattributevalue = '';
	$modshowattribute = 'Y';
	$modname = "Y";
}
##

$tr = "";

$no++;
$countcolumn=0;
$product_type_id = $_GET['product_type_id'];
$product_category_id = $_GET['product_category_id'];

##product category by shortcode
if($modcategory!="")
{

	$mod_product_type_id = array();
	$modcategory = explode("||",$modcategory);
	foreach ($modcategory as $key => $value)//take care quote mark
	{
		$cleanstr = str_replace("'","\'", $value);
		$modcategory[$key] = $cleanstr;
	}

	$modstring = implode("','",$modcategory);
	$modstring = trim($modstring);
	$modstring = preg_replace("/#?[a-z0-9]+;/i","",$modstring);//take care # mark and amp; or 038;

	$modsql = "select
					b.product_type_id
				from
					wp_ecart_product_category a,
					wp_ecart_product_category_type_link b
				where
					a.category IN ('$modstring') and
					b.product_category_id = a.product_category_id
				order by
					b.product_type_id";
	$query = mysql_query($modsql);

	while($result = mysql_fetch_array($query))
	{
		$mod_product_type_id[] =  $result['product_type_id'];
	}

	if(is_array($mod_product_type_id) && count($mod_product_type_id)>0)
	{
		//$catcount = count($mod_product_type_id);
		$typestring = implode("','",$mod_product_type_id);
		$modqry_product_type = " and product_type_id IN('$typestring') ";
	}
	else
	{
		$modqry_product_type='';
	}

		###modcategory sub
		if($modsubcategory != '') {
			$mod_subproduct_type_id = array();
			$modsubcategory = explode("||",$modsubcategory);
			foreach ($modsubcategory as $key => $value)//take care quote mark
			{
				$cleanstr = str_replace("'","\'", $value);
				$modsubcategory[$key] = $cleanstr;
			}

			$modsubstring = implode("','",$modsubcategory);
			$modsubstring = trim($modsubstring);
			$modsubstring = preg_replace("/#?[a-z0-9]+;/i","",$modsubstring);//take care # mark and amp; or 038;

			$sql = "SELECT product_type_id FROM wp_ecart_product_type where type IN ('$modsubstring') ";
			$query = mysql_query($sql);

			if(mysql_num_rows($query)>0)
			{
				while($result = mysql_fetch_array($query))
				{
					$mod_subproduct_type_id[] =  $result['product_type_id'];
				}
				$typestring = implode("','",$mod_subproduct_type_id);
				$modqry_product_type = " and product_type_id IN('$typestring') ";
			}

		}
		###

}
##


###orderby shortcode
if($modordersby!=""){

$mod_order = array();
$modordersby = explode(",",$modordersby);
$modorderquery="";
$ordstring="";

	if($modorders!=''){$ordstring=$modorders;}

	foreach($modordersby as $value)
	{
		if($value=='price')
		{
			if($show_tax == "Y")
			{
					$mod_order[] = "selling_price_inc_tax $ordstring";
					$mod_order[] = "special_selling_price_inc_tax $ordstring";
			}
			else
			{
					$mod_order[] = "selling_price_exc_tax $ordstring";
					$mod_order[] = "special_selling_price_exc_tax $ordstring";
			}
		}

			if($value=='category')
			{
					$mod_order[]="product_type_id $ordstring";
			}

				if($value=='product_name')
				{
					$mod_order[]="product_name $ordstring";
				}


	}

		if(count($mod_order)>0)
		{
			$modorderquery = implode(",",$mod_order);
		}

	//echo $modorderquery;

}
else
{
	$modorderquery = "product_name";
}
###

####attributeshortcode
		if($modattribute!="" && $modattributevalue !=""){
			$mod_attribute = array();
			$modattribute_type = explode("||",$modattribute);
			$modattribute_value= explode("||",$modattributevalue);
			$modattribute_typelist="";
			$modattribute_valuelist="";
			$modwhereattribute = "";

			foreach ($modattribute_type as $key => $value)//take care quote mark
			{
				$cleanstr = str_replace("'","\'", $value);
				$modattribute_typelist = trim($cleanstr);
				$modattribute_typelist = preg_replace("/#?[a-z0-9]+;/i","",$modattribute_typelist);//take care # mark and amp; or 038;
				$modattribute_type[$key] = $modattribute_typelist;
			}

			foreach ($modattribute_value as $mkey => $mvalue)//take care quote mark
			{
				$cleanstr = str_replace("'","\'", $mvalue);
				$modattribute_valuelist = trim($cleanstr);
				$modattribute_valuelist = preg_replace("/#?[a-z0-9]+;/i","",$modattribute_valuelist);//take care # mark and amp; or 038;
				$modattribute_value[$mkey] = $modattribute_valuelist;
			}

			for($i=0;$i<count($modattribute_type);$i++)
			{
				//echo $i." || ";
				if(count($modattribute_type) == 1 || $i == count($modattribute_type)-1)
				{
					$connector = "";

				}
				else
				{
					$connector = " OR ";
				}
				$modwhereattribute .= " b.attribute_type = '".$modattribute_type[$i]."' AND b.attribute_value = '".$modattribute_value[$i]."' ".$connector  ;

			}
			// WHERE b.attribute_type IN('$modattribute_typelist') AND b.attribute_value IN('$modattribute_valuelist')
			$attributequerystring="SELECT distinct a.*
			 						  FROM wp_ecart_product as a
			 						  RIGHT OUTER JOIN wp_ecart_product_attribute as b ON a.product_id = b.product_id
			 						  WHERE (".$modwhereattribute.")";

			$query = mysql_query($attributequerystring);

			if(mysql_num_rows($query) <= 0)
			{
				$attributequerystring = "SELECT * FROM wp_ecart_product WHERE product_id !='' ";
			}

		}
		####



if($product_type_id == "0" || $product_type_id == "")
{
	$query_product_type = "SELECT product_type_id FROM wp_ecart_product_category_type_link WHERE product_category_id='$product_category_id'";
	$qry_product_type = " AND product_type_id IN ($query_product_type)";

	if($product_category_id == "0" || $product_category_id == "")
		$qry_product_type = "";

	$orderquery="product_name";
}
else
{
	$qry_product_type = " AND product_type_id=\"$product_type_id\" ";
	$orderquery="product_name";
}

$show_tax = check_show_tax();
$limit = 10;
while($limit%$modcolumns!=0 || $limit%$modcolumns == $limit)
{
	$limit++;
}

   if($modattribute!="" && $modattributevalue !=""){
   	$qry_product = mysql_query($attributequerystring." AND inactive <> 'Y' $modqry_product_type ORDER BY $modorderquery LIMIT $no,$limit");
   }
   elseif($modcategory!=""){
   	$qry_product = mysql_query("SELECT * FROM wp_ecart_product WHERE inactive <> 'Y' $modqry_product_type ORDER BY $modorderquery LIMIT $no,$limit");
   }
   else{
    $qry_product = mysql_query("SELECT * FROM wp_ecart_product WHERE inactive <> 'Y' $qry_product_type ORDER BY $orderquery LIMIT $no,$limit");
   }


$num_product = mysql_num_rows($qry_product);
while($result = mysql_fetch_array($qry_product))
{
	$product_id = $result['product_id'];

	$link_image = $result['product_image'];
	$product_nametext = $result['product_name'];
	$product_description = $result['product_description'];
	
	//$product_image = "<a href='$link_image' class='MagicThumb' rel='image-size: fit-screen; buttons-display:close' onclick='return false;'>";
	$product_image = "<a href='$link_image' title='".$result['product_name']."' rel='lightbox' onclick='return false;'>";
	$product_image .= "<img src='$link_image' class='product_list_img' >";
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
		$pricehide = "<input type='hidden' name='price_inc_tax_$product_id' value='".$result['selling_price_inc_tax']."'>";
		$pricehide .= "<input type='hidden' name='price_exc_tax_$product_id' value='".$result['selling_price_exc_tax']."'>";
	}
	else
	{
		$price = "<del>$".number_format($selling_price, 2, '.', ',')."</del> " . "<b>$".number_format($special_selling_price, 2, '.', ',')."</b>";
		$pricehide = "<input type='hidden' name='price_inc_tax_$product_id' value='".$result['special_selling_price_inc_tax']."'>";
		$pricehide .= "<input type='hidden' name='price_exc_tax_$product_id' value='".$result['special_selling_price_exc_tax']."'>";
	}

	$product_nametext = wordwrap($product_nametext, 20, "\n", true);
	$product_name = "<a href='".ECART_LINK."ecart=product_detail&product_id=$product_id' class='ecart_link'>".$product_nametext."</a>";

	if($no%2 == 0){
		$tr_product_list = "tr_product_list_ganjil";}
	else{
		$tr_product_list = "tr_product_list_genap";}

	$product_attribute = "";
	$qry_product_attribute = mysql_query("SELECT * FROM wp_ecart_product_attribute WHERE product_id='$product_id' ");
	while($data_product_attribute = mysql_fetch_array($qry_product_attribute))
	{
		$attribute_type = $data_product_attribute['attribute_type'];
		$attribute_value = $data_product_attribute['attribute_value'];

		$product_attribute .= show_product_attribute($attribute_type, $attribute_value);
	}

	$qty = "<input type='text' name='qty_$product_id' style='width:50px; text-align:center'>";
	$qty .= "<input type='hidden' name='arr_product_id[]' value='$product_id'>";

	If($modview=="list")
	{
		$tr .=  "<tr class='tr_product_list $tr_product_list' id='$no'>";
		if($modimage!="N")
		{
			$tr .=  "<td>".$product_image."</td>";
		}
			$tr .=  "<td>".$qty."</td>".$pricehide;

		if($modprice!="N")
		{
			$tr .=  "<td>".$price."</td>";
		}

			$tr .=	"<td>".$result['product_amount']."</td>";
		if($modshowattribute !="N")
		{
			$tr .=	"<td>".$product_attribute."</td>";
		}
		if($moddescription !="N")
		{
			$tr .=	"<td>".$product_description."</td>";
		}
		if($modname!="N")
		{
			$tr .=	"<td>".$product_name."</td>";
		}
		$tr .=	"</tr>";
	}
	else
	{//gridview

		$product_image = "<a href='$link_image' title='".$product_nametext."' rel='lightbox' onclick='return false;'>";
		$product_image .= "<img src='$link_image' width='100' style='display:block'>";
		$product_image .= "</a>";
		//$product_image = $product_image;
		$modwidth = (100/$modcolumns)."%";
		$tdstring .= "<div class='griddiv' style='width:$modwidth;'>";
		if($modimage!="N")
		{
			$tdstring .= $product_image;
		}
		if($modprice=="Y")
		{
			$tdstring .= $qty."<br>";
			$tdstring .= $price.$pricehide."<br>";
		}
		if($modname!="N")
		{
			$tdstring .= $product_name;
		}
		$tdstring .= "</div>";

		$countcolumn++;
		if($countcolumn == $modcolumns)
		{
			$tr .= "<tr class='tr_product_list $tr_product_list' id='$no'>";  // create a bounding table
			$tr .= "<td style='border:0;'>".$tdstring."</td>";
			//$tr .= "<<td>$price</td>";
			//$tr .= "<td>$qty</td>.$pricehide";
			//$tr .= "<tr><td colspan =\"2\"><a href=\"singleProduct.php?pid=$productID&uid=$uid\">View Product<br/><br/><br/></td><td></td></tr>";
			$tr .= "</tr>";
			$tdstring ="";

			$countcolumn=0;
		}
		else if($countcolumn > 0 && $countcolumn < $modcolumns)
		{
			if($no == $num_product-1)
			{
				$tr .= "<tr class='tr_product_list $tr_product_list' id='$no'>";  // create a bounding table
				$tr .= "<td style='border:0;'>".$tdstring."</td>";
				//$tr .= "<<td>$price</td>";
				//$tr .= "<td>$qty</td>.$pricehide";
				//$tr .= "<tr><td colspan =\"2\"><a href=\"singleProduct.php?pid=$productID&uid=$uid\">View Product<br/><br/><br/></td><td></td></tr>";
				$tr .= "</tr>";
				$tdstring ="";

				$countcolumn=0;
			}
		}

	}

	$no++;
}

$arr['product_list'][] = $tr;

echo json_encode($arr);

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
?>