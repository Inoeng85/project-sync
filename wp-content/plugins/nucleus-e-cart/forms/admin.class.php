<?php
class admin{
	var $from       = "william@nucleuslogic.com";
	var $to         = "henry@nucleuslogic.com";
	var $subject    = "Just Test Message!";
	var $body       = "<b>Just Test Message!</b>";

	function home_admin()
	{
		echo "<h1>Nucleus E-Cart</h1>";
		echo "<p>Version 1.22</p>";
		echo '<script type="text/javascript" src="'.ECART_URL.'/library/lightbox-form.js"></script>';
		echo "<link type='text/css' rel='stylesheet' href='".ECART_URL."/library/lightbox-form.css'>";

		echo "<div id='shadowing'></div>";

		echo "<div id='box' class='box'>";
		echo "<img src='".ECART_URL."/library/sync_now.gif'>";
		echo "</div>";

		echo "<br>";
		echo "<fieldset class='fieldset'>";
		echo "<legend>Synchronize</legend>";
		echo "<table cellpadding='5'>";
		echo "<tr><td>";
		echo "<input type='button' class='button-secondary' value='Synchronize Sales and Customer To Nucleus' onclick='sync_export_sales_customer()'>";
		echo "<br>";
		echo "<br>";
		echo "<input type='button' class='button-secondary' value='Synchronize Product From Nucleus' onclick='sync_product()'>";
		echo "<br>";
		echo "<br>";
		echo "<input type='button' class='button-secondary' value='Synchronize Customer To Nucleus' onclick='sync_export_all_customer()'>";
		echo "</td></tr>";
		echo "</table>";
		echo "</fieldset>";

		echo "<br>";
		echo "<fieldset class='fieldset'>";
		echo "<legend>Register New Customer</legend>";
		echo "<table cellpadding='5'>";
		echo "<tr><td>";
		echo "<input type='button' class='button-secondary' value='Register New Customer' onclick=window.location='".ECART_FORM_PATH."&sec=register_customer'>";
		echo "</td></tr>";
		echo "</table>";
		echo "</fieldset>";

		echo "<br>";
		echo "<fieldset class='fieldset'>";
		echo "<legend>Setting</legend>";
		echo "<table cellpadding='5'>";
		echo "<tr><td>";
		echo "<input type='button' class='button-secondary' value='API Setting' onclick=window.location='".ECART_FORM_PATH."&sec=form_setting'>";
		echo "<br>";
		echo "<br>";
		echo "<input type='button' class='button-secondary' value='Product List Content Management' onclick=window.location='".ECART_FORM_PATH."&sec=product_list_content_management'>";
		echo "<br>";
		echo "<br>";
		echo "<input type='button' class='button-secondary' value='Order Email Content Management' onclick=window.location='".ECART_FORM_PATH."&sec=email_order_content_management'>";
		echo "<br>";
		echo "<br>";
		echo "<input type='button' class='button-secondary' value='Order Success Page Content Management' onclick=window.location='".ECART_FORM_PATH."&sec=order_success_content_management'>";
		echo "<br>";
		echo "<br>";
		echo "<input type='button' class='button-secondary' value='Product Category Management' onclick=window.location='".ECART_FORM_PATH."&sec=category_management'>";
		echo "<br>";
		echo "<br>";
		echo "<input type='button' class='button-secondary' value='Link Management' onclick=window.location='".ECART_FORM_PATH."&sec=link_management'>";


		echo "</td></tr>";
		echo "</table>";
		echo "</fieldset>";

		echo "<br>";
		echo "<fieldset class='fieldset'>";
		global $wpdb;

		# NUCLEUS API SETTING
		$sql = "SELECT * FROM ".ECART_TBL_PREFIX."nucleus_api_config where  1";
		$results = $wpdb->get_results($sql);

		$company_name = $results[0]->company_name;
		$company_url = $results[0]->company_url;
		$nucleus_api_username = $results[0]->nucleus_api_username;
		$nucleus_api_password = $results[0]->nucleus_api_password;
		$nucleus_api_key = $results[0]->nucleus_api_key;
		$nucleus_api_connection_status = $results[0]->nucleus_api_connection_status;

		if($nucleus_api_connection_status == "SUCCESS")
			$status_connection = "<b style='color:green'>Connected</b>";
		else
			$status_connection = "<b style='color:red'>Not Connected</b>";

		# EWAY API SETTING
		$sql = "SELECT * FROM ".ECART_TBL_PREFIX."eway_api_config where 1";
		$results = $wpdb->get_results($sql);

		$eway_connection = $results[0]->eway_connection;
		$eway_api_key = $results[0]->eway_api_key;
		$eway_password = $results[0]->eway_password;
		$eway_CustomerID = $results[0]->eway_CustomerID;

		$eway_api_connection_status = $results[0]->eway_api_connection_status;

		if($eway_api_connection_status == "SUCCESS")
			$eway_api_status_connection = "<b style='color:green'>Connected</b>";
		else
			$eway_api_status_connection = "<b style='color:red'>Not Connected</b>";

		#########################################################

		echo "<legend>API Status</legend>";
		echo "<form method='post' action='".ECART_FORM_PATH."&sec=refresh_setting' id='form_api'>";

		echo "<table cellpadding='5'>";
		echo "<tr><td width='150'>Nucleus API Status</td><td>$status_connection</td></tr>";
		echo "<tr><td>eWay API Status</td><td>$eway_api_status_connection</td></tr>";
		echo "<tr><td colspan='2'>";
		echo "<input type='submit' class='button-primary' value='Refresh Status' name='submit_refresh'>";
		echo "</td></tr>";
		echo "</table>";

		echo "<input type='hidden' class='text_setting' name='company_name' value=\"$company_name\">";
		echo "<input type='hidden' class='text_setting' name='company_url' value=\"$company_url\">";
		echo "<input type='hidden' class='text_setting' name='nucleus_api_username' value=\"$nucleus_api_username\">";
		echo "<input type='hidden' class='text_setting' name='nucleus_api_key' value=\"$nucleus_api_key\">";
		echo "<input type='hidden' name='saved_nucleus_api_password' value=\"$nucleus_api_password\">";

		echo "<input type='hidden' class='text_setting' name='eway_connection' value=\"$eway_connection\">";
		echo "<input type='hidden' class='text_setting' name='eway_api_key' value=\"$eway_api_key\">";
		echo "<input type='hidden' name='saved_eway_password' value=\"$eway_password\">";
		echo "<input type='hidden' name='eway_CustomerID' value=\"$eway_CustomerID\">";

		echo "<input type='hidden' name='ECART_DIR' value=\"".base64_encode(ECART_DIR)."\">";
		echo "<input type='hidden' name='ECART_URL' value=\"".base64_encode(ECART_URL)."\">";
		echo "<input type='hidden' name='ABSPATH' value=\"".base64_encode(ABSPATH)."\">";

		echo "</form>";


		echo "</fieldset>";

		?>
		<style>
		.text_setting{
			width:300px;
		}

		.fieldset{
			border:1px solid;
			width:300px;
			padding:5px;
			border-color: #AAAAAA;
		}
		</style>

		<script>
		function sync_product()
		{
			openbox(0);
			var ajaxurl = "<?php echo ECART_URL ?>ajax/import_product.php";
			var data = jQuery("#form_api").serialize();

			jQuery.post(ajaxurl, data, function(response) {
				if(response == "SUCCESS")
				{
					alert("Synchronize Product From Nucleus Success!");
					closebox();
				}
				else
				{
					alert("Synchronize Product From Nucleus Failed!");
					closebox();
				}

			});
		}

		function sync_export_sales_customer()
		{
			openbox(0);
			var ajaxurl = "<?php echo ECART_URL ?>ajax/export_sales_customer.php";
			var data = jQuery("#form_api").serialize();

			jQuery.post(ajaxurl, data, function(response) {
				if(response == "SUCCESS")
				{
					alert("Synchronize Sales and Customer To Nucleus Success!");
					closebox();
				}
				else
				{
					alert("Synchronize Sales and Customer To Nucleus Failed!");
					closebox();
				}

			});
		}

		function sync_export_all_customer()
		{
			openbox(0);
			var ajaxurl = "<?php echo ECART_URL ?>ajax/export_all_customer.php";
			var data = jQuery("#form_api").serialize();

			jQuery.post(ajaxurl, data, function(response) {
				if(response == "SUCCESS")
				{
					alert("Synchronize Customer To Nucleus Success!");
					closebox();
				}
				else
				{
					alert("Synchronize Customer To Nucleus Failed!");
					closebox();
				}

			});
		}
		</script>
		<?php

	}

