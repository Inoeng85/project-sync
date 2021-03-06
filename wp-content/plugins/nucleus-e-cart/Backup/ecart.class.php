<?php
//session_destroy();
		
class ecart{
	var $from       = "nucleus.logic@marquantinternational.com";
	var $to         = "nucleus.logic@marquantinternational.com";
	var $subject    = "Just Test Message!";
	var $body       = "<b>Just Test Message!</b>";

	function __construct() 
	{
		if ( get_option( 'permalink_structure' ) != '' )
			$separator = "?";
		else
			$separator = "&";

		define('ECART_LINK', esc_url(get_option('ecart')) . $separator);
		
		$https_url = show_ecart_config("https_url");
		$site_url = get_site_url();

		$HTTPS_ECART_LINK = str_replace($site_url,$https_url, ECART_LINK);
		define('HTTPS_ECART_LINK', $HTTPS_ECART_LINK);
		
		//echo '<link href="'.ECART_URL.'library/magicthumb.css" rel="stylesheet" type="text/css" media="screen"/>';
		//echo '<script src="'.ECART_URL.'library/magicthumb.js" type="text/javascript"></script>';
		echo '<link href="'.ECART_URL.'library/lightbox.css" rel="stylesheet" type="text/css" media="screen"/>';
        echo '<script src="'.ECART_URL.'library/lightbox.js" type="text/javascript"></script>';
		echo '<script src="'.ECART_URL.'library/validation.js" type="text/javascript"></script>';
		
		echo '<style>#lightbox{display:none}</style>';
		
		if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' && $_GET['sid'] != session_id())
		{
			$old_session = $this->dec_session($_GET['es']);
			$sid = $_GET['sid'];
			session_id($sid);
			ob_start();
			session_start();
			ob_clean() ;
			
			if($_GET['es'])
				$_SESSION = $old_session;
		}
		
		if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
		{
			$this->js_autorun();
		}
		/*
		echo "<pre>";
		print_r($_SESSION);
		echo "</pre>";
		*/
	}
	
	function enc_session()
	{
		$session = serialize($_SESSION);
		$session = base64_encode($session);
		
		return $session;
	}
	
	function dec_session($session)
	{
		$session = base64_decode($session);
		$session = unserialize($session);
		
		return $session;
	}
	
	function logout()
	{
		if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
		{
			session_destroy();
			wp_redirect(ECART_LINK."ecart=logout"); exit;
		}
		else
		{
			mysql_query("DELETE FROM wp_ecart_shopping_cart WHERE unique_id='".session_id()."' ");
			
			session_destroy();
			wp_redirect(ECART_LINK); exit;
		}
	}
	
	function product_list()
	{
		$this->add_to_cart_bulk();
		$this->product_type();
		$show_tax = $this->check_show_tax();
		
		include_once(ECART_DIR."functions/common_function.php");
		
		/*
		echo "Click here for 'How to use our online shop'.<br>
		Dairy Orders - Please confirm your delivery arrangements in the Message field on the Checkout Page. Please contact us if you need assistance.";
		*/
		$content = show_ecart_config('product_list_content_management');
		echo nl2br($content);
		
		echo "<input type='hidden' id='ABSPATH' value=\"".base64_encode(ABSPATH)."\">";
		echo "<input type='hidden' id='ECART_LINK' value=\"".base64_encode(ECART_LINK)."\">";
		
		echo "<div id='product_list_content'>";
		
		$qry_all_product = mysql_query("SELECT * FROM wp_ecart_product WHERE inactive <> 'Y'");
		$num_all_product = mysql_num_rows($qry_all_product);
		
		$no = 0;
		$qry_product = mysql_query("SELECT * FROM wp_ecart_product WHERE inactive <> 'Y' LIMIT 20");
		$num_product = mysql_num_rows($qry_product);
		while($result = mysql_fetch_array($qry_product))
		{
			$product_id = $result['product_id'];
			 
			$link_image = $result['product_image'];					
			//$product_image = "<a href='$link_image' class='MagicThumb ecart_link' rel='image-size: fit-screen; buttons-display:close' onclick='return false;'>";
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
			
		$table = "<h1>Fresh Local Produce</h1>";

		if($num_product == 0)
		{
			$table .= "There is no product on this category";
		}
		else
		{
			$trigger_load = $no-1;
			$table .= "<form method='POST'>";
			$table .= "<table class='table_product'>";
			$table .= "<tr><td>Pic</td><td>Quantity</td><td>Price</td><td>Amount</td><td>&nbsp;</td><td>Product</td></tr>";
			$table .= $tr;
			$table .= "</table>";
			$table .= "<div class='div_add_to_cart'><input type='submit' value='' name='add_to_cart' class='button_add_to_cart'></div>";
			$table .= "<input type='hidden' value='$num_all_product' id='num_all_product'>";
			$table .= "<input type='hidden' value='$trigger_load' id='trigger_load'>";
			$table .= "<input type='hidden' value='0' id='product_category_id'>";
			$table .= "<input type='hidden' value='0' id='product_type_id'>";
			
			$table .= "</form>";
		}
		
		echo $table;
		
		echo "</div>";
		echo "<div id='last_msg_loader' align='center'></div>";
		
		?>
		<script>
			var trigger_load = jQuery("#trigger_load").val(); 
			trigger_load = parseInt(trigger_load);
			var product_type_id = jQuery("#product_type_id").val(); 
			var product_category_id = jQuery("#product_category_id").val(); 
			
			var p = jQuery(".product_type");
			var position = p.position();
			
			//var lastp = jQuery("#last_msg_loader");
			//var lastposition = lastp.position(); 
			var lastposition;
			var endposition;
			var bottom_window;
			var center_window;
			
			jQuery(window).scroll(function(){	
			
				endposition = jQuery("#content").position(); 
				bottom_window = endposition.top + jQuery("#content").height();
				
				center_window = jQuery(window).height() / 1.5;
			
				if(jQuery(window).scrollTop() > position.top && jQuery(window).scrollTop() < (bottom_window - center_window))
				{
					jQuery(".div_add_to_cart").show();
				}
				else
				{
					jQuery(".div_add_to_cart").hide();
				}
					
				//if  (jQuery(window).scrollTop() == jQuery(document).height() - jQuery(window).height()){
				lastposition = jQuery("#last_msg_loader").position(); 
				
				if  (jQuery(window).scrollTop() > (lastposition.top - jQuery(window).height())){
					
					if(product_category_id != jQuery("#product_category_id").val())
					{
						trigger_load = jQuery("#trigger_load").val();
						trigger_load = parseInt(trigger_load);						
					}
					
					var ID=jQuery(".tr_product_list:last").attr("id");
					var num_all_product = jQuery("#num_all_product").val();
					
					num_all_product = parseInt(num_all_product);
					ID = parseInt(ID);
					
					if(ID < num_all_product && ID >= trigger_load)
					{	
						trigger_load = ID + 5;
								
						jQuery("#trigger_load").val(trigger_load);
						scroll_product();
					}
				}
			}); 
			
			function scroll_product()
			{
				var ID=jQuery(".tr_product_list:last").attr("id");
				jQuery('div#last_msg_loader').html("<img src='<?php echo ECART_URL ?>/images/loading_icon_f.gif'  style='box-shadow:none; border-radius:0;'>");
				
				var url_link = "<?php echo ECART_URL ?>ajax/ajax_product_list_parser.php?ABSPATH=" + jQuery("#ABSPATH").val() + "&ECART_LINK=" + jQuery("#ECART_LINK").val() + "&no=" + ID + "&product_type_id=" + jQuery("#product_type_id").val() + "&product_category_id=" + jQuery("#product_category_id").val();
			
				jQuery.ajax({
					type: "GET",
					dataType: "json",
					beforeSend: function(x) {
						if(x && x.overrideMimeType) {
							x.overrideMimeType("application/json;charset=UTF-8");
						}
					},
					url: url_link,
					success: function(data) {
						var arr_data = data.product_list;
						
						jQuery.each(arr_data,function(index, value){ 
							
							jQuery(".tr_product_list:last").after(value);
							jQuery('div#last_msg_loader').empty();
						});
						//MagicThumb.stop();
						//MagicThumb.refresh();
					}
				});			
			}
			
		</script>
		<?php
	
	}
	
	function order_success()
	{
		$this->css_style();
		$invoice_number = $_GET['invoice_number'];
		
		$content = show_ecart_config('order_success_content_management');
		$message = nl2br($content);
		
		if($invoice_number != "")
			$detail_order = $this->detail_order($invoice_number);
	
		$message = str_replace("{order_detail}",$detail_order,$message);
		
		echo $message;
		
	}
	
	function detail_order($invoice_number)
	{
		$qry_invoice = mysql_query("SELECT * FROM wp_ecart_sales_order WHERE invoice_number='$invoice_number' ");
		$data_invoice = mysql_fetch_array($qry_invoice);
		$customer_id = $data_invoice['customer_id'];
		
		$show_tax = $this->check_show_tax_success($customer_id);
		
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
		
		$message = "<table cellpadding='5' class='detail_order'>";
		$message .= "<tr><td>Order ID</td><td colspan='3'><b>$invoice_number</b></td></tr>";
		$message .= "<tr><td>First Name</td><td width='200'>$first_name</td><td>Last Name</td><td>$last_name</td></tr>";
		$message .= "<tr><td>Delivery Address</td><td>$address</td><td>Suburb</td><td>$city</td></tr>";
		$message .= "<tr><td>State</td><td>$state</td><td>Postcode</td><td>$postcode</td></tr>";
		$message .= "<tr><td>Phone</td><td>$phone</td><td>Mobile</td><td>$mobile_phone</td></tr>";
		$message .= "<tr><td>Email</td><td colspan='3'>$email</td></tr>";
		$message .= "<tr><td>Payment Method</td><td colspan='3'>Credit Card</td></tr>";
		$message .= "</table>";
		
		$grand_total = 0;
		$message .= "<table cellpadding='5' cellspacing='0' border='1' class='product_type'>";
		$message .= "<tr><td style='text-align:center'>Qty</td><td>Description</td><td style='text-align:center'>Size</td><td style='text-align:center'>Each</td><td style='text-align:center'>Sub Total</td></tr>";
		$qry_sales_order_detail = mysql_query("SELECT * FROM wp_ecart_sales_order_detail a, wp_ecart_product b WHERE invoice_number='$invoice_number' AND b.product_id=a.product_id ");
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
				
			$message .= "<tr><td style='text-align:center'>$quantity</td><td>$product_name</td><td style='text-align:center'>$product_amount</td><td style='text-align:center'>$price_label</td><td style='text-align:center'>$total_label</td></tr>";
		}
		
		if($show_tax == "Y")
		{
			$total_label = "Your Order Total - incl. GST (FREE Shipping!)";
		}
		else
		{
			$total_label = "Your Order Total";
		}
		
		$grand_total_label = "$".number_format($grand_total, 2, '.', ',');
		
		$message .= "<tr><td>&nbsp;</td><td colspan='3'>$total_label</td><td style='text-align:center'><b>$grand_total_label</b></td></tr>";
		$message .= "</table>";
	
		return $message;
		
	}
	