	function register_customer()
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

		echo "<h2>Register New Customer</h2>";
		echo "<form method='POST' action='".ECART_FORM_PATH."&sec=process_customer_register' onsubmit='return validation_registration()'>";
		//echo "<form method='POST' action='' onsubmit='return validation_registration()'>";

		echo "<table>";
		echo "<tr><td>First Name <span style='color: red'>*</span></td><td><input type='text' name='first_name' id='first_name' class='text_setting'></td></tr>";
		echo "<tr><td>Last Name <span style='color: red'>*</span></td><td><input type='text' name='last_name' id='last_name' class='text_setting'></td></tr>";
		echo "<tr><td>Delivery Address <span style='color: red'>*</span></td><td><input type='text' name='address' id='address' class='text_setting'></td></tr>";
		echo "<tr><td>Suburb <span style='color: red'>*</span></td><td><input type='text' name='city' id='city' class='text_setting'></td></tr>";
		echo "<tr><td>State <span style='color: red'>*</span></td><td><input type='text' name='state' id='state' class='text_setting'></td></tr>";
		echo "<tr><td>Postcode <span style='color: red'>*</span></td><td><input type='text' name='postcode' id='postcode' onkeyup='check_postcode()' onchange='check_postcode()' class='text_setting'><input type='hidden' id='match_postcode'></td></tr>";
		echo "<tr><td>Country <span style='color: red'>*</span></td><td>$country</td></tr>";
		echo "<tr><td>Phone No. <span style='color: red'>*</span></td><td><input type='text' name='phone' id='phone' class='text_setting'></td></tr>";
		echo "<tr><td>Mobile No.</td><td><input type='text' name='mobile_phone' id='mobile_phone' class='text_setting'></td></tr>";
		echo "<tr><td>Fax</td><td><input type='text' name='fax' id='fax' class='text_setting'></td></tr>";
		echo "<tr><td>Email <span style='color: red'>*</span></td><td><input type='text' name='email' id='email' class='text_setting'></td></tr>";
		echo "<tr><td>Password <span style='color: red'>*</span></td><td><input type='password' name='password' id='password' class='text_setting'></td></tr>";
		echo "<tr><td>Confirm Password <span style='color: red'>*</span></td><td><input type='password' name='confirm_password' id='confirm_password' class='text_setting'></td></tr>";
		echo "<tr><td>Where did you hear about us</td><td>$hear_about_us</td></tr>";
		echo "<tr><td>Message</td><td><textarea name='message' id='message' class='text_setting'></textarea></td></tr>";
		echo "</table>";
		echo "<br><br>";
		echo "<input type='submit' class='button-primary' value='Register'>&nbsp;&nbsp;";

		echo "<input type='button' class='button-secondary' value='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' onclick=window.location='".ECART_FORM_PATH."'>";

		echo "</form>";