	function check_show_tax_success($customer_id)
	{		
		if($this->show_ecart_config_new('show_tax') == "N")
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
	
	function product_list_old()
	{		
		global $wpdb;
		
		$this->add_to_cart_bulk();
		$this->product_type();
		
		echo "Click here for 'How to use our online shop'.<br>
		Dairy Orders - Please confirm your delivery arrangements in the Message field on the Checkout Page. Please contact us if you need assistance.";
		
		echo "<input type='hidden' id='ABSPATH' value=\"".base64_encode(ABSPATH)."\">";
		echo "<input type='hidden' id='ECART_LINK' value=\"".base64_encode(ECART_LINK)."\">";
		
		echo "<div id='product_list_content'>";
		echo "<h1>Fresh Local Produce</h1>";
		
		$sql = "SELECT * FROM ".ECART_TBL_PREFIX."product where inactive <> 'Y' LIMIT 20 ";
		$results = $wpdb->get_results($sql);

		echo "<form method='POST'>";
		echo "<table class='table_product'>";
		echo "<tr><td>Pic</td><td>Quantity</td><td>Price</td><td>Amount</td><td>Product</td></tr>";
		
		$show_tax = $this->check_show_tax();
		
		foreach($results as $result)
		{
			$product_id = $result->product_id;
			
			$link_image = "http://www.lillypillyorganics.com.au/ProductImages%5C311.jpg";
			$link_image = $result->product_image;
			
			$product_image = "<a href='$link_image' class='MagicThumb' rel='image-size: fit-screen; buttons-display:close' onclick='return false;' class='ecart_link'>";
			$product_image .= "<img src='$link_image' width='50' style='display:block'>";
			$product_image .= "</a>";
			
			if($show_tax == "Y")
			{
				$selling_price = $result->selling_price_inc_tax;
				$special_selling_price = $result->special_selling_price_inc_tax;	
			}
			else
			{
				$selling_price = $result->selling_price_exc_tax;
				$special_selling_price = $result->special_selling_price_exc_tax;
			}
			
			if($result->product_on_special == 0)
			{
				$price = "$".number_format($selling_price, 2, '.', ',');
				$price .= "<input type='hidden' name='price_inc_tax_$product_id' value='".$result->selling_price_inc_tax."'>";
				$price .= "<input type='hidden' name='price_exc_tax_$product_id' value='".$result->selling_price_exc_tax."'>";
			}
			else
			{
				$price = "<del>$".number_format($selling_price, 2, '.', ',')."</del> " . "<b>$".number_format($special_selling_price, 2, '.', ',')."</b>";
				$price .= "<input type='hidden' name='price_inc_tax_$product_id' value='".$result->special_selling_price_inc_tax."'>";
				$price .= "<input type='hidden' name='price_exc_tax_$product_id' value='".$result->special_selling_price_exc_tax."'>";
			}
			
			$product_name = "<a href='".ECART_LINK."ecart=product_detail&product_id=$product_id' class='ecart_link'>".$result->product_name."</a>";
			
			$qty = "<input type='text' name='qty_$product_id' style='width:50px; text-align:center'>";
			$qty .= "<input type='hidden' name='arr_product_id[]' value='$product_id'>";
			echo "<tr><td>".$product_image."</td><td>".$qty."</td><td>".$price."</td><td>".$result->product_amount."</td><td>".$product_name."</td></tr>";
		}
		
		echo "</table>";
		echo "<input type='submit' value='Add To Cart' name='add_to_cart'>";
		echo "</form>";
		echo "</div>";
	
	}
	
	function add_to_cart_bulk()
	{
		if(isset($_POST['add_to_cart']))
		{
			global $wpdb;
			
			$unique_id = session_id();
			$ip_address = $_SERVER['REMOTE_ADDR'];
			
			$arr_product_id = $_POST['arr_product_id'];
		
			foreach($arr_product_id as $product_id)
			{
				$quantity = $_POST['qty_'.$product_id];
				$price_inc_tax = $_POST['price_inc_tax_'.$product_id];
				$price_exc_tax = $_POST['price_exc_tax_'.$product_id];
				
				if($quantity != "")
				{
					$sql = "SELECT * FROM ".ECART_TBL_PREFIX."shopping_cart WHERE unique_id=\"$unique_id\" AND product_id=\"$product_id\" ";
					$wpdb->query($sql);
					if($wpdb->num_rows == 0)
					{
						$wpdb->query("INSERT INTO ".ECART_TBL_PREFIX."shopping_cart (
																						unique_id,
																						ip_address,
																						product_id,
																						quantity,
																						price_inc_tax,
																						price_exc_tax
																					) 
																					VALUES
																					(
																						\"$unique_id\",
																						\"$ip_address\",
																						\"$product_id\",
																						\"$quantity\",
																						\"$price_inc_tax\",
																						\"$price_exc_tax\"
																					)																	
																					");
					}
					else
					{
						$results = $wpdb->get_results($sql);
						$quantity = $quantity + $results[0]->quantity;	
								
						$wpdb->query("UPDATE ".ECART_TBL_PREFIX."shopping_cart SET 
																					price_inc_tax = \"$price_inc_tax\",
																					price_exc_tax = \"$price_exc_tax\",
																					quantity = \"$quantity\" 
																					WHERE 
																					unique_id=\"$unique_id\" AND 
																					product_id=\"$product_id\" ");
					}		
				}
			}
			wp_redirect(ECART_LINK."ecart=shopping_cart"); exit;
		}
	}
	
	function product_detail()
	{
		$product_id = $_GET['product_id'];
	
		global $wpdb;
		$sql = "SELECT * FROM ".ECART_TBL_PREFIX."product where product_id='$product_id'";
		$results = $wpdb->get_results($sql);
		
		echo "<form method='post' action='".ECART_LINK."ecart=process_add_cart'>";
		echo "<input type='hidden' name='product_id' value='$product_id'>";
		echo $results[0]->product_name;
		echo "<br>";
		echo "<img src='".$results[0]->product_image."' width='300' style='display:block'>";
		echo "<br>";
		echo $results[0]->product_description;
		echo "<br>";
		echo $results[0]->product_amount;
		echo "<br>";
		
		$show_tax = $this->check_show_tax();

		if($show_tax == "Y")
		{
			$selling_price = $results[0]->selling_price_inc_tax;
			$special_selling_price = $results[0]->special_selling_price_inc_tax;	
		}
		else
		{
			$selling_price = $results[0]->selling_price_exc_tax;
			$special_selling_price = $results[0]->special_selling_price_exc_tax;
		}

		if($results[0]->product_on_special == "1")
		{
			$price = "<del>$".number_format($selling_price, 2, '.', ',')."</del> " . "<b>$".number_format($special_selling_price, 2, '.', ',')."</b>";
			$price .= "<input type='hidden' name='price_inc_tax_$product_id' value='".$results[0]->special_selling_price_inc_tax."'>";
			$price .= "<input type='hidden' name='price_exc_tax_$product_id' value='".$results[0]->special_selling_price_exc_tax."'>";
		}
		else
		{
			$price = "$".number_format($selling_price, 2, '.', ',');
			$price .= "<input type='hidden' name='price_inc_tax_$product_id' value='".$results[0]->selling_price_inc_tax."'>";
			$price .= "<input type='hidden' name='price_exc_tax_$product_id' value='".$results[0]->selling_price_exc_tax."'>";
		}
		
		echo $price;
		
		echo "<br>";
		echo "<input type='submit' value='Add To Shopping Cart'>";
		echo "</form>";
	}
	
	function check_show_tax()
	{		
		if(show_ecart_config('show_tax') == "N")
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
	}
	
	function process_add_cart()
	{
		global $wpdb;
		
		$product_id = $_POST['product_id'];
		$price_inc_tax = $_POST['price_inc_tax_'.$product_id];
		$price_exc_tax = $_POST['price_exc_tax_'.$product_id];
		
		$unique_id = session_id();
		$ip_address = $_SERVER['REMOTE_ADDR'];

		$sql = "SELECT * FROM ".ECART_TBL_PREFIX."shopping_cart WHERE unique_id=\"$unique_id\" AND product_id=\"$product_id\" ";
		$wpdb->query($sql);
		if($wpdb->num_rows == 0)
		{
			$quantity = 1;
			$wpdb->query("INSERT INTO ".ECART_TBL_PREFIX."shopping_cart (
																			unique_id,
																			ip_address,
																			product_id,
																			quantity,
																			price_inc_tax,
																			price_exc_tax
																		) 
																		VALUES
																		(
																			\"$unique_id\",
																			\"$ip_address\",
																			\"$product_id\",
																			\"$quantity\",
																			\"$price_inc_tax\",
																			\"$price_exc_tax\"
																		)																	
																		");
		}
		else
		{
			$results = $wpdb->get_results($sql);
			$quantity = $results[0]->quantity;	
			$quantity++;
					
			$wpdb->query("UPDATE ".ECART_TBL_PREFIX."shopping_cart SET 
																		price_inc_tax = \"$price_inc_tax\",
																		price_exc_tax = \"$price_exc_tax\",
																		quantity=\"$quantity\" 
																		WHERE 
																		unique_id=\"$unique_id\" AND 
																		product_id=\"$product_id\" ");
		}
		
		wp_redirect(ECART_LINK."ecart=shopping_cart"); exit;
	}
	
	function shopping_cart()
	{
		global $wpdb;
		
		echo '<script src="'.ECART_URL.'library/jshashtable.js" type="text/javascript"></script>';
		echo '<script src="'.ECART_URL.'library/jquery.numberformatter.js" type="text/javascript"></script>';
		
		$unique_id = session_id();
		$ip_address = $_SERVER['REMOTE_ADDR'];
		
		echo "<h1>Your Shopping Cart</h1>";

		$sql = "SELECT * FROM ".ECART_TBL_PREFIX."shopping_cart a, ".ECART_TBL_PREFIX."product b where a.unique_id='$unique_id' and a.ip_address='$ip_address' and b.product_id=a.product_id ";
		
		$wpdb->query($sql);
		if($wpdb->num_rows == 0)
		{
			echo "Shopping Cart is empty<br><br>";
			echo "<input type='button' value='Back to Shopping' onclick=window.location='".ECART_LINK."'>";
		
		}
		else
		{
			$results = $wpdb->get_results($sql);	

			echo "<form id='shopping_cart'>";
			echo "<input type='button' value='Update Changes' onclick='update_shopping_cart()'>";
			echo "<div id='alert_update' style='display:none'></div>";
			echo "<br><br>";

			echo "<input type='hidden' id='ABSPATH' name='ABSPATH' value=\"".base64_encode(ABSPATH)."\">";
			echo "<input type='hidden' id='unique_id' name='unique_id' value=\"".$unique_id."\">";
			echo "<input type='hidden' id='ip_address' name='ip_address' value=\"".$ip_address."\">";
			echo "<table class='table_product'>";
			
			echo "<tr><td>Qty</td><td>Product</td><td>Size</td><td>Price</td><td>Subtotal</td><td>Action</td></tr>";
			
			$show_tax = $this->check_show_tax();
			
			$total = 0;
			foreach($results as $result)
			{
				$product_id = $result->product_id;
				
				if($show_tax == "Y")
					$price = $result->price_inc_tax;
				else
					$price = $result->price_exc_tax; 

				$quantity = $result->quantity;			
				$subtotal_raw = $quantity * $price;
				$total += $subtotal_raw;
				
				$price = number_format($price, 2, '.', ',');
				$subtotal = number_format($subtotal_raw, 2, '.', ',');
				
				$price = "$price<input type='hidden' id='price_$product_id' name='price_$product_id' value='$price'>";
				$subtotal = "<span id='span_subtotal_$product_id'>$subtotal</span>";
				$subtotal .= "<input type='hidden' id='val_subtotal_$product_id' value='$subtotal_raw'>";
				
				$qty = "<input type='text' onkeyup=\"change_quantity('$product_id')\" onkeydown=\"change_quantity('$product_id')\" id='qty_$product_id' name='qty_$product_id' style='width:50px; text-align:center' value='".$result->quantity."'>";
				$qty .= "<input type='hidden' name='arr_product_id[]' value='$product_id'>";
				echo "<tr id='tr_$product_id'><td>$qty</td><td>".$result->product_name."</td><td>".$result->product_amount."</td><td>$".$price."</td><td>$".$subtotal."</td><td><input type='button' value='Delete' onclick=\"confirm_delete('$product_id')\"></td></tr>";
			}
			
			$total = number_format($total, 2, '.', ',');
			$total = "<span id='grand_total'>$total</span>";
			echo "<tr><td colspan='3'><input type='button' value='Update Changes' onclick='update_shopping_cart()'></td><td align='right'>Totals : </td><td colspan='2'>$".$total."</td></tr>";
			echo "</table>";
			
			echo "<input type='button' value='Back to Shopping' onclick=window.location='".ECART_LINK."'>";
			echo "<input type='button' value='Checkout' onclick=window.location='".HTTPS_ECART_LINK."ecart=checkout&sid=".session_id()."&es=".$this->enc_session()."'>";
			
			echo "</form>";
		}

		$this->js();
		$this->css_style();
	}
	
	function checkout()
	{	
		if($_SESSION['customer_id'] == "")
		{
			$source = "checkout";
			$this->not_login($source);
		}
		else
		{
			$this->confirmation_checkout();
		}	
	}
	
	function confirmation_checkout()
	{
		global $wpdb;
		
		$unique_id = session_id();
		$ip_address = $_SERVER['REMOTE_ADDR'];
		
		echo "<form method='POST' action='".HTTPS_ECART_LINK."ecart=process_confirmation_checkout' id='checkout' onsubmit='return validation_credit_card()'>";
		
		echo "<input type='hidden' name='ECART_DIR' value=\"".base64_encode(ECART_DIR)."\">";
		echo "<input type='hidden' name='ECART_URL' value=\"".base64_encode(ECART_URL)."\">";
		echo "<input type='hidden' name='ECART_LINK' value=\"".base64_encode(ECART_LINK)."\">";
		echo "<input type='hidden' name='ABSPATH' value=\"".base64_encode(ABSPATH)."\">";
		
		echo "<input type='hidden' name='unique_id' value=\"".$unique_id."\">";
		echo "<input type='hidden' name='ip_address' value=\"".$ip_address."\">";
		echo "<input type='hidden' name='customer_id' value=\"".$_SESSION['customer_id']."\">";
				
		echo "<h1>Your Order</h1>";

		$sql = "SELECT * FROM ".ECART_TBL_PREFIX."shopping_cart a, ".ECART_TBL_PREFIX."product b where a.unique_id='$unique_id' and a.ip_address='$ip_address' and b.product_id=a.product_id ";

		$results = $wpdb->get_results($sql);
		
		echo "<table class='product_type'>";
			
		echo "<tr><td>Quantity</td><td>Product</td><td>Size</td><td>Price</td><td>Subtotal</td></tr>";
		
		$show_tax = $this->check_show_tax();
		
		$total = 0;
		$total_inc_tax = 0;
		$total_exc_tax = 0;
		foreach($results as $result)
		{
			$product_id = $result->product_id;
			
			if($show_tax == "Y")
				$price = $result->price_inc_tax;
			else
				$price = $result->price_exc_tax; 

			$quantity = $result->quantity;			
			$subtotal_raw = $quantity * $price;
			$total += $subtotal_raw;
			
			$total_inc_tax += $quantity * $result->price_inc_tax;
			$total_exc_tax += $quantity * $result->price_exc_tax;
			
			$price = number_format($price, 2, '.', ',');
			$subtotal = number_format($subtotal_raw, 2, '.', ',');
			
			$price = "$price<input type='hidden' id='price_$product_id' name='price_$product_id' value='$price'>";
			$subtotal = "<span id='span_subtotal_$product_id'>$subtotal</span>";
			$subtotal .= "<input type='hidden' id='val_subtotal_$product_id' value='$subtotal_raw'>";
			
			$qty = $result->quantity;
			echo "<tr id='tr_$product_id'><td>$qty</td><td>".$result->product_name."</td><td>".$result->product_amount."</td><td>$".$price."</td><td>$".$subtotal."</td></tr>";
		}
		
		#echo $total_inc_tax."<br>";
		#echo $total_exc_tax."<br>";
		
		$tax = $total_inc_tax - $total_exc_tax;
		if($tax < 0) $tax = 0;
		
		$total = number_format($total, 2, '.', ',');
		$total = "<span id='grand_total'>$total</span>";
		
		if($show_tax == "Y")
			echo "<tr><td colspan='3'></td><td align='right'>Tax Included : </td><td colspan='2'>$".number_format($tax, 2, '.', ',')."</td></tr>";
		
		echo "<tr><td colspan='3'></td><td align='right'>Totals : </td><td colspan='2'>$".$total."</td></tr>";
		echo "</table>";
		
		echo "<input type='button' value='Back To Shopping Cart' onclick=window.location='".ECART_LINK."ecart=shopping_cart'>";
		
		echo "&nbsp;<br>";
		echo "<a name='default_delivery_address'></a>";
		echo "<h1>Delivery Address</h1>";
		
		echo "<div class='payment_detail'>";
		
		$sql = "SELECT * FROM ".ECART_TBL_PREFIX."customer_address where customer_id='".$_SESSION['customer_id']."' ";

		$results = $wpdb->get_results($sql);
		$default_delivery = (isset($results[0]->delivery_contact_name) && !empty($results[0]->delivery_contact_name) ? $results[0]->delivery_contact_name."<br>" : "");
		$default_delivery .= (isset($results[0]->delivery_address_line1) && !empty($results[0]->delivery_address_line1) ? $results[0]->delivery_address_line1."<br>" : "");
		$default_delivery .= (isset($results[0]->delivery_address_line2) && !empty($results[0]->delivery_address_line2) ? $results[0]->delivery_address_line2."<br>" : "");
		$default_delivery .= (isset($results[0]->delivery_city) && !empty($results[0]->delivery_city) ? $results[0]->delivery_city.", " : "");
		$default_delivery .= (isset($results[0]->delivery_state) && !empty($results[0]->delivery_state) ? $results[0]->delivery_state." " : "");
		$default_delivery .= (isset($results[0]->delivery_postcode) && !empty($results[0]->delivery_postcode) ? $results[0]->delivery_postcode."<br>" : "");
		$default_delivery .= (isset($results[0]->delivery_country) && !empty($results[0]->delivery_country) ? $results[0]->delivery_country."<br>" : "");
		$default_delivery .= "<a href='".HTTPS_ECART_LINK."ecart=form_edit_profile&source=".$_GET['ecart']."&sid=".session_id()."&es=".$_GET['es']."#default_delivery_address' class='ecart_link'>[Change]</a>";
		
		$default_delivery .= "<input type='hidden' name='default_contact_name' value=\"".$results[0]->delivery_contact_name."\" >";
		$default_delivery .= "<input type='hidden' name='default_delivery_address_line1' value=\"".$results[0]->delivery_address_line1."\" >";
		$default_delivery .= "<input type='hidden' name='default_delivery_address_line2' value=\"".$results[0]->delivery_address_line2."\" >";
		$default_delivery .= "<input type='hidden' name='default_delivery_city' value=\"".$results[0]->delivery_city."\" >";
		$default_delivery .= "<input type='hidden' name='default_delivery_state' value=\"".$results[0]->delivery_state."\" >";
		$default_delivery .= "<input type='hidden' name='default_delivery_postcode' value=\"".$results[0]->delivery_postcode."\" >";
		$default_delivery .= "<input type='hidden' name='default_delivery_country' value=\"".$results[0]->delivery_country."\" >";
		
		$new_delivery = "<div class='fields'>
							<label for='new_contact_name'>Contact Name</label>
							<input type='text' name='new_contact_name'>
						</div>";
		$new_delivery .= "<div class='fields'>
							<label for='new_delivery_address_line1'>Address Line 1</label>
							<input type='text' name='new_delivery_address_line1'>
						</div>";
		$new_delivery .= "<div class='fields'>
							<label for='new_delivery_address_line2'>Address Line 2</label>
							<input type='text' name='new_delivery_address_line2'>
						</div>";
		$new_delivery .= "<div class='fields'>
							<label for='new_delivery_city'>City</label>
							<input type='text' name='new_delivery_city'>
						</div>";
		$new_delivery .= "<div class='fields'>
							<label for='new_delivery_state'>State</label>
							<input type='text' name='new_delivery_state'>
						</div>";
		$new_delivery .= "<div class='fields'>
							<label for='new_delivery_postcode'>Postcode</label>
							<input type='text' name='new_delivery_postcode'>
						</div>";
		$new_delivery .= "<div class='fields'>
							<label for='new_delivery_country'>Country</label>
							".$results[0]->delivery_country."
							<input type='hidden' name='new_delivery_country' value='".$results[0]->delivery_country."'>
						</div>";				
						
			
		echo "<table>";
		echo "<tr>";
		echo "<td><input type='radio' name='delivery_type' value='default' checked> Default Delivery Address</td>";
		echo "<td><input type='radio' name='delivery_type' value='new'> New Delivery Address</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>$default_delivery</td>";
		echo "<td>$new_delivery</td>";
		echo "</tr>";
		
		echo "</table>";
		
		echo "</div>";
		
		$qry_customer = mysql_query("SELECT * FROM wp_ecart_customer a WHERE a.customer_id='".$_SESSION['customer_id']."' ");
		$data_customer = mysql_fetch_array($qry_customer);
		$TokenCustomerID = $data_customer['TokenCustomerID'];
		
		echo "<a name='payment_detail'></a>";
		echo "<h1>Payment Detail</h1>";
		
		$this->payment_form($TokenCustomerID);
		
		if($GLOBALS['ERROR_EWAY'] == false)
		{
		?>
		<br>
		<div>
		<input type="button" value="Submit Transaction" onclick="process_checkout()" id="checkout_button" style="float:left">
		</div>
		<?php
		}
		?>
		</form>
		
		
		<?php		
		$this->css_style();
		$this->js();
	}
	
	function payment_form($TokenCustomerID)
	{
		if($TokenCustomerID == "")
		{
			$this->form_input_credit_card();
			echo "<input type='hidden' name='form_credit' value='new'>";
		}
		else
		{
			$qry_eway = mysql_query("SELECT * FROM wp_ecart_eway_api_config WHERE 1");
			$data_eway = mysql_fetch_array($qry_eway);

			$eway_connection = $data_eway['eway_connection'];
			$eway_api_key = $data_eway['eway_api_key'];
			$eway_password = base64_decode($data_eway['eway_password']);
			$eway_CustomerID = $data_eway['eway_CustomerID'];
			
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
			if ($err) 
			{
				echo "An error occured while connecting to eWay system, Please refresh this browser again!";
			}
			else
			{
				$client->namespaces['man'] = 'https://www.eway.com.au/gateway/managedpayment';
				// set SOAP header
				$headers = "<man:eWAYHeader><man:eWAYCustomerID>" . $eway_CustomerID. "</man:eWAYCustomerID><man:Username>" . $eway_api_key . "</man:Username><man:Password>" . $eway_password . "</man:Password></man:eWAYHeader>";
				$client->setHeaders($headers);
				
				$requestbody = array(
					'man:managedCustomerID' => $TokenCustomerID
				);
				$soapaction = 'https://www.eway.com.au/gateway/managedpayment/QueryCustomer';
				$result = $client->call('man:QueryCustomer', $requestbody, '', $soapaction);
				
				/*
				echo "<pre>";
				print_r($result);
				echo "</pre>";
				*/
				if ($client->fault) 
				{		
					$this->form_input_credit_card();
					echo "<input type='hidden' name='form_credit' value='new'>";
				} 
				else 
				{
					$err = $client->getError();
					if ($err) 
					{
						echo "An error occured while connecting to eWay system, Please refresh this browser again!";
					} 
					else 
					{
						if($result['CCNumber'] == "")
						{
							$this->form_input_credit_card();
							echo "<input type='hidden' name='form_credit' value='new'>";
						}
						else
						{						
							echo "<input type='hidden' name='form_credit' value='exist'>";
							?>
							<div class="payment_detail">
								<div class="fields">
									<label for="EWAY_CARDNAME">
										Card Holder</label>
										<?php echo $result['CCName'] ?>
										&nbsp;
								</div>
								<div class="fields">
									<label for="EWAY_CARDNUMBER">
										Card Number</label>
										<?php echo $result['CCNumber'] ?>
										&nbsp;
								</div>
								<div class="fields">
									<label for="EWAY_CARDEXPIRYMONTH">
										Expiry Date</label>
										<?php echo $result['CCExpiryMonth'] ." / ";
										echo $result['CCExpiryYear'] ?>
										&nbsp;
								</div>
								<div class="fields">
									<a href="<?php echo HTTPS_ECART_LINK?>ecart=change_credit_card_detail&source=<?php echo $_GET['ecart']; ?>&sid=<?php echo session_id(); ?>&es=<?php echo $_GET['es']; ?>"  class="ecart_link">[Change]</a>
								</div>
												
								<div class="fields">
									<label for="secure">&nbsp;</label>
									<img src="<?php echo ECART_URL?>images/secure_by_eway.gif" style="box-shadow:none; border-radius:0" height="50">
									<img src="<?php echo ECART_URL?>images/credit-cards.gif" style="box-shadow:none; border-radius:0" height="50">
								</div>		
							</div>
							
							<?php
						}
					}
				}
			
			
			}

			/*
			$GLOBALS['EWAY_CONNECTION'] = $eway_connection;
			$GLOBALS['EWAY_API_USERNAME'] = $eway_api_key;
			$GLOBALS['EWAY_API_PASSWORD'] = $eway_password;
				
			include_once(ECART_DIR."eway_function/Rapid3.0.php");
			//Create RapidAPI Service
			$service = new RapidAPI();

			//Create AccessCode Request Object
			$request = new CreateAccessCodeRequest();
			
			$request->RedirectUrl = "http://www.nucleuslogic.com";
			$request->Method = "UpdateTokenCustomer";
			$request->Customer->TokenCustomerID = $TokenCustomerID;
			
			$Response = $service->CreateAccessCode($request);
			
			if($GLOBALS['ERROR_EWAY'] == false)
			{
				if(isset($Response->Errors))
				{
					$this->form_input_credit_card();
					echo "<input type='hidden' name='form_credit' value='new'>";
				}
				else
				{		
					echo "<input type='hidden' name='form_credit' value='exist'>";
					?>
					<div class="payment_detail">
						<div class="fields">
							<label for="EWAY_CARDNAME">
								Card Holder</label>
								<?php echo (isset($Response->Customer->CardName) && !empty($Response->Customer->CardName) ? $Response->Customer->CardName:"") ?>
								&nbsp;
						</div>
						<div class="fields">
							<label for="EWAY_CARDNUMBER">
								Card Number</label>
								<?php echo (isset($Response->Customer->CardNumber) && !empty($Response->Customer->CardNumber)  ? $Response->Customer->CardNumber:"") ?>
								&nbsp;
						</div>
						<div class="fields">
							<label for="EWAY_CARDEXPIRYMONTH">
								Expiry Date</label>
								<?php echo (isset($Response->Customer->CardExpiryMonth) && !empty($Response->Customer->CardExpiryMonth)  ? $Response->Customer->CardExpiryMonth ." / " : "");
								echo (isset($Response->Customer->CardExpiryYear) && !empty($Response->Customer->CardExpiryYear)  ? $Response->Customer->CardExpiryYear : "") ?>
								&nbsp;
						</div>
						<div class="fields">
							<a href="<?php echo ECART_LINK?>ecart=change_credit_card_detail&source=<?php echo $_GET['ecart']; ?>"  class="ecart_link">[Change]</a>
						</div>
										
						<div class="fields">
							<label for="secure">&nbsp;</label>
							<img src="<?php echo ECART_URL?>images/secure_by_eway.gif" style="box-shadow:none; border-radius:0" height="50">
							<img src="<?php echo ECART_URL?>images/credit-cards.gif" style="box-shadow:none; border-radius:0" height="50">
						</div>		
					</div>
					
					<?php
				}
			}
			else
			{
				echo "An error occured while connecting to eWay system, Please refresh this browser again!";
			}
			*/
		}
	}
	
	function form_input_credit_card($Response="")
	{
		?>	
		<div class="payment_detail">
			<div class="fields">
				<label for="EWAY_CARDNAME">
					Card Holder</label>
				<input type='text' class='input_text' name='EWAY_CARDNAME' id='EWAY_CARDNAME' value="<?php echo (isset($Response->Customer->CardName) && !empty($Response->Customer->CardName) ? $Response->Customer->CardName:"") ?>" />
			</div>
			<div class="fields">
				<label for="EWAY_CARDNUMBER">
					Card Number</label>
				<input type='text' class='input_text' name='EWAY_CARDNUMBER' id='EWAY_CARDNUMBER' value="<?php echo (isset($Response->Customer->CardNumber) && !empty($Response->Customer->CardNumber)  ? $Response->Customer->CardNumber:"") ?>" />
			</div>
			<div class="fields">
				<label for="EWAY_CARDEXPIRYMONTH">
					Expiry Date</label>
				<select ID="EWAY_CARDEXPIRYMONTH" name="EWAY_CARDEXPIRYMONTH">
					<?php
					   if (isset($Response->Customer->CardExpiryMonth)&& !empty($Response->Customer->CardExpiryMonth)) {
							$expiry_month = $Response->Customer->CardExpiryMonth;
						} else {
							$expiry_month = date('m');
						}
						for($i = 1; $i <= 12; $i++) {
							$s = sprintf('%02d', $i);
							echo "<option value='$s'";
							if ( $expiry_month == $i ) {
								echo " selected='selected'";
							}
							echo ">$s</option>\n";
						}
					?>
				</select>
				/
				<select ID="EWAY_CARDEXPIRYYEAR" name="EWAY_CARDEXPIRYYEAR">
					<?php
						$i = date("y");
						$j = $i+11;
						for ($i; $i <= $j; $i++) {
							echo "<option value='$i'";
							if ( $Response->Customer->CardExpiryYear == $i ) {
								echo " selected='selected'";
							}
							echo ">$i</option>\n";
						}
					?>
				</select>
			</div>
			
			<div class="fields">
				<label for="secure">&nbsp;</label>
				<img src="<?php echo ECART_URL?>images/secure_by_eway.gif" style="box-shadow:none; border-radius:0" height="50">
				<img src="<?php echo ECART_URL?>images/credit-cards.gif" style="box-shadow:none; border-radius:0" height="50">
			</div>		
		</div>
		<?php
	
	}
	
	function not_login($source)
	{
		$array_country = $this->get_array_country();
		
		$country = "<select id='country' name='country'>";
		foreach($array_country as $key=>$value)
		{
			$country .= "<option value='$value'>$value</option>";
		}
		$country .= "</select>";
		
		$array_hear_about_us = $this->get_array_hear_about_us();
		$hear_about_us = "<select name='hear_about_us'>";
		$hear_about_us .= "<option value=''>Please Select...</option>";
		foreach($array_hear_about_us as $key=>$value)
		{
			$hear_about_us .= "<option value='$value'>$value</option>";
		}
		$hear_about_us .= "</select>";
		
		echo "<h2>Returning Customer? Login Below</h2>";
		
		echo "<form method='POST' action='".HTTPS_ECART_LINK."ecart=process_login&sid=".session_id()."&es=".$_GET['es']."'>";
		echo "<input type='hidden' name='source' value='$source'>";
		echo "<table>";
		echo "<tr><td>Email</td><td>Password</td></tr>";
		echo "<tr><td><input type='text' name='email'></td><td><input type='password' name='password'></td><td><input type='submit' name='Login' value='Login'></td><td><a href='".ECART_LINK."ecart=form_forget_password' class='ecart_link'>Forget Password?</a></td></tr>";
		echo "</table>";
		echo "</form>";
		
		echo "<h2>New Customer?</h2>";
		echo "<form method='POST' action='".HTTPS_ECART_LINK."ecart=process_customer_register&sid=".session_id()."&es=".$_GET['es']."' onsubmit='return validation_registration()'>";
		//echo "<form method='POST' action='' onsubmit='return validation_registration()'>";

		echo "<input type='hidden' name='source' value='$source'>";
		echo "<div>Enter your detail to complete your first order and they will be saved for you next time.</div>";
		echo "<table>";
		echo "<tr><td>First Name <span style='color: red'>*</span></td><td><input type='text' name='first_name' id='first_name'></td></tr>";
		echo "<tr><td>Last Name <span style='color: red'>*</span></td><td><input type='text' name='last_name' id='last_name'></td></tr>";
		echo "<tr><td>Delivery Address <span style='color: red'>*</span></td><td><input type='text' name='address' id='address'></td></tr>";
		echo "<tr><td>Suburb <span style='color: red'>*</span></td><td><input type='text' name='city' id='city'></td></tr>";
		echo "<tr><td>State <span style='color: red'>*</span></td><td><input type='text' name='state' id='state'></td></tr>";
		echo "<tr><td>Postcode <span style='color: red'>*</span></td><td><input type='text' name='postcode' id='postcode' onkeyup='check_postcode()' onchange='check_postcode()'><input type='hidden' id='match_postcode'></td></tr>";
		echo "<tr><td>Country <span style='color: red'>*</span></td><td>$country</td></tr>";
		echo "<tr><td>Phone No. <span style='color: red'>*</span></td><td><input type='text' name='phone' id='phone'></td></tr>";
		echo "<tr><td>Mobile No.</td><td><input type='text' name='mobile_phone' id='mobile_phone'></td></tr>";
		echo "<tr><td>Fax</td><td><input type='text' name='fax' id='fax'></td></tr>";
		echo "<tr><td>Email <span style='color: red'>*</span></td><td><input type='text' name='email' id='email'></td></tr>";
		echo "<tr><td>Password <span style='color: red'>*</span></td><td><input type='password' name='password' id='password'></td></tr>";
		echo "<tr><td>Confirm Password <span style='color: red'>*</span></td><td><input type='password' name='confirm_password' id='confirm_password'></td></tr>";
		echo "<tr><td>Where did you hear about us</td><td>$hear_about_us</td></tr>";
		echo "<tr><td>Message</td><td><textarea name='message' id='message'></textarea></td></tr>";
		echo "</table>";		
		
		if(show_ecart_config('link_terms_condition') == "")
			$link_terms_condition = "";
		else
			$link_terms_condition = "Click <a href=\"".show_ecart_config('link_terms_condition')."\" target='_black' class='ecart_link'>Here</a> to view Terms and Conditions <br>" ; 
		
		if(show_ecart_config('link_refund_policy') == "")
			$link_refund_policy = "";
		else
			$link_refund_policy = "Click <a href=\"".show_ecart_config('link_refund_policy')."\" target='_black' class='ecart_link'>Here</a> to Refund Policy <br>" ; 

		
		echo "<br>";
		echo $link_terms_condition;
		echo $link_refund_policy;
		
		echo "<br>";
		
		if($source == "checkout")
			echo "<input type='submit' value='Proceed to Payment'>";
		else
			echo "<input type='submit' value='Register'>";
		
		echo "</form>";
		
		?>
		<script>
		function check_postcode()
		{
			var ajaxurl = "<?php echo HTTPS_ECART_URL ?>ajax/cek_postcode.php";
			var data = "postcode=" + jQuery("#postcode").val() + "&ABSPATH=<?php echo base64_encode(ABSPATH) ?>";
			
			jQuery.post(ajaxurl, data, function(response) {
				jQuery("#match_postcode").val(response)
			});	
		}
		</script>
		<?php
	}
	
	function process_customer_register()
	{
		global $wpdb;
		
		$email = $_POST['email'];
		$source = $_POST['source'];
		
		if($email != "")
		{
			$wpdb->query("SELECT * FROM ".ECART_TBL_PREFIX."customer WHERE email = \"$email\" ");
			if($wpdb->num_rows == 0)
			{
				$first_name = $_POST['first_name'];
				$last_name = $_POST['last_name'];
				$address = $_POST['address'];
				$city = $_POST['city'];
				$state = $_POST['state'];
				$postcode = $_POST['postcode'];
				$country = $_POST['country'];
				$phone = $_POST['phone'];
				$mobile_phone = $_POST['mobile_phone'];
				$fax = $_POST['fax'];
				$email = $_POST['email'];
				$password = md5($_POST['password']);
				$hear_about_us = $_POST['hear_about_us'];
				$message = $_POST['message'];
				$register_date = date("Y-m-d H:i:s");
				
				$sql = "INSERT INTO ".ECART_TBL_PREFIX."customer 	(
																		first_name,
																		last_name,
																		email,
																		password,						
																		phone,
																		mobile_phone,
																		fax,
																		hear_about_us,
																		register_date,
																		note
																	)
																	VALUES
																	(
																		\"$first_name\",
																		\"$last_name\",
																		\"$email\",
																		\"$password\",															
																		\"$phone\",
																		\"$mobile_phone\",
																		\"$fax\",
																		\"$hear_about_us\",
																		\"$register_date\",
																		\"$message\"
																	)
																	";
				$wpdb->query($sql);
				
				$sql = "SELECT * FROM ".ECART_TBL_PREFIX."customer WHERE email = \"$email\" ";
				$results = $wpdb->get_results($sql);
				$customer_id = $results[0]->customer_id;
				
				$sql = "INSERT INTO ".ECART_TBL_PREFIX."customer_address 	(
																				customer_id,
																				mailing_address_line1,
																				mailing_address_line2,
																				mailing_city,
																				mailing_state,
																				mailing_postcode,
																				mailing_country,
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
																				\"$customer_id\",
																				\"$address\",
																				\"\",
																				\"$city\",
																				\"$state\",
																				\"$postcode\",
																				\"$country\",
																				\"$first_name $last_name\",
																				\"$address\",
																				\"\",
																				\"$city\",
																				\"$state\",
																				\"$postcode\",
																				\"$country\"
																			)
																			";
				$wpdb->query($sql);

				$this->process_login();
			}
			else
			{
				$_SESSION['customer_id'] = "";
				echo "<script>alert('Email address already exist, Please try another email address!'); window.location='".HTTPS_ECART_LINK."ecart=$source&sid=".session_id()."&es=".$_GET['es']."'</script>";
			}
		}
		else
		{
			$_SESSION['customer_id'] = "";
			//echo "<script>alert('Email address must be filled!'); window.location='".ECART_LINK."ecart=$source'</script>";
			echo "<script>alert('Email address must be filled!'); window.location='".HTTPS_ECART_LINK."ecart=$source&sid=".session_id()."&es=".$_GET['es']."'</script>";
		}
	}
	
	function process_login()
	{
		global $wpdb;
		
		$email = $_POST['email'];
		$password = $_POST['password'];
		$password = md5($password);
		$source = $_POST['source'];
	
		$sql = "SELECT * FROM ".ECART_TBL_PREFIX."customer where email=\"$email\" AND password=\"$password\" LIMIT 1";
		$wpdb->query($sql);
		if($wpdb->num_rows == 0)
		{
			$_SESSION['customer_id'] = "";
			
			echo "<script>alert('Login Failed'); window.location='".HTTPS_ECART_LINK."ecart=$source&sid=".session_id()."&es=".$_GET['es']."'</script>";
		}
		else
		{
			$results = $wpdb->get_results($sql);
			$_SESSION['customer_id'] = $results[0]->customer_id;
					
			//echo "<script>alert('Login Success'); window.location='".HTTPS_ECART_LINK."ecart=$source&sid=".session_id()."'</script>";
			
			$es = $this->enc_session();
			wp_redirect(HTTPS_ECART_LINK."ecart=$source&sid=".session_id()."&es=$es"); exit;
		}

		//wp_redirect(ECART_LINK."ecart=$source"); exit;
	}

	function product_type()
	{
		echo "<h1>Select a Category</h1>";
		
		global $wpdb;
		$sql = "SELECT * FROM wp_ecart_product_category where 1";
		$results = $wpdb->get_results($sql);
		
		echo "<table class='product_type' cellpadding='5' cellspacing='0'>";
		echo "<tr>";
		foreach($results as $result)
		{
			echo "<td><span class='span_product_type ecart_link' onclick=\"load_product_list('".$result->product_category_id."')\">".$result->category."</span></td>";
		}
		echo "</tr>";
		echo "</table>";
		
		$this->css_style();
		$this->js();
	
	}
	
	
	function form_edit_profile()
	{
		if($_SESSION['customer_id'] == "")
		{
			$source = "form_edit_profile";
			$this->not_login($source);
		}
		else
		{
			$this->action_form_edit_profile();
		}	
	
	}
	
	
	
	function action_form_edit_profile()
	{
		$customer_id = $_SESSION['customer_id'];
		$source = $_GET['source'];
		
		$qry_customer = mysql_query("select * from wp_ecart_customer a, wp_ecart_customer_address b where a.customer_id='$customer_id' and b.customer_id=a.customer_id");
		$data_customer = mysql_fetch_array($qry_customer);
		$TokenCustomerID = $data_customer['TokenCustomerID'];
		
		echo "<div id='form_edit_profile'>";
		
		echo "<form method='POST' action='".HTTPS_ECART_LINK."ecart=process_edit_profile&source=$source&sid=".session_id()."&es=".$_GET['es']."' onsubmit='return validation_edit_profile()'>";
		
		echo "<input type='hidden' value='$TokenCustomerID' name='TokenCustomerID'>";
		
		echo "<fieldset class='fieldset'>";
		echo "<legend>Profile</legend>";
		echo "<div class='fields'><label for='first_name'>First Name <span style='color: red'>*</span></label><input type='text' class='input_text' name='first_name' id='first_name' value=\"".$data_customer['first_name']."\"></div>";
		echo "<div class='fields'><label for='last_name'>Last Name <span style='color: red'>*</span></label><input type='text' class='input_text' name='last_name' id='last_name' value=\"".$data_customer['last_name']."\"></div>";		
		echo "<div class='fields'><label for='phone'>Phone No. <span style='color: red'>*</span></label><input type='text' class='input_text' name='phone' id='phone' value=\"".$data_customer['phone']."\"></div>";
		echo "<div class='fields'><label for='mobile_phone'>Mobile No.</label><input type='text' class='input_text' name='mobile_phone' id='mobile_phone' value=\"".$data_customer['mobile_phone']."\"></div>";
		echo "<div class='fields'><label for='fax'>Fax</label><input type='text' class='input_text' name='fax' id='fax' value=\"".$data_customer['fax']."\"></div>";
		
		echo "</fieldset>";
		echo "<br>";
		
		echo "<fieldset class='fieldset'>";
		echo "<legend>Login Details</legend>";
		echo "<div class='fields'><label for='email'>Email <span style='color: red'>*</span></label><input type='text' class='input_text' name='email' id='email' value=\"".$data_customer['email']."\"></div>";
		echo "<div class='fields'><label for='password'>Password</label><input type='password' class='input_text' name='password' id='password'></div>";
		echo "<div class='fields'><label for='confirm_password'>Confirm Password</label><input type='password' class='input_text' name='confirm_password' id='confirm_password'></div>";
		echo "<div class='fields'><label for='confirm_password'>&nbsp;</label><span style='font-size:11px; color: red'>Leave blank if password not change</span></div>";
		echo "</fieldset>";
		echo "<br>";
		
		$array_country = $this->get_array_country();	
		$country = "<select name='mailing_country' class='input_text'>";
		foreach($array_country as $key=>$value)
		{
			$country .= "<option value='$value' ";
			
			if($data_customer['mailing_country'] == $value)
				$country .= " selected";
			
			$country .= ">$value</option>";
		}
		$country .= "</select>";
		
		echo "<fieldset class='fieldset'>";
		echo "<legend>Mailing Address</legend>";
		echo "<div class='fields'><label for='mailing_address_line1'>Address Line 1 <span style='color: red'>*</span></label><input type='text' class='input_text' name='mailing_address_line1' id='mailing_address_line1' value=\"".$data_customer['mailing_address_line1']."\"></div>";
		echo "<div class='fields'><label for='mailing_address_line2'>Address Line 2</label><input type='text' class='input_text' name='mailing_address_line2' id='mailing_address_line2' value=\"".$data_customer['mailing_address_line2']."\"></div>";
		echo "<div class='fields'><label for='mailing_city'>Suburb <span style='color: red'>*</span></label><input type='text' class='input_text' name='mailing_city' id='mailing_city' value=\"".$data_customer['mailing_city']."\"></div>";
		echo "<div class='fields'><label for='mailing_state'>State <span style='color: red'>*</span></label><input type='text' class='input_text' name='mailing_state' id='mailing_state' value=\"".$data_customer['mailing_state']."\"></div>";
		echo "<div class='fields'><label for='mailing_postcode'>Postcode <span style='color: red'>*</span></label><input type='text' class='input_text' name='mailing_postcode' id='mailing_postcode' value=\"".$data_customer['mailing_postcode']."\"></div>";
		echo "<div class='fields'><label for='mailing_country'>Country <span style='color: red'>*</span></label>$country</div>";
		echo "</fieldset>";
		echo "<br>";
		
		$array_country = $this->get_array_country();	
		$country = "<select name='delivery_country' class='input_text'>";
		foreach($array_country as $key=>$value)
		{
			$country .= "<option value='$value' ";
			
			if($data_customer['delivery_country'] == $value)
				$country .= " selected";
			
			$country .= ">$value</option>";
		}
		$country .= "</select>";
		
		echo "<a name='default_delivery_address'></a>";
		echo "<fieldset class='fieldset'>";
		echo "<legend>Default Delivery Address</legend>";
		echo "<div class='fields'><label for='delivery_contact_name'>Contact Name <span style='color: red'>*</span></label><input type='text' class='input_text' name='delivery_contact_name' id='delivery_contact_name' value=\"".$data_customer['delivery_contact_name']."\"></div>";
		echo "<div class='fields'><label for='delivery_address_line1'>Address Line 1 <span style='color: red'>*</span></label><input type='text' class='input_text' name='delivery_address_line1' id='delivery_address_line1' value=\"".$data_customer['delivery_address_line1']."\"></div>";
		echo "<div class='fields'><label for='delivery_address_line2'>Address Line 2</label><input type='text' class='input_text' name='delivery_address_line2' id='delivery_address_line2' value=\"".$data_customer['delivery_address_line2']."\"></div>";
		echo "<div class='fields'><label for='delivery_city'>Suburb <span style='color: red'>*</span></label><input type='text' class='input_text' name='delivery_city' id='delivery_city' value=\"".$data_customer['delivery_city']."\"></div>";
		echo "<div class='fields'><label for='delivery_state'>State <span style='color: red'>*</span></label><input type='text' class='input_text' name='delivery_state' id='delivery_state' value=\"".$data_customer['delivery_state']."\"></div>";
		echo "<div class='fields'><label for='delivery_postcode'>Postcode <span style='color: red'>*</span></label><input type='text' class='input_text' name='delivery_postcode' id='delivery_postcode' value=\"".$data_customer['delivery_postcode']."\"></div>";
		echo "<div class='fields'><label for='delivery_country'>Country <span style='color: red'>*</span></label>$country</div>";
		echo "</fieldset>";
		echo "<br>";
		
		$array_hear_about_us = $this->get_array_hear_about_us();
		$hear_about_us = "<select name='hear_about_us' class='input_text'>";
		$hear_about_us .= "<option value=''>Please Select...</option>";
		foreach($array_hear_about_us as $key=>$value)
		{
			$hear_about_us .= "<option value='$value' ";
			
			if($data_customer['hear_about_us'] == $value)
				$hear_about_us .= " selected ";
			
			$hear_about_us .=" >$value</option>";
		}
		$hear_about_us .= "</select>";
		
		echo "<fieldset class='fieldset'>";
		echo "<legend>Optional</legend>";
		echo "<div class='fields'><label for='hear_about_us'>Where did you hear about us</label>$hear_about_us&nbsp;</div>";
		echo "<div class='fields'><label for='message'>Message</label><textarea name='message' class='input_text'>".$data_customer['note']."</textarea></div>";
		echo "</fieldset>";
		echo "<br>";
		
		echo "<a name='payment_detail'></a>";
		echo "<fieldset class='fieldset'>";
		echo "<legend>Payment Details</legend>";
	
		$this->payment_form($TokenCustomerID);

		echo "</fieldset>";
		echo "<br>";
		
		echo "<br>";
		
		if($source != "")
			echo "<input type='button' value='Back' onclick=\"window.location='".HTTPS_ECART_LINK."ecart=$source&sid=".session_id()."&es=".$_GET['es']."'\">&nbsp;";
		
		echo "<input type='submit' value='Update Profile'>";
		
		echo "</form>";
		
		echo "</div>";
		
		$this->css_style();
	
	}
	
	function process_edit_profile()
	{
		$TokenCustomerID = $_POST['first_name'];
		$source = $_GET['source'];
		
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$phone = $_POST['phone'];
		$mobile_phone = $_POST['mobile_phone'];
		$fax = $_POST['fax'];
		
		$email = $_POST['email'];
		$password = $_POST['password'];
		
		$mailing_address_line1 = $_POST['mailing_address_line1'];
		$mailing_address_line2 = $_POST['mailing_address_line2'];
		$mailing_city = $_POST['mailing_city'];
		$mailing_state = $_POST['mailing_state'];
		$mailing_postcode = $_POST['mailing_postcode'];
		$mailing_country = $_POST['mailing_country'];
		
		$delivery_contact_name = $_POST['delivery_contact_name'];
		$delivery_address_line1 = $_POST['delivery_address_line1'];
		$delivery_address_line2 = $_POST['delivery_address_line2'];
		$delivery_city = $_POST['delivery_city'];
		$delivery_state = $_POST['delivery_state'];
		$delivery_postcode = $_POST['delivery_postcode'];
		$delivery_country = $_POST['delivery_country'];
		
		$hear_about_us = $_POST['hear_about_us'];
		$note = $_POST['message'];
		
		if($password != "")
			$qry_password = "password = \"".md5($password)."\", ";
		
		mysql_query("UPDATE wp_ecart_customer SET
													first_name = \"$first_name\",
													last_name = \"$last_name\",
													email = \"$email\",
													$qry_password
													phone = \"$phone\",
													mobile_phone = \"$mobile_phone\",
													fax = \"$fax\",
													hear_about_us = \"$hear_about_us\",
													register_date = \"$register_date\",
													note = \"$note\"
												WHERE
													customer_id = \"".$_SESSION['customer_id']."\"
												");
	
		mysql_query("UPDATE wp_ecart_customer_address SET
															mailing_address_line1 = \"$mailing_address_line1\",
															mailing_address_line2 = \"$mailing_address_line2\",
															mailing_city = \"$mailing_city\",
															mailing_state = \"$mailing_state\",
															mailing_postcode = \"$mailing_postcode\",
															mailing_country = \"$mailing_country\",
															delivery_contact_name = \"$delivery_contact_name\",
															delivery_address_line1 = \"$delivery_address_line1\",
															delivery_address_line2 = \"$delivery_address_line2\",
															delivery_city = \"$delivery_city\",
															delivery_state = \"$delivery_state\",
															delivery_postcode = \"$delivery_postcode\",
															delivery_country = \"$delivery_country\"
														WHERE
															customer_id = \"".$_SESSION['customer_id']."\"
														");
		
		$create_customer = "SUCCESS";
		
		if($_POST['form_credit'] == "new")
		{
			$EWAY_CARDNAME = $_POST['EWAY_CARDNAME'];
			$EWAY_CARDNUMBER = $_POST['EWAY_CARDNUMBER'];
			$EWAY_CARDEXPIRYMONTH = $_POST['EWAY_CARDEXPIRYMONTH'];
			$EWAY_CARDEXPIRYYEAR = $_POST['EWAY_CARDEXPIRYYEAR'];
			$EWAY_CARDSTARTMONTH = $_POST['EWAY_CARDSTARTMONTH'];
			$EWAY_CARDSTARTYEAR = $_POST['EWAY_CARDSTARTYEAR'];
			$EWAY_CARDISSUENUMBER = $_POST['EWAY_CARDISSUENUMBER'];
			$EWAY_CARDCVN = $_POST['EWAY_CARDCVN'];
			
			$qry_eway = mysql_query("SELECT * FROM wp_ecart_eway_api_config WHERE 1");
			$data_eway = mysql_fetch_array($qry_eway);

			$eway_connection = $data_eway['eway_connection'];
			$eway_api_key = $data_eway['eway_api_key'];
			$eway_password = base64_decode($data_eway['eway_password']);
			$eway_CustomerID = $data_eway['eway_CustomerID'];
			
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
				$responseMessage = "An Error occured when connection to eway server, Please try again!";
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
				'man:Country' => strtolower($this->show_country_code($mailing_country)),
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
			
			if ($client->fault) 
			{
				$responseMessage = "An Error occured when connection to eway server, Please try again!";
				//echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
				//echo $result['faultstring'];
				$create_customer = "FAILED";
			} 
			else 
			{
				$err = $client->getError();
				if ($err) 
				{
					$responseMessage = "An Error occured when connection to eway server, Please try again!";
					$create_customer = "FAILED";
				} 
				else 
				{
					$TokenCustomerID = $result;
					$create_customer = "SUCCESS";
				}
			}
			
			if($create_customer == "SUCCESS")
			{
				$qry_update_token = mysql_query("UPDATE wp_ecart_customer SET 
																	TokenCustomerID='$TokenCustomerID', 
																	AccessCode='$AccessCode', 
																	caverno='$caverno' 
																	
																WHERE customer_id='".$_SESSION['customer_id']."' ");
			}
			
			/*
			$GLOBALS['EWAY_CONNECTION'] = $eway_connection;
			$GLOBALS['EWAY_API_USERNAME'] = $eway_api_key;
			$GLOBALS['EWAY_API_PASSWORD'] = $eway_password;

			include_once(ECART_DIR."eway_function/Rapid3.0.php");
			
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
			$request->Customer->Country = strtolower($this->show_country_code($mailing_country));
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
						$lblError .= $service->APIConfig[$error]."\\n";
					else
						$lblError .= $error;
				}
				
				$responseMessage = $lblError;		
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
					$create_customer = "SUCCESS";
					$AccessCode = $Response->AccessCode;
					$TokenCustomerID = $result->TokenCustomerID;
					$caverno = base64_encode($EWAY_CARDCVN);
					
					$qry_update_token = mysql_query("UPDATE wp_ecart_customer SET 
																	TokenCustomerID='$TokenCustomerID', 
																	AccessCode='$AccessCode', 
																	caverno='$caverno' 
																	
																WHERE customer_id='".$_SESSION['customer_id']."' ");
				}
				else
				{
					$create_customer = "FAILED";
					
					$responseMessage = $this->response_code($result->ResponseCode)."\\n";
					
					if(isset($result->ResponseMessage))
					{
						//Get Error Messages from Error Code. Error Code Mappings are in the Config.ini file
						$ResponseMessageArray = explode(",", $result->ResponseMessage);
						
						//$responseMessage = "";

						foreach ( $ResponseMessageArray as $message )
						{
							if(isset($service->APIConfig[$message]))
								$responseMessage .= $service->APIConfig[$message]."\\n";
							else
								$responseMessage .= $message."\\n";
						}

						#echo $responseMessage;
					}
				}
			}
			*/
		}
		
		$source = $_GET['source'];
		if($source == "")
			$ecart_link = "form_edit_profile&sid=".session_id()."&es=".$_GET['es'] ;
		else
			$ecart_link = $source."&sid=".session_id()."&es=".$_GET['es']."#default_delivery_address" ;
	
		if($create_customer == "SUCCESS")
			echo "<script>alert('Your Profile was updated'); window.location='".HTTPS_ECART_LINK."ecart=$ecart_link';</script>";
		else
			echo "<script>alert('$responseMessage'); window.location='".HTTPS_ECART_LINK."ecart=$ecart_link';</script>";
	}
		
	function change_credit_card_detail()
	{
		$customer_id = $_SESSION['customer_id'];
		
		$qry_customer = mysql_query("select * from wp_ecart_customer a, wp_ecart_customer_address b where a.customer_id='$customer_id' and b.customer_id=a.customer_id");
		$data_customer = mysql_fetch_array($qry_customer);
		$TokenCustomerID = $data_customer['TokenCustomerID'];
		
		$qry_eway = mysql_query("SELECT * FROM wp_ecart_eway_api_config WHERE 1");
		$data_eway = mysql_fetch_array($qry_eway);

		$eway_connection = $data_eway['eway_connection'];
		$eway_api_key = $data_eway['eway_api_key'];
		$eway_password = base64_decode($data_eway['eway_password']);
		$eway_CustomerID = $data_eway['eway_CustomerID'];
			
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
		if ($err) 
		{
			echo "An error occured while connecting to eWay system, Please refresh this browser again!";
		}
		else
		{
			$client->namespaces['man'] = 'https://www.eway.com.au/gateway/managedpayment';
			// set SOAP header
			$headers = "<man:eWAYHeader><man:eWAYCustomerID>" . $eway_CustomerID. "</man:eWAYCustomerID><man:Username>" . $eway_api_key . "</man:Username><man:Password>" . $eway_password . "</man:Password></man:eWAYHeader>";
			$client->setHeaders($headers);
			
			$requestbody = array(
				'man:managedCustomerID' => $TokenCustomerID
			);
			$soapaction = 'https://www.eway.com.au/gateway/managedpayment/QueryCustomer';
			$result = $client->call('man:QueryCustomer', $requestbody, '', $soapaction);
			
			if ($client->fault) 
			{		
				$this->form_input_credit_card();
				echo "<input type='hidden' name='form_credit' value='new'>";
			} 
			else 
			{
				$err = $client->getError();
				if ($err) 
				{
					echo "An error occured while connecting to eWay system, Please refresh this browser again!";
				} 
				else 
				{
					$Response = new StdClass();
					$Response->Customer->CardName = $result['CCName'];
					$Response->Customer->CardNumber = $result['CCNumber'];
					$Response->Customer->CardExpiryMonth = $result['CCExpiryMonth'];
					$Response->Customer->CardExpiryYear = $result['CCExpiryYear'];
					
					$source = $_GET['source'];
					if($source == "")
						$ecart_link = "form_edit_profile&sid=".session_id()."&es=".$_GET['es']."#payment_detail" ;
					else
						$ecart_link = $source."&sid=".session_id()."&es=".$_GET['es']."#payment_detail" ;
					
					echo "<form method='POST' action='".HTTPS_ECART_LINK."ecart=process_update_credit_card_detail&source=$source&sid=".session_id()."&es=".$_GET['es']."' onsubmit='return validation_credit_card()'>";
					
					echo "<input type='hidden' value='$AccessCode' name='AccessCode'>";
					echo "<input type='hidden' value='$TokenCustomerID' name='TokenCustomerID'>";
					echo "<input type='hidden' value='$FormActionURL' name='FormActionURL'>";
					$this->form_input_credit_card($Response);
					
					echo "<input type='button' value='Back' onclick=\"window.location='".HTTPS_ECART_LINK."ecart=$ecart_link'\">&nbsp;";
					echo "<input type='submit' value='Update Credit Card'>";
					echo "</form>";
				}
			}
		
		
		}

		/*
		$GLOBALS['EWAY_CONNECTION'] = $eway_connection;
		$GLOBALS['EWAY_API_USERNAME'] = $eway_api_key;
		$GLOBALS['EWAY_API_PASSWORD'] = $eway_password;
			
		include_once(ECART_DIR."eway_function/Rapid3.0.php");
		//Create RapidAPI Service
		$service = new RapidAPI();

		//Create AccessCode Request Object
		$request = new CreateAccessCodeRequest();
		
		$request->RedirectUrl = "http://www.nucleuslogic.com";
		$request->Method = "UpdateTokenCustomer";
		$request->Customer->TokenCustomerID = $TokenCustomerID;
		
		$Response = $service->CreateAccessCode($request);

		$AccessCode = $Response->AccessCode;
		$FormActionURL = $Response->FormActionURL; 
		
		$request = new StdClass();
		$request->Customer->CardName = $result['CCName']
		$request->Customer->CardNumber = $result['CCNumber'];
		$request->Customer->CardExpiryMonth = $result['CCExpiryMonth'];
		$request->Customer->CardExpiryYear = $result['CCExpiryYear'];
		
		$source = $_GET['source'];
		if($source == "")
			$ecart_link = "form_edit_profile#payment_detail" ;
		else
			$ecart_link = $source."#payment_detail" ;
		
		echo "<form method='POST' action='".ECART_LINK."ecart=process_update_credit_card_detail&source=$source' onsubmit='return validation_credit_card()'>";
		
		echo "<input type='hidden' value='$AccessCode' name='AccessCode'>";
		echo "<input type='hidden' value='$FormActionURL' name='FormActionURL'>";
		$this->form_input_credit_card($Response);
		
		echo "<input type='button' value='Back' onclick=\"window.location='".ECART_LINK."ecart=$ecart_link'\">&nbsp;";
		echo "<input type='submit' value='Update Credit Card'>";
		echo "</form>";
		*/

		$this->css_style();
	}
	
	function process_update_credit_card_detail()
	{
		$source = $_GET['source'];
		$qry_eway = mysql_query("SELECT * FROM wp_ecart_eway_api_config WHERE 1");
		$data_eway = mysql_fetch_array($qry_eway);

		$eway_connection = $data_eway['eway_connection'];
		$eway_api_key = $data_eway['eway_api_key'];
		$eway_password = base64_decode($data_eway['eway_password']);
		$eway_CustomerID = $data_eway['eway_CustomerID'];
		
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
			$responseMessage = "An Error occured when connection to eway server, Please try again!";
			$create_customer = "FAILED";
			echo "<script>alert('$responseMessage'); window.location='".ECART_LINK."ecart=change_credit_card_detail&source=$source';</script>";
			exit();
		}
		
		$client->namespaces['man'] = 'https://www.eway.com.au/gateway/managedpayment';
		// set SOAP header
		$headers = "<man:eWAYHeader><man:eWAYCustomerID>" . $eway_CustomerID. "</man:eWAYCustomerID><man:Username>" . $eway_api_key . "</man:Username><man:Password>" . $eway_password . "</man:Password></man:eWAYHeader>";
		$client->setHeaders($headers);
		
		# CUSTOMER DETAIL
		$TokenCustomerID = $_POST['TokenCustomerID'];
		$qry_customer = mysql_query("SELECT * FROM wp_ecart_customer a, wp_ecart_customer_address b WHERE a.TokenCustomerID='$TokenCustomerID' and b.customer_id=a.customer_id ");
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
		
		$requestbody = array(
			'man:managedCustomerID' => $_POST['TokenCustomerID'],
			'man:Title' => "Mr.",
			'man:FirstName' => $first_name,
			'man:LastName' => $last_name,
			'man:Address' => $Street,
			'man:Suburb' => $mailing_city,
			'man:State' => $mailing_state,
			'man:Company' => "Nucleus E-Cart",
			'man:PostCode' => $mailing_postcode,
			'man:Country' => strtolower($this->show_country_code($mailing_country)),
			'man:Email' => "email@email.com",
			'man:Fax' => $fax,
			'man:Phone' => $phone,
			'man:Mobile' => $mobile_phone,
			'man:CustomerRef' => "Nucleus E-Cart",
			'man:JobDesc' => $_POST['JobDesc'],
			'man:Comments' => $note,
			'man:URL' => $_POST['URL'],
			'man:CCNumber' => $_POST['EWAY_CARDNUMBER'],
			'man:CCNameOnCard' => $_POST['EWAY_CARDNAME'],
			'man:CCExpiryMonth' => $_POST['EWAY_CARDEXPIRYMONTH'],
			'man:CCExpiryYear' => $_POST['EWAY_CARDEXPIRYYEAR'],
		);
		$soapaction = 'https://www.eway.com.au/gateway/managedpayment/UpdateCustomer';
		$result = $client->call('man:UpdateCustomer', $requestbody, '', $soapaction);
		
		if ($client->fault) 
		{
			//echo "An Error occured when connection to eway server, Please try again!";
			//echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
			$responseMessage = $result['faultstring'];
			$responseMessage = "An Error occured when connection to eway server, Please try again!";
			$create_customer = "FAILED";
			echo "<script>alert('$responseMessage'); window.location='".ECART_LINK."ecart=change_credit_card_detail&source=$source';</script>";
		} 
		else 
		{
			$err = $client->getError();
			if ($err) 
			{
				$responseMessage = "An Error occured when connection to eway server, Please try again!";
				$create_customer = "FAILED";
				echo "<script>alert('$responseMessage'); window.location='".HTTPS_ECART_LINK."ecart=change_credit_card_detail&source=$source&sid=".session_id()."&es=".$_GET['es']."';</script>";
			} 
			else 
			{
				$TokenCustomerID = $result;
				$create_customer = "SUCCESS";
				echo "<script>alert('Credit card detail was updated'); window.location='".HTTPS_ECART_LINK."ecart=change_credit_card_detail&source=$source&sid=".session_id()."&es=".$_GET['es']."';</script>";
			}
		}

		/*
		$GLOBALS['EWAY_CONNECTION'] = $eway_connection;
		$GLOBALS['EWAY_API_USERNAME'] = $eway_api_key;
		$GLOBALS['EWAY_API_PASSWORD'] = $eway_password;

		include_once(ECART_DIR."eway_function/Rapid3.0.php");
		
		$service = new RapidAPI();
		
		//set POST variables
		$url = $_POST['FormActionURL'];
		$AccessCode = $_POST['AccessCode'];
		$EWAY_CARDNAME = $_POST['EWAY_CARDNAME'];
		$EWAY_CARDNUMBER = $_POST['EWAY_CARDNUMBER'];
		$EWAY_CARDEXPIRYMONTH = $_POST['EWAY_CARDEXPIRYMONTH'];
		$EWAY_CARDEXPIRYYEAR = $_POST['EWAY_CARDEXPIRYYEAR'];
		$EWAY_CARDSTARTMONTH = $_POST['EWAY_CARDSTARTMONTH'];
		$EWAY_CARDSTARTYEAR = $_POST['EWAY_CARDSTARTYEAR'];
		$EWAY_CARDISSUENUMBER = $_POST['EWAY_CARDISSUENUMBER'];
		$EWAY_CARDCVN = $_POST['EWAY_CARDCVN'];
		
		$fields = array(
					'EWAY_ACCESSCODE' => urlencode($AccessCode),
					'EWAY_CARDNAME' => urlencode($EWAY_CARDNAME),
					'EWAY_CARDNUMBER' => urlencode($EWAY_CARDNUMBER),
					'EWAY_CARDEXPIRYMONTH' => $EWAY_CARDEXPIRYMONTH,
					'EWAY_CARDEXPIRYYEAR' => $EWAY_CARDEXPIRYYEAR,
					'EWAY_CARDSTARTMONTH' => $EWAY_CARDSTARTMONTH,
					'EWAY_CARDSTARTYEAR' => $EWAY_CARDSTARTYEAR,
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

		$request->AccessCode = $AccessCode;

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
			$caverno = base64_encode($EWAY_CARDCVN);
			
			mysql_query("UPDATE wp_ecart_customer SET	
														TokenCustomerID = '$TokenCustomerID',
														caverno = '$caverno',
														AccessCode = '$AccessCode'
														
														WHERE customer_id = '".$_SESSION['customer_id']."'
														
														");
			
			echo "<script>alert('Credit card detail was updated'); window.location='".ECART_LINK."ecart=change_credit_card_detail&source=$source';</script>";
		}
		else
		{		
			$responseMessage = $this->response_code($result->ResponseCode)."\\n";
			
			if(isset($result->ResponseMessage))
			{
				//Get Error Messages from Error Code. Error Code Mappings are in the Config.ini file
				$ResponseMessageArray = explode(",", $result->ResponseMessage);

				foreach ( $ResponseMessageArray as $message )
				{
					if(isset($service->APIConfig[$message]))
						$responseMessage .= $service->APIConfig[$message]."\\n";
					else
						$responseMessage .= $message."\\n";
				}
			}
			
			echo "<script>alert('$responseMessage'); window.location='".ECART_LINK."ecart=change_credit_card_detail&source=$source';</script>";
		}
		*/
	}
	
	function product_special()
	{
		echo "<div align='center'>";
		echo "<h1>Product Special!</h1>";
		
		//echo $number_product;
		echo "<br>&nbsp;";
		#$feed_url = "http://rss.detik.com/index.php/detikcom";
		#$this->getFeed($feed_url);
		
		$qry = mysql_query("select * from  wp_ecart_product where product_on_special='1' AND inactive <> 'Y'");
		while($data = mysql_fetch_array($qry))
		{
			$product_id = $data['product_id'];
			$product_name = $data['product_name'];
			$product_image = $data['product_image'];
			$selling_price_inc_tax = "$".number_format($data['selling_price_inc_tax'], 2, '.', ',');
			$selling_price_exc_tax = $data['selling_price_exc_tax'];
			$special_selling_price_inc_tax = "$".number_format($data['special_selling_price_inc_tax'], 2, '.', ',');
			$product_amount = $data['product_amount'];
			
			//$product_image_link = "<a href='$product_image' class='MagicThumb' rel='image-size: fit-screen; buttons-display:close' onclick='return false;'>";
			$product_image_link = "<a href='$product_image' title='".$product_name."' rel='lightbox' onclick='return false;'>";
			$product_image_link .= "<img src='$product_image' width='150' style='display:block'>";
			$product_image_link .= "</a>";
			
			
			echo "<a href='".ECART_LINK."ecart=product_detail&product_id=$product_id' class='ecart_link'>".$product_name."</a>";;
			echo "<br>&nbsp;";
			echo "$special_selling_price_inc_tax ($product_amount)";
			echo "<br>&nbsp;";
			echo $product_image_link;
			
			echo "<hr>";
		}
	
		echo "</div>";
	
	}
	
	function shopping_cart_widget()
	{
		global $wpdb;
		echo "<div id='shopping_cart_widget'>";
		
		$unique_id = session_id();
		$ip_address = $_SERVER['REMOTE_ADDR'];

		$sql = "SELECT * FROM ".ECART_TBL_PREFIX."shopping_cart a, ".ECART_TBL_PREFIX."product b where a.unique_id='$unique_id' and a.ip_address='$ip_address' and b.product_id=a.product_id ";
		
		$wpdb->query($sql);
		if($wpdb->num_rows == 0)
		{
			echo "Shopping Cart is empty<br><br>";	
		}
		else
		{
			$results = $wpdb->get_results($sql);
			
			
			echo "<form id='shopping_cart'>";
			echo "<input type='hidden' id='ABSPATH' name='ABSPATH' value=\"".base64_encode(ABSPATH)."\">";
			echo "<input type='hidden' id='unique_id' name='unique_id' value=\"".$unique_id."\">";
			echo "<input type='hidden' id='ip_address' name='ip_address' value=\"".$ip_address."\">";
			
			echo "<div id='table_shopping_cart_widget'>";
			echo "<table class='shopping_cart_widget' width='100%'>";
			$show_tax = $this->check_show_tax();		
			$total = 0;
			$no = 0;
			foreach($results as $result)
			{
				$product_id = $result->product_id;
				
				if($show_tax == "Y")
					$price = $result->price_inc_tax;
				else
					$price = $result->price_exc_tax; 

				$quantity = $result->quantity;			
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
				
				$qty = $result->quantity;
				echo "<tr id='tr_$product_id' class='$tr_shopping_cart'><td>$qty</td><td>".$result->product_amount."</td><td>".$result->product_name."</td><td>$".$subtotal."</td><td><a onclick=\"confirm_delete('$product_id')\" class='ecart_link'>Remove</a></td></tr>";

			}			
			echo "</table>";
			echo "<a href='".ECART_LINK."ecart=shopping_cart' class='ecart_link'>Edit Cart</a>";
			echo "<br>&nbsp;";
			//echo "<br>&nbsp;";
			echo "<div>To complete your purchase click</div>";
			echo "&nbsp;";
			echo "<input type='button' onclick=\"window.location='".HTTPS_ECART_LINK."ecart=checkout&sid=".session_id()."&es=".$this->enc_session()."'\" value='Proceed to Checkout' style='padding:0; height:20px'>";
			echo "<br>";
			if($_SESSION['customer_id'] != "")
			{
				echo "&nbsp;";
				echo "<div align='center'><input type='button' onclick='confirm_logout()' value='Logout' style='padding:0; height:30px; width:80px'></div>";
			}
			echo "&nbsp;";
			echo "</div>";
			
			echo "</form>";
		}
		
		echo "</div>";
		
		?>
		<script>
		function confirm_logout()
		{
			var a = confirm("Are you sure want to logout?");
			if(a)
			{
				window.location="<?php echo HTTPS_ECART_LINK ?>ecart=logout";
			}
		}
		</script>
		
		<style>
		
		table.shopping_cart_widget tr td{
			padding:5px;
			font-size:11px;
		}
		
		#shopping_cart_widget *{
			font-size:11px;
		}
		
		</style>
		<?php
	
	}
	
	function show_forget_password()
	{
		
	
	}
	
	function update_delivery_address()
	{
		
	
	}
	
	function form_forget_password()
	{
		echo "<form method='post' action='".ECART_LINK."ecart=process_form_forget_password'>";
		echo "Insert Your Email Address <input type='text' name='email_address'> <input type='submit' value='Submit'>";
		echo "</form>";
	}
	
	function process_form_forget_password()
	{
		$email_address = $_POST['email_address'];
		$qry_show = mysql_query("select * from wp_ecart_customer where email='$email_address' ");
		$data_email = mysql_fetch_array($qry_show);
		
		$customer_id = $data_email['customer_id'];
		$first_name = $data_email['first_name'];
		$last_name = $data_email['last_name'];
		$email = $data_email['email'];
		
		if($customer_id != "")
		{
			$expired = time()+3600;

			$token = base64_encode($customer_id."::".$first_name."::".$last_name."::".$email."::".$expired);
			$url_reset = ECART_LINK."ecart=reset_password&token=$token";
			
			$email_subject = "Reset your password";
      
			$email_body = "Hi $first_name $last_name,<br><br>Can't remember your password? Don't worry about it - it happens.<br>We can help.<br><br>";
			$email_body .= "Your email is: <b>$email</b><br><br>";
			$email_body .= "Just click this link to reset your password:<br>";
			$email_body .= "<a href='$url_reset' target='_blank'>$url_reset</a>"; 
			$email_body .= "<br><br>";     
			$email_body .= "---";
			$email_body .= "<br><br>";
			$email_body .= "Didn't ask to reset your password?<br><br>";
			$email_body .= "If you didn't ask for your password, it's likely that another user entered your email address by mistake while trying to reset their password. If that's the case, you don't need to take any further action and can safely disregard this email.";

			$this->to = $email;
			$this->from = "Reset Password <noreply@resetpassword.com>";
			$this->subject = $email_subject;
			$this->body = $email_body;
			
			$this->sendmail();
		}
		echo "<script>alert(\"Instructions for signing in have been emailed to you\"); window.location='".ECART_LINK."'</script>";
		//echo $email_body;
	}
	
	function reset_password()
	{
		$token = base64_decode($_GET[token]);
		$ex_token = explode("::",$token);
		$customer_id = $ex_token[0];
		$first_name = $ex_token[1];
		$last_name = $ex_token[2];
		$email = $ex_token[3];
		$expired = $ex_token[4];
		
		if(time()<$expired)
		{
			//$qry_user = mysql_query("select a.username, b.name_account from tbl_account_users a, tbl_account_users_detail b where a.user_id='$user_id' and a.company_code='$company_code' and b.user_id=a.user_id", callDB(COMPANY));
			$qry_user = mysql_query("select * from wp_ecart_customer where customer_id='$customer_id'");
			$data_user = mysql_fetch_array($qry_user);

			$customer_id = $data_user['customer_id'];
			$email = $data_user['email'];
			$first_name = $data_user['first_name'];
			$last_name = $data_user['last_name'];
			
			if($customer_id != "")
			{
				?>
				<form method="post" action="<?php echo ECART_LINK ?>ecart=action_reset_password" onsubmit="return validation()">
				  <div class='login'>
				  <table cellpadding="5" cellspacing="0" width="100%">    
					<tr>
					  <td><b>Reset your password</b><br><br>Hi <?php echo $first_name." ".$last_name; ?>,<br>Please use the form below to set a new password.</td>
					</tr>
					<tr>
					  <td><b>Your Email</b><br><input readonly type="text" name="email" id="email" class="inputbox_login" value="<?php echo $email ?>">
					  <input type="hidden" name="customer_id" id="customer_id" class="inputbox_login" value="<?php echo $customer_id ?>">
					  </td>
					</tr> 
					<tr>
					  <td><b>Choose a new password</b><br><input type="password" name="password" id="password" class="inputbox_login"></td>
					</tr>
					<tr>
					  <td><b>Confirm your new password</b><br><input type="password" name="confirm_password" id="confirm_password" class="inputbox_login"></td>
					</tr>     
					<tr>
					  <td align="left"><input type="submit" name="submit-login" value="Reset my password" class="button"></td>
					</tr> 
					<tr>
					  <td><hr class="extras"></td>
					</tr> 
					<tr>
					  <td>Your new password will change after resetting</a></td>
					</tr>    
				  </table>38
				  </div> 
				</form> 

				<script>
				function validation()
				{
					var password = document.getElementById("password");
					var confirm_password = document.getElementById("confirm_password");

					if(password.value == "")
					{
						alert("Password must be filled!");
						password.focus()
						return false;
					}
					else if(password.value != confirm_password.value)
					{
						alert("Password confirmation not match!");
						confirm_password.focus();
						return false;
					}  
				}
				</script> 
				<?php  
			}
			else
			{
				echo "<script>alert(\"This forget password request has expired, please request forget password again!\"); window.location='".ECART_LINK."'</script>";
			}
		}
		else
		{
			echo "<script>alert(\"This forget password request has expired, please request forget password again!\"); window.location='".ECART_LINK."'</script>";
		}
	}
	
	function action_reset_password()
	{
		$customer_id = $_POST['customer_id'];
		$password = $_POST['password'];
		mysql_query("update wp_ecart_customer set password='".md5($password)."' where customer_id='$customer_id' ");
		
		echo "<script>alert(\"Your password has been changed, now you can login with the new password!\"); window.location='".ECART_LINK."'</script>";
	}
	
	function sendmail()
	{
		$to = $this->to;
		$from = $this->from;
		$subject = $this->subject;

		//begin of HTML message
		$message =  $this->body;

		$headers  = "From: $from\r\n";
		$headers .= "Content-type: text/html\r\n";

		//options to send to cc+bcc
		//$headers .= "Cc: [email]maa@p-i-s.cXom[/email]";
		$headers .= "Bcc: nucleus.logic@marquantinternational.com";

		// now lets send the email.
		mail($to, $subject, $message, $headers);
	}
	
	function show_country_code($country)
	{
		$array_country = $this->get_array_country();
		$key = array_search($country, $array_country);
		
		return $key;
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
	
	function get_array_hear_about_us()
	{
		$array_hear_about_us = array( 
			"Google",
			"Other Search Engine",
			"Yellow Pages",
			"Local Paper",
			"Magazine",
			"Friend",
			"Repeat Customer",
			"Other"
		);
		
		return $array_hear_about_us;
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
	
	
	function css_style()
	{
		?>
		<style>
			table.product_type tr td{
				border:1px solid #EDEDED;	
				vertical-align: middle;
				padding:5px;
				-moz-hyphens: none;
			}
			
			table.detail_order tr td{
				padding:5px;
			}
			
			table.table_product tr td{
				border:1px solid #EDEDED;	
				vertical-align: middle;
				padding:5px;
			}
			span.span_product_type{
				cursor:pointer;
			}
			
			.fields {
				clear:both;
				padding: 5px 5px;
			}
			label {
				float: left;
				width: 150px;
			}
			
			.fieldset{
				border:1px solid; 
				width:550px; 
				padding:5px;
				border-color: #AAAAAA;
			}
			
			#form_edit_profile .fieldset{
				border:1px solid; 
				width:550px; 
				padding:5px;
				border-color: #AAAAAA;
			}
			
			#form_edit_profile label {
				float: left;
				width: 200px;
			}
			
			#form_edit_profile .input_text{
				width: 300px;
			}
			
			#form_edit_profile *{
				font-size:12px;
			}
			
			#form_edit_profile input,
			#form_edit_profile textarea
			{
				padding: 3px;
			}	
			
			.button_add_to_cart{
				position:fixed;
				bottom:100px;
				background: url("<?php echo ECART_URL ?>images/add-to-cart.png") !important;
				border: 0px !important; 
				width:200px;
				height:50px;
			}
			
			.div_add_to_cart{
				width:200px;
				float:right;
				display:none;
			}
			
		</style>
		<?php
	}
	
	function js_autorun()
	{
		?>
		<script>
		jQuery(document).ready(function() {
			jQuery( "ul#menu-main-menu li a" ).each(function(){		
				var a = jQuery( this ).attr( "href" ).replace(new RegExp("https","g"),"http");
				jQuery( this ).attr( "href", a );
			});
		});
		</script>
		<?php
	}
	
	function js()
	{
		?>
		<script>
		
		function process_checkout()
		{
			if(validation_credit_card())
			{
				jQuery("#checkout_button").after("<img src='<?php echo ECART_URL ?>images/loading_icon.gif' style='box-shadow:none; border-radius:0; margin:9px'>");
				
				var ajaxurl = "<?php echo HTTPS_ECART_URL ?>ajax/process_checkout.php";
				var data = jQuery("#checkout").serialize();
				
				jQuery.post(ajaxurl, data, function(response) {
					var arr_response = response.split("||"); 
					if(arr_response[0] == "SUCCESS")
					{
						alert("Thank you for your ordering, We will process your order shortly");
						window.location = "<?php echo ECART_LINK ?>ecart=order_success&invoice_number=" + arr_response[1];
					}
					else
					{
						alert(response);
					}
					jQuery("#checkout_button").next().remove();
				});	
			}
		}
		
		function load_product_list(product_category_id)
		{
			var url_link = "<?php echo ECART_URL ?>ajax/ajax_product_list.php?ABSPATH=" + jQuery("#ABSPATH").val() + "&ECART_LINK=" + jQuery("#ECART_LINK").val() + "&product_category_id=" + product_category_id;
			
			jQuery.ajax({
				type: "GET",
				dataType: "json",
				beforeSend: function(x) {
					if(x && x.overrideMimeType) {
					x.overrideMimeType("application/json;charset=UTF-8");
					}
				},
				url: url_link,
				success: function(data) {
					var arr_data = data.product_list;
					
					jQuery.each(arr_data,function(index, value){ 
						jQuery("#product_list_content").html(value);
					});
					//alert(data);
					//MagicThumb.stop();
					//MagicThumb.refresh();
				}
			});
		}
		
		function change_product_type()
		{
			var url_link = "<?php echo ECART_URL ?>ajax/ajax_product_list_subcategory.php?ABSPATH=" + jQuery("#ABSPATH").val() + "&ECART_LINK=" + jQuery("#ECART_LINK").val() + "&product_category_id=" + jQuery("#product_category_id").val() + "&product_type_id=" + jQuery("#product_type_id").val();
			
			jQuery.ajax({
				type: "GET",
				dataType: "json",
				beforeSend: function(x) {
					if(x && x.overrideMimeType) {
					x.overrideMimeType("application/json;charset=UTF-8");
					}
				},
				url: url_link,
				success: function(data) {
					var arr_data = data.product_list;
					
					jQuery.each(arr_data,function(index, value){ 
						jQuery("#div_product_list").html(value);
					});
					//alert(data);
					//MagicThumb.stop();
					//MagicThumb.refresh();
				}
			});
		}
		
		function refresh_shopping_cart_widget()
		{		
			var url_link = "<?php echo ECART_URL ?>ajax/table_shopping_cart_widget.php?ABSPATH=<?php echo base64_encode(ABSPATH); ?>&ECART_LINK=<?php echo base64_encode(ECART_LINK); ?>&unique_id=<?php echo session_id(); ?>&ip_address=<?php echo $_SERVER['REMOTE_ADDR']; ?>";
			
			jQuery.ajax({
				type: "GET",
				dataType: "json",
				beforeSend: function(x) {
					if(x && x.overrideMimeType) {
					x.overrideMimeType("application/json;charset=UTF-8");
					}
				},
				url: url_link,
				success: function(data) {
					var arr_data = data.shopping_cart_widget;
					
					jQuery.each(arr_data,function(index, value){ 
						jQuery("#table_shopping_cart_widget").html(value);
					});
					//alert(data);
				}
			});
		}
		
		function update_shopping_cart()
		{
			var ajaxurl = "<?php echo ECART_URL ?>ajax/update_shopping_cart.php";
			var data = jQuery("#shopping_cart").serialize();
			
			jQuery.post(ajaxurl, data, function(response) {
				jQuery("#alert_update").hide();
				jQuery("#alert_update").html("<b>Shopping Cart Updated</b>");
				jQuery("#alert_update").show("slow").delay(500);		
				jQuery("#alert_update").hide("slow");
				refresh_shopping_cart_widget();
			});		
		}
		
		function delete_shopping_cart(product_id)
		{
			var ajaxurl = "<?php echo ECART_URL ?>ajax/delete_shopping_cart.php";
			var data = jQuery("#shopping_cart").serialize();
			data = data + "&product_id=" + product_id;
			
			jQuery.post(ajaxurl, data, function(response) {
			
				if(response == "DATA_EXIST")
				{
					jQuery("#tr_" + product_id).remove();
					change_grand_total();
				}
				else
				{
					var no_data = "Shopping Cart is empty<br><br>";
					no_data += "<input type='button' value='Back to Shopping' onclick=window.location='<?php echo ECART_LINK ?>'>";
					
					jQuery("#shopping_cart").html(no_data);
				}
				refresh_shopping_cart_widget();
			});	
		}
		
		function confirm_delete(product_id)
		{
			var a = confirm("Are you sure want to delete this product from shopping cart?");
			if(a)
			{
				delete_shopping_cart(product_id);	
			}
		}
		
		function change_quantity(product_id)
		{
			var subtotal = parseFloat(jQuery("#price_" + product_id).val()) * parseFloat(jQuery("#qty_" + product_id).val());
			
			if(isNaN(subtotal)) subtotal = 0;
			if(isNaN(jQuery("#qty_" + product_id).val())) subtotal = 0;
			
			jQuery("#val_subtotal_" + product_id).val(subtotal);
			
			subtotal = jQuery.formatNumber(subtotal, {format:"#,###.00", locale:"us"});
			jQuery("#span_subtotal_" + product_id).html(subtotal);
			
			change_grand_total();
		}
		
		function change_grand_total()
		{
			var grand_total = 0;
			jQuery("[id^=val_subtotal_]").each(function() {
				grand_total = grand_total + parseFloat(jQuery(this).val());
				
				//alert(jQuery(this).val());
			});
			
			grand_total = jQuery.formatNumber(grand_total, {format:"#,###.00", locale:"us"});
		
			jQuery("#grand_total").html(grand_total);
				
		}
		
		</script>
		<?php
	}


}
?>