		?>
		<style>
		.text_setting{
			width:300px;
		}
		</style>
		<script>
		function validation_registration()
		{
			var first_name = jQuery("#first_name").val();
			var last_name = jQuery("#last_name").val();
			var address = jQuery("#address").val();
			var city = jQuery("#city").val();
			var state = jQuery("#state").val();
			var postcode = jQuery("#postcode").val();
			var match_postcode = jQuery("#match_postcode").val();
			var country = jQuery("#country").val();
			var phone = jQuery("#phone").val();
			var mobile_phone = jQuery("#mobile_phone").val();
			var fax = jQuery("#fax").val();
			var email = jQuery("#email").val();
			var password = jQuery("#password").val();
			var confirm_password = jQuery("#confirm_password").val();
			var message = jQuery("#message").val();


			if(first_name == "")
			{
				alert("First Name must be filled!");
				jQuery("#first_name").focus();
				return false;
			}
			else if(last_name == "")
			{
				alert("Last Name must be filled!");
				jQuery("#last_name").focus();
				return false;
			}
			else if(address == "")
			{
				alert("Address must be filled!");
				jQuery("#address").focus();
				return false;
			}
			else if(city == "")
			{
				alert("Suburb must be filled!");
				jQuery("#city").focus();
				return false;
			}
			else if(state == "")
			{
				alert("State must be filled!");
				jQuery("#state").focus();
				return false;
			}
			else if(postcode == "")
			{
				alert("Postcode must be filled!");
				jQuery("#postcode").focus();
				return false;
			}
			else if(phone == "")
			{
				alert("Phone must be filled!");
				jQuery("#phone").focus();
				return false;
			}
			else if(email == "")
			{
				alert("Email must be filled!");
				jQuery("#email").focus();
				return false;
			}
			else if(password == "")
			{
				alert("Password must be filled!");
				jQuery("#password").focus();
				return false;
			}
			else if(password != confirm_password)
			{
				alert("Confirm Password not match!");
				jQuery("#password").focus();
				return false;
			}
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


				$email_subject = "Detail Login Lilly Pilly Organics";

				$email_body = "Hi $first_name $last_name,<br><br>Welcome to Lilly Pilly Organics<br><br>";
				$email_body .= "This is your login detail<br>";
				$email_body .= "Your username: <b>$email</b><br>";
				$email_body .= "Your password: <b>".$_POST['password']."</b><br>";

				$email_body .= "<br><br>Thank You<br><br><br>";
				$email_body .= "Lilly Pilly Organics";

				$this->to = $email;
				$this->from = "Lilly Pilly Organics <noreply@newpassword.com>";
				$this->subject = $email_subject;
				$this->body = $email_body;

				$this->sendmail();

				echo "<script>alert('Registration New Customer!'); window.location='".ECART_FORM_PATH."'</script>";
			}
			else
			{
				#$_SESSION['customer_id'] = "";
				echo "<script>alert('Email address already exist, Please try another email address!'); window.location='".ECART_FORM_PATH."&sec=register_customer'</script>";
				#echo "<script>alert('Email address already exist, Please try another email address!'); window.location='".HTTPS_ECART_LINK."ecart=$source&sid=".session_id()."&es=".$_GET['es']."'</script>";
			}
		}
		else
		{
			#$_SESSION['customer_id'] = "";
			echo "<script>alert('Email address must be filled!'); window.location='".ECART_FORM_PATH."&sec=register_customer'</script>";
			//echo "<script>alert('Email address must be filled!'); window.location='".HTTPS_ECART_LINK."ecart=$source&sid=".session_id()."&es=".$_GET['es']."'</script>";
		}

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
		$headers .= "Bcc: puput@nucleuslogic.com";

		// now lets send the email.
		mail($to, $subject, $message, $headers);
	}

	function link_management()
	{
		$link_terms_condition = show_ecart_config('link_terms_condition');
		$link_refund_policy = show_ecart_config('link_refund_policy');

		echo "<h1>Link Management</h1>";
		echo "<br>";

		echo "<form method='post' action='".ECART_FORM_PATH."&sec=process_link_management'>";
		echo "<table>";
		echo "<tr><td>Terms Conditions Link</td><td>:</td><td><input type='text' style='width:300px' name='link_terms_condition' value=\"$link_terms_condition\"></td></tr>";
		echo "<tr><td>Refund Policy Link</td><td>:</td><td><input type='text' style='width:300px' name='link_refund_policy' value=\"$link_refund_policy\"></td></tr>";

		echo "<tr><td colspan='3'>";
		echo "<br>";
		echo "<input type='button' class='button-secondary' value='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' onclick=window.location='".ECART_FORM_PATH."'>";
		echo "&nbsp;&nbsp;";
		echo "<input type='submit' class='button-primary' value='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Update&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'>";
		echo "</td></tr>";
		echo "</table>";
		echo "</form>";
	}

	function process_link_management()
	{
		$link_terms_condition = $_POST['link_terms_condition'];
		$link_refund_policy = $_POST['link_refund_policy'];

		mysql_query("UPDATE wp_ecart_config SET config_value=\"$link_terms_condition\" WHERE config_name='link_terms_condition' ");
		mysql_query("UPDATE wp_ecart_config SET config_value=\"$link_refund_policy\" WHERE config_name='link_refund_policy' ");

		echo "<script>alert('Links was Updated');window.location='".ECART_FORM_PATH."'</script>";

	}

	function category_management()
	{
		echo '<script type="text/javascript" src="'.ECART_URL.'library/selectbox.js"></script>';

		echo '<script type="text/javascript" src="'.ECART_URL.'library/lightbox-form.js"></script>';
		echo "<link type='text/css' rel='stylesheet' href='".ECART_URL."library/lightbox-form.css'>";

		echo "<div id='shadowing'></div>";

		echo "<div id='box' class='box_subcategory'>";
		echo "<div id='form_subcategory'></div>";
		echo "</div>";

		echo "<h1>Product Category Management</h1>";
		echo "<br>";
		echo "<input type='hidden' id='ECART_DIR' name='ECART_DIR' value=\"".base64_encode(ECART_DIR)."\">";
		echo "<input type='hidden' id='ABSPATH' name='ABSPATH' value=\"".base64_encode(ABSPATH)."\">";
		echo "<input type='text' name='new_category' id='new_category' class='text_input' placeholder='Insert New Category'> ";
		echo "<input type='button' value='Add New Category' class='button-secondary' onclick='insert_new_category()'>";
		echo "<br>&nbsp;";

		$qry_category = mysql_query("SELECT * FROM wp_ecart_product_category");

		echo "<table id='table_category' class='wp-list-table widefat fixed posts' style='width:500px'>";
		echo "<thead><tr><th>Category</th><th width='200'>Action</th></tr></thead>";
		echo "<tbody>";

		while($data_category = mysql_fetch_array($qry_category))
		{
			$category_id = $data_category['product_category_id'];
			$category_name = "<span id='span_cat_$category_id'>".$data_category['category']."</span><input type='text' style='display:none' class='text_input' name='text_cat_$category_id' id='text_cat_$category_id' value=\"".$data_category['category']."\">";
			echo "<tr id='tr_cat_$category_id'><td>".$category_name."</td><td><span id='span_action_$category_id'><span onclick=\"cat_edit('$category_id')\" class='pointer'>Edit</span> - <span onclick=\"cat_delete('$category_id')\" class='pointer'>Delete</span> - <span onclick=\"cat_subcategory('$category_id')\" class='pointer'>View Subcategory</span></span><span id='span_process_$category_id' style='display:none'><span onclick=\"cat_update_edit('$category_id')\" class='pointer'>Update</span> - <span onclick=\"cat_cancel_edit('$category_id')\" class='pointer'>Cancel</span></span></td></tr>";
		}
		echo "</tbody>";
		echo "</table>";

		echo "<br>";
		echo "<input type='button' class='button-secondary' value='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' onclick=window.location='".ECART_FORM_PATH."'>";


		?>
		<script>
		function cat_edit(cat_id)
		{
			jQuery("#text_cat_" + cat_id).val(jQuery("#span_cat_" + cat_id).html());

			jQuery("#span_action_" + cat_id).hide();
			jQuery("#span_cat_" + cat_id).hide();
			jQuery("#span_process_" + cat_id).show();
			jQuery("#text_cat_" + cat_id).show();
		}

		function cat_update_edit(cat_id)
		{
			var ajaxurl = "<?php echo ECART_URL ?>ajax/ajax_category.php";
			var data = "ECART_DIR=" + jQuery("#ECART_DIR").val() + "&ABSPATH=" + jQuery("#ABSPATH").val();
			data += "&action=update_new_category" + "&category=" + encodeURIComponent(jQuery("#text_cat_" + cat_id).val()) + "&product_category_id=" + cat_id;

			jQuery.post(ajaxurl, data, function(response) {
				if(response == "SUCCESS")
				{
					jQuery("#span_cat_" + cat_id).html(jQuery("#text_cat_" + cat_id).val());
				}

				jQuery("#span_action_" + cat_id).show();
				jQuery("#span_cat_" + cat_id).show();
				jQuery("#span_process_" + cat_id).hide();
				jQuery("#text_cat_" + cat_id).hide();

			});
		}

		function cat_cancel_edit(cat_id)
		{
			jQuery("#span_action_" + cat_id).show();
			jQuery("#span_cat_" + cat_id).show();
			jQuery("#span_process_" + cat_id).hide();
			jQuery("#text_cat_" + cat_id).hide();
		}

		function insert_new_category()
		{
			if(jQuery("#new_category").val() != "")
			{
				var ajaxurl = "<?php echo ECART_URL ?>ajax/ajax_category.php";
				var data = "ECART_DIR=" + jQuery("#ECART_DIR").val() + "&ABSPATH=" + jQuery("#ABSPATH").val();
				data += "&action=insert_new_category" + "&new_category=" + encodeURIComponent(jQuery("#new_category").val());

				jQuery.post(ajaxurl, data, function(product_category_id) {
					if(isNaN(product_category_id))
					{
						alert("Create New Category Failed");
					}
					else
					{
						var category_name = "<span id='span_cat_" + product_category_id + "'>" + jQuery("#new_category").val() + "</span><input type='text' style='display:none' class='text_input' name='text_cat_" + product_category_id + "' id='text_cat_" + product_category_id + "' value=\"" + jQuery("#new_category").val() + "\">";
						var tr = "<tr id='tr_cat_" + product_category_id + "'><td>" + category_name + "</td><td><span id='span_action_" + product_category_id + "'><span onclick=\"cat_edit('" + product_category_id + "')\" class='pointer'>Edit</span> - <span onclick=\"cat_delete('" + product_category_id + "')\" class='pointer'>Delete</span> - <span onclick=\"cat_subcategory('" + product_category_id + "')\" class='pointer'>View Subcategory</span></span><span id='span_process_" + product_category_id + "' style='display:none'><span onclick=\"cat_update_edit('" + product_category_id + "')\" class='pointer'>Update</span> - <span onclick=\"cat_cancel_edit('" + product_category_id + "')\" class='pointer'>Cancel</span></span></td></tr>";

						jQuery("#table_category").append(tr);
						jQuery("#new_category").val("");
					}
				});
			}
		}

		function cat_delete(cat_id)
		{
			var a = confirm("Are you sure want to delete this category");
			if(a)
			{
				var ajaxurl = "<?php echo ECART_URL ?>ajax/ajax_category.php";
				var data = "ECART_DIR=" + jQuery("#ECART_DIR").val() + "&ABSPATH=" + jQuery("#ABSPATH").val();
				data += "&action=delete_new_category" + "&product_category_id=" + cat_id;

				jQuery.post(ajaxurl, data, function(response) {
					if(response == "SUCCESS")
					{
						jQuery("#tr_cat_" + cat_id).remove();
					}
				});
			}
		}

		function cat_subcategory(cat_id)
		{

			var ajaxurl = "<?php echo ECART_URL ?>ajax/ajax_category.php";
			var data = "ECART_DIR=" + jQuery("#ECART_DIR").val() + "&ABSPATH=" + jQuery("#ABSPATH").val();
			data += "&action=view_subcategory" + "&product_category_id=" + cat_id;

			jQuery.post(ajaxurl, data, function(subcategory) {
				jQuery("#form_subcategory").html(subcategory);
				openbox(0);
			});
		}

		function selectAll(selectBox,selectAll)
		{
			// have we been passed an ID
			if (typeof selectBox == "string") {
				selectBox = document.getElementById(selectBox);
			}
			// is the select box a multiple select box?
			if (selectBox.type == "select-multiple") {
				for (var i = 0; i < selectBox.options.length; i++) {
					selectBox.options[i].selected = selectAll;
				}
			}
		}

		function submit_subcategory(cat_id)
		{
			selectAll('assign_product_type',true);
			var form_assign_subcategory = jQuery("#form_assign_subcategory").serialize();

			var ajaxurl = "<?php echo ECART_URL ?>ajax/ajax_category.php";
			var data = "ECART_DIR=" + jQuery("#ECART_DIR").val() + "&ABSPATH=" + jQuery("#ABSPATH").val();
			data += "&action=assign_subcategory" + "&product_category_id=" + cat_id + "&" + form_assign_subcategory;

			jQuery.post(ajaxurl, data, function(subcategory) {
				closebox();
			});
		}

		</script>

		<style>
		.pointer{
			cursor:pointer;
		}

		.text_input
		{
			width:200px;
		}
		</style>
		<?php

	}

	function insert_new_category()
	{
		$category = $_POST['new_category'];
		mysql_query("INSERT INTO wp_ecart_product_category (category,active) VALUES (\"$category\", '1')") or die (mysql_error());
		echo mysql_insert_id();
	}

	function update_new_category()
	{
		$product_category_id = $_POST['product_category_id'];
		$category = $_POST['category'];

		mysql_query("UPDATE wp_ecart_product_category SET category=\"$category\" WHERE product_category_id='$product_category_id'") or die (mysql_error());
		echo "SUCCESS";
	}

	function delete_new_category()
	{
		$product_category_id = $_POST['product_category_id'];
		mysql_query("DELETE FROM wp_ecart_product_category WHERE product_category_id='$product_category_id'") or die (mysql_error());
		mysql_query("DELETE FROM wp_ecart_product_category_type_link WHERE product_category_id='$product_category_id'");
		echo "SUCCESS";
	}

	function view_subcategory()
	{
		$product_category_id = $_POST['product_category_id'];
		$qry_category_id = mysql_query("SELECT * FROM wp_ecart_product_category WHERE product_category_id='$product_category_id'") or die (mysql_error());
		$data_category_id = mysql_fetch_array($qry_category_id);

		$select1 = "<select name='all_product_type' id='all_product_type' class='selectbox'  multiple size='10' style='width:200px'>";
		#$qry_product_type = mysql_query("SELECT * FROM wp_ecart_product_type");
		$qry_product_type = mysql_query("SELECT * FROM wp_ecart_product_type WHERE product_type_id NOT IN (SELECT a.product_type_id FROM wp_ecart_product_category_type_link a, wp_ecart_product_type b, wp_ecart_product_category c WHERE b.product_type_id=a.product_type_id AND c.product_category_id=a.product_category_id)");
		while($data_product_type = mysql_fetch_array($qry_product_type))
		{
			$select1 .= "<option value='".$data_product_type['product_type_id']."'>".$data_product_type['type']."</option>";
		}
		$select1 .= "</select>";

		$select2 = "<select name='assign_product_type[]' id='assign_product_type' class='selectbox'  multiple size='10' style='width:200px'>";
		$qry_product_type = mysql_query("SELECT * FROM wp_ecart_product_category_type_link a, wp_ecart_product_type b WHERE a.product_category_id='$product_category_id' AND b.product_type_id=a.product_type_id");
		while($data_product_type = mysql_fetch_array($qry_product_type))
		{
			$select2 .= "<option value='".$data_product_type['product_type_id']."'>".$data_product_type['type']."</option>";
		}
		$select2 .= "</select>";

		echo "<h2 align='center'>Subcategory for ".$data_category_id['category']."</h2>";
		echo "<form id='form_assign_subcategory' method='post'>";
		echo "<table>
		<tr>
			<td align='center'>Available subcategory</td>
			<td></td>
			<td align='center'>Subcategory for ".$data_category_id['category']."</td>
		</tr>
		<tr>
			<td>$select1</td>
            <td align='center' valign='middle'>
                <input type='button' class='button-secondary' value=\"&raquo;\" onClick=\"moveSelectedOptions(document.getElementById('all_product_type'),document.getElementById('assign_product_type'),false); return false;\">
				<br><br>
				<input type='button' class='button-secondary' value=\"&laquo;\" onClick=\"moveSelectedOptions(document.getElementById('assign_product_type'),document.getElementById('all_product_type'),false); return false;\">

            </td>
			<td>$select2</td>
		</tr>
		<tr>
			<td colspan='3' align='right'>
				<input type='button' class='button-secondary' value='Cancel' onclick='closebox();'>&nbsp;

				<input type='button' class='button-primary' value='Update' onclick=\"submit_subcategory($product_category_id)\">

			</td>
		</tr>
		</table>";
		echo "</form>";
		//<input type='button' class='button-secondary' value=\"&laquo;\" onClick=\"removeSelectedOptions(document.getElementById('assign_product_type')); return false;\">

	}

	function assign_subcategory()
	{
		$product_category_id = $_POST['product_category_id'];
		$assign_product_type = $_POST['assign_product_type'];

		mysql_query("DELETE FROM wp_ecart_product_category_type_link WHERE product_category_id='$product_category_id'");
		foreach($assign_product_type as $product_type_id)
		{
			mysql_query("INSERT INTO wp_ecart_product_category_type_link (product_category_id,product_type_id) VALUES ('$product_category_id','$product_type_id') ");
		}
	}


	function form_setting()
	{
		global $wpdb;

		# NUCLEUS API SETTING
		$sql = "SELECT * FROM ".ECART_TBL_PREFIX."nucleus_api_config where 1";
		$results = $wpdb->get_results($sql);

		$company_name = $results[0]->company_name;
		$company_url = $results[0]->company_url;
		$nucleus_api_username = $results[0]->nucleus_api_username;
		$nucleus_api_password = $results[0]->nucleus_api_password;
		$nucleus_api_key = $results[0]->nucleus_api_key;
		$nucleus_api_connection_status = $results[0]->nucleus_api_connection_status;

		if($nucleus_api_connection_status == "SUCCESS")
			$status_connection = "<b style='color:green'>Connected</b>";
		else
			$status_connection = "<b style='color:red'>Not Connected</b>";

		# EWAY API SETTING
		$sql = "SELECT * FROM ".ECART_TBL_PREFIX."eway_api_config where 1";
		$results = $wpdb->get_results($sql);

		$eway_connection = $results[0]->eway_connection;
		$eway_api_key = $results[0]->eway_api_key;
		$eway_password = $results[0]->eway_password;
		$eway_CustomerID = $results[0]->eway_CustomerID;

		$eway_api_connection_status = $results[0]->eway_api_connection_status;

		if($eway_api_connection_status == "SUCCESS")
			$eway_api_status_connection = "<b style='color:green'>Connected</b>";
		else
			$eway_api_status_connection = "<b style='color:red'>Not Connected</b>";

		if($eway_connection == "Live") $check_live = " checked ";
		if($eway_connection == "Sandbox") $check_sandbox = " checked ";

		$https_url = show_ecart_config("https_url");
		$uaid = show_ecart_config("ua_id");
        $cc = show_ecart_config("cc_list");
        $cc_stat = show_ecart_config("cc_stat");
	        if($cc_stat==1)
	        {
	        	$checkccon ='checked';
	        }
	        else
	        {
	        	$checkccoff ='checked';
	        }
		$sender = show_ecart_config("email_sender");

		echo "<h1>Nucleus E-Cart API Setting</h1>";

		echo "<form method='post' action='".ECART_FORM_PATH."&sec=save_setting' id='form_api'>";
		echo "<fieldset class='fieldset'>";
		echo "<legend>Nucleus API Setting</legend>";
		echo "<table>";
		echo "<tr><td width='250'>Authorized Company Name</td><td><input type='text' class='text_setting' name='company_name' value=\"$company_name\"></td></tr>";
		echo "<tr><td>Authorized Company URL</td><td><input type='text' class='text_setting' name='company_url' value=\"$company_url\"></td></tr>";
		echo "<tr><td>Admin Username</td><td><input type='text' class='text_setting' name='nucleus_api_username' value=\"$nucleus_api_username\"></td></tr>";
		echo "<tr><td>Admin Password</td><td><input type='password' class='text_setting' name='nucleus_api_password' id='nucleus_api_password'></td></tr>";
		echo "<tr><td>Re-type Admin Password</td><td><input type='password' class='text_setting' name='retype_nucleus_api_password' id='retype_nucleus_api_password'></td></tr>";
		echo "<tr><td>Nucleus API Key Secret</td><td><input type='text' class='text_setting' name='nucleus_api_key' value=\"$nucleus_api_key\"></td></tr>";
		echo "<tr><td>Status Conection</td><td><span id='span_status_connection'>$status_connection</span></td></tr>";
		echo "<tr><td>&nbsp;</td><td><input type='button' class='button-secondary' value='Test Connection' onclick='test_connection_nucleus_api()'></td></tr>";
		echo "</table>";
		echo "</fieldset>";

		echo "<br>";
		echo "<fieldset class='fieldset'>";
		echo "<legend>eWay API Setting</legend>";
		echo "<table>";
		echo "<tr><td width='250'>eWay Connection</td><td><input type='radio' name='eway_connection' value='Live' $check_live> Live &nbsp;&nbsp;&nbsp;<input type='radio' name='eway_connection' value='Sandbox' $check_sandbox> Sandbox </td></tr>";
		echo "<tr><td>eWay CustomerID</td><td><input type='text' class='text_setting' name='eway_CustomerID' value=\"$eway_CustomerID\"></td></tr>";
		echo "<tr><td>eWay Username</td><td><input type='text' class='text_setting' name='eway_api_key' value=\"$eway_api_key\"></td></tr>";
		echo "<tr><td>eWay Password</td><td><input type='password' class='text_setting' name='eway_password' id='eway_password'></td></tr>";
		echo "<tr><td>Re-type eWay Password</td><td><input type='password' class='text_setting' name='retype_eway_password' id='retype_eway_password'></td></tr>";
		echo "<tr><td>Status Conection</td><td><span id='span_eway_api_status_connection'>$eway_api_status_connection</span></td></tr>";
		echo "<tr><td>&nbsp;</td><td><input type='button' class='button-secondary' value='Test Connection' onclick='test_connection_eway_api()'></td></tr>";
		echo "</table>";
		echo "</fieldset>";

		echo "<br>";
		echo "<fieldset class='fieldset'>";
		echo "<legend>HTTPS Setting</legend>";
		echo "<table>";
		echo "<tr><td width='250'>HTTPS URL</td><td><input type='text' class='text_setting' name='https_url' value=\"$https_url\"></td></tr>";
		echo "</table>";
		echo "</fieldset>";

		echo "<br>";
		echo "<fieldset class='fieldset'>";
		echo "<legend>UAID Setting</legend>";
		echo "<table>";
		echo "<tr><td width='250'>Tracking ID</td><td><input type='text' class='text_setting' name='UA_ID' value=\"$uaid\"></td></tr>";
		echo "</table>";
		echo "</fieldset>";

		echo "<br>";
		echo "<fieldset class='fieldset'>";
		echo "<legend>Confirmation EmailCc Setting</legend>";
		echo "<table>";
		echo "<tr><td colspan ='2'><input type='radio' name='ccgroup' value='1' $checkccon>On";
		echo "<input type='radio' name='ccgroup' value='0' style='margin-left:10px' $checkccoff>Off</td></tr>";
		echo "<tr><td width='250'>EmailCc List</td><td><input type='text' class='text_setting' name='cc_list' value=\"$cc\"></td></tr>";
		echo "<tr><td colspan=2><p class='description'>Note: You can add multiple email receivers by using commas between email addresses</p></td></tr>";
		echo "</table>";
		echo "</fieldset>";

		echo "<br>";
		echo "<fieldset class='fieldset'>";
		echo "<legend>Sender Email Setting</legend>";
		echo "<table>";
		echo "<tr><td width='250'>Email From</td><td><input type='text' class='text_setting' name='sender_email' value=\"$sender\"></td></tr>";
		echo "<tr><td colspan=2><p class='description'>Note: You can only set one email address as sender / Email from</p></td></tr>";
		echo "</table>";
		echo "</fieldset>";

		echo "<br>";

		echo "<input type='hidden' name='saved_nucleus_api_password' value=\"".$nucleus_api_password."\">";
		echo "<input type='hidden' name='saved_eway_password' value=\"".$eway_password."\">";
		echo "<input type='hidden' name='ECART_DIR' value=\"".base64_encode(ECART_DIR)."\">";
		echo "<input type='hidden' name='ECART_URL' value=\"".base64_encode(ECART_URL)."\">";

		echo "<input type='submit' class='button-primary' value='Update Setting'>&nbsp;&nbsp;";
		echo "<input type='button' class='button-secondary' value='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' onclick=window.location='".ECART_FORM_PATH."'>";
		echo "</form>";

		?>
		<script>
		function test_connection_nucleus_api()
		{
			var nucleus_api_password = jQuery("#nucleus_api_password").val();
			var retype_nucleus_api_password = jQuery("#retype_nucleus_api_password").val();

			if((nucleus_api_password != "" && nucleus_api_password != retype_nucleus_api_password) || (retype_nucleus_api_password != "" && nucleus_api_password != retype_nucleus_api_password))
			{
				alert("Nucleus API Admin Password not match");
			}
			else
			{
				var ajaxurl = "<?php echo ECART_URL ?>ajax/test_connection_nucleus_api.php";
				var data = jQuery("#form_api").serialize();

				jQuery.post(ajaxurl, data, function(response) {

					if(response == "SUCCESS")
					{
						jQuery("#span_status_connection").html("<b style='color:green'>Connected</b>");
						alert("Connection Success");
					}
					else
					{
						jQuery("#span_status_connection").html("<b style='color:red'>Not Connected</b>");
						alert("Connection Failed");
					}

				});
			}

			return false;
		}

		function test_connection_eway_api()
		{
			var eway_password = jQuery("#eway_password").val();
			var retype_eway_password = jQuery("#retype_eway_password").val();

			if((eway_password != "" && eway_password != retype_eway_password) || (retype_eway_password != "" && eway_password != retype_eway_password))
			{
				alert("Eway Password not match");
			}
			else
			{

				var ajaxurl = "<?php echo ECART_URL ?>ajax/test_connection_eway_api.php";
				var data = jQuery("#form_api").serialize();

				jQuery.post(ajaxurl, data, function(response) {
					if(response == "SUCCESS")
					{
						jQuery("#span_eway_api_status_connection").html("<b style='color:green'>Connected</b>");
						alert("Connection Success");
					}
					else
					{
						jQuery("#span_eway_api_status_connection").html("<b style='color:red'>Not Connected</b>");
						alert("Connection Failed");
					}
				});
			}
			return false;
		}
		</script>

		<style>
		.text_setting{
			width:300px;
		}

		.fieldset{
			border:1px solid;
			width:550px;
			padding:5px;
			border-color: #AAAAAA;
		}
		</style>
		<?php
	}

	function save_setting()
	{
		$company_name = $_POST['company_name'];
		$company_url = $_POST['company_url'];
		$nucleus_api_username = $_POST['nucleus_api_username'];
		$nucleus_api_password = $_POST['nucleus_api_password'];
		$nucleus_api_key = $_POST['nucleus_api_key'];
		$https_url = $_POST['https_url'];
		$uaid =  $_POST['UA_ID'];
		$cc_list = $_POST['cc_list'];
		$ccstat = $_POST['ccgroup'];
		$sender = $_POST['sender_email'];

		if($nucleus_api_password != "")
			$qry_nucleus_api_password = "nucleus_api_password = \"".md5($nucleus_api_password)."\", ";
		else
			$qry_nucleus_api_password = "";

		$nucleus_api_connection_status = $this->test_nucleus_api_connection_status();

		global $wpdb;
		$table = ECART_TBL_PREFIX."nucleus_api_config";
		$wpdb->query("UPDATE $table SET
										company_name = \"$company_name\",
										company_url = \"$company_url\",
										nucleus_api_username = \"$nucleus_api_username\",
										$qry_nucleus_api_password
										nucleus_api_key = \"$nucleus_api_key\",
										nucleus_api_connection_status = \"$nucleus_api_connection_status\"
										");

		$eway_connection = $_POST['eway_connection'];
		$eway_api_key = $_POST['eway_api_key'];
		$eway_password = $_POST['eway_password'];
		$eway_CustomerID = $_POST['eway_CustomerID'];

		if($eway_password != "")
			$qry_eway_password = "eway_password = \"".base64_encode($eway_password)."\", ";
		else
			$qry_eway_password = "";

		$eway_api_connection_status = $this->test_eway_api_connection_status();

		$table = ECART_TBL_PREFIX."eway_api_config";
		$wpdb->query("UPDATE $table SET
										eway_connection = \"$eway_connection\",
										eway_CustomerID = \"$eway_CustomerID\",
										eway_api_key = \"$eway_api_key\",
										$qry_eway_password
										eway_api_connection_status = \"$eway_api_connection_status\"
										");

		mysql_query("UPDATE wp_ecart_config SET config_value=\"$https_url\" WHERE config_name='https_url' ");
		mysql_query("UPDATE wp_ecart_config SET config_value=\"$uaid\" WHERE config_name='ua_id' ");
		mysql_query("UPDATE wp_ecart_config SET config_value=\"$cc_list\" WHERE config_name='cc_list' ");
		mysql_query("UPDATE wp_ecart_config SET config_value=\"$ccstat\" WHERE config_name='cc_stat' ");

		$stat = strpos((string)$sender, "@");
		if (filter_var($sender, FILTER_VALIDATE_EMAIL)) {
			if ($stat !== false) {
				// VALID
				mysql_query("UPDATE wp_ecart_config SET config_value=\"$sender\" WHERE config_name='email_sender' ");
			}
		}

		echo "<script>alert('Nucleus E-Cart API Setting Saved.'); window.location='".ECART_FORM_PATH."'</script>";
	}

	function refresh_setting()
	{
		if(isset($_POST['submit_refresh']))
		{
			$nucleus_api_connection_status = $this->test_nucleus_api_connection_status();
			$eway_api_connection_status = $this->test_eway_api_connection_status();

			global $wpdb;
			$table = ECART_TBL_PREFIX."nucleus_api_config";
			$wpdb->query("UPDATE $table SET nucleus_api_connection_status = \"$nucleus_api_connection_status\" ");

			$table = ECART_TBL_PREFIX."eway_api_config";
			$wpdb->query("UPDATE $table SET eway_api_connection_status = \"$eway_api_connection_status\" ");
		}

		echo "<script>window.location='".ECART_FORM_PATH."'</script>";
	}

	function test_nucleus_api_connection_status()
	{
		$company_name = $_POST['company_name'];
		$company_url = $_POST['company_url'];
		$nucleus_api_username = $_POST['nucleus_api_username'];
		$nucleus_api_password = $_POST['nucleus_api_password'];
		$nucleus_api_key = $_POST['nucleus_api_key'];
		$ECART_URL = $_POST['ECART_URL'];
		$saved_nucleus_api_password = $_POST['saved_nucleus_api_password'];
		$success = true;

		if(strpos($company_url,"nucleuslogic.com"))
		{
			$post_url = "http://cloud.nucleuslogic.com";
		}
		else if(strpos($company_url,"testnucleus.com"))
		{
			$post_url = "http://cloud.testnucleus.com";
		}
		else if(strpos($company_url,"localhost"))
		{
			$post_url = $company_url;
		}

		if($success == true)
		{
			$url = $post_url."/application/app_ecart_api/test_connection/test_connection.php";

			$fields = array(
						'company_name' => urlencode($company_name),
						'company_url' => urlencode($company_url),
						'nucleus_api_username' => urlencode($nucleus_api_username),
						'nucleus_api_password' => urlencode($nucleus_api_password),
						'nucleus_api_key' => urlencode($nucleus_api_key),
						'ECART_URL' => urlencode($ECART_URL),
						'saved_nucleus_api_password' => urlencode($saved_nucleus_api_password),
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

			return $output;

		}
		else
		{
			return "FAILED";
		}


	}

	function test_eway_api_connection_status()
	{
		$eway_connection = $_POST['eway_connection'];
		$eway_api_key = $_POST['eway_api_key'];
		$eway_password = $_POST['eway_password'];
		$eway_CustomerID = $_POST['eway_CustomerID'];

		if($eway_password == "")
			$eway_password = base64_decode($_POST['saved_eway_password']);

		define('ECART_DIR',base64_decode($_POST['ECART_DIR']));

		$GLOBALS['EWAY_CONNECTION'] = $eway_connection;
		$GLOBALS['EWAY_API_USERNAME'] = $eway_api_key;
		$GLOBALS['EWAY_API_PASSWORD'] = $eway_password;

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
			return "FAILED";
		}

		$client->namespaces['man'] = 'https://www.eway.com.au/gateway/managedpayment';
		// set SOAP header
		$headers = "<man:eWAYHeader><man:eWAYCustomerID>" . $eway_CustomerID. "</man:eWAYCustomerID><man:Username>" . $eway_api_key . "</man:Username><man:Password>" . $eway_password . "</man:Password></man:eWAYHeader>";
		$client->setHeaders($headers);

		$requestbody = array(
			'man:managedCustomerID' => '123456'
		);
		$soapaction = 'https://www.eway.com.au/gateway/managedpayment/QueryCustomer';
		$result = $client->call('man:QueryCustomer', $requestbody, '', $soapaction);

		if ($client->fault)
		{
			if($result['faultstring'] == "Invalid ManagedCustomerID")
			{
				return "SUCCESS";
			}
			else
			{
				return "FAILED";
			}
		}
		else
		{
			$err = $client->getError();
			if ($err)
			{
				return "FAILED";
			}
			else
			{
				return "SUCCESS";
			}
		}

		/*
		include_once(ECART_DIR."eway_function/Rapid3.0.php");

		//Create RapidAPI Service
		$service = new RapidAPI();

		//Create AccessCode Request Object
		$request = new CreateAccessCodeRequest();

		//Populate values for Customer Object
		//Note: Title is Required Field When Create/Update a TokenCustomer
		$request->Customer->Title = "Mr.";
		//Note: FirstName is Required Field When Create/Update a TokenCustomer
		$request->Customer->FirstName = "Nucleus";
		//Note: LastName is Required Field When Create/Update a TokenCustomer
		$request->Customer->LastName = "Logic";
		//Note: Country is Required Field When Create/Update a TokenCustomer
		$request->Customer->Country = "au";

		$request->RedirectUrl = "http://www.nucleuslogic.com";
		$request->Method = "CreateTokenCustomer";

		$result = $service->CreateAccessCode($request);

		//print_r($result);
		if(isset($result->AccessCode))
		{
			return "SUCCESS";
		}
		else
		{
			return "FAILED";
		}
		*/
	}

	function product_list_content_management()
	{
		echo "<h1>Product List Content Management</h1>";
		echo "<br>";
		echo "<div style='width:700px'>";
		echo "<form method='post' action='".ECART_FORM_PATH."&sec=update_product_list_content_management'>";
		$content = show_ecart_config('product_list_content_management');

		wp_editor( $content, 'listingeditor', $settings = array('textarea_name' => post_content) );

		echo "<br>";
		echo "<input type='button' class='button-secondary' value='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' onclick=window.location='".ECART_FORM_PATH."'>";
		echo "&nbsp;&nbsp;";
		echo "<input type='submit' class='button-primary' value='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Update&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'>";

		echo "</form>";
		echo "</div>";

		echo "<style>
		.mceContentBody {
			max-width: none !important;
		}
		</style>
		";
	}

	function update_product_list_content_management()
	{
		//$post_content = $this->replace_char($_POST['post_content']);
		$post_content = $_POST['post_content'];

		mysql_query("UPDATE wp_ecart_config SET config_value=\"$post_content\" WHERE config_name='product_list_content_management' ");

		echo "<script>alert('Product List Content was Updated');window.location='".ECART_FORM_PATH."'</script>";
	}

	function email_order_content_management()
	{
		echo "<h1>Order Email Content Management</h1>";
		echo "<br>";
		echo "<div style='width:700px'>";
		echo "<form method='post' action='".ECART_FORM_PATH."&sec=update_email_order_content_management'>";
		$content = show_ecart_config('email_order_content_management');

		wp_editor( $content, 'listingeditor', $settings = array('textarea_name' => post_content) );

		echo "<br>";
		echo "<input type='button' class='button-secondary' value='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' onclick=window.location='".ECART_FORM_PATH."'>";
		echo "&nbsp;&nbsp;";
		echo "<input type='submit' class='button-primary' value='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Update&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'>";

		echo "</form>";
		echo "</div>";

		echo "<style>
		.mceContentBody {
			max-width: none !important;
		}
		</style>
		";
	}

	function update_email_order_content_management()
	{
		//$post_content = $this->replace_char($_POST['post_content']);
		$post_content = $_POST['post_content'];

		$qry_cek_content = mysql_query("SELECT * FROM wp_ecart_config WHERE config_name='email_order_content_management' ");
		if(mysql_num_rows($qry_cek_content) == 0)
			mysql_query("INSERT INTO wp_ecart_config (config_name, config_value) VALUES ('email_order_content_management', \"$post_content\") ");
		else
			mysql_query("UPDATE wp_ecart_config SET config_value=\"$post_content\" WHERE config_name='email_order_content_management' ");

		echo "<script>alert('Order Email Content was Updated');window.location='".ECART_FORM_PATH."'</script>";
	}

	function order_success_content_management()
	{
		echo "<h1>Order Success Page Content Management</h1>";
		echo "<br>";
		echo "<div style='width:700px'>";
		echo "<form method='post' action='".ECART_FORM_PATH."&sec=update_order_success_content_management'>";
		$content = show_ecart_config('order_success_content_management');

		wp_editor( $content, 'listingeditor', $settings = array('textarea_name' => post_content) );

		echo "<br>";
		echo "<input type='button' class='button-secondary' value='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' onclick=window.location='".ECART_FORM_PATH."'>";
		echo "&nbsp;&nbsp;";
		echo "<input type='submit' class='button-primary' value='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Update&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'>";

		echo "</form>";
		echo "</div>";

		echo "<style>
		.mceContentBody {
			max-width: none !important;
		}
		</style>
		";

	}

	function update_order_success_content_management()
	{
		//$post_content = $this->replace_char($_POST['post_content']);
		$post_content = $_POST['post_content'];

		$qry_cek_content = mysql_query("SELECT * FROM wp_ecart_config WHERE config_name='order_success_content_management' ");
		if(mysql_num_rows($qry_cek_content) == 0)
			mysql_query("INSERT INTO wp_ecart_config (config_name, config_value) VALUES ('order_success_content_management', \"$post_content\") ");
		else
			mysql_query("UPDATE wp_ecart_config SET config_value=\"$post_content\" WHERE config_name='order_success_content_management' ");

		echo "<script>alert('Order Email Content was Updated');window.location='".ECART_FORM_PATH."'</script>";
	}

	function replace_char($char)
	{
		$char = str_replace('"','""',$char);
		$char = str_replace("\\","\\\\",$char);

		return $char;
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
}

?